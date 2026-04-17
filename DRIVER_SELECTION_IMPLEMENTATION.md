# Driver Selection Implementation Summary

## Overview
Implemented a complete driver selection functionality for trip bookings, similar to the guide selection system but tailored for drivers with vehicle-specific filters and availability checking.

## Features Implemented

### 1. Backend - Database Model Methods
**File:** `app/models/RegUserSelectionModel.php`

Added two comprehensive methods:

#### `getDriversForTrip($tripId)`
- Retrieves all available drivers for a specific trip
- Filters drivers based on:
  - **Trip requirements**: Vehicle capacity must be >= number of people
  - **Availability**: Checks driver_unavailable_dates to exclude unavailable drivers
  - **Trip dates**: Filters out drivers unavailable during trip duration
- Organizes results into filter categories:
  - All Drivers
  - Top Rated (4.0+ rating)
  - Verified Drivers
  - Budget Friendly (≤ $100 per day)
- Returns comprehensive driver/vehicle data including pricing

#### `filterDriversByTrip($tripId, $filters)`
- Applies advanced filters to driver results
- Supports filtering by:
  - **Rating**: Minimum rating threshold
  - **Verification**: DL verified or account verified
  - **Pricing**: Min/max per-day and per-km charges
  - **Age**: Driver age range
  - **Vehicle capacity**: Seating capacity range
  - **Child seats**: Minimum child seats required
  - **Vehicle type**: Filter by make/brand
  - **Languages**: Driver language filters

### 2. Backend - Controller Endpoints
**File:** `app/controllers/RegUser.php`

Added three new endpoints:

#### `driversSelect($tripId)`
- Main route handler for driver selection interface
- Loads drivers with filters, user currency, and formatting
- Renders HTML, CSS, and JS resources

#### `getDriversData($tripId)` (JSON API)
- Returns all available drivers as JSON
- Converts pricing to user's currency
- Used for initial data loading and client-side processing

#### `filterDrivers($tripId)` (JSON API - POST)
- Accepts filter parameters via JSON
- Calls model filter method
- Returns filtered driver list with currency conversion
- Supports live preview filtering

### 3. Frontend - HTML View
**File:** `app/views/Trips/addOns/drivers/driversSelect.php`

Created comprehensive UI including:

#### Search Section
- Title and subtitle explaining the purpose
- Search input field
- Filter toggle button
- Quick filter chips (All, Top Rated, Verified, Budget Friendly)

#### Advanced Filter Popup
- Rating slider (0-5 stars)
- Price range sliders (per day, per km)
- Driver age range sliders
- Verification checkbox
- Vehicle seating capacity inputs
- Child seats filter
- Vehicle type selector (Toyota, Honda, Nissan, Mitsubishi, Suzuki, Other)
- Language selector (dynamic, populated from data)
- Active filter chips showing current filters
- Quick preset buttons (Top Rated, Budget Friendly, Verified)
- Live preview toggle
- Reset and Apply buttons

#### Driver Cards
- Display driver name and vehicle info
- Vehicle image (front view photo)
- Rating and driver age
- Vehicle details: seating capacity, child seats
- Pricing: Per Day and Per Km charges
- Verification badge
- Save to favorites button
- Select and View buttons
- Category sections (All, Top Rated, Verified, Budget Friendly)

#### Confirmation Modal
- Confirmation dialog when selecting a driver
- Shows driver name and vehicle info
- Cancel and Confirm buttons

### 4. Frontend - Styling
**File:** `public/css/regUser/trips/addOns/drivers/driverSelect.css`

Complete modern CSS including:
- Responsive design (mobile-first)
- CSS variables for theming
- Card-based layout with hover effects
- Filter popup with two-column layout
- Range slider styling
- Modal styling with backdrop blur
- Smooth animations and transitions
- Support for all screen sizes

Key features:
- Primary color: `#006a71` (teal)
- Card width: 270px with responsive adjustments
- Horizontal scrolling for "All Drivers" section
- Grid layout for filtered categories
- Two-column filter grid on desktop
- Single-column on mobile

### 5. Frontend - JavaScript Logic
**File:** `public/js/regUser/trips/addOns/drivers/driverSelect.js`

Implemented `DriverSelectionManager` class with:

#### Initialization
- Elements binding
- Event listener setup
- Data fetching

#### Filtering System
- Filter data collection from UI
- Backend API integration
- Client-side fallback filtering
- Live preview updates
- Filter state management
- Active filter chips display

#### Filter Types
- Rating threshold
- Verified status
- Price range (per day)
- Driver age range
- Vehicle capacity range
- Child seats requirement
- Vehicle type selection
- Language preferences

#### Presets
- "Top Rated": Sets rating to 4.5+
- "Budget Friendly": Limits to 25th percentile of prices
- "Verified": Shows only verified drivers

#### Selection Process
- Confirmation dialog
- Data collection from selected card
- Communication with parent window
- Pricing information passing

## Data Flow

```
Trip Created (with number of people, start date, end date)
    ↓
User clicks "Select Driver"
    ↓
driversSelect($tripId) loads
    ↓
getDriversForTrip($tripId) called
    ↓
Database query:
  - Gets trip details (numberOfPeople, dates)
  - Gets drivers with vehicles (capacity >= people)
  - Excludes drivers unavailable during trip dates
  - Returns structured categories
    ↓
Frontend displays drivers with filters
    ↓
User applies filters (optional)
    ↓
filterDrivers() API called with filter data
    ↓
Database returns filtered results
    ↓
User selects a driver
    ↓
Driver data sent to parent window
    ↓
Trip updated with selected driver
```

## Database Queries

### Main Query (getDriversForTrip)
```sql
SELECT DISTINCT
  u.id, u.fullname, u.phone, u.email, u.profile_photo, u.dob, u.verified,
  v.vehicleId, v.make, v.model, v.year, v.color, v.seatingCapacity, v.childSeats,
  v.licensePlate, v.frontViewPhoto, v.description,
  vp.vehicleChargePerKm, vp.driverChargePerKm, vp.vehicleChargePerDay, vp.driverChargePerDay,
  vp.minimumKm, vp.minimumDays,
  TIMESTAMPDIFF(YEAR, u.dob, CURDATE()) as age,
  pd.bio, pd.languages, pd.averageRating, pd.dlVerified
FROM users u
INNER JOIN vehicles v ON u.id = v.driverId
LEFT JOIN vehicle_pricing vp ON v.vehicleId = vp.vehicleId
LEFT JOIN profile_details pd ON u.id = pd.userId
WHERE u.account_type = 'driver'
  AND v.isApproved = 1
  AND v.status = 1
  AND v.availability = 1
  AND v.seatingCapacity >= :numberOfPeople
  AND u.id NOT IN (
    SELECT DISTINCT driverId 
    FROM driver_unavailable_dates 
    WHERE unavailableDate BETWEEN :startDate AND :endDate
  )
ORDER BY pd.averageRating DESC, vp.vehicleChargePerDay ASC
```

## Filter Categories

### All Drivers
- All available drivers matching basic criteria

### Top Rated (4.0+)
- Drivers with rating >= 4.0
- Sorted by rating descending

### Verified Drivers
- Drivers with DL verification or account verification
- Shows trusted drivers

### Budget Friendly
- Drivers with daily rate <= $100
- Best value options

## Currency Conversion

- All prices converted to user's currency using `convertCharge()` helper
- Displays currency symbol and formatted amount
- Supports multiple currency types via user session

## Integration Points

### Parent Window Communication
When driver is selected, sends data:
```javascript
{
  driverId: number,
  vehicleId: number,
  fullName: string,
  vehicleInfo: string,
  averageRating: number,
  profilePhoto: string,
  make: string,
  model: string,
  year: number,
  seatingCapacity: number,
  childSeats: number,
  licensePlate: string,
  totalChargePerDay: number,
  totalChargePerKm: number,
  currency: string,
  currencySymbol: string,
  formattedChargePerDay: string,
  formattedChargePerKm: string
}
```

## Availability Checking

The system checks `driver_unavailable_dates` table to:
- Exclude drivers unavailable on any trip date
- Support reason field (personal/booked)
- Allow drivers to mark themselves unavailable
- Prevent double-booking conflicts

## Mobile Responsiveness

- Single column layout on phones
- Two column layout on tablets
- Full responsive grid on desktop
- Touch-friendly filter controls
- Optimized modal sizing
- Scrollable containers with smooth scrolling

## Performance Optimizations

- Single query per driver load (includes all joins)
- Filter categories pre-computed in model
- Client-side filtering fallback
- Lazy loading of driver cards
- Efficient filter state management
- CSS variables for fast theming

## Files Modified/Created

### Created:
1. `app/views/Trips/addOns/drivers/driversSelect.php`
2. `public/css/regUser/trips/addOns/drivers/driverSelect.css`
3. `public/js/regUser/trips/addOns/drivers/driverSelect.js`

### Modified:
1. `app/models/RegUserSelectionModel.php` - Added 2 methods
2. `app/controllers/RegUser.php` - Added 3 endpoints

## Testing Checklist

- [ ] Verify drivers appear for trip with valid dates and capacity
- [ ] Test all filter combinations
- [ ] Verify unavailable dates exclude drivers
- [ ] Test currency conversion
- [ ] Test modal selection process
- [ ] Test responsive design on mobile
- [ ] Verify pricing calculations
- [ ] Test live preview toggle
- [ ] Test preset filters
- [ ] Verify error handling
