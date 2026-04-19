// Trip management script for table-based user trip dashboard.
let tripsData = [];
let currentEditingTrip = null;
const URL_ROOT = 'http://localhost/test';

const SECTION_CONFIG = {
    pending: {
        statusKey: 'pending',
        gridId: 'pendingTripsGrid',
        countId: 'pending-count',
        searchInputId: 'pendingSearchInput',
        sortSelectId: 'pendingSortFilter'
    },
    wconfirmation: {
        statusKey: 'wconfirmation',
        gridId: 'wconfirmationTripsGrid',
        countId: 'wconfirmation-count',
        searchInputId: 'wconfirmationSearchInput',
        sortSelectId: 'wconfirmationSortFilter'
    },
    awpayment: {
        statusKey: 'awpayment',
        gridId: 'awpaymentTripsGrid',
        countId: 'awpayment-count',
        searchInputId: 'awpaymentSearchInput',
        sortSelectId: 'awpaymentSortFilter'
    },
    scheduled: {
        statusKey: 'scheduled',
        gridId: 'scheduledTripsGrid',
        countId: 'scheduled-count',
        searchInputId: 'scheduledSearchInput',
        sortSelectId: 'scheduledSortFilter'
    },
    ongoing: {
        statusKey: 'ongoing',
        gridId: 'ongoingTripsGrid',
        countId: 'ongoing-count',
        searchInputId: 'ongoingSearchInput',
        sortSelectId: 'ongoingSortFilter'
    },
    completed: {
        statusKey: 'completed',
        gridId: 'completedTripsGrid',
        countId: 'completed-count',
        searchInputId: 'completedSearchInput',
        sortSelectId: 'completedSortFilter'
    }
};

const EMPTY_SECTION_META = {
    pending: { icon: 'clock', label: 'No pending trips yet.' },
    wconfirmation: { icon: 'hourglass-half', label: 'No trips waiting for confirmation.' },
    awpayment: { icon: 'credit-card', label: 'No trips awaiting payment right now.' },
    scheduled: { icon: 'calendar-check', label: 'No scheduled trips right now.' },
    ongoing: { icon: 'plane', label: 'No ongoing trips at the moment.' },
    completed: { icon: 'check-circle', label: 'No completed trips yet.' }
};

const STATUS_LABELS = {
    pending: 'Pending',
    wConfirmation: 'Waiting Confirmation',
    wconfirmation: 'Waiting Confirmation',
    awPayment: 'Awaiting Payment',
    awpayment: 'Awaiting Payment',
    scheduled: 'Scheduled',
    ongoing: 'Ongoing',
    completed: 'Completed',
    cancelled: 'Cancelled',
    canceled: 'Cancelled'
};

let popup;
let popupTitle;
let createTripForm;
let submitBtn;
let startDateInput;
let endDateInput;


    initializeTripDashboard();


function initializeTripDashboard() {
    popup = document.getElementById('popup');
    popupTitle = document.getElementById('popup-title');
    createTripForm = document.getElementById('create-trip-form');
    submitBtn = document.getElementById('submit-trip');
    startDateInput = document.getElementById('start-date');
    endDateInput = document.getElementById('end-date');

    if (!popup || !createTripForm || !submitBtn) {
        return;
    }

    bindSectionNavigation();
    bindFilters();
    bindFormActions();

    loadUserTrips();

    const initiallyActiveSection = document.querySelector('.nav-link.active')?.dataset.section || 'pending';
    switchSection(initiallyActiveSection);
}

async function loadUserTrips() {
    try {
        const response = await fetch(`${URL_ROOT}/RegUser/getUserTrips`);
        const data = await response.json();

        if (!data.success) {
            alert(`Failed to load trips: ${data.message || 'Unknown error'}`);
            return;
        }

        tripsData = Array.isArray(data.trips) ? data.trips : [];
        renderTrips();
    } catch (error) {
        console.error('Error loading trips:', error);
        alert(`Error loading trips: ${error.message}`);
    }
}

function bindSectionNavigation() {
    document.querySelectorAll('.nav-link[data-section]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const section = link.dataset.section;
            switchSection(section);
        });
    });
}

function switchSection(sectionKey) {
    document.querySelectorAll('.nav-link[data-section]').forEach((link) => {
        link.classList.toggle('active', link.dataset.section === sectionKey);
    });

    document.querySelectorAll('.trip-section').forEach((section) => {
        section.style.display = 'none';
    });

    const sectionElement = document.getElementById(`${sectionKey}-section`);
    if (sectionElement) {
        sectionElement.style.display = 'block';
    }
}

function bindFilters() {
    Object.values(SECTION_CONFIG).forEach((config) => {
        const searchInput = document.getElementById(config.searchInputId);
        const sortSelect = document.getElementById(config.sortSelectId);

        if (searchInput) {
            searchInput.addEventListener('input', renderTrips);
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', renderTrips);
        }
    });
}

function renderTrips() {
    const groupedTrips = {};

    Object.entries(SECTION_CONFIG).forEach(([sectionKey, config]) => {
        const sectionTrips = tripsData.filter((trip) => normalizeStatus(trip.status) === config.statusKey);
        groupedTrips[sectionKey] = sectionTrips;

        const filteredTrips = applySectionFilters(sectionKey, sectionTrips);
        renderSectionRows(sectionKey, filteredTrips, sectionTrips.length);
        updateSectionCount(config.countId, sectionTrips.length);
    });

    updateStats(groupedTrips);
    closeAllTripMenus();
}

function applySectionFilters(sectionKey, trips) {
    const config = SECTION_CONFIG[sectionKey];
    const searchTerm = (document.getElementById(config.searchInputId)?.value || '').trim().toLowerCase();
    const sortBy = document.getElementById(config.sortSelectId)?.value || 'newest';

    let filteredTrips = [...trips];

    if (searchTerm) {
        filteredTrips = filteredTrips.filter((trip) => {
            const searchable = [
                trip.tripTitle,
                trip.description,
                trip.startDate,
                trip.endDate,
                getTripStatusLabel(trip.status)
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            return searchable.includes(searchTerm);
        });
    }

    filteredTrips.sort((a, b) => {
        if (sortBy === 'title') {
            return (a.tripTitle || '').localeCompare(b.tripTitle || '');
        }

        const timeA = getSortTimestamp(a);
        const timeB = getSortTimestamp(b);

        if (sortBy === 'oldest') {
            return timeA - timeB;
        }

        return timeB - timeA;
    });

    return filteredTrips;
}

function renderSectionRows(sectionKey, filteredTrips, originalCount) {
    const grid = document.getElementById(SECTION_CONFIG[sectionKey].gridId);
    if (!grid) {
        return;
    }

    if (filteredTrips.length === 0) {
        const hasFilter = hasSectionFilter(sectionKey);
        grid.innerHTML = createEmptyRowMarkup(sectionKey, hasFilter || originalCount > 0);
        return;
    }

    grid.innerHTML = filteredTrips.map((trip) => createTripRowMarkup(trip)).join('');
}

function createTripRowMarkup(trip) {
    const tripId = Number(trip.tripId);
    const statusClass = normalizeStatus(trip.status);
    const statusLabel = getTripStatusLabel(trip.status);
    const people = Number(trip.numberOfPeople) || 1;

    return `
        <tr class="trip-row" data-trip-id="${tripId}" onclick="handleTripRowClick(event, ${tripId})">
            <td>
                <div class="trip-main">
                    <h4 class="trip-name">${escapeHtml(trip.tripTitle || 'Untitled Trip')}</h4>
                    <p class="trip-desc">${escapeHtml(trip.description || 'No description provided.')}</p>
                </div>
            </td>
            <td>
                <div class="trip-date-range">
                    <span class="trip-date-main">${escapeHtml(formatDateRange(trip.startDate, trip.endDate))}</span>
                    <span class="trip-date-meta">${escapeHtml(getDurationLabel(trip.startDate, trip.endDate))}</span>
                </div>
            </td>
            <td>
                <span class="people-pill">
                    <i class="fas fa-users"></i>
                    ${people} ${people === 1 ? 'person' : 'people'}
                </span>
            </td>
            <td>
                <span class="trip-status status-${statusClass}">${escapeHtml(statusLabel)}</span>
            </td>
            <td>
                <span class="trip-updated">${escapeHtml(formatDateTime(trip.updatedAt || trip.createdAt || trip.startDate))}</span>
            </td>
            <td class="actions-cell">
                <button class="btn-view-trip" type="button" onclick="openTripFromAction(event, ${tripId})">
                    <i class="fas fa-eye"></i>
                    View
                </button>
                <div class="trip-menu-container">
                    <button class="trip-menu-btn" type="button" onclick="toggleTripMenu(${tripId}, event)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="trip-menu-dropdown" id="menu-${tripId}">
                        <button class="trip-menu-item edit" type="button" onclick="editTrip(${tripId}, event)">
                            <i class="fas fa-pen"></i>
                            Edit
                        </button>
                        <button class="trip-menu-item delete" type="button" onclick="deleteTrip(${tripId}, event)">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `;
}

function createEmptyRowMarkup(sectionKey, filteredView) {
    if (filteredView) {
        return `
            <tr class="no-trips-row">
                <td colspan="6">
                    <i class="fas fa-search"></i>
                    <p>No trips match your current filters.</p>
                </td>
            </tr>
        `;
    }

    const meta = EMPTY_SECTION_META[sectionKey] || { icon: 'inbox', label: 'No trips found.' };
    return `
        <tr class="no-trips-row">
            <td colspan="6">
                <i class="fas fa-${meta.icon}"></i>
                <p>${meta.label}</p>
            </td>
        </tr>
    `;
}

function updateSectionCount(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function updateStats(groupedTrips) {
    setText('totalTripsCount', tripsData.length);
    setText('pendingStatsCount', groupedTrips.pending.length);
    setText('wconfirmationStatsCount', groupedTrips.wconfirmation.length);
    setText('awpaymentStatsCount', groupedTrips.awpayment.length);
    setText('scheduledStatsCount', groupedTrips.scheduled.length);
    setText('ongoingStatsCount', groupedTrips.ongoing.length);
    setText('completedStatsCount', groupedTrips.completed.length);
}

function setText(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function hasSectionFilter(sectionKey) {
    const config = SECTION_CONFIG[sectionKey];
    if (!config) {
        return false;
    }

    const searchText = (document.getElementById(config.searchInputId)?.value || '').trim();
    const sortValue = document.getElementById(config.sortSelectId)?.value || 'newest';
    return searchText.length > 0 || sortValue !== 'newest';
}

function normalizeStatus(status) {
    const safeStatus = String(status || '').trim();
    if (!safeStatus) {
        return 'pending';
    }

    const lowered = safeStatus.toLowerCase();
    if (lowered === 'wconfirmation') {
        return 'wconfirmation';
    }

    if (lowered === 'awpayment') {
        return 'awpayment';
    }

    return lowered;
}

function getTripStatusLabel(status) {
    if (!status) {
        return 'Unknown';
    }

    return STATUS_LABELS[status] || STATUS_LABELS[status.toLowerCase()] || status;
}

function getSortTimestamp(trip) {
    const dateValue = trip.updatedAt || trip.createdAt || trip.startDate || null;
    const timestamp = Date.parse(dateValue);
    return Number.isNaN(timestamp) ? 0 : timestamp;
}

function formatDateRange(startDate, endDate) {
    return `${formatShortDate(startDate)} - ${formatShortDate(endDate)}`;
}

function formatShortDate(dateValue) {
    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

function formatDateTime(dateValue) {
    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) {
        return 'Not updated';
    }

    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

function getDurationLabel(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return 'Dates not set';
    }

    const diff = Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1;
    if (diff <= 1) {
        return '1 day';
    }

    return `${diff} days`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function toggleTripMenu(tripId, event) {
    if (event) {
        event.stopPropagation();
    }

    const menu = document.getElementById(`menu-${tripId}`);
    if (!menu) {
        return;
    }

    const shouldShow = !menu.classList.contains('show');
    closeAllTripMenus();
    menu.classList.toggle('show', shouldShow);
}

function closeAllTripMenus() {
    document.querySelectorAll('.trip-menu-dropdown.show').forEach((menu) => {
        menu.classList.remove('show');
    });
}

function handleTripRowClick(event, tripId) {
    if (event.target.closest('.actions-cell')) {
        return;
    }

    navigateToTripEventList(tripId);
}

function openTripFromAction(event, tripId) {
    event.stopPropagation();
    navigateToTripEventList(tripId);
}

function navigateToTripEventList(tripId) {
    const trip = tripsData.find((item) => Number(item.tripId) === Number(tripId));

    if (shouldOpenOngoingTripView(trip)) {
        window.location.href = `${URL_ROOT}/tripMarker/ongoingTrip/${tripId}`;
        return;
    }

    window.location.href = `${URL_ROOT}/RegUser/tripEventList/${tripId}`;
}

function shouldOpenOngoingTripView(trip) {
    if (!trip) {
        return false;
    }

    if (normalizeStatus(trip.status) !== 'ongoing') {
        return false;
    }

    const tripStartIso = toIsoDate(trip.startDate);
    const todayIso = toIsoDate(new Date());

    return tripStartIso && todayIso && tripStartIso === todayIso;
}

function toIsoDate(value) {
    const date = value instanceof Date ? value : new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function editTrip(tripId, event) {
    if (event) {
        event.stopPropagation();
    }

    const trip = tripsData.find((item) => Number(item.tripId) === Number(tripId));
    if (!trip) {
        alert('Trip not found');
        return;
    }

    currentEditingTrip = trip;

    document.getElementById('trip-title').value = trip.tripTitle || '';
    document.getElementById('trip-description').value = trip.description || '';
    document.getElementById('people-count').value = Number(trip.numberOfPeople) || 1;
    document.getElementById('start-date').value = toDateInputValue(trip.startDate);
    document.getElementById('end-date').value = toDateInputValue(trip.endDate);

    popupTitle.textContent = 'Update Trip';
    submitBtn.textContent = 'Update Trip';
    syncDateMinimums(false);

    openPopup();
    closeAllTripMenus();
}

async function deleteTrip(tripId, event) {
    if (event) {
        event.stopPropagation();
    }

    const confirmed = confirm('Are you sure you want to delete this trip? This action cannot be undone.');
    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(`${URL_ROOT}/RegUser/deleteTrip`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ tripId })
        });

        const result = await response.json();
        if (!result.success) {
            alert(`Error deleting trip: ${result.message || 'Unknown error'}`);
            return;
        }

        alert('Trip deleted successfully!');
        await loadUserTrips();
    } catch (error) {
        console.error('Error deleting trip:', error);
        alert('An error occurred while deleting the trip.');
    } finally {
        closeAllTripMenus();
    }
}

function bindFormActions() {
    const createTripBtn = document.getElementById('create-trip-btn');
    const cancelBtn = document.getElementById('cancel-popup');
    const closeBtn = document.getElementById('close-popup-btn');

    createTripBtn?.addEventListener('click', () => {
        prepareCreateForm();
        openPopup();
    });

    cancelBtn?.addEventListener('click', closePopup);
    closeBtn?.addEventListener('click', closePopup);

    popup.addEventListener('click', (event) => {
        if (event.target === popup) {
            closePopup();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && popup.style.display === 'flex') {
            closePopup();
        }
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.trip-menu-container')) {
            closeAllTripMenus();
        }
    });

    startDateInput?.addEventListener('change', () => {
        syncDateMinimums(!currentEditingTrip);
    });

    createTripForm.addEventListener('submit', handleTripFormSubmit);
}

function prepareCreateForm() {
    currentEditingTrip = null;
    createTripForm.reset();
    popupTitle.textContent = 'Create New Trip';
    submitBtn.textContent = 'Create Trip';
    syncDateMinimums(true);
}

function openPopup() {
    popup.style.display = 'flex';
    popup.setAttribute('aria-hidden', 'false');
    document.body.classList.add('trip-modal-open');

    setTimeout(() => {
        document.getElementById('trip-title')?.focus();
    }, 50);
}

function closePopup() {
    popup.style.display = 'none';
    popup.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('trip-modal-open');
    createTripForm.reset();
    submitBtn.disabled = false;
    submitBtn.textContent = 'Create Trip';
    popupTitle.textContent = 'Create New Trip';
    currentEditingTrip = null;
    syncDateMinimums(true);
}

function syncDateMinimums(enforceToday = true) {
    const today = new Date().toISOString().split('T')[0];
    if (startDateInput) {
        if (enforceToday) {
            startDateInput.setAttribute('min', today);
        } else {
            startDateInput.removeAttribute('min');
        }
    }

    const selectedStartDate = startDateInput?.value || (enforceToday ? today : '');
    if (endDateInput) {
        if (selectedStartDate) {
            endDateInput.setAttribute('min', selectedStartDate);
        } else {
            endDateInput.removeAttribute('min');
        }

        if (selectedStartDate && endDateInput.value && endDateInput.value < selectedStartDate) {
            endDateInput.value = selectedStartDate;
        }
    }
}

function toDateInputValue(rawDate) {
    if (!rawDate) {
        return '';
    }

    if (typeof rawDate === 'string' && rawDate.length >= 10) {
        return rawDate.slice(0, 10);
    }

    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toISOString().split('T')[0];
}

async function handleTripFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(createTripForm);
    const tripData = {
        tripTitle: (formData.get('trip_title') || '').trim(),
        description: (formData.get('trip_description') || '').trim(),
        numberOfPeople: parseInt(formData.get('people_count'), 10),
        startDate: formData.get('start_date'),
        endDate: formData.get('end_date')
    };

    if (!tripData.tripTitle) {
        alert('Please enter a trip title.');
        return;
    }

    if (!tripData.startDate || !tripData.endDate) {
        alert('Please select both start and end dates.');
        return;
    }

    if (new Date(tripData.startDate) > new Date(tripData.endDate)) {
        alert('End date cannot be before start date.');
        return;
    }

    if (!tripData.numberOfPeople || tripData.numberOfPeople < 1) {
        alert('Please enter a valid number of people.');
        return;
    }

    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = currentEditingTrip ? 'Updating...' : 'Creating...';

    try {
        let endpoint = `${URL_ROOT}/RegUser/createTrip`;
        let method = 'POST';

        if (currentEditingTrip) {
            endpoint = `${URL_ROOT}/RegUser/updatetrip`;
            method = 'PUT';
            tripData.tripId = currentEditingTrip.tripId;
        }

        const response = await fetch(endpoint, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(tripData)
        });

        const result = await response.json();
        if (!result.success) {
            alert(`Error: ${result.message || 'Unable to save trip.'}`);
            return;
        }

        alert(currentEditingTrip ? 'Trip updated successfully!' : 'Trip created successfully!');
        closePopup();
        await loadUserTrips();
    } catch (error) {
        console.error('Trip form submission error:', error);
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

window.toggleTripMenu = toggleTripMenu;
window.editTrip = editTrip;
window.deleteTrip = deleteTrip;
window.handleTripRowClick = handleTripRowClick;
window.openTripFromAction = openTripFromAction;
window.naviagateToTripEventList = navigateToTripEventList;
