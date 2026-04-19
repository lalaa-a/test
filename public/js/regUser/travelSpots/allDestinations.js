(function () {
    const searchInput = document.getElementById('destinationSearch');
    const searchButton = document.getElementById('searchButton');
    const cards = Array.from(document.querySelectorAll('.spot-card'));
    const provinceFilter = document.getElementById('provinceFilter');
    const provinceForm = document.getElementById('provinceFilterForm');
    const resultsInfo = document.getElementById('searchResultsInfo');
    const serverNoResults = document.getElementById('spotNoResults');
    const clientNoResults = document.getElementById('spotNoResultsClient');

    if (provinceFilter && provinceForm) {
        provinceFilter.addEventListener('change', function () {
            provinceForm.submit();
        });
    }

    if (!cards.length) {
        return;
    }

    const activeFilterChip = document.querySelector('.filter-chip.active');
    const activeFilterLabel = activeFilterChip
        ? activeFilterChip.textContent.replace(/\s*\(\d+\)\s*$/, '').trim()
        : 'Selected Filter';

    const activeProvinceLabel = provinceFilter && provinceFilter.value !== 'all'
        ? provinceFilter.value
        : 'All Provinces';

    function updateResultsInfo(visibleCount, term) {
        if (!resultsInfo) {
            return;
        }

        if (term) {
            resultsInfo.textContent = 'Showing ' + visibleCount + ' destination(s) for "' + term + '" in ' + activeFilterLabel + ' (' + activeProvinceLabel + ')';
            return;
        }

        resultsInfo.textContent = 'Showing ' + visibleCount + ' destination(s) in ' + activeFilterLabel + ' (' + activeProvinceLabel + ')';
    }

    function applySearch() {
        const term = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        cards.forEach(function (card) {
            const searchBlob = (card.getAttribute('data-search') || '').toLowerCase();
            const isVisible = term === '' || searchBlob.indexOf(term) !== -1;

            card.classList.toggle('is-hidden', !isVisible);
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (serverNoResults) {
            serverNoResults.style.display = 'none';
        }

        if (clientNoResults) {
            clientNoResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        updateResultsInfo(visibleCount, term);
    }

    if (searchInput) {
        searchInput.addEventListener('input', applySearch);
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                applySearch();
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', applySearch);
    }
})();
