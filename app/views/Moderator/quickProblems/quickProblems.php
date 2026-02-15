<!-- Quick Problems Center -->
<div class="problems-center">
    <!-- Header -->
    <div class="problems-header">
        <div class="problems-title">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Users Problem Center</h2>
        </div>
        <div class="problems-stats">
            <div class="stat-pill pending-pill">
                <i class="fas fa-clock"></i>
                <span id="pendingCount">0</span> Pending
            </div>
            <div class="stat-pill completed-pill">
                <i class="fas fa-check-circle"></i>
                <span id="completedCount">0</span> Completed
            </div>
            <div class="stat-pill total-pill">
                <i class="fas fa-list"></i>
                <span id="totalCount">0</span> Total
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="problems-filters">
        <button class="filter-tab active" data-filter="all">
            <i class="fas fa-inbox"></i> All Issues
        </button>
        <button class="filter-tab" data-filter="pending">
            <i class="fas fa-clock"></i> Pending
        </button>
        <button class="filter-tab" data-filter="completed">
            <i class="fas fa-check-circle"></i> Completed
        </button>
        <button class="refresh-problems-btn" id="refreshProblemsBtn" title="Refresh">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <!-- Problems List -->
    <div class="problems-list" id="problemsList">
        <!-- Populated dynamically by JS -->
    </div>
</div>

<!-- Problem Detail Modal -->
<div class="problem-modal-overlay" id="problemModal" style="display: none;">
    <div class="problem-modal">
        <div class="modal-header">
            <h3><i class="fas fa-file-alt"></i> Problem Details</h3>
            <button class="modal-close-btn" id="closeModalBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Filled dynamically -->
        </div>
        <div class="modal-footer" id="modalFooter">
            <!-- Action buttons inserted dynamically -->
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="qp-toast-container" id="qpToastContainer"></div>
