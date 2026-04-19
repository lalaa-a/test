(function () {
	'use strict';

	const root = document.getElementById('driversExploreTop');
	if (!root) {
		return;
	}

	const searchInput = root.querySelector('#driverSearch');
	const searchButton = root.querySelector('#driverSearchButton');
	const resultsInfo = root.querySelector('#searchResultsInfo');
	const noResults = root.querySelector('#driverNoResults');
	const filterDetails = root.querySelector('#filterToggle');

	const sections = Array.from(root.querySelectorAll('.drivers-section[data-filter]'));
	const cards = Array.from(root.querySelectorAll('.driver-card'));
	const filterChips = Array.from(root.querySelectorAll('.filter-chip[data-category]'));
	const filterDropdownItems = Array.from(root.querySelectorAll('.filter-dropdown-item[data-category]'));

	if (!searchInput || sections.length === 0 || cards.length === 0) {
		return;
	}

	let activeCategory = getInitialCategory();

	function getInitialCategory() {
		const activeChip = filterChips.find(function (chip) {
			return chip.classList.contains('active');
		});

		if (activeChip && activeChip.dataset.category) {
			return String(activeChip.dataset.category);
		}

		const activeDropdownItem = filterDropdownItems.find(function (item) {
			return item.classList.contains('active');
		});

		if (activeDropdownItem && activeDropdownItem.dataset.category) {
			return String(activeDropdownItem.dataset.category);
		}

		return 'all';
	}

	function normalizeTerm(value) {
		return String(value || '').toLowerCase().trim();
	}

	function getCategoryLabel(category) {
		const normalizedCategory = String(category || 'all');
		const source = filterChips.find(function (chip) {
			return chip.dataset.category === normalizedCategory;
		}) || filterDropdownItems.find(function (item) {
			return item.dataset.category === normalizedCategory;
		});

		if (!source) {
			return 'All categories';
		}

		return source.textContent.replace(/\s*\(\d+\)\s*$/, '').trim() || 'All categories';
	}

	function setActiveCategory(category) {
		activeCategory = String(category || 'all');

		filterChips.forEach(function (chip) {
			chip.classList.toggle('active', chip.dataset.category === activeCategory);
		});

		filterDropdownItems.forEach(function (item) {
			item.classList.toggle('active', item.dataset.category === activeCategory);
		});
	}

	function cardMatches(card, term) {
		const searchBlob = normalizeTerm(card.getAttribute('data-search') || '');
		if (searchBlob !== '') {
			return searchBlob.indexOf(term) !== -1;
		}

		return normalizeTerm(card.textContent).indexOf(term) !== -1;
	}

	function sectionIncludedByCategory(sectionFilter) {
		return activeCategory === 'all' || sectionFilter === activeCategory;
	}

	function applySearch() {
		const term = normalizeTerm(searchInput.value);
		let visibleCards = 0;
		let visibleSections = 0;

		sections.forEach(function (section) {
			const sectionFilter = String(section.dataset.filter || 'all');
			const categoryMatch = sectionIncludedByCategory(sectionFilter);
			const sectionCards = Array.from(section.querySelectorAll('.driver-card'));
			let sectionVisibleCards = 0;

			sectionCards.forEach(function (card) {
				const matchesTerm = term === '' || cardMatches(card, term);
				const visible = categoryMatch && matchesTerm;

				card.classList.toggle('is-search-hidden', !visible);

				if (visible) {
					sectionVisibleCards += 1;
					visibleCards += 1;
				}
			});

			const showSection = categoryMatch && sectionVisibleCards > 0;
			section.classList.toggle('is-search-hidden', !showSection);

			if (showSection) {
				visibleSections += 1;
			}
		});

		if (noResults) {
			noResults.style.display = visibleCards === 0 ? 'block' : 'none';
		}

		if (!resultsInfo) {
			return;
		}

		if (term === '' && activeCategory === 'all') {
			resultsInfo.style.display = 'none';
			resultsInfo.textContent = '';
			return;
		}

		const queryPart = term === '' ? '' : ' for "' + term + '"';
		resultsInfo.style.display = 'block';
		resultsInfo.textContent = 'Showing ' + visibleCards + ' result(s) in ' + visibleSections + ' section(s)' + queryPart + ' (' + getCategoryLabel(activeCategory) + ').';
	}

	function handleCategorySelection(event) {
		event.preventDefault();

		const trigger = event.currentTarget;
		const category = trigger.dataset.category || 'all';
		setActiveCategory(category);

		if (filterDetails && filterDetails.hasAttribute('open')) {
			filterDetails.removeAttribute('open');
		}

		const href = trigger.getAttribute('href');
		if (href && href.charAt(0) === '#') {
			const targetSection = root.querySelector(href);
			if (targetSection) {
				targetSection.scrollIntoView({
					behavior: 'smooth',
					block: 'start'
				});
			}
		}

		applySearch();
	}

	searchInput.addEventListener('input', applySearch);
	searchInput.addEventListener('keydown', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			applySearch();
		}
	});

	if (searchButton) {
		searchButton.addEventListener('click', applySearch);
	}

	filterChips.forEach(function (chip) {
		chip.addEventListener('click', handleCategorySelection);
	});

	filterDropdownItems.forEach(function (item) {
		item.addEventListener('click', handleCategorySelection);
	});

	setActiveCategory(activeCategory);
	applySearch();
})();
