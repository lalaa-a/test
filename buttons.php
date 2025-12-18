<style>

    /* 3 dots menu buttons */

    .dot-menu-container {
        position: relative;
        display: inline-block;
    }

    .dot-menu-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        color: var(--text-secondary);
        transition: all 0.2s ease;
        position: relative;
    }

    .dot-menu-btn:hover {
        background-color: var(--tertiary-color);
        color: var(--text-primary);
    }

    .dot-menu-btn i {
        font-size: 1rem;
    }

    .dot-menu-dropdown {
        position: absolute;
        top: 0;
        right: 100%;
        background: var(--card-background);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 120px;
        z-index: 100;
        display: none;
        border: 1px solid var(--border-color);
    }

    .dot-menu-dropdown.show {
        display: block;
    }

    .dot-menu-item {
        display: block;
        width: 100%;
        padding: 10px 16px;
        text-decoration: none;
        color: var(--text-primary);
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        transition: background 0.2s ease;
        font-size: 0.9rem;
    }

    .dot-menu-item:hover {
        background: var(--background-gray);
    }

    .dot-menu-item.edit {
        color: var(--primary-color);
    }
    .dot-menu-item.delete {
        color: #dc3545;
    }



    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 106, 113, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .date-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .popup-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 25px;
    }

</style>

<html>

    <-- 3 dots -->
    <div class="dot-menu-container">
        <button class="dot-menu-btn" onclick="categoryManager.toggleTripMenu(event)">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dot-menu-dropdown" id="menu">
            <button class="dot-menu-item edit" onclick="">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="dot-menu-item delete" onclick="">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <h2>Create New Trip</h2>
            
            <form id="create-trip-form">
                <div class="form-group">
                    <label for="trip-title">Trip Title</label>
                    <input type="text" id="trip-title" name="trip_title" placeholder="Enter trip title" required>
                </div>
                
                <div class="form-group">
                    <label for="trip-description">Description</label>
                    <textarea id="trip-description" name="trip_description" placeholder="Describe your trip..." rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Trip Dates</label>
                    <div class="date-inputs">
                        <div>
                            <label for="start-date" style="font-size: 0.8rem; color: var(--text-secondary);">Start Date</label>
                            <input type="date" id="start-date" name="start_date" required>
                        </div>
                        <div>
                            <label for="end-date" style="font-size: 0.8rem; color: var(--text-secondary);">End Date</label>
                            <input type="date" id="end-date" name="end_date" required>
                        </div>
                    </div>
                </div>
                
                <div class="popup-buttons">
                    <button type="button" class="btn-cancel" id="cancel-popup">Cancel</button>
                    <button type="submit" class="btn-create" id="submit-trip">Create Trip</button>
                </div>
            </form>

        </div>
    </div>

</html>