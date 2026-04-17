(function(){
    // Check if TravelSpotManager already exists and clean up
    if (window.GuideSelectionManager) {
        console.log('TravelSpotManger already exists, cleaning up...');
        // Clean up any existing instance
        if (window.guideSelectionManager) {
            // Clean up event listeners if needed
            delete window.guideSelectionManager;
        }
        delete window.GuideSelectionManager;
    }
    
    class GuideSelectionManager {

        constructor() {
            this.selectedGuideId = null;
            this.selectedCard = null;
            this.selectedGuideName = null;
            this.guidesData = []; // Store fetched guides data
            const urlParams = new URLSearchParams(window.location.search);
            this.tripId = urlParams.get('tripId');

            this.initializeElements();
            this.addEventListeners();
            // init filters with manually fetched data
            this.fetchAndInitFilters();
        }

        initializeElements(){
            this.confirmModel = document.getElementById('confirmationModal');
            this.selectingGuide = document.getElementById('selecting-guide-name');
            this.confirmBtn = document.getElementById('confirmBtn');
            this.cancelBtn = document.getElementById("cancelBtn");
            // Filter popup elements
            this.filterToggle = document.getElementById('filterToggle');
            this.filterPopup = document.getElementById('filterPopup');
            this.filterCloseBtn = document.getElementById('filterCloseBtn');
            this.filterApplyBtn = document.getElementById('filterApplyBtn');
            this.filterResetBtn = document.getElementById('filterResetBtn');
            this.filterVerified = document.getElementById('filterVerified');

            // new advanced filter controls
            this.filterRatingSlider = document.getElementById('filterRatingSlider');
            this.filterRatingDisplay = document.getElementById('filterRatingDisplay');
            this.filterPriceMinSlider = document.getElementById('filterPriceMinSlider');
            this.filterPriceMaxSlider = document.getElementById('filterPriceMaxSlider');
            this.filterPriceMinDisplay = document.getElementById('filterPriceMinDisplay');
            this.filterPriceMaxDisplay = document.getElementById('filterPriceMaxDisplay');
            this.filterAgeMinSlider = document.getElementById('filterAgeMinSlider');
            this.filterAgeMaxSlider = document.getElementById('filterAgeMaxSlider');
            this.filterAgeMinDisplay = document.getElementById('filterAgeMinDisplay');
            this.filterAgeMaxDisplay = document.getElementById('filterAgeMaxDisplay');
            this.filterAvailable = document.getElementById('filterAvailable');
            this.filterChargeType = document.getElementById('filterChargeType');
            this.filterGroupMin = document.getElementById('filterGroupMin');
            this.filterGroupMax = document.getElementById('filterGroupMax');
            this.filterLanguageSelect = document.getElementById('filterLanguageSelect');
            this.filterMatchCount = document.getElementById('filterMatchCount');
            this.filterActiveCount = document.getElementById('filterActiveCount');
            this.filterActiveChips = document.getElementById('filterActiveChips');
            this.filterLivePreview = document.getElementById('filterLivePreview');
        }

        addEventListeners(){

            this.confirmBtn.addEventListener('click', () => {
                this.selectGuide(this.selectedGuideId);
                this.hideModal();
            } )

            this.cancelBtn.addEventListener('click', () => this.hideModal());

            // Add event listeners for select and view buttons
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('select-driver-btn')) {
                    const card = e.target.closest('.driver-card');
                    if (card) {
                        const guideId = card.dataset.userId;
                        const guideName = card.querySelector('.driver-name').textContent;
                        this.showConfirmation(guideId, guideName);
                    }
                }

                if (e.target.classList.contains('view-driver-btn')) {
                    const card = e.target.closest('.driver-card');
                    if (card) {
                        const guideId = card.dataset.userId;
                        this.viewGuide(guideId);
                    }
                }
            });

            // filter popup events
            if(this.filterToggle){
                this.filterToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openFilterPopup();
                });
            }

            if(this.filterCloseBtn){
                this.filterCloseBtn.addEventListener('click', () => this.closeFilterPopup());
            }

            if(this.filterApplyBtn){
                this.filterApplyBtn.addEventListener('click', () => this.applyFilters());
            }

            if(this.filterResetBtn){
                this.filterResetBtn.addEventListener('click', () => this.resetFilters());
            }

            // close on overlay click
            if(this.filterPopup){
                this.filterPopup.addEventListener('click', (e) => {
                    if(e.target === this.filterPopup) this.closeFilterPopup();
                });
            }

            // close on ESC
            document.addEventListener('keydown', (e) => {
                if(e.key === 'Escape' && this.filterPopup?.classList.contains('show')){
                    this.closeFilterPopup();
                }
            });

            // quick presets
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.applyPreset(btn.dataset.preset);
                });
            });

            // live preview listeners
            const liveInputs = [
                this.filterRatingSlider,
                this.filterPriceMinSlider,
                this.filterPriceMaxSlider,
                this.filterAgeMinSlider,
                this.filterAgeMaxSlider,
                this.filterVerified,
                this.filterAvailable,
                this.filterChargeType,
                this.filterGroupMin,
                this.filterGroupMax,
                this.filterLanguageSelect
            ].filter(Boolean);

            liveInputs.forEach(el => {
                el.addEventListener('input', () => {
                    this.updateSliderFills();
                    this.updateHeaderAndChips();
                    if(this.filterLivePreview?.checked){
                        this.applyFilters({ close: false });
                    }
                });
                el.addEventListener('change', () => {
                    this.updateSliderFills();
                    this.updateHeaderAndChips();
                    if(this.filterLivePreview?.checked){
                        this.applyFilters({ close: false });
                    }
                });
            });

            // language dropdown already included in liveInputs above
            // Make main filter chips behave as tabs
            document.querySelectorAll('.filter-chip').forEach(chip => {
                chip.addEventListener('click', (e) => {
                    const category = chip.dataset.category;
                    // mark active
                    document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c === chip));
                    // show matching section
                    this.showFilterSection(category);
                });
            });
        }

        showFilterSection(category){
            // Show only the section matching the selected category; if 'all', show all sections
            const sections = document.querySelectorAll('section.drivers-section');
            sections.forEach(sec => {
                try{
                    const key = sec.getAttribute('data-filter');
                    if(!key) return;
                    // Show only the section that matches the selected category.
                    // For 'all', show only the section whose data-filter is 'all'.
                    if(category === key){
                        sec.style.display = '';
                    } else {
                        sec.style.display = 'none';
                    }
                }catch(e){}
            });
            // scroll to top of drivers list for context
            const first = document.querySelector('section.drivers-section [data-filter]') || document.querySelector('.drivers-container, .drivers-container-grid');
            const container = document.querySelector('.drivers-container') || document.querySelector('.drivers-container-grid');
            if(container) container.scrollTop = 0;
        }

        updateMainFilterTabs(){
            // ensure initial active tab matches existing .filter-chip.active or defaults to 'all'
            const active = document.querySelector('.filter-chip.active');
            const category = active?.dataset?.category || 'all';
            this.showFilterSection(category);
        }

        async fetchGuidesData() {
            try {
                const spotId = window.location.pathname.split('/').pop();
                const baseUrl = window.location.origin + '/test';
                const tripQuery = this.tripId ? `?tripId=${encodeURIComponent(this.tripId)}` : '';
                const response = await fetch(`${baseUrl}/RegUser/getGuidesData/${spotId}${tripQuery}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    return data.guides || [];
                } else {
                    console.error('API error:', data.message);
                    return [];
                }
                
            } catch (error) {
                console.error('Failed to fetch guides data:', error);
                return [];
            }
        }

        async fetchAndInitFilters() {
            console.log('Fetching guides data manually...');
            this.guidesData = await this.fetchGuidesData();
            console.log('Fetched guides data:', this.guidesData);
            this.initFilters();
            // initialize main filter tabs visibility
            this.updateMainFilterTabs();
        }

        showConfirmation(guideId, guideName) {
            this.selectedGuideId = guideId;
            this.selectedGuideName = guideName;
            this.selectedCard = document.getElementById(`guide-${guideId}`);
            if (this.selectedCard) {
                this.selectedCard.classList.add('selected');
            }
            this.confirmModel.classList.add('show');
            this.selectingGuide.innerHTML = guideName;
        }

        hideModal() {
            this.confirmModel.classList.remove('show');
            if (this.selectedCard) {
                this.selectedCard.classList.remove('selected');
            }
            this.selectedGuideId = null;
            this.selectedCard = null;
        }

        selectGuide(guideId) {
            console.log('Selected guide with ID:', guideId);
            
            // Find guide in guidesData to get all details including charge info
            const guide = this.guidesData.find(g => g.userId == guideId);

            
            if (!guide) {
                console.error('Guide not found in guidesData');
                return;
            }
            
            // Get guide data from the selected card
            const selectedCard = document.querySelector(`[data-user-id="${guideId}"]`);
            const guideData = {
                guideId: guideId,
                fullName: selectedCard.querySelector('.driver-name').textContent,
                averageRating: selectedCard.querySelector('.rating').textContent,
                profilePhoto: selectedCard.querySelector('.driver-avatar img').src,
                bio: selectedCard.querySelector('.driver-description').textContent,
                // Include charge information
                baseCharge: guide.baseCharge || 0,
                convertedCharge: guide.convertedCharge || guide.baseCharge || 0,
                chargeType: guide.chargeType ,
                currency: guide.currency || 'LKR',
                currencySymbol: guide.currencySymbol || 'Rs',
                formattedCharge: guide.formattedCharge || 'Rs 0',
                locationId: guide.locationId
            };

            console.log('Guide charge data:', {
                baseCharge: guideData.baseCharge,
                convertedCharge: guideData.convertedCharge,
                chargeType: guideData.chargeType,
                currency: guideData.currency
            });

            if (window.opener && !window.opener.closed) {
                if (window.location.origin === window.opener.location.origin) {
                    console.log('Same origin, calling function directly.');
                    window.opener.tripEventListManager.handleGuideSelection(guideData);
                    window.close();
                } else {
                    window.opener.postMessage({
                        type: 'GUIDE_SELECTED',
                        guideData: guideData
                    }, window.opener.location.origin);
                    window.close();
                }
            }
        }  

        // --- Filter popup and logic ---
        initFilters(){
            console.log("init filters called");
            const guides = this.guidesData || [];
            if(!guides.length) {
                console.log('No guides data available for filtering');
                return;
            }

            // gather unique languages
            const langSet = new Set();
            let maxPrice = 0;
            let maxAge = 0;
            let minAge = Infinity;

            guides.forEach(g => {
                if(g.languages){
                    try{
                        const parts = g.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                        parts.forEach(p=>langSet.add(p));
                    }catch(e){
                        console.error('Error parsing languages:', e, g.languages);
                    }
                }
                if(g.baseCharge) maxPrice = Math.max(maxPrice, parseFloat(g.baseCharge));
                if(g.age) maxAge = Math.max(maxAge, parseInt(g.age));
                if(g.age) minAge = Math.min(minAge, parseInt(g.age));
            });

            if(!isFinite(minAge) || minAge <= 0) minAge = 18;
            minAge = Math.max(18, Math.floor(minAge));

            // populate languages as dropdown options
            if(this.filterLanguageSelect){
                // Keep the "All Languages" option and add language options
                const currentValue = this.filterLanguageSelect.value;
                this.filterLanguageSelect.innerHTML = '<option value="">All Languages</option>';
                
                Array.from(langSet).sort().forEach(lang => {
                    const opt = document.createElement('option');
                    opt.value = lang;
                    opt.textContent = lang;
                    this.filterLanguageSelect.appendChild(opt);
                });
                
                // Restore previous selection if it still exists
                if(currentValue && langSet.has(currentValue)) {
                    this.filterLanguageSelect.value = currentValue;
                }
            }

            const computedMaxPrice = Math.ceil(maxPrice || 1000);
            if(this.filterPriceMinSlider && this.filterPriceMaxSlider && computedMaxPrice > 0){
                this.filterPriceMinSlider.min = 0;
                this.filterPriceMinSlider.max = computedMaxPrice;
                this.filterPriceMinSlider.value = 0;

                this.filterPriceMaxSlider.min = 0;
                this.filterPriceMaxSlider.max = computedMaxPrice;
                this.filterPriceMaxSlider.value = computedMaxPrice;

                if(this.filterPriceMinDisplay) this.filterPriceMinDisplay.textContent = 'Any';
                if(this.filterPriceMaxDisplay) this.filterPriceMaxDisplay.textContent = 'Any';
            }

            const computedMaxAge = Math.max(80, Math.ceil(maxAge || 80));
            if(this.filterAgeMinSlider && this.filterAgeMaxSlider && computedMaxAge > 0){
                this.filterAgeMinSlider.min = minAge;
                this.filterAgeMinSlider.max = computedMaxAge;
                this.filterAgeMinSlider.value = minAge;

                this.filterAgeMaxSlider.min = minAge;
                this.filterAgeMaxSlider.max = computedMaxAge;
                this.filterAgeMaxSlider.value = computedMaxAge;

                if(this.filterAgeMinDisplay) this.filterAgeMinDisplay.textContent = 'Any';
                if(this.filterAgeMaxDisplay) this.filterAgeMaxDisplay.textContent = 'Any';
            }

            // wire slider displays
            if(this.filterRatingSlider && this.filterRatingDisplay){
                this.filterRatingSlider.addEventListener('input', () => {
                    this.filterRatingDisplay.textContent = this.filterRatingSlider.value;
                });
            }
            const syncRangePair = (minSlider, maxSlider) => {
                if(!minSlider || !maxSlider) return;
                const minVal = parseFloat(minSlider.value);
                const maxVal = parseFloat(maxSlider.value);
                if(minVal > maxVal){
                    // keep the handle the user moved as-is by pushing the other
                    if(document.activeElement === minSlider){
                        maxSlider.value = String(minVal);
                    } else {
                        minSlider.value = String(maxVal);
                    }
                }
            };

            const updateMinMaxLabels = (minSlider, maxSlider, minLabelEl, maxLabelEl) => {
                if(!minSlider || !maxSlider) return;
                const minVal = parseFloat(minSlider.value);
                const maxVal = parseFloat(maxSlider.value);
                const minMin = parseFloat(minSlider.min || 0);
                const maxMax = parseFloat(maxSlider.max || 0);
                if(minLabelEl) minLabelEl.textContent = (minVal <= minMin) ? 'Any' : String(Math.round(minVal));
                if(maxLabelEl) maxLabelEl.textContent = (maxVal >= maxMax) ? 'Any' : String(Math.round(maxVal));
            };

            if(this.filterPriceMinSlider && this.filterPriceMaxSlider){
                const onPriceChange = () => {
                    syncRangePair(this.filterPriceMinSlider, this.filterPriceMaxSlider);
                    updateMinMaxLabels(this.filterPriceMinSlider, this.filterPriceMaxSlider, this.filterPriceMinDisplay, this.filterPriceMaxDisplay);
                };
                this.filterPriceMinSlider.addEventListener('input', onPriceChange);
                this.filterPriceMaxSlider.addEventListener('input', onPriceChange);
                onPriceChange();
            }

            if(this.filterAgeMinSlider && this.filterAgeMaxSlider){
                const onAgeChange = () => {
                    syncRangePair(this.filterAgeMinSlider, this.filterAgeMaxSlider);
                    updateMinMaxLabels(this.filterAgeMinSlider, this.filterAgeMaxSlider, this.filterAgeMinDisplay, this.filterAgeMaxDisplay);
                };
                this.filterAgeMinSlider.addEventListener('input', onAgeChange);
                this.filterAgeMaxSlider.addEventListener('input', onAgeChange);
                onAgeChange();
            }

            this.updateSliderFills();
            this.updateHeaderAndChips();
        }

        openFilterPopup(){
            if(this.filterPopup) this.filterPopup.classList.add('show');
            this.updateSliderFills();
            this.updateHeaderAndChips();
        }

        closeFilterPopup(){
            if(this.filterPopup) this.filterPopup.classList.remove('show');
        }

        resetFilters(){
            if(this.filterRatingSlider) this.filterRatingSlider.value = 0, this.filterRatingDisplay.textContent = '0';
            if(this.filterVerified) this.filterVerified.checked = false;
            if(this.filterAvailable) this.filterAvailable.checked = false;
            if(this.filterPriceMinSlider && this.filterPriceMaxSlider){
                this.filterPriceMinSlider.value = this.filterPriceMinSlider.min || 0;
                this.filterPriceMaxSlider.value = this.filterPriceMaxSlider.max || 1000;
                if(this.filterPriceMinDisplay) this.filterPriceMinDisplay.textContent = 'Any';
                if(this.filterPriceMaxDisplay) this.filterPriceMaxDisplay.textContent = 'Any';
            }
            if(this.filterLanguageSelect) {
                this.filterLanguageSelect.value = '';
            }
            if(this.filterAgeMinSlider && this.filterAgeMaxSlider){
                this.filterAgeMinSlider.value = this.filterAgeMinSlider.min || 18;
                this.filterAgeMaxSlider.value = this.filterAgeMaxSlider.max || 80;
                if(this.filterAgeMinDisplay) this.filterAgeMinDisplay.textContent = 'Any';
                if(this.filterAgeMaxDisplay) this.filterAgeMaxDisplay.textContent = 'Any';
            }
            if(this.filterChargeType) this.filterChargeType.value = 'any';
            if(this.filterGroupMin) this.filterGroupMin.value = '';
            if(this.filterGroupMax) this.filterGroupMax.value = '';
            this.updateSliderFills();
            this.updateHeaderAndChips();
            this.applyFilters({ close: false });
        }

        async applyFilters(options = {}){
            const close = options.close !== false;
            
            // Prepare filter data
            const filterData = this.getFilterData();
            const filtersActive = this.isFiltersActive(filterData);

            if (!filtersActive) {
                // No filters active, show all guides
                document.querySelectorAll('.driver-card').forEach(card => {
                    card.style.display = '';
                });
                const guides = this.guidesData || [];
                this.updateMatchCount(guides.length, guides.length);
                if(close) this.closeFilterPopup();
                return;
            }

            try {
                // Get spot ID from current page
                const spotId = window.location.pathname.split('/').pop();
                
                // Show loading state
                this.showLoadingState();
                
                // Make backend request with correct URL format
                const baseUrl = window.location.origin + '/test';
                const tripQuery = this.tripId ? `?tripId=${encodeURIComponent(this.tripId)}` : '';
                const response = await fetch(`${baseUrl}/RegUser/filterGuides/${spotId}${tripQuery}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(filterData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Update UI with filtered results
                    this.displayFilteredGuides(data.guides);
                    this.updateMatchCount(data.count, this.guidesData?.length || 0);
                } else {
                    throw new Error(data.message || 'Filter request failed');
                }
                
            } catch (error) {
                console.error('Filter request failed:', error);
                // Fallback to client-side filtering
                this.applyClientSideFilters(filterData);
            } finally {
                this.hideLoadingState();
                if(close) this.closeFilterPopup();
            }
        }

        getFilterData() {
            const minRating = parseFloat(this.filterRatingSlider?.value || 0) || 0;
            const requireVerified = this.filterVerified?.checked || false;
            const requireAvailable = this.filterAvailable?.checked || false;
            const selectedLang = this.filterLanguageSelect?.value || '';
            const selectedLangs = selectedLang ? [selectedLang] : [];
            const chargeType = this.filterChargeType?.value || 'any';
            const groupMin = parseInt(this.filterGroupMin?.value) || 0;
            const groupMax = parseInt(this.filterGroupMax?.value) || 0;

            const rawMinPrice = parseFloat(this.filterPriceMinSlider?.value) || 0;
            const rawMaxPrice = parseFloat(this.filterPriceMaxSlider?.value) || 0;
            const rawMinAge = parseInt(this.filterAgeMinSlider?.value) || 0;
            const rawMaxAge = parseInt(this.filterAgeMaxSlider?.value) || 0;

            const minPriceMin = parseFloat(this.filterPriceMinSlider?.min || 0);
            const maxPriceMax = parseFloat(this.filterPriceMaxSlider?.max || 0);
            const minAgeMin = parseInt(this.filterAgeMinSlider?.min || 0);
            const maxAgeMax = parseInt(this.filterAgeMaxSlider?.max || 0);

            // If user leaves full range selected, treat it as "Any" (send 0 so backend ignores)
            const minPriceSend = (this.filterPriceMinSlider && rawMinPrice <= minPriceMin) ? 0 : rawMinPrice;
            const maxPriceSend = (this.filterPriceMaxSlider && rawMaxPrice >= maxPriceMax) ? 0 : rawMaxPrice;
            const minAgeSend = (this.filterAgeMinSlider && rawMinAge <= minAgeMin) ? 0 : rawMinAge;
            const maxAgeSend = (this.filterAgeMaxSlider && rawMaxAge >= maxAgeMax) ? 0 : rawMaxAge;

            return {
                rating: minRating,
                verified: requireVerified,
                available: requireAvailable,
                minPrice: minPriceSend,
                maxPrice: maxPriceSend,
                minAge: minAgeSend,
                maxAge: maxAgeSend,
                chargeType: chargeType === 'any' ? '' : chargeType,
                minGroupSize: groupMin || 0,
                maxGroupSize: groupMax || 0,
                languages: selectedLangs
            };
        }

        showLoadingState() {
            const container = document.querySelector('.drivers-container');
            if (container) {
                container.style.opacity = '0.6';
                container.style.pointerEvents = 'none';
            }
        }

        hideLoadingState() {
            const container = document.querySelector('.drivers-container');
            if (container) {
                container.style.opacity = '';
                container.style.pointerEvents = '';
            }
        }

        displayFilteredGuides(guides) {
            const container = document.querySelector('.drivers-container');
            if (!container) return;

            // Hide all existing cards
            document.querySelectorAll('.driver-card').forEach(card => {
                card.style.display = 'none';
            });

            // Create a set of guide IDs for quick lookup
            const guideIds = new Set(guides.map(g => String(g.userId)));

            // Show cards for filtered guides
            document.querySelectorAll('.driver-card').forEach(card => {
                const id = card.getAttribute('data-user-id');
                if (guideIds.has(id)) {
                    card.style.display = '';
                }
            });

            // If no existing cards match, we might need to create new ones
            // For now, we'll just show what we have
        }

        applyClientSideFilters(filterData) {
            // Fallback client-side filtering (original logic)
            const guides = this.guidesData || [];
            const visibleIds = new Set();

            guides.forEach(g => {
                const rating = parseFloat(g.averageRating) || 0;
                const verified = !!g.tlVerified || !!g.dlVerified || false;
                const price = parseFloat(g.baseCharge) || 0;
                const age = parseInt(g.age) || 0;
                const available = g.isActive === 1 || g.isActive === true || false;
                const gMin = parseInt(g.minGroupSize) || 0;
                const gMax = parseInt(g.maxGroupSize) || Infinity;
                const ct = (g.chargeType || '').toString();
                let langs = [];
                if(g.languages){
                    langs = g.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                }

                if(filterData.rating && rating < filterData.rating) return;
                if(filterData.verified && !verified) return;
                if(filterData.minPrice && price < filterData.minPrice) return;
                if(filterData.maxPrice && price > filterData.maxPrice) return;
                if(filterData.minAge && age < filterData.minAge) return;
                if(filterData.maxAge && age > filterData.maxAge) return;
                if(filterData.available && !available) return;
                if(filterData.chargeType && ct !== filterData.chargeType) return;
                if(filterData.minGroupSize && gMax < filterData.minGroupSize) return;
                if(filterData.maxGroupSize && gMin > filterData.maxGroupSize) return;
                if(filterData.languages?.length){
                    const has = filterData.languages.some(l => langs.includes(l));
                    if(!has) return;
                }

                visibleIds.add(String(g.userId));
            });

            // show/hide cards
            document.querySelectorAll('.driver-card').forEach(card => {
                const id = card.getAttribute('data-user-id');
                card.style.display = visibleIds.has(id) ? '' : 'none';
            });

            this.updateMatchCount(visibleIds.size, guides.length);
        }

        isFiltersActive(filterData){
            // Supports both shapes:
            // - backend payload: {rating, verified, available, minPrice, maxPrice, minAge, maxAge, ...}
            // - UI state: {minRating, requireVerified, requireAvailable, selectedLangs, ...}
            const hasMinRating = (filterData.rating ?? filterData.minRating ?? 0) > 0;
            const hasVerified = !!(filterData.verified ?? filterData.requireVerified);
            const hasAvailable = !!(filterData.available ?? filterData.requireAvailable);
            const hasChargeType = !!(filterData.chargeType && filterData.chargeType !== 'any' && filterData.chargeType !== '');
            const hasGroupMin = (filterData.minGroupSize ?? filterData.groupMin ?? 0) > 0;
            const groupMaxVal = (filterData.maxGroupSize ?? filterData.groupMax);
            const hasGroupMax = Number.isFinite(groupMaxVal) && groupMaxVal > 0 && groupMaxVal !== Infinity;

            const langs = filterData.languages ?? filterData.selectedLangs;
            const hasLangs = Array.isArray(langs) && langs.length > 0;

            const minPriceVal = filterData.minPrice ?? 0;
            const maxPriceVal = filterData.maxPrice ?? 0;
            const minAgeVal = filterData.minAge ?? 0;
            const maxAgeVal = filterData.maxAge ?? 0;

            // For UI state, we only consider price/age active if user moved away from bounds.
            const priceMinActive = this.filterPriceMinSlider
                ? Number.isFinite(minPriceVal) && minPriceVal > parseFloat(this.filterPriceMinSlider.min || 0)
                : Number.isFinite(minPriceVal) && minPriceVal > 0;
            const priceMaxActive = this.filterPriceMaxSlider
                ? Number.isFinite(maxPriceVal) && maxPriceVal < parseFloat(this.filterPriceMaxSlider.max || 0)
                : Number.isFinite(maxPriceVal) && maxPriceVal > 0;

            const ageMinActive = this.filterAgeMinSlider
                ? Number.isFinite(minAgeVal) && minAgeVal > parseInt(this.filterAgeMinSlider.min || 0)
                : Number.isFinite(minAgeVal) && minAgeVal > 0;
            const ageMaxActive = this.filterAgeMaxSlider
                ? Number.isFinite(maxAgeVal) && maxAgeVal < parseInt(this.filterAgeMaxSlider.max || 0)
                : Number.isFinite(maxAgeVal) && maxAgeVal > 0;

            return (
                hasMinRating ||
                hasVerified ||
                hasAvailable ||
                priceMinActive ||
                priceMaxActive ||
                ageMinActive ||
                ageMaxActive ||
                hasChargeType ||
                hasGroupMin ||
                hasGroupMax ||
                hasLangs
            );
        }

        updateMatchCount(matchCount, total){
            if(this.filterMatchCount) this.filterMatchCount.textContent = String(matchCount);
            // Also show info near top search section if exists
            const info = document.getElementById('searchResultsInfo');
            if(info){
                if(matchCount === total){
                    info.style.display = 'none';
                } else if(matchCount === 0) {
                    info.style.display = 'block';
                    info.innerHTML = 'No guides match these filters. <button type="button" class="inline-link" id="clearFiltersInline">Clear filters</button>';
                    const clearBtn = document.getElementById('clearFiltersInline');
                    clearBtn?.addEventListener('click', () => this.resetFilters());
                } else {
                    info.style.display = 'block';
                    info.textContent = `Showing ${matchCount} of ${total} guides`;
                }
            }
        }

        updateHeaderAndChips(){
            const guides = this.guidesData || [];
            const state = this.getFilterState();
            const activeLabels = this.getActiveFilterLabels(state);

            if(this.filterResetBtn){
                this.filterResetBtn.disabled = activeLabels.length === 0;
            }

            if(this.filterActiveCount){
                this.filterActiveCount.textContent = String(activeLabels.length);
            }
            if(this.filterActiveChips){
                this.filterActiveChips.innerHTML = '';
                activeLabels.forEach(({ key, label }) => {
                    const chip = document.createElement('span');
                    chip.className = 'active-chip';
                    chip.innerHTML = `${label} <button type="button" aria-label="Remove">×</button>`;
                    chip.querySelector('button')?.addEventListener('click', () => {
                        this.clearFilterByKey(key);
                        this.updateSliderFills();
                        this.updateHeaderAndChips();
                        if(this.filterLivePreview?.checked){
                            this.applyFilters({ close: false });
                        }
                    });
                    this.filterActiveChips.appendChild(chip);
                });
            }

            // match count preview
            const filtered = this.previewFilterCount(guides, state);
            this.updateMatchCount(filtered, guides.length);
        }

        getFilterState(){
            const numberOr = (raw, fallback) => {
                const n = typeof raw === 'string' && raw.trim() === '' ? NaN : Number(raw);
                return Number.isFinite(n) ? n : fallback;
            };

            const minPrice = this.filterPriceMinSlider ? numberOr(this.filterPriceMinSlider.value, 0) : 0;
            const maxPrice = this.filterPriceMaxSlider ? numberOr(this.filterPriceMaxSlider.value, Infinity) : Infinity;
            const minAge = this.filterAgeMinSlider ? numberOr(this.filterAgeMinSlider.value, 0) : 0;
            const maxAge = this.filterAgeMaxSlider ? numberOr(this.filterAgeMaxSlider.value, Infinity) : Infinity;
            return {
                minRating: parseFloat(this.filterRatingSlider?.value || 0) || 0,
                requireVerified: this.filterVerified?.checked || false,
                requireAvailable: this.filterAvailable?.checked || false,
                minPrice,
                maxPrice,
                selectedLangs: this.filterLanguageSelect?.value ? [this.filterLanguageSelect.value] : [],
                minAge,
                maxAge,
                chargeType: this.filterChargeType?.value || 'any',
                groupMin: parseInt(this.filterGroupMin?.value) || 0,
                groupMax: parseInt(this.filterGroupMax?.value) || Infinity,
            };
        }

        getActiveFilterLabels(state){
            const labels = [];
            if(state.minRating > 0) labels.push({ key: 'rating', label: `Rating ≥ ${state.minRating}` });
            if(state.requireVerified) labels.push({ key: 'verified', label: 'Verified' });
            if(state.requireAvailable) labels.push({ key: 'available', label: 'Available' });
            if(this.filterPriceMinSlider && parseFloat(state.minPrice) > parseFloat(this.filterPriceMinSlider.min)) labels.push({ key: 'price', label: `Price ≥ ${Math.round(state.minPrice)}` });
            if(this.filterPriceMaxSlider && parseFloat(state.maxPrice) < parseFloat(this.filterPriceMaxSlider.max)) labels.push({ key: 'price', label: `Price ≤ ${Math.round(state.maxPrice)}` });
            if(state.selectedLangs.length) labels.push({ key: 'languages', label: `Language: ${state.selectedLangs[0]}` });
            if(this.filterAgeMinSlider && parseInt(state.minAge) > parseInt(this.filterAgeMinSlider.min)) labels.push({ key: 'age', label: `Age ≥ ${state.minAge}` });
            if(this.filterAgeMaxSlider && parseInt(state.maxAge) < parseInt(this.filterAgeMaxSlider.max)) labels.push({ key: 'age', label: `Age ≤ ${state.maxAge}` });
            if(state.chargeType !== 'any') labels.push({ key: 'chargeType', label: state.chargeType === 'per_day' ? 'Per Day' : 'Per Person' });
            if(state.groupMin) labels.push({ key: 'groupMin', label: `Group ≥ ${state.groupMin}` });
            if(isFinite(state.groupMax) && state.groupMax !== Infinity) labels.push({ key: 'groupMax', label: `Group ≤ ${state.groupMax}` });
            return labels;
        }

        clearFilterByKey(key){
            if(key === 'rating' && this.filterRatingSlider){ this.filterRatingSlider.value = 0; this.filterRatingDisplay.textContent = '0'; }
            if(key === 'verified' && this.filterVerified){ this.filterVerified.checked = false; }
            if(key === 'available' && this.filterAvailable){ this.filterAvailable.checked = false; }
            if(key === 'price' && this.filterPriceMinSlider && this.filterPriceMaxSlider){
                this.filterPriceMinSlider.value = this.filterPriceMinSlider.min;
                this.filterPriceMaxSlider.value = this.filterPriceMaxSlider.max;
                if(this.filterPriceMinDisplay) this.filterPriceMinDisplay.textContent = 'Any';
                if(this.filterPriceMaxDisplay) this.filterPriceMaxDisplay.textContent = 'Any';
            }
            if(key === 'languages' && this.filterLanguageSelect){ this.filterLanguageSelect.value = ''; }
            if(key === 'age' && this.filterAgeMinSlider && this.filterAgeMaxSlider){
                this.filterAgeMinSlider.value = this.filterAgeMinSlider.min;
                this.filterAgeMaxSlider.value = this.filterAgeMaxSlider.max;
                if(this.filterAgeMinDisplay) this.filterAgeMinDisplay.textContent = 'Any';
                if(this.filterAgeMaxDisplay) this.filterAgeMaxDisplay.textContent = 'Any';
            }
            if(key === 'chargeType' && this.filterChargeType){ this.filterChargeType.value = 'any'; }
            if(key === 'groupMin' && this.filterGroupMin){ this.filterGroupMin.value = ''; }
            if(key === 'groupMax' && this.filterGroupMax){ this.filterGroupMax.value = ''; }
        }

        previewFilterCount(guides, state){
            if(!this.isFiltersActive(state)) return guides.length;
            let count = 0;
            guides.forEach(g => {
                const rating = parseFloat(g.averageRating) || 0;
                const verified = !!g.tlVerified || !!g.dlVerified || false;
                const price = parseFloat(g.baseCharge) || 0;
                const age = parseInt(g.age) || 0;
                const available = g.isActive === 1 || g.isActive === true || false;
                const gMin = parseInt(g.minGroupSize) || 0;
                const gMax = parseInt(g.maxGroupSize) || Infinity;
                const ct = (g.chargeType || '').toString();
                let langs = [];
                if(g.languages){
                    langs = g.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                }
                if(rating < state.minRating) return;
                if(state.requireVerified && !verified) return;
                if(state.requireAvailable && !available) return;
                if(state.minPrice && isFinite(state.minPrice) && price < state.minPrice) return;
                if(state.maxPrice && isFinite(state.maxPrice) && price > state.maxPrice) return;
                if(state.minAge && isFinite(state.minAge) && age < state.minAge) return;
                if(state.maxAge && isFinite(state.maxAge) && age > state.maxAge) return;
                if(state.chargeType !== 'any' && ct !== state.chargeType) return;
                if(state.groupMin && gMax < state.groupMin) return;
                if(isFinite(state.groupMax) && gMin > state.groupMax) return;
                if(state.selectedLangs.length){
                    const has = state.selectedLangs.some(l => langs.includes(l));
                    if(!has) return;
                }
                count++;
            });
            return count;
        }

        updateSliderFills(){
            const sliders = [
                this.filterRatingSlider,
                this.filterPriceMinSlider,
                this.filterPriceMaxSlider,
                this.filterAgeMinSlider,
                this.filterAgeMaxSlider
            ].filter(Boolean);
            sliders.forEach(slider => {
                const min = parseFloat(slider.min || 0);
                const max = parseFloat(slider.max || 100);
                const val = parseFloat(slider.value || 0);
                const pct = ((val - min) / (max - min)) * 100;
                slider.style.setProperty('--fill', `${pct}%`);
            });

            const updateRangeSelection = (minSlider, maxSlider) => {
                if(!minSlider || !maxSlider) return;
                const container = minSlider.closest('.range-dual-single');
                if(!container) return;

                const minBound = parseFloat(minSlider.min || 0);
                const maxBound = parseFloat(maxSlider.max || 100);
                const minVal = parseFloat(minSlider.value || minBound);
                const maxVal = parseFloat(maxSlider.value || maxBound);
                const denom = (maxBound - minBound) || 1;

                const startPct = ((Math.min(minVal, maxVal) - minBound) / denom) * 100;
                const endPct = ((Math.max(minVal, maxVal) - minBound) / denom) * 100;

                container.style.setProperty('--range-start', `${startPct}%`);
                container.style.setProperty('--range-end', `${endPct}%`);

                // Improve usability when thumbs overlap (bring the active one on top)
                const step = parseFloat(minSlider.step || 1) || 1;
                if(minVal >= (maxVal - step)){
                    // When close, let min thumb be on top so it can be grabbed
                    minSlider.style.zIndex = '5';
                    maxSlider.style.zIndex = '4';
                } else {
                    minSlider.style.zIndex = '3';
                    maxSlider.style.zIndex = '4';
                }
            };

            updateRangeSelection(this.filterPriceMinSlider, this.filterPriceMaxSlider);
            updateRangeSelection(this.filterAgeMinSlider, this.filterAgeMaxSlider);
        }

        applyPreset(preset){
            const guides = this.guidesData || [];
            if(preset === 'top_rated' && this.filterRatingSlider){
                this.filterRatingSlider.value = 4.5;
                this.filterRatingDisplay.textContent = '4.5';
            }
            if(preset === 'verified' && this.filterVerified){
                this.filterVerified.checked = true;
            }
            if(preset === 'budget' && this.filterPriceMaxSlider){
                // set price to 25th percentile of baseCharge
                const prices = guides.map(g => parseFloat(g.baseCharge) || 0).filter(p => p > 0).sort((a,b)=>a-b);
                const idx = Math.floor((prices.length - 1) * 0.25);
                const q = prices.length ? prices[idx] : parseFloat(this.filterPriceMaxSlider.max);
                const target = Math.max(parseFloat(this.filterPriceMaxSlider.min || 0), Math.min(q, parseFloat(this.filterPriceMaxSlider.max)));
                if(this.filterPriceMinSlider){
                    this.filterPriceMinSlider.value = this.filterPriceMinSlider.min;
                    if(this.filterPriceMinDisplay) this.filterPriceMinDisplay.textContent = 'Any';
                }
                this.filterPriceMaxSlider.value = target;
                if(this.filterPriceMaxDisplay) this.filterPriceMaxDisplay.textContent = Math.round(target);
            }
            this.updateSliderFills();
            this.updateHeaderAndChips();
            if(this.filterLivePreview?.checked){
                this.applyFilters({ close: false });
            }
        }
    }

    window.GuideSelectionManager = GuideSelectionManager;
    window.guideSelectionManager = new GuideSelectionManager();
})();