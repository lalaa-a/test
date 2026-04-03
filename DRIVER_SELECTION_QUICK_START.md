# Driver Selection System - Quick Start Guide

## How to Use

### For End Users

1. **Access Driver Selection**
   - Navigate to a trip's "Add Driver" section
   - URL format: `/RegUser/driversSelect/{tripId}`

2. **View Available Drivers**
   - The page automatically loads drivers available for your trip dates
   - Drivers are filtered by:
     - Vehicle capacity (must fit your group size)
     - Availability (no personal/booked dates during trip)

3. **Search Drivers**
   - Use the search bar to find drivers by name or location
   - Results update in real-time

4. **Apply Filters**
   - Click the "Filter" button to open advanced filter options
   - Available filters:
     - **Rating**: Minimum driver rating (0-5 stars)
     - **Price**: Daily and per-km charges
     - **Verification**: Show only verified drivers
     - **Vehicle**: Seating capacity, child seats, vehicle type
     - **Languages**: Driver language skills
     - **Driver Age**: Age range preferences

5. **Use Quick Presets**
   - **Top Rated**: Shows 4.5+ rated drivers
   - **Budget Friendly**: Shows cheapest options
   - **Verified**: Shows verified drivers only

6. **Select a Driver**
   - Click "Select" on a driver card
   - Confirm selection in popup
   - Driver is added to your trip

7. **View Driver Details**
   - Click "View" to see driver profile
   - Shows full vehicle information, ratings, reviews

### For Developers

#### Route Registration
```php
// In your router/routing config
$router->get('/RegUser/driversSelect/:tripId', 'RegUser@driversSelect');
$router->get('/RegUser/getDriversData/:tripId', 'RegUser@getDriversData');
$router->post('/RegUser/filterDrivers/:tripId', 'RegUser@filterDrivers');
```

#### Backend Usage
```php
// Load driver selection for a trip
$this->driversSelect($tripId);

// Get drivers as JSON
$response = $this->getDriversData($tripId); // GET request

// Filter drivers with custom criteria
$response = $this->filterDrivers($tripId); // POST with JSON body
```

#### Filter Request Format
```json
{
  "rating": 4.0,
  "verified": true,
  "minPrice": 0,
  "maxPrice": 500,
  "minAge": 25,
  "maxAge": 65,
  "vehicleType": "Toyota",
  "minSeatingCapacity": 4,
  "maxSeatingCapacity": 8,
  "childSeats": 2,
  "languages": ["English", "Spanish"]
}
```

#### JavaScript Integration
```javascript
// Window receives driver selection
window.tripEventListManager.handleDriverSelection(driverData);

// Or via postMessage
window.addEventListener('message', (event) => {
  if (event.data.type === 'DRIVER_SELECTED') {
    const driverData = event.data.driverData;
    // Handle driver selection
  }
});
```

## Database Tables Used

### users
- User information (id, fullname, profile_photo, dob, etc.)
- Account type = 'driver'

### vehicles
- Vehicle details (make, model, year, seatingCapacity, etc.)
- Foreign key: driverId
- Status & availability flags
- Approval status

### vehicle_pricing
- Pricing information (vehicleChargePerDay, driverChargePerDay, etc.)
- Per-km and per-day rates
- Minimum charges

### driver_unavailable_dates
- Unavailable date ranges
- Reason (personal/booked)
- Foreign key: driverId, tripId

### profile_details
- Driver profile info (bio, languages, rating, etc.)
- Verification status
- Foreign key: userId

## Customization

### Styling
Edit `public/css/regUser/trips/addOns/drivers/driverSelect.css`

Key CSS variables:
```css
--primary-color: #006a71;        /* Primary teal */
--primary-hover: #0891b2;        /* Hover state */
--card-width: 270px;             /* Card dimensions */
--card-gap: 20px;                /* Spacing */
```

### Filter Options
Modify `public/js/regUser/trips/addOns/drivers/driverSelect.js`:

```javascript
// Add new filter type
getFilterData() {
  return {
    newFilter: this.filterNewElement?.value || ''
  };
}
```

### Filter Categories
Edit model method `getDriversForTrip()` in `RegUserSelectionModel.php`:

```php
// Add new category
$newCategory = [];
foreach ($drivers as $driver) {
  if ($driver->meets_criteria) {
    $newCategory[] = $driver;
  }
}

$mainFilters['new_category'] = [
  'name' => 'New Category',
  'count' => count($newCategory),
  'accounts' => $newCategory
];
```

## Performance Considerations

### Query Optimization
- Single query retrieves all driver data
- Joins optimized with INNER/LEFT joins
- Unavailable dates checked in subquery
- Results indexed by userId

### Frontend Optimization
- Lazy loads driver cards
- Client-side filtering fallback
- Efficient CSS with custom properties
- Minimal DOM manipulation

### Caching Opportunities
- Cache driver list per trip (15 min TTL)
- Cache pricing conversion rates
- Cache language list
- Cache vehicle makes

## Troubleshooting

### No Drivers Appear
1. Check if drivers exist in database
2. Verify vehicle status = 1 (active)
3. Verify vehicle isApproved = 1
4. Check if vehicles have pricing data
5. Verify no unavailable dates block entire trip period

### Filters Not Working
1. Check browser console for errors
2. Verify filter elements are properly bound
3. Check network tab for filter API response
4. Verify JSON format is correct

### Currency Not Converting
1. Check user session currency is set
2. Verify `convertCharge()` helper function exists
3. Check currency conversion rates are configured
4. Verify currency symbols are defined

### Modal Not Closing
1. Check console for JavaScript errors
2. Verify confirmationModal element exists
3. Check if parent window is accessible
4. Verify postMessage origin matches

## Examples

### Display drivers from specific region
```php
// Modify query in getDriversForTrip()
$query .= " AND u.address LIKE :region";
$this->db->bind(':region', "%$region%");
```

### Add minimum experience requirement
```php
// Check driver created_at timestamp
$query .= " AND DATEDIFF(NOW(), u.created_at) >= :minDays";
$this->db->bind(':minDays', 90); // 3 months minimum
```

### Exclude specific drivers
```php
// Add to filter method
if (isset($filters['excludeDriverIds'])) {
  $placeholders = implode(',', array_fill(0, count($filters['excludeDriverIds']), '?'));
  $query .= " AND u.id NOT IN ($placeholders)";
  // Bind each ID
}
```

## Support

For issues or questions:
1. Check the DRIVER_SELECTION_IMPLEMENTATION.md file for detailed documentation
2. Review database schema to ensure all tables exist
3. Verify routing is properly configured
4. Check browser console for errors
5. Review server error logs
