<?php
/**
 * Currency Helper
 * Handles currency conversion for guide charges
 * All charges in database are stored in LKR
 */

// Exchange rates (LKR as base)
// Update these periodically or fetch from API
function getCurrencyRates() {
    return [
        'LKR' => 1.0,
        'USD' => 0.0031,    // 1 LKR = 0.0031 USD
        'EUR' => 0.0029,    // 1 LKR = 0.0029 EUR
        'GBP' => 0.0025,    // 1 LKR = 0.0025 GBP
        'AUD' => 0.0048,    // 1 LKR = 0.0048 AUD
        'CAD' => 0.0042,    // 1 LKR = 0.0042 CAD
        'JPY' => 0.47,      // 1 LKR = 0.47 JPY
        'CNY' => 0.022,     // 1 LKR = 0.022 CNY
        'INR' => 0.26,      // 1 LKR = 0.26 INR
        'SGD' => 0.0042,    // 1 LKR = 0.0042 SGD
        'MYR' => 0.014,     // 1 LKR = 0.014 MYR
        'THB' => 0.11,      // 1 LKR = 0.11 THB
        'KRW' => 4.2,       // 1 LKR = 4.2 KRW
        'CHF' => 0.0028,    // 1 LKR = 0.0028 CHF
        'SEK' => 0.033,     // 1 LKR = 0.033 SEK
        'NOK' => 0.034,     // 1 LKR = 0.034 NOK
        'DKK' => 0.022,     // 1 LKR = 0.022 DKK
        'NZD' => 0.0052,    // 1 LKR = 0.0052 NZD
        'HKD' => 0.024,     // 1 LKR = 0.024 HKD
        'ZAR' => 0.057      // 1 LKR = 0.057 ZAR
    ];
}

// Currency symbols
function getCurrencySymbol($currencyCode) {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'LKR' => 'Rs',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'JPY' => '¥',
        'CNY' => '¥',
        'INR' => '₹',
        'SGD' => 'S$',
        'MYR' => 'RM',
        'THB' => '฿',
        'KRW' => '₩',
        'CHF' => 'CHF',
        'SEK' => 'kr',
        'NOK' => 'kr',
        'DKK' => 'kr',
        'NZD' => 'NZ$',
        'HKD' => 'HK$',
        'ZAR' => 'R'
    ];
    
    return $symbols[$currencyCode] ?? $currencyCode;
}

/**
 * Convert amount from LKR to target currency
 * @param float $amountLKR - Amount in LKR
 * @param string $targetCurrency - Target currency code
 * @return float - Converted amount
 */
function convertFromLKR($amountLKR, $targetCurrency = 'USD') {
    $rates = getCurrencyRates();
    
    if (!isset($rates[$targetCurrency])) {
        error_log("Currency not found: $targetCurrency, defaulting to USD");
        $targetCurrency = 'USD';
    }
    
    $convertedAmount = $amountLKR * $rates[$targetCurrency];
    
    // Round based on currency
    if (in_array($targetCurrency, ['JPY', 'KRW'])) {
        // No decimal places for these currencies
        return round($convertedAmount, 0);
    } else {
        // 2 decimal places for most currencies
        return round($convertedAmount, 2);
    }
}

/**
 * Format price with currency symbol
 * @param float $amount - Amount to format
 * @param string $currencyCode - Currency code
 * @return string - Formatted price string
 */
function formatPrice($amount, $currencyCode = 'USD') {
    $symbol = getCurrencySymbol($currencyCode);
    
    // Format based on currency
    if (in_array($currencyCode, ['JPY', 'KRW'])) {
        return $symbol . number_format($amount, 0);
    } else {
        return $symbol . number_format($amount, 2);
    }
}

/**
 * Get user's currency from session or default to USD
 * @return string - Currency code
 */
function getUserCurrency() {
    return getSession('user_currency', 'USD');
}

/**
 * Convert service charge to user's currency (for guides, drivers, etc.)
 * @param float $chargeLKR - Charge in LKR
 * @param string|null $userCurrency - User's currency (null to get from session)
 * @return array - ['amount' => converted amount, 'currency' => currency code, 'formatted' => formatted string]
 */
function convertCharge($chargeLKR, $userCurrency = null) {
    if ($userCurrency === null) {
        $userCurrency = getUserCurrency();
    }
    
    $convertedAmount = convertFromLKR($chargeLKR, $userCurrency);
    
    return [
        'amount' => $convertedAmount,
        'currency' => $userCurrency,
        'formatted' => formatPrice($convertedAmount, $userCurrency),
        'symbol' => getCurrencySymbol($userCurrency)
    ];
}

/**
 * Alias for backward compatibility
 * @deprecated Use convertCharge() instead
 */
function convertGuideCharge($chargeLKR, $userCurrency = null) {
    return convertCharge($chargeLKR, $userCurrency);
}
