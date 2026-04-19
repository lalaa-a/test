<!-- Page Header with Title and Action Button -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title-section">
            <h1 class="page-title">Travel Packages Management</h1>
            <p class="page-subtitle">Build reusable packages from existing travel spots</p>
        </div>
        <button id="add-package-btn" class="btn-add-vehicle" type="button">
            <i class="fas fa-plus"></i>
            Add Package
        </button>
    </div>
</div>

<section class="search-section">
    <div class="search-container">
        <div class="search-input-wrapper">
            <input
                type="text"
                class="search-input"
                id="package-search-input"
                placeholder="Search packages by name, description, or spot..."
                autocomplete="off"
            >
            <button class="search-icon" id="package-search-btn" type="button" aria-label="Search packages">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <p class="search-results-info" id="package-search-results-info"></p>
</section>

<section class="packages-section">
    <h2 class="section-title">Published Packages</h2>
    <div id="packages-card-grid" class="packages-card-grid"></div>

    <div class="no-results" id="packages-empty-state" style="display: none;">
        <div class="no-results-icon"><i class="fas fa-box-open"></i></div>
        <h3 class="no-results-title">No packages found</h3>
        <p class="no-results-text">Create a package to get started.</p>
    </div>
</section>

<!-- Add Package Popup -->
<div id="package-popup" class="modal">
    <div class="modal-content package-modal">
        <div class="modal-header">
            <h3><i class="fas fa-box-open"></i> Add Travel Package</h3>
            <button class="modal-close" id="package-popup-close" type="button">&times;</button>
        </div>

        <div class="modal-body">
            <form id="package-form" class="package-form">
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Package Information</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="packageName">Package Name *</label>
                            <input type="text" id="packageName" name="packageName" required>
                        </div>
                        <div class="form-group">
                            <label for="durationDays">Duration (days) *</label>
                            <input type="number" id="durationDays" name="durationDays" min="1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimatedPriceLkr">Estimated Price (LKR)</label>
                            <input type="number" id="estimatedPriceLkr" name="estimatedPriceLkr" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="packageStatus">Status *</label>
                            <select id="packageStatus" name="packageStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="packageOverview">Overview *</label>
                            <textarea id="packageOverview" name="packageOverview" rows="4" placeholder="Describe the package experience..." required></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="packageDetails">Highlights / Details</label>
                            <textarea id="packageDetails" name="packageDetails" rows="4" placeholder="Add package highlights, inclusions, and notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-map-marked-alt"></i> Add Travel Spots to Package *</h4>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="package-spot-search">Search Spot by Name</label>
                            <div class="nearby-search-container">
                                <input type="text" id="package-spot-search" placeholder="Search travel spots...">
                                <button type="button" id="search-package-spot-btn" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <div id="package-spot-results" class="nearby-results"></div>
                            <div id="selected-package-spots" class="selected-nearby"></div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><i class="fas fa-camera"></i> Package Photos *</h4>
                    <div class="upload-instructions">
                        <p><i class="fas fa-info-circle"></i> Upload at least one package photo. The first image is used as the package cover.</p>
                    </div>

                    <div class="photo-upload-grid package-photo-grid">
                        <?php for ($photoSlot = 1; $photoSlot <= 6; $photoSlot++): ?>
                            <div class="photo-upload-slot" data-slot="<?php echo $photoSlot; ?>">
                                <div class="upload-preview" id="packageUploadPreview<?php echo $photoSlot; ?>">
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p><?php echo $photoSlot === 1 ? 'Cover Photo' : 'Photo ' . $photoSlot; ?></p>
                                        <?php if ($photoSlot === 1): ?>
                                            <span class="upload-hint">Required</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input
                                    type="file"
                                    id="packagePhotoUpload<?php echo $photoSlot; ?>"
                                    accept="image/*"
                                    style="display: none;"
                                    <?php echo $photoSlot === 1 ? 'required' : ''; ?>
                                >
                                <button type="button" class="btn-upload-photo" data-target="packagePhotoUpload<?php echo $photoSlot; ?>">
                                    <i class="fas fa-plus"></i> Upload
                                </button>
                                <button type="button" class="btn-remove-photo" data-slot="<?php echo $photoSlot; ?>" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" id="cancel-package-btn" class="btn-cancel">Cancel</button>
                    <button type="submit" id="submit-package-btn" class="btn-create">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>