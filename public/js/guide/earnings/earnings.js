
(function () {
    if (window.EarningsManager) {
        if (window.earningsManager) {
            delete window.earningsManager;
        }
        delete window.EarningsManager;
    }

    class EarningsManager {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.charts = {};
            this.earnings = { pending: [], paid: [], refunded: [] };
            this.filteredEarnings = { pending: [], paid: [], refunded: [] };
            this.summary = null;
            this.colspans = { pending: 6, paid: 6, refunded: 7 };
            this.init();
        }

        init() {
            this.loadChartJS().then(() => {
                this.bindEvents();
                this.loadAll();
            });
        }

        loadChartJS() {
            return new Promise((resolve) => {
                if (window.Chart) { resolve(); return; }
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                script.onload = resolve;
                document.head.appendChild(script);
            });
        }

        bindEvents() {
            // Section navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const section = link.dataset.section;
                    if (section) this.switchSection(section);
                });
            });

            // Search inputs
            const pendingSearch = document.getElementById('pendingSearchInput');
            if (pendingSearch) pendingSearch.addEventListener('input', () => this.filterTable('pending'));

            const paidSearch = document.getElementById('paidSearchInput');
            if (paidSearch) paidSearch.addEventListener('input', () => this.filterTable('paid'));

            const refundedSearch = document.getElementById('refundedSearchInput');
            if (refundedSearch) refundedSearch.addEventListener('input', () => this.filterTable('refunded'));

            // Paid site filter
            const paidSiteFilter = document.getElementById('paidSiteFilter');
            if (paidSiteFilter) paidSiteFilter.addEventListener('change', () => this.filterTable('paid'));
        }

        async loadAll() {
            try {
                const [summaryRes, pendingRes, paidRes, refundedRes, monthlyRes] = await Promise.all([
                    fetch(`${this.URL_ROOT}/Guide/getEarningsSummary`),
                    fetch(`${this.URL_ROOT}/Guide/getEarningsByStatus/pending`),
                    fetch(`${this.URL_ROOT}/Guide/getEarningsByStatus/paid`),
                    fetch(`${this.URL_ROOT}/Guide/getEarningsByStatus/refunded`),
                    fetch(`${this.URL_ROOT}/Guide/getMonthlyEarnings`)
                ]);

                const [summaryData, pendingData, paidData, refundedData, monthlyData] = await Promise.all([
                    this.parseJson(summaryRes, 'summary'),
                    this.parseJson(pendingRes, 'pending'),
                    this.parseJson(paidRes, 'paid'),
                    this.parseJson(refundedRes, 'refunded'),
                    this.parseJson(monthlyRes, 'monthly')
                ]);

                if (summaryData.success) {
                    this.summary = summaryData.summary;
                    this.updateStats();
                }

                if (pendingData.success)  this.earnings.pending  = pendingData.earnings  || [];
                if (paidData.success)     this.earnings.paid     = paidData.earnings     || [];
                if (refundedData.success) this.earnings.refunded = refundedData.earnings || [];

                this.filteredEarnings = {
                    pending:  [...this.earnings.pending],
                    paid:     [...this.earnings.paid],
                    refunded: [...this.earnings.refunded]
                };

                this.renderTable('pending');
                this.renderTable('paid');
                this.renderTable('refunded');
                this.updateNavBadges();

                if (monthlyData.success) {
                    this.renderMonthlyChart(monthlyData.monthly || []);
                }
                this.renderBreakdownChart();

            } catch (err) {
                console.error('Error loading earnings:', err);
                if (window.showNotification) window.showNotification('Error loading earnings data', 'error');
            }
        }

        async parseJson(response, label) {
            const contentType = response.headers.get('content-type') || '';

            if (!response.ok) {
                const body = await response.text();
                throw new Error(`Earnings ${label} request failed (${response.status}): ${body.slice(0, 180)}`);
            }

            if (!contentType.includes('application/json')) {
                const body = await response.text();
                throw new Error(`Earnings ${label} endpoint did not return JSON: ${body.slice(0, 180)}`);
            }

            return response.json();
        }

        updateStats() {
            const s = this.summary;
            if (!s) return;
            this.setText('statTotalEarned',    this.fmt(s.total_earned));
            this.setText('statPendingAmount',  this.fmt(s.pending_amount));
            this.setText('statPendingCount',   s.pending_count  || 0);
            this.setText('statPaidAmount',     this.fmt(s.paid_amount));
            this.setText('statPaidCount',      s.paid_count     || 0);
            this.setText('statRefundedAmount', this.fmt(s.refunded_amount));
            this.setText('statRefundedCount',  s.refunded_count || 0);
        }

        updateNavBadges() {
            this.setText('navPendingCount',  this.earnings.pending.length);
            this.setText('navPaidCount',     this.earnings.paid.length);
            this.setText('navRefundedCount', this.earnings.refunded.length);
        }

        switchSection(section) {
            document.querySelectorAll('.earnings-section').forEach(el => el.style.display = 'none');
            const target = document.getElementById(`${section}-section`);
            if (target) target.style.display = 'block';

            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            const activeLink = document.querySelector(`.nav-link[data-section="${section}"]`);
            if (activeLink) activeLink.classList.add('active');
        }

        filterTable(section) {
            const searchEl = document.getElementById(`${section}SearchInput`);
            const term = searchEl ? searchEl.value.toLowerCase().trim() : '';

            let data = [...this.earnings[section]];

            if (term) {
                data = data.filter(row => {
                    const tripId  = String(row.tripId       || '').toLowerCase();
                    const reason  = String(row.refundReason || '').toLowerCase();
                    return tripId.includes(term) || reason.includes(term);
                });
            }

            if (section === 'paid') {
                const siteFilter = document.getElementById('paidSiteFilter');
                if (siteFilter && siteFilter.value !== 'all') {
                    const val = parseInt(siteFilter.value);
                    data = data.filter(row => parseInt(row.pDoneSite) === val);
                }
            }

            this.filteredEarnings[section] = data;
            this.renderTable(section);
        }

        renderTable(section) {
            const tbody = document.getElementById(`${section}TableBody`);
            if (!tbody) return;

            const data = this.filteredEarnings[section];

            if (!data || data.length === 0) {
                const icons = { pending: 'inbox', paid: 'check-circle', refunded: 'undo-alt' };
                const msgs  = { pending: 'No pending payments', paid: 'No paid earnings yet', refunded: 'No refunded trips' };
                tbody.innerHTML = `
                    <tr class="no-data-row">
                        <td colspan="${this.colspans[section]}">
                            <i class="fas fa-${icons[section]}"></i>
                            <p>${msgs[section]}</p>
                        </td>
                    </tr>`;
                return;
            }

            if (section === 'pending')  tbody.innerHTML = data.map((r, i) => this.buildPendingRow(r, i + 1)).join('');
            if (section === 'paid')     tbody.innerHTML = data.map((r, i) => this.buildPaidRow(r, i + 1)).join('');
            if (section === 'refunded') tbody.innerHTML = data.map((r, i) => this.buildRefundedRow(r, i + 1)).join('');
        }

        buildPendingRow(r, idx) {
            return `
                <tr>
                    <td>${idx}</td>
                    <td class="trip-id-cell">#${r.tripId || '-'}</td>
                    <td class="amount-cell">${this.fmt(r.guideCharge)}</td>
                    <td>${this.fmt(r.totalTripCharge)}</td>
                    <td><span class="status-badge pending"><i class="fas fa-clock"></i> Awaiting Payment</span></td>
                    <td>${this.formatDate(r.createdAt)}</td>
                </tr>`;
        }

        buildPaidRow(r, idx) {
            const siteStatus = parseInt(r.pDoneSite)
                ? '<span class="status-badge paid"><i class="fas fa-check"></i> Received</span>'
                : '<span class="status-badge awaiting"><i class="fas fa-hourglass-half"></i> Awaiting Payout</span>';
            return `
                <tr>
                    <td>${idx}</td>
                    <td class="trip-id-cell">#${r.tripId || '-'}</td>
                    <td class="amount-cell">${this.fmt(r.guideCharge)}</td>
                    <td>${this.formatDate(r.pDateTraveller)}</td>
                    <td>${r.pDateSite ? this.formatDate(r.pDateSite) : '<span style="color:#999">-</span>'}</td>
                    <td>${siteStatus}</td>
                </tr>`;
        }

        buildRefundedRow(r, idx) {
            const refundAmt = r.refundAmount != null ? r.refundAmount : r.guideCharge;
            const siteStatus = parseInt(r.pDoneSite)
                ? '<span class="status-badge paid"><i class="fas fa-check"></i> Received</span>'
                : '<span class="status-badge awaiting"><i class="fas fa-hourglass-half"></i> Awaiting Payout</span>';
            return `
                <tr>
                    <td>${idx}</td>
                    <td class="trip-id-cell">#${r.tripId || '-'}</td>
                    <td class="amount-cell">${this.fmt(r.guideCharge)}</td>
                    <td class="amount-cell">${this.fmt(refundAmt)}</td>
                    <td>${this.formatDate(r.refundDate)}</td>
                    <td class="reason-cell" title="${this.esc(r.refundReason || '')}">${this.esc(r.refundReason || '-')}</td>
                    <td>${siteStatus}</td>
                </tr>`;
        }

        renderMonthlyChart(monthly) {
            const canvas = document.getElementById('monthlyEarningsChart');
            if (!canvas) return;

            if (this.charts.monthly) this.charts.monthly.destroy();

            const labels   = monthly.map(m => m.monthLabel);
            const paid     = monthly.map(m => parseFloat(m.paid)     || 0);
            const pending  = monthly.map(m => parseFloat(m.pending)  || 0);
            const refunded = monthly.map(m => parseFloat(m.refunded) || 0);

            this.charts.monthly = new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Paid',
                            data: paid,
                            borderColor: '#2e7d32',
                            backgroundColor: 'rgba(46, 125, 50, 0.08)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Pending',
                            data: pending,
                            borderColor: '#f57c00',
                            backgroundColor: 'rgba(245, 124, 0, 0.08)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Refunded',
                            data: refunded,
                            borderColor: '#c62828',
                            backgroundColor: 'rgba(198, 40, 40, 0.08)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { size: 12 }, usePointStyle: true, boxWidth: 8 }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` LKR ${ctx.parsed.y.toLocaleString('en-LK', { minimumFractionDigits: 2 })}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => 'LKR ' + v.toLocaleString('en-LK') },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        renderBreakdownChart() {
            const canvas = document.getElementById('paymentBreakdownChart');
            if (!canvas) return;

            if (this.charts.breakdown) this.charts.breakdown.destroy();

            const s        = this.summary || {};
            const paid     = parseFloat(s.paid_amount)     || 0;
            const pending  = parseFloat(s.pending_amount)  || 0;
            const refunded = parseFloat(s.refunded_amount) || 0;
            const total    = paid + pending + refunded || 1;

            const colors = ['#2e7d32', '#f57c00', '#c62828'];
            const labels = ['Paid', 'Pending', 'Refunded'];
            const values = [paid, pending, refunded];

            this.charts.breakdown = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` LKR ${ctx.parsed.toLocaleString('en-LK', { minimumFractionDigits: 2 })}`
                            }
                        }
                    }
                }
            });

            const legend = document.getElementById('doughnutLegend');
            if (legend) {
                legend.innerHTML = labels.map((label, i) => `
                    <div class="legend-item">
                        <span class="legend-dot" style="background:${colors[i]}"></span>
                        <span class="legend-label">${label}</span>
                        <span class="legend-value">${((values[i] / total) * 100).toFixed(1)}%</span>
                    </div>`).join('');
            }
        }

        fmt(val) {
            const n = parseFloat(val) || 0;
            return 'LKR ' + n.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        formatDate(dt) {
            if (!dt) return '<span style="color:#999">-</span>';
            const d = new Date(dt);
            if (isNaN(d)) return '<span style="color:#999">-</span>';
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        esc(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        setText(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        }
    }

    window.EarningsManager = EarningsManager;
    window.earningsManager = new EarningsManager();
})();
