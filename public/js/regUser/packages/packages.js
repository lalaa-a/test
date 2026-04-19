(function() {
    if (window.regUserPackageExplorer && typeof window.regUserPackageExplorer.destroy === 'function') {
        window.regUserPackageExplorer.destroy();
        delete window.regUserPackageExplorer;
    }

    class RegUserPackageExplorer {
        constructor() {
            this.URL_ROOT = 'http://localhost/test';
            this.UP_ROOT = 'http://localhost/test/public/uploads';
            this.allPackages = [];
            this.activePackageData = null;

            this.initializeElements();
            this.attachEventListeners();
            this.loadPackages();
        }

        initializeElements() {
            this.packageGrid = document.getElementById('user-package-grid');
            this.emptyState = document.getElementById('user-package-empty');

            this.searchInput = document.getElementById('user-package-search');
            this.searchButton = document.getElementById('user-package-search-btn');
            this.searchInfo = document.getElementById('user-package-search-info');

            this.modal = document.getElementById('user-package-modal');
            this.modalClose = document.getElementById('user-package-modal-close');

            this.mainPhoto = document.getElementById('user-package-main-photo');
            this.thumbnailStrip = document.getElementById('user-package-thumbnails');
            this.packageTitle = document.getElementById('user-package-title');
            this.packageOverview = document.getElementById('user-package-overview');
            this.packageDuration = document.getElementById('user-package-duration');
            this.packagePrice = document.getElementById('user-package-price');
            this.packageSpots = document.getElementById('user-package-spots');
        }

        attachEventListeners() {
            if (this.searchButton) {
                this.searchButton.addEventListener('click', () => this.filterPackages());
            }

            if (this.searchInput) {
                this.searchInput.addEventListener('input', () => this.filterPackages());
                this.searchInput.addEventListener('keypress', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        this.filterPackages();
                    }
                });
            }

            if (this.modalClose) {
                this.modalClose.addEventListener('click', () => this.hideModal());
            }

            if (this.modal) {
                this.modal.addEventListener('click', (event) => {
                    if (event.target === this.modal) {
                        this.hideModal();
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    this.hideModal();
                }
            });
        }

        destroy() {
            this.hideModal();
        }

        async loadPackages() {
            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/getPackageCatalog`);
                const data = await response.json();

                if (!data.success) {
                    this.notify(data.message || 'Failed to load packages', 'error');
                    return;
                }

                this.allPackages = Array.isArray(data.packages) ? data.packages : [];
                this.filterPackages();

            } catch (error) {
                console.error('Error loading package catalog:', error);
                this.notify('Failed to load packages', 'error');
            }
        }

        filterPackages() {
            const query = this.searchInput ? this.searchInput.value.trim().toLowerCase() : '';

            const filteredPackages = this.allPackages.filter((packageItem) => {
                if (!query) {
                    return true;
                }

                const searchable = [
                    packageItem.packageName,
                    packageItem.overview,
                    packageItem.packageDetails,
                    packageItem.spotNames
                ]
                    .filter(Boolean)
                    .join(' ')
                    .toLowerCase();

                return searchable.includes(query);
            });

            this.renderPackageCards(filteredPackages);
            this.updateSearchInfo(filteredPackages.length, this.allPackages.length, query);
        }

        updateSearchInfo(filteredCount, totalCount, query) {
            if (!this.searchInfo) {
                return;
            }

            if (!query) {
                this.searchInfo.style.display = 'none';
                this.searchInfo.textContent = '';
                return;
            }

            this.searchInfo.style.display = 'block';
            this.searchInfo.textContent = `${filteredCount} of ${totalCount} packages match "${query}"`;
        }

        renderPackageCards(packageCards) {
            if (!this.packageGrid) {
                return;
            }

            this.packageGrid.innerHTML = '';

            if (!packageCards || packageCards.length === 0) {
                if (this.emptyState) {
                    this.emptyState.style.display = 'block';
                }
                return;
            }

            if (this.emptyState) {
                this.emptyState.style.display = 'none';
            }

            packageCards.forEach((packageItem) => {
                const article = document.createElement('article');
                article.className = 'user-package-card';
                article.setAttribute('tabindex', '0');

                const coverPhoto = packageItem.coverPhotoPath
                    ? `${this.UP_ROOT}${packageItem.coverPhotoPath}`
                    : '';

                const formattedPrice = packageItem.estimatedPriceLkr !== null && packageItem.estimatedPriceLkr !== ''
                    ? `LKR ${Number(packageItem.estimatedPriceLkr).toLocaleString()}`
                    : 'Price not set';

                article.innerHTML = `
                    ${coverPhoto
                        ? `<img class="user-package-cover" src="${coverPhoto}" alt="${this.escapeHtml(packageItem.packageName || '')}">`
                        : '<div class="user-package-cover placeholder"><i class="fa-solid fa-image"></i></div>'
                    }
                    <div class="user-package-body">
                        <h3 class="user-package-title">${this.escapeHtml(packageItem.packageName || '')}</h3>
                        <p class="user-package-overview">${this.escapeHtml(packageItem.overview || '')}</p>
                        <div class="user-package-meta">
                            <span><i class="fa-solid fa-calendar-day"></i> ${Number(packageItem.durationDays) || 1} days</span>
                            <span><i class="fa-solid fa-location-dot"></i> ${Number(packageItem.spotCount) || 0} spots</span>
                        </div>
                        <div class="user-package-price">${formattedPrice}</div>
                    </div>
                `;

                article.addEventListener('click', () => this.openPackageDetails(Number(packageItem.packageId)));
                article.addEventListener('keypress', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        this.openPackageDetails(Number(packageItem.packageId));
                    }
                });

                this.packageGrid.appendChild(article);
            });
        }

        async openPackageDetails(packageId) {
            if (!packageId) {
                return;
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/getPackageCatalogDetails/${packageId}`);
                const data = await response.json();

                if (!data.success) {
                    this.notify(data.message || 'Failed to load package details', 'error');
                    return;
                }

                this.activePackageData = data.packageData;
                this.populateModal(this.activePackageData);
                this.showModal();

            } catch (error) {
                console.error('Error loading package details:', error);
                this.notify('Failed to load package details', 'error');
            }
        }

        populateModal(packageData) {
            if (!packageData || !packageData.mainDetails) {
                return;
            }

            const details = packageData.mainDetails;
            const photos = Array.isArray(packageData.photos) ? packageData.photos : [];
            const spots = Array.isArray(packageData.spots) ? packageData.spots : [];

            this.packageTitle.textContent = details.packageName || '';
            this.packageOverview.textContent = details.overview || '';
            this.packageDuration.textContent = `${Number(details.durationDays) || 1} days`;

            this.packagePrice.textContent = details.estimatedPriceLkr !== null && details.estimatedPriceLkr !== ''
                ? `LKR ${Number(details.estimatedPriceLkr).toLocaleString()}`
                : 'Price not set';

            this.thumbnailStrip.innerHTML = '';

            if (photos.length > 0) {
                const firstPhoto = `${this.UP_ROOT}${photos[0].photoPath}`;
                this.mainPhoto.src = firstPhoto;
                this.mainPhoto.alt = details.packageName || 'Package photo';

                photos.forEach((photo, index) => {
                    const thumb = document.createElement('img');
                    thumb.className = `user-package-thumb ${index === 0 ? 'active' : ''}`;
                    thumb.src = `${this.UP_ROOT}${photo.photoPath}`;
                    thumb.alt = `Package photo ${index + 1}`;

                    thumb.addEventListener('click', () => {
                        this.mainPhoto.src = thumb.src;
                        const allThumbs = this.thumbnailStrip.querySelectorAll('.user-package-thumb');
                        allThumbs.forEach((item) => item.classList.remove('active'));
                        thumb.classList.add('active');
                    });

                    this.thumbnailStrip.appendChild(thumb);
                });
            } else {
                this.mainPhoto.src = '';
                this.mainPhoto.alt = 'No photo available';
            }

            this.packageSpots.innerHTML = '';
            if (spots.length === 0) {
                this.packageSpots.innerHTML = '<p>No stops available for this package.</p>';
                return;
            }

            spots.forEach((spot) => {
                const card = document.createElement('div');
                card.className = 'user-package-spot-card';

                const location = `${spot.province || ''}${spot.province && spot.district ? ', ' : ''}${spot.district || ''}`;
                const note = spot.spotNote ? `Note: ${spot.spotNote}` : '';

                card.innerHTML = `
                    <h4>Day ${Number(spot.dayNumber) || 1} · Stop ${Number(spot.visitOrder) || 1}</h4>
                    <p><strong>${this.escapeHtml(spot.spotName || '')}</strong></p>
                    <p>${this.escapeHtml(location)}</p>
                    ${note ? `<p>${this.escapeHtml(note)}</p>` : ''}
                `;

                this.packageSpots.appendChild(card);
            });
        }

        showModal() {
            if (!this.modal) {
                return;
            }

            this.modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        hideModal() {
            if (!this.modal) {
                return;
            }

            this.modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        escapeHtml(value) {
            const text = String(value || '');
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        notify(message, type) {
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, type || 'info');
                return;
            }

            console.log(`[${type || 'info'}] ${message}`);
        }
    }

    window.regUserPackageExplorer = new RegUserPackageExplorer();
})();
