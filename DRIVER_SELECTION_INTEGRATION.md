# Driver Selection Integration Guide

## Key Integration Points

### 1. Trip Event Handler Integration

The driver selection system communicates back to the parent window via the `tripEventListManager` object. Ensure your parent page has this handler:

```javascript
// In your trip management page
const tripEventListManager = {
  handleDriverSelection: function(driverData) {
    console.log('Driver selected:', driverData);
    
    // Add to trip drivers
    this.addDriverToTrip(driverData);
    
    // Update UI
    this.updateTripDisplay();
    
    // Close selection window
    window.close();
  },
  
  addDriverToTrip: function(driver) {
    // Store driver data in your trip object
    trip.selectedDrivers.push({
      driverId: driver.driverId,
      vehicleId: driver.vehicleId,
      name: driver.fullName,
      vehicle: `${driver.make} ${driver.model}`,
      seating: driver.seatingCapacity,
      pricePerDay: driver.totalChargePerDay,
      pricePerKm: driver.totalChargePerKm,
      currency: driver.currency
    });
  },
  
  updateTripDisplay: function() {
    // Re-render trip details with new driver
    renderTripSummary();
  }
};
```

### 2. URL Structure

The driver selection system uses this URL pattern:

```
/RegUser/driversSelect/{tripId}
```

Call it from your trip management page:

```php
// In your template
$driverSelectUrl = URL_ROOT . '/RegUser/driversSelect/' . $tripId;
$html = "<a href='$driverSelectUrl' target='_blank'>Select Driver</a>";

// Or with a button that opens in popup
echo "<button onclick=\"window.open('$driverSelectUrl', 'driverSelect', 'width=1200,height=800');\">Add Driver</button>";
```

### 3. Database Schema Verification

Ensure these tables exist with the shown columns:

#### users table
```sql
- id (PRIMARY KEY)
- account_type (ENUM: 'driver', 'guide', etc.)
- fullname
- dob
- profile_photo
- verified (TINYINT)
- created_at
```

#### vehicles table
```sql
- vehicleId (PRIMARY KEY)
- driverId (FOREIGN KEY → users.id)
- make
- model
- year
- color
- seatingCapacity
- childSeats
- licensePlate
- frontViewPhoto
- description
- status (TINYINT: 1=active)
- availability (TINYINT: 1=available)
- isApproved (TINYINT: 1=approved)
```

#### vehicle_pricing table
```sql
- pricingId (PRIMARY KEY)
- vehicleId (FOREIGN KEY → vehicles.vehicleId)
- driverId (FOREIGN KEY → users.id)
- vehicleChargePerKm (DECIMAL)
- driverChargePerKm (DECIMAL)
- vehicleChargePerDay (DECIMAL)
- driverChargePerDay (DECIMAL)
- minimumKm (DECIMAL)
- minimumDays (DECIMAL)
```

#### driver_unavailable_dates table
```sql
- id (PRIMARY KEY)
- driverId (FOREIGN KEY → users.id, INDEX)
- unavailableDate (DATE, INDEX)
- reason (ENUM: 'personal', 'booked')
- personalReason (TEXT)
- tripId (FOREIGN KEY → created_trips.tripId, nullable)
```

#### profile_details table
```sql
- userId (FOREIGN KEY → users.id)
- bio
- languages
- averageRating
- dlVerified (TINYINT)
```

#### created_trips table
```sql
- tripId (PRIMARY KEY)
- userId
- numberOfPeople (INT)
- startDate (DATE)
- endDate (DATE)
- status
```

### 4. Helper Functions Required

Ensure these helper functions exist in your codebase:

#### Currency Conversion
```php
/**
 * Convert amount from USD to user's currency
 */
function convertCharge($amount, $currency = 'USD') {
  // Must return array with:
  // - 'amount': converted amount
  // - 'formatted': formatted with currency symbol
  // - 'symbol': currency symbol
  
  // Example implementation:
  $rates = [
    'USD' => 1.0,
    'EUR' => 0.92,
    'GBP' => 0.79,
    'JPY' => 149.5
  ];
  
  $converted = $amount * ($rates[$currency] ?? 1.0);
  
  return [
    'amount' => $converted,
    'formatted' => $rates[$currency] === 1.0 ? '$' . round($converted, 2) : round($converted, 2) . ' ' . $currency,
    'symbol' => getCurrencySymbol($currency)
  ];
}

/**
 * Get currency symbol for display
 */
function getCurrencySymbol($currency) {
  $symbols = [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥'
  ];
  return $symbols[$currency] ?? $currency;
}

/**
 * Get user's currency from session
 */
function getUserCurrency() {
  return getSession('user_currency') ?? 'USD';
}
```

### 5. Session Management

The system expects these session variables:

```php
// Set during user login/registration
setSession('user_id', $userId);
setSession('user_currency', $currency); // e.g., 'USD'
setSession('user_country', $country);   // e.g., 'US'

// Retrieved by the driver selection system
$currency = getUserCurrency(); // From session
$userId = getSession('user_id');
```

### 6. Error Handling

The system includes error handling. Ensure these configurations:

```php
// In your error handler
error_reporting(E_ALL);
ini_set('display_errors', 0); // Log errors, don't display
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/logs/php_error.log');

// Database errors
try {
  // Database operations
} catch (PDOException $e) {
  error_log("Database error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
}

// API errors
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  return;
}
```

### 7. Routing Configuration

Ensure your routing system supports these routes:

```php
// Route patterns
/RegUser/driversSelect/:tripId        // GET - Main page
/RegUser/getDriversData/:tripId       // GET - AJAX data
/RegUser/filterDrivers/:tripId        // POST - AJAX filters

// In your router
public function route() {
  // Extract: controller/method/params
  $parts = explode('/', trim($_GET['url'], '/'));
  
  $controller = $parts[0] ?? 'Home';
  $method = $parts[1] ?? 'index';
  $params = array_slice($parts, 2);
  
  // Load controller and call method
  $controllerClass = $this->loadClass($controller);
  call_user_func_array([$controllerClass, $method], $params);
}
```

### 8. Asset Loading

The system loads external assets. Ensure these are available:

```html
<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Custom fonts -->
<link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
```

### 9. Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Trip Management Page                      │
│  - User clicks "Select Driver"                              │
│  - Opens: /RegUser/driversSelect/{tripId}                  │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│              Driver Selection Interface                      │
│  - driversSelect.php (HTML/UI)                              │
│  - driverSelect.css (Styling)                               │
│  - driverSelect.js (Logic)                                  │
└──────────────┬──────────────────────────────────────────────┘
               │
               ├──► getDriversData() ◄─── RegUserSelectionModel
               │                          getDriversForTrip()
               │
               ├──► filterDrivers() ◄──── RegUserSelectionModel
               │                          filterDriversByTrip()
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Queries                          │
│  - Fetch drivers for trip                                   │
│  - Check availability                                       │
│  - Apply filters                                            │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│                 JSON Response to Frontend                    │
│  - Driver list with pricing                                 │
│  - Filtered results                                         │
│  - Currency conversion                                      │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│              Display & User Selection                        │
│  - Show driver cards                                        │
│  - Apply filters                                            │
│  - Confirm selection                                        │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│           postMessage to Parent Window                       │
│  - Send driverData                                          │
│  - Parent receives via tripEventListManager                 │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│        Update Trip with Selected Driver                      │
│  - Store in trip object                                     │
│  - Update UI                                                │
│  - Save to database                                         │
└─────────────────────────────────────────────────────────────┘
```

### 10. Testing Checklist

```php
// Test database connectivity
$db = new Database();
$db->query("SELECT 1");
echo "Database OK";

// Test driver retrieval
$model = new RegUserSelectionModel();
$drivers = $model->getDriversForTrip(1);
echo count($drivers['all']['accounts']) . " drivers found";

// Test filtering
$filters = ['rating' => 4.0, 'verified' => true];
$filtered = $model->filterDriversByTrip(1, $filters);
echo count($filtered) . " drivers match filters";

// Test currency conversion
$converted = convertCharge(100, 'EUR');
echo $converted['formatted']; // Should show EUR amount

// Test session
setSession('user_currency', 'GBP');
echo getUserCurrency(); // Should return 'GBP'
```

## Common Issues & Solutions

### Issue: No drivers appearing
**Solution**: 
1. Check `vehicles.status = 1` and `vehicles.availability = 1`
2. Verify `vehicles.isApproved = 1`
3. Check `vehicle_pricing` has data for each vehicle
4. Verify trip dates don't conflict with `driver_unavailable_dates`

### Issue: Currency showing as USD for everyone
**Solution**: 
1. Check user session has `user_currency` set
2. Verify `convertCharge()` function exists
3. Check currency conversion rates are configured
4. Verify `getCurrencySymbol()` returns correct symbols

### Issue: Filters not working
**Solution**: 
1. Check browser console for JS errors
2. Verify filter elements have correct IDs
3. Check network tab for API response errors
4. Verify JSON format matches expected structure

### Issue: Driver selection not saving
**Solution**: 
1. Check `tripEventListManager` exists in parent window
2. Verify `handleDriverSelection()` method is defined
3. Check postMessage origin matches
4. Verify database connection for save operation

## Performance Tuning

### Database Indexes
```sql
CREATE INDEX idx_vehicles_driverId ON vehicles(driverId);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_driver_dates_driverId ON driver_unavailable_dates(driverId);
CREATE INDEX idx_driver_dates_date ON driver_unavailable_dates(unavailableDate);
CREATE INDEX idx_pricing_vehicleId ON vehicle_pricing(vehicleId);
```

### Query Optimization
- Add LIMIT to initial load (e.g., first 100 drivers)
- Cache filter categories (rating, verified, budget)
- Use pagination for filtered results
- Lazy load driver photos

### Frontend Optimization
- Minify CSS and JavaScript
- Use CSS sprites for icons
- Implement virtual scrolling for large lists
- Cache API responses (60 seconds)
