# Currency Conversion & Charge Calculation Implementation

## Overview
Implemented currency conversion for service charges (guides, drivers, etc.) based on tourist's preferred currency stored in session. Charges are shown in selection cards and calculated correctly for per-person rates multiplied by number of travelers.

**Generic Functions**: All functions are designed to work with guides, drivers, and any other service providers.

## Files Modified/Created

### 1. **Currency Helper** (NEW)
**File**: `app/helpers/currency_helper.php`
- Exchange rates for 20+ currencies (LKR as base)
- Conversion functions from LKR to user currency
- Currency symbol mapping
- Price formatting functions

**Main Functions**:
- `convertCharge($chargeLKR, $userCurrency)` - Generic function for guides, drivers, etc.
- `convertFromLKR($amount, $targetCurrency)` - Core conversion
- `formatPrice($amount, $currencyCode)` - Format with symbol
- `getUserCurrency()` - Get from session
- `getCurrencySymbol($currencyCode)` - Get currency symbol

### 2. **Backend - RegUser Controller**
**File**: `app/controllers/RegUser.php`
**Changes**:
- Added `require_once currency_helper.php`
- `guidesSelect()`: Passes `userCurrency` and `currencySymbol` to view
- `getGuidesData()`: Converts all guide charges to user's currency in API response
- `getTripDetails()`: NEW endpoint to get trip info including `numberOfPeople`

### 3. **Frontend - Guide Selection View**
**File**: `app/views/Trips/addOns/guides/guidesSelect.php`
**Changes**:
- Guide cards now display converted prices using `convertCharge()`
- Shows currency symbol and formatted amount in user's currency

**Note**: Same approach can be used for driver selection views - just call `convertCharge()` with driver charges.

### 4. **Frontend - Guide Selection JavaScript**
**File**: `public/js/regUser/trips/addOns/guides/guidesSelect.js`
**Changes**:
- `selectGuide()`: Now includes charge data in guide selection:
  - `baseCharge` (original LKR amount)
  - `convertedCharge` (converted to user currency)
  - `chargeType` (per_day/per_person)
  - `currency` & `currencySymbol`
  - `formattedCharge` (ready to display)

### 5. **Frontend - Trip Event List JavaScript**
**File**: `public/js/regUser/trips/tripEventList.js`
**Changes**:
- `handleGuideSelection()`: Enhanced to display guide charges
- `calculateAndDisplayGuideCharge()`: NEW function that:
  - Fetches trip details from `getTripDetails()` endpoint
  - For **per_person** charges: Multiplies rate by `numberOfPeople`
  - Shows breakdown: "Rs 5,000 × 3 people = Rs 15,000 total"
  - For **per_day** charges: Shows single rate
  - Displays in user's currency from session

### 6. **Styling**
**File**: `public/css/regUser/trips/tripEventList.css`
**Added**:
- `.guide-charge` - Container with blue background
- `.charge-breakdown` - Shows per-person calculation
- `.charge-total` - Highlighted total amount
- Responsive and styled to match existing design

## How It Works

### 1. Currency Selection During Registration
- Tourist selects currency on page 1 of registration (e.g., USD, EUR, GBP)
- Currency code stored in `users` table (`currency_code` column)
- On login, currency stored in session: `$_SESSION['user_currency']`

### 2. Guide Selection Page
```
User views guides/drivers → Backend converts LKR prices → Displays in user's currency
```

**Example**:
- Service charge: 10,000 LKR per day
- Tourist currency: USD
- Displayed as: **$31.00 per day**

### 3. Guide Charge Calculation (Per Person)
```
Guide selected → Fetch trip details → Get numberOfPeople → Calculate total
```

**Example**:
- Guide charges: 5,000 LKR per person
- Number of people: 3
- Tourist currency: EUR
- Calculation:
  - 5,000 LKR × 0.0029 = €14.50 per person
  - €14.50 × 3 people = **€43.50 total**

**Display**:
```
Guide Fee: €14.50 × 3 people
Total: €43.50 EUR
```

### 4. Guide Charge Calculation (Per Day)
```
Guide selected → Display single rate in user's currency
```

**Example**:
- Guide charges: 15,000 LKR per day
- Tourist currency: GBP
- Displayed as: **£37.50 per day**

## Database Schema

### `users` table
```sql
currency_code VARCHAR(10) DEFAULT 'USD'
```

### `guide_locations` table (existing)
```sql
baseCharge DECIMAL(10,2)  -- Stored in LKR
chargeType ENUM('per_day', 'per_person', 'per_trip')
```

### `created_trips` table (existing)
```sql
numberOfPeople INT
tripId INT
userId INT
```

## API Endpoints

### GET `/RegUser/getGuidesData/{spotId}`
**Response**:
```json
{
  "success": true,
  "guides": [
    {
      "userId": 123,
      "fullname": "John Doe",
      "baseCharge": 10000,
      "convertedCharge": 31.00,
      "chargeType": "per_day",
      "currency": "USD",
      "currencySymbol": "$",
      "formattedCharge": "$31.00"
    }
  ],
  "currency": "USD",
  "currencySymbol": "$"
}
```

### GET `/RegUser/getTripDetails/{tripId}`
**Response**:
```json
{
  "success": true,
  "trip": {
    "tripId": 456,
    "numberOfPeople": 3,
    "tripTitle": "Beach Vacation",
    "startDate": "2026-03-01",
    "endDate": "2026-03-05"
  }
}
```

## Supported Currencies
- USD (US Dollar) $
- EUR (Euro) €
- GBP (British Pound) £
- LKR (Sri Lankan Rupee) Rs
- AUD, CAD, JPY, CNY, INR, SGD, MYR, THB, KRW, CHF, SEK, NOK, DKK, NZD, HKD, ZAR

## Exchange Rates
All guide charges stored in **LKR** in database.
Exchange rates defined in `currency_helper.php`:
```php
'USD' => 0.0031,  // 1 LKR = 0.0031 USD
'EUR' => 0.0029,  // 1 LKR = 0.0029 EUR
'GBP' => 0.0025,  // 1 LKR = 0.0025 GBP
// ... etc
```

**Note**: Exchange rates are static. For production, consider:
- Using a live exchange rate API (e.g., exchangerate-api.com)
- Updating rates daily via cron job
- Caching rates in database

## Testing
1. Register as tourist, select EUR currency
2. Login (currency stored in session)
3. Create trip with numberOfPeople = 3
4. Select travel spot
5. Click "Add Guide"
6. View guide cards - should show prices in EUR
7. Select a guide with chargeType = 'per_person'
8. Guide section should display:
   - "Guide Fee: €14.50 × 3 people"
   - "Total: €43.50 EUR"

## Using with Drivers

The same functions work for drivers! Here's how to implement:

### In Driver Controller:
```php
// Get user currency
$userCurrency = getUserCurrency();
$currencySymbol = getCurrencySymbol($userCurrency);

// Convert driver charges
foreach ($driversData as $driver) {
    $converted = convertCharge($driver->baseCharge, $userCurrency);
    $driver->convertedCharge = $converted['amount'];
    $driver->currency = $userCurrency;
    $driver->currencySymbol = $currencySymbol;
    $driver->formattedCharge = $converted['formatted'];
}
```

### In Driver View:
```php
<div class="driver-pricing">
    <span class="price-amount">
        <?php 
        $converted = convertCharge($driver->baseCharge, $userCurrency);
        echo $converted['formatted'];
        ?>
    </span>
    <span class="price-type">
        <?php echo $driver->chargeType === 'per_day' ? 'Per Day' : 'Per Hour'; ?>
    </span>
</div>
```

### In Driver Selection JavaScript:
```javascript
// Include charge data when selecting driver
const driverData = {
    driverId: driverId,
    fullName: selectedCard.querySelector('.driver-name').textContent,
    baseCharge: driver.baseCharge,
    convertedCharge: driver.convertedCharge,
    chargeType: driver.chargeType,
    currency: driver.currency,
    currencySymbol: driver.currencySymbol,
    formattedCharge: driver.formattedCharge
};
```

## Future Enhancements
- Live exchange rate API integration
- Cache exchange rates with daily updates
- Admin panel to manage exchange rates
- Historical rate tracking for invoices
- Multi-currency invoicing
