(function(){
    // Check if DriverSelectionManager already exists and clean up
    if (window.DriverSelectionManager) {
        console.log('DriverSelectionManager already exists, cleaning up...');
        if (window.driverSelectionManager) {
            delete window.driverSelectionManager;
        }
        delete window.DriverSelectionManager;
    }
    
    class DriverSelectionManager {

        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            
            this.selectedDriverId = null;
            this.selectedVehicleId = null;
            this.selectedCard = null;
            this.selectedDriverName = null;
            this.driversData = []; // Store fetched drivers data
            
            // Get segment index from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            this.segmentIndex = parseInt(urlParams.get('segment')) || 0;
            console.log(`Driver selection for segment: ${this.segmentIndex}`);

            this.initializeElements();
            this.addEventListeners();
            // init filters with manually fetched data
            this.fetchAndInitFilters();
        }

        initializeElements(){
            this.confirmModel = document.getElementById('confirmationModal');
            this.selectingDriver = document.getElementById('selecting-driver-name');
            this.confirmBtn = document.getElementById('confirmBtn');
            this.cancelBtn = document.getElementById("cancelBtn");
            // Filter popup elements
            this.filterToggle = document.getElementById('filterToggle');
            this.filterPopup = document.getElementById('filterPopup');
            this.filterCloseBtn = document.getElementById('filterCloseBtn');
            this.filterApplyBtn = document.getElementById('filterApplyBtn');
            this.filterResetBtn = document.getElementById('filterResetBtn');
            this.filterVerified = document.getElementById('filterVerified');

            // advanced filter controls
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
            this.filterMinSeatingCapacity = document.getElementById('filterMinSeatingCapacity');
            this.filterMaxSeatingCapacity = document.getElementById('filterMaxSeatingCapacity');
            this.filterChildSeats = document.getElementById('filterChildSeats');
            this.filterVehicleType = document.getElementById('filterVehicleType');
            this.filterLanguageSelect = document.getElementById('filterLanguageSelect');
            this.filterMatchCount = document.getElementById('filterMatchCount');
            this.filterActiveCount = document.getElementById('filterActiveCount');
            this.filterActiveChips = document.getElementById('filterActiveChips');
            this.filterLivePreview = document.getElementById('filterLivePreview');
        }

        addEventListeners(){

            this.confirmBtn.addEventListener('click', () => {
                this.selectDriver(this.selectedDriverId,this.selectedVehicleId);
                this.hideModal();
            } )

            this.cancelBtn.addEventListener('click', () => this.hideModal());

            // Add event listeners for select and view buttons
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('select-driver-btn')) {
                    const card = e.target.closest('.driver-card');
                    if (card) {
                        const driverId = card.dataset.userId;
                        const vehicleId = card.dataset.vehicleId;
                        const driverName = card.querySelector('.driver-name-compact')?.textContent || 'Driver';
                        this.showConfirmation(driverId, vehicleId, driverName);
                    }
                }

                if (e.target.classList.contains('view-driver-btn')) {
                    const card = e.target.closest('.driver-card');
                    if (card) {
                        const driverId = card.dataset.userId;
                        this.viewDriver(driverId);
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
                this.filterMinSeatingCapacity,
                this.filterMaxSeatingCapacity,
                this.filterChildSeats,
                this.filterVehicleType,
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

        async fetchDriversData() {
            // Use embedded data from PHP instead of fetching from backend
            if (window.driversDataEmbedded && Array.isArray(window.driversDataEmbedded)) {
                console.log('Using embedded drivers data:', window.driversDataEmbedded.length, 'drivers');
                return window.driversDataEmbedded;
            }
            
            // Fallback to backend fetch if embedded data not available
            console.warn('No embedded drivers data found, falling back to backend fetch...');
            try {
                const tripId = window.location.pathname.split('/').pop();
                const baseUrl = window.location.origin + '/test';
                const response = await fetch(`${baseUrl}/RegUser/getDriversData/${tripId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    return data.drivers || [];
                } else {
                    console.error('API error:', data.message);
                    return [];
                }
                
            } catch (error) {
                console.error('Failed to fetch drivers data:', error);
                return [];
            }
        }

        async fetchAndInitFilters() {
            console.log('Fetching drivers data manually...');
            this.driversData = await this.fetchDriversData();
            console.log('Fetched drivers data:', this.driversData);
            this.initFilters();
            // initialize main filter tabs visibility
            this.updateMainFilterTabs();
        }

        showConfirmation(driverId, vehicleId, driverName) {
            this.selectedDriverId = driverId;
            this.selectedVehicleId = vehicleId;
            this.selectedDriverName = driverName;
            this.selectedCard = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
            if (this.selectedCard) {
                this.selectedCard.classList.add('selected');
            }
            this.confirmModel.classList.add('show');
            this.selectingDriver.innerHTML = driverName;
        }

        hideModal() {
            this.confirmModel.classList.remove('show');
            if (this.selectedCard) {
                this.selectedCard.classList.remove('selected');
            }
            this.selectedDriverId = null;
            this.selectedVehicleId = null;
            this.selectedCard = null;
        }

        selectDriver(driverId,vehicleId) {
            console.log('Selected driver with ID:', driverId, 'and vehicle:', vehicleId);
            
            // Find driver in driversData to get all details
            const driver = this.driversData.find(d => d.userId == driverId && d.vehicleId == vehicleId);
            
            if (!driver) {
                console.error('Driver not found in driversData');
                return;
            }
            
            // Get driver data from the selected card
            const selectedCard = document.querySelector(`[data-user-id="${driverId}"][data-vehicle-id="${vehicleId}"]`);
            
            // Prepare complete driver data matching handleDriverSe
            // lection expectations
            const driverData = {
                segmentIndex: this.segmentIndex, // Include segment index
                userId: driver.userId,
                vehicleId: driver.vehicleId,
                fullName: driver.fullname || driver.fullName,
                profilePhoto: driver.profilePhoto ? `${this.UP_ROOT}${driver.profilePhoto}` : `${this.URL_ROOT}/public/img/signup/profile.png`,
                averageRating: parseFloat(driver.averageRating) || 0,
                age: driver.age || 0,
                languages: driver.languages || '',
                verified: driver.verified || false,
                // Vehicle information
                vehicleId: driver.vehicleId,
                make: driver.make,
                model: driver.model,
                year: driver.year,
                vehicleType: driver.vehicleType || driver.make,
                vehiclePhoto: driver.vehiclePhoto,
                color: driver.color,
                seatingCapacity: driver.seatingCapacity,
                childSeats: driver.childSeats || 0,
                licensePlate: driver.licensePlate,
                // Pricing information
                vehicleChargePerDay: parseFloat(driver.vehicleChargePerDay) || 0,
                driverChargePerDay: parseFloat(driver.driverChargePerDay) || 0,
                totalChargePerDay: parseFloat(driver.totalChargePerDay) || 0,
                vehicleChargePerKm: parseFloat(driver.vehicleChargePerKm) || 0,
                driverChargePerKm: parseFloat(driver.driverChargePerKm) || 0,
                totalChargePerKm: parseFloat(driver.totalChargePerKm) || 0,
                currency: driver.currency || 'USD',
                currencySymbol: driver.currencySymbol || '$',
                formattedChargePerDay: driver.formattedChargePerDay || `${driver.currencySymbol || '$'}${(driver.totalChargePerDay || 0).toFixed(2)}`,
                formattedChargePerKm: driver.formattedChargePerKm || `${driver.currencySymbol || '$'}${(driver.totalChargePerKm || 0).toFixed(2)}`
            };

            console.log('Driver selection data:', driverData);

            if (window.opener && !window.opener.closed) {
                if (window.location.origin === window.opener.location.origin) {
                    console.log('Same origin, calling function directly.');
                    window.opener.tripEventListManager.handleDriverSelection(driverData);
                    window.close();
                } else {
                    window.opener.postMessage({
                        type: 'DRIVER_SELECTED',
                        driverData: driverData
                    }, window.opener.location.origin);
                    window.close();
                }
            }
        }

        viewDriver(driverId) {
            console.log('View driver:', driverId);
            // TODO: Implement view driver profile
        }

        // --- Filter popup and logic ---
        initFilters(){
            console.log("init filters called");
            const drivers = this.driversData || [];
            if(!drivers.length) {
                console.log('No drivers data available for filtering');
                return;
            }

            // gather unique languages and vehicle makes
            const langSet = new Set();
            const vehicleSet = new Set();
            let maxPrice = 0;
            let maxAge = 0;
            let minAge = Infinity;
            let maxCapacity = 0;

            drivers.forEach(d => {
                if(d.languages){
                    try{
                        const parts = d.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                        parts.forEach(p=>langSet.add(p));
                    }catch(e){
                        console.error('Error parsing languages:', e, d.languages);
                    }
                }
                if(d.make) vehicleSet.add(d.make);
                if(d.totalChargePerDay) maxPrice = Math.max(maxPrice, parseFloat(d.totalChargePerDay));
                if(d.age) maxAge = Math.max(maxAge, parseInt(d.age));
                if(d.age) minAge = Math.min(minAge, parseInt(d.age));
                if(d.seatingCapacity) maxCapacity = Math.max(maxCapacity, parseInt(d.seatingCapacity));
            });

            if(!isFinite(minAge) || minAge <= 0) minAge = 18;
            minAge = Math.max(18, Math.floor(minAge));

            // populate languages as dropdown options
            if(this.filterLanguageSelect){
                const currentValue = this.filterLanguageSelect.value;
                this.filterLanguageSelect.innerHTML = '<option value="">All Languages</option>';
                
                Array.from(langSet).sort().forEach(lang => {
                    const opt = document.createElement('option');
                    opt.value = lang;
                    opt.textContent = lang;
                    this.filterLanguageSelect.appendChild(opt);
                });
                
                if(currentValue && langSet.has(currentValue)) {
                    this.filterLanguageSelect.value = currentValue;
                }
            }

            // Update vehicle type dropdown
            if(this.filterVehicleType){
                const currentValue = this.filterVehicleType.value;
                // Keep existing options and update them
                const select = this.filterVehicleType;
                // Preserve first option (All Types)
                while(select.options.length > 1){
                    select.remove(1);
                }
                
                Array.from(vehicleSet).sort().forEach(make => {
                    const opt = document.createElement('option');
                    opt.value = make;
                    opt.textContent = make;
                    select.appendChild(opt);
                });
                
                if(currentValue) {
                    select.value = currentValue;
                }
            }

            const computedMaxPrice = Math.ceil(maxPrice || 500);
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

            // Update capacity sliders
            if(this.filterMinSeatingCapacity && this.filterMaxSeatingCapacity){
                this.filterMinSeatingCapacity.max = maxCapacity;
                this.filterMaxSeatingCapacity.max = maxCapacity;
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
            if(this.filterPriceMinSlider && this.filterPriceMaxSlider){
                this.filterPriceMinSlider.value = this.filterPriceMinSlider.min || 0;
                this.filterPriceMaxSlider.value = this.filterPriceMaxSlider.max || 500;
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
            if(this.filterMinSeatingCapacity) this.filterMinSeatingCapacity.value = '';
            if(this.filterMaxSeatingCapacity) this.filterMaxSeatingCapacity.value = '';
            if(this.filterChildSeats) this.filterChildSeats.value = '';
            if(this.filterVehicleType) this.filterVehicleType.value = 'all';
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
                // No filters active, show all drivers
                document.querySelectorAll('.driver-card').forEach(card => {
                    card.style.display = '';
                });
                const drivers = this.driversData || [];
                this.updateMatchCount(drivers.length, drivers.length);
                if(close) this.closeFilterPopup();
                return;
            }

            try {
                // Get trip ID from current page
                const tripId = window.location.pathname.split('/').pop();
                
                // Show loading state
                this.showLoadingState();
                
                // Make backend request
                const baseUrl = window.location.origin + '/test';
                const response = await fetch(`${baseUrl}/RegUser/filterDrivers/${tripId}`, {
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
                    this.displayFilteredDrivers(data.drivers);
                    this.updateMatchCount(data.count, this.driversData?.length || 0);
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
            const selectedLang = this.filterLanguageSelect?.value || '';
            const selectedLangs = selectedLang ? [selectedLang] : [];
            const vehicleType = this.filterVehicleType?.value || 'all';
            const minCapacity = parseInt(this.filterMinSeatingCapacity?.value) || 0;
            const maxCapacity = parseInt(this.filterMaxSeatingCapacity?.value) || 0;
            const childSeats = parseInt(this.filterChildSeats?.value) || 0;

            const rawMinPrice = parseFloat(this.filterPriceMinSlider?.value) || 0;
            const rawMaxPrice = parseFloat(this.filterPriceMaxSlider?.value) || 0;
            const rawMinAge = parseInt(this.filterAgeMinSlider?.value) || 0;
            const rawMaxAge = parseInt(this.filterAgeMaxSlider?.value) || 0;

            const minPriceMin = parseFloat(this.filterPriceMinSlider?.min || 0);
            const maxPriceMax = parseFloat(this.filterPriceMaxSlider?.max || 0);
            const minAgeMin = parseInt(this.filterAgeMinSlider?.min || 0);
            const maxAgeMax = parseInt(this.filterAgeMaxSlider?.max || 0);

            // If user leaves full range selected, treat it as "Any"
            const minPriceSend = (this.filterPriceMinSlider && rawMinPrice <= minPriceMin) ? 0 : rawMinPrice;
            const maxPriceSend = (this.filterPriceMaxSlider && rawMaxPrice >= maxPriceMax) ? 0 : rawMaxPrice;
            const minAgeSend = (this.filterAgeMinSlider && rawMinAge <= minAgeMin) ? 0 : rawMinAge;
            const maxAgeSend = (this.filterAgeMaxSlider && rawMaxAge >= maxAgeMax) ? 0 : rawMaxAge;

            return {
                rating: minRating,
                verified: requireVerified,
                minPrice: minPriceSend,
                maxPrice: maxPriceSend,
                minAge: minAgeSend,
                maxAge: maxAgeSend,
                vehicleType: vehicleType === 'all' ? '' : vehicleType,
                minSeatingCapacity: minCapacity || 0,
                maxSeatingCapacity: maxCapacity || 0,
                childSeats: childSeats || 0,
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

        displayFilteredDrivers(drivers) {
            const container = document.querySelector('.drivers-container');
            if (!container) return;

            // Hide all existing cards
            document.querySelectorAll('.driver-card').forEach(card => {
                card.style.display = 'none';
            });

            // Create a set of driver IDs for quick lookup
            const driverIds = new Set(drivers.map(d => String(d.userId)));

            // Show cards for filtered drivers
            document.querySelectorAll('.driver-card').forEach(card => {
                const id = card.getAttribute('data-user-id');
                if (driverIds.has(id)) {
                    card.style.display = '';
                }
            });
        }

        applyClientSideFilters(filterData) {
            // Fallback client-side filtering
            const drivers = this.driversData || [];
            const visibleIds = new Set();

            drivers.forEach(d => {
                const rating = parseFloat(d.averageRating) || 0;
                const verified = !!d.dlVerified || !!d.verified || false;
                const price = parseFloat(d.totalChargePerDay) || 0;
                const age = parseInt(d.age) || 0;
                const capacity = parseInt(d.seatingCapacity) || 0;
                const childSeats = parseInt(d.childSeats) || 0;
                const make = (d.make || '').toString();
                let langs = [];
                if(d.languages){
                    langs = d.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                }

                if(filterData.rating && rating < filterData.rating) return;
                if(filterData.verified && !verified) return;
                if(filterData.minPrice && price < filterData.minPrice) return;
                if(filterData.maxPrice && price > filterData.maxPrice) return;
                if(filterData.minAge && age < filterData.minAge) return;
                if(filterData.maxAge && age > filterData.maxAge) return;
                if(filterData.vehicleType && make !== filterData.vehicleType) return;
                if(filterData.minSeatingCapacity && capacity < filterData.minSeatingCapacity) return;
                if(filterData.maxSeatingCapacity && capacity > filterData.maxSeatingCapacity) return;
                if(filterData.childSeats && childSeats < filterData.childSeats) return;
                if(filterData.languages?.length){
                    const has = filterData.languages.some(l => langs.includes(l));
                    if(!has) return;
                }

                visibleIds.add(String(d.userId));
            });

            // show/hide cards
            document.querySelectorAll('.driver-card').forEach(card => {
                const id = card.getAttribute('data-user-id');
                card.style.display = visibleIds.has(id) ? '' : 'none';
            });

            this.updateMatchCount(visibleIds.size, drivers.length);
        }

        isFiltersActive(filterData){
            const hasMinRating = (filterData.rating ?? 0) > 0;
            const hasVerified = !!filterData.verified;
            const hasVehicleType = !!(filterData.vehicleType && filterData.vehicleType !== '' && filterData.vehicleType !== 'all');
            const hasMinCapacity = (filterData.minSeatingCapacity ?? 0) > 0;
            const hasMaxCapacity = (filterData.maxSeatingCapacity ?? 0) > 0;
            const hasChildSeats = (filterData.childSeats ?? 0) > 0;

            const langs = filterData.languages ?? [];
            const hasLangs = Array.isArray(langs) && langs.length > 0;

            const minPriceVal = filterData.minPrice ?? 0;
            const maxPriceVal = filterData.maxPrice ?? 0;
            const minAgeVal = filterData.minAge ?? 0;
            const maxAgeVal = filterData.maxAge ?? 0;

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
                priceMinActive ||
                priceMaxActive ||
                ageMinActive ||
                ageMaxActive ||
                hasVehicleType ||
                hasMinCapacity ||
                hasMaxCapacity ||
                hasChildSeats ||
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
                    info.innerHTML = 'No drivers match these filters. <button type="button" class="inline-link" id="clearFiltersInline">Clear filters</button>';
                    const clearBtn = document.getElementById('clearFiltersInline');
                    clearBtn?.addEventListener('click', () => this.resetFilters());
                } else {
                    info.style.display = 'block';
                    info.textContent = `Showing ${matchCount} of ${total} drivers`;
                }
            }
        }

        updateHeaderAndChips(){
            const drivers = this.driversData || [];
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
            const filtered = this.previewFilterCount(drivers, state);
            this.updateMatchCount(filtered, drivers.length);
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
                minPrice,
                maxPrice,
                selectedLangs: this.filterLanguageSelect?.value ? [this.filterLanguageSelect.value] : [],
                minAge,
                maxAge,
                vehicleType: this.filterVehicleType?.value || 'all',
                minCapacity: parseInt(this.filterMinSeatingCapacity?.value) || 0,
                maxCapacity: parseInt(this.filterMaxSeatingCapacity?.value) || 0,
                childSeats: parseInt(this.filterChildSeats?.value) || 0
            };
        }

        getActiveFilterLabels(state){
            const labels = [];
            if(state.minRating > 0) labels.push({ key: 'rating', label: `Rating ≥ ${state.minRating}` });
            if(state.requireVerified) labels.push({ key: 'verified', label: 'Verified' });
            if(this.filterPriceMinSlider && parseFloat(state.minPrice) > parseFloat(this.filterPriceMinSlider.min)) labels.push({ key: 'price', label: `Price ≥ ${Math.round(state.minPrice)}` });
            if(this.filterPriceMaxSlider && parseFloat(state.maxPrice) < parseFloat(this.filterPriceMaxSlider.max)) labels.push({ key: 'price', label: `Price ≤ ${Math.round(state.maxPrice)}` });
            if(state.selectedLangs.length) labels.push({ key: 'languages', label: `Language: ${state.selectedLangs[0]}` });
            if(this.filterAgeMinSlider && parseInt(state.minAge) > parseInt(this.filterAgeMinSlider.min)) labels.push({ key: 'age', label: `Age ≥ ${state.minAge}` });
            if(this.filterAgeMaxSlider && parseInt(state.maxAge) < parseInt(this.filterAgeMaxSlider.max)) labels.push({ key: 'age', label: `Age ≤ ${state.maxAge}` });
            if(state.vehicleType !== 'all') labels.push({ key: 'vehicleType', label: `Vehicle: ${state.vehicleType}` });
            if(state.minCapacity) labels.push({ key: 'capacity', label: `Min Seats: ${state.minCapacity}` });
            if(state.maxCapacity) labels.push({ key: 'capacity', label: `Max Seats: ${state.maxCapacity}` });
            if(state.childSeats) labels.push({ key: 'childSeats', label: `Child Seats: ${state.childSeats}` });
            return labels;
        }

        clearFilterByKey(key){
            if(key === 'rating' && this.filterRatingSlider){ this.filterRatingSlider.value = 0; this.filterRatingDisplay.textContent = '0'; }
            if(key === 'verified' && this.filterVerified){ this.filterVerified.checked = false; }
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
            if(key === 'vehicleType' && this.filterVehicleType){ this.filterVehicleType.value = 'all'; }
            if(key === 'capacity'){
                if(this.filterMinSeatingCapacity) this.filterMinSeatingCapacity.value = '';
                if(this.filterMaxSeatingCapacity) this.filterMaxSeatingCapacity.value = '';
            }
            if(key === 'childSeats' && this.filterChildSeats){ this.filterChildSeats.value = ''; }
        }

        previewFilterCount(drivers, state){
            if(!this.isFiltersActive(state)) return drivers.length;
            let count = 0;
            drivers.forEach(d => {
                const rating = parseFloat(d.averageRating) || 0;
                const verified = !!d.dlVerified || !!d.verified || false;
                const price = parseFloat(d.totalChargePerDay) || 0;
                const age = parseInt(d.age) || 0;
                const capacity = parseInt(d.seatingCapacity) || 0;
                const childSeats = parseInt(d.childSeats) || 0;
                const make = (d.make || '').toString();
                let langs = [];
                if(d.languages){
                    langs = d.languages.split(/[,;|]/).map(s=>s.trim()).filter(Boolean);
                }
                if(rating < state.minRating) return;
                if(state.requireVerified && !verified) return;
                if(state.minPrice && isFinite(state.minPrice) && price < state.minPrice) return;
                if(state.maxPrice && isFinite(state.maxPrice) && price > state.maxPrice) return;
                if(state.minAge && isFinite(state.minAge) && age < state.minAge) return;
                if(state.maxAge && isFinite(state.maxAge) && age > state.maxAge) return;
                if(state.vehicleType !== 'all' && make !== state.vehicleType) return;
                if(state.minCapacity && capacity < state.minCapacity) return;
                if(state.maxCapacity && capacity > state.maxCapacity) return;
                if(state.childSeats && childSeats < state.childSeats) return;
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

                const step = parseFloat(minSlider.step || 1) || 1;
                if(minVal >= (maxVal - step)){
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
            const drivers = this.driversData || [];
            if(preset === 'top_rated' && this.filterRatingSlider){
                this.filterRatingSlider.value = 4.5;
                this.filterRatingDisplay.textContent = '4.5';
            }
            if(preset === 'verified' && this.filterVerified){
                this.filterVerified.checked = true;
            }
            if(preset === 'budget' && this.filterPriceMaxSlider){
                // set price to 25th percentile
                const prices = drivers.map(d => parseFloat(d.totalChargePerDay) || 0).filter(p => p > 0).sort((a,b)=>a-b);
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

    window.DriverSelectionManager = DriverSelectionManager;
    window.driverSelectionManager = new DriverSelectionManager();
})();
