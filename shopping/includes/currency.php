<?php
/**
 * Global Multi-Currency & Dynamic Pricing Engine
 * Handles full global currency conversion, formatting, and region persistence.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Complete Global Currencies Definition
 * Exchange rates relative to INR (base = 1.0)
 */
function get_currency_definitions() {
    return [
        'USD' => [
            'code' => 'USD',
            'symbol' => '$',
            'name' => 'US Dollar',
            'country' => 'United States',
            'flag' => '🇺🇸',
            'rate' => 0.012,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'EUR' => [
            'code' => 'EUR',
            'symbol' => '€',
            'name' => 'Euro',
            'country' => 'European Union',
            'flag' => '🇪🇺',
            'rate' => 0.011,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'GBP' => [
            'code' => 'GBP',
            'symbol' => '£',
            'name' => 'British Pound',
            'country' => 'United Kingdom',
            'flag' => '🇬🇧',
            'rate' => 0.0095,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'INR' => [
            'code' => 'INR',
            'symbol' => '₹',
            'name' => 'Indian Rupee',
            'country' => 'India',
            'flag' => '🇮🇳',
            'rate' => 1.0,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'CAD' => [
            'code' => 'CAD',
            'symbol' => 'CA$',
            'name' => 'Canadian Dollar',
            'country' => 'Canada',
            'flag' => '🇨🇦',
            'rate' => 0.016,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'AUD' => [
            'code' => 'AUD',
            'symbol' => 'AU$',
            'name' => 'Australian Dollar',
            'country' => 'Australia',
            'flag' => '🇦🇺',
            'rate' => 0.018,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'JPY' => [
            'code' => 'JPY',
            'symbol' => '¥',
            'name' => 'Japanese Yen',
            'country' => 'Japan',
            'flag' => '🇯🇵',
            'rate' => 1.85,
            'decimals' => 0,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'CNY' => [
            'code' => 'CNY',
            'symbol' => 'CN¥',
            'name' => 'Chinese Yuan',
            'country' => 'China',
            'flag' => '🇨🇳',
            'rate' => 0.086,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'AED' => [
            'code' => 'AED',
            'symbol' => 'AED ',
            'name' => 'UAE Dirham',
            'country' => 'United Arab Emirates',
            'flag' => '🇦🇪',
            'rate' => 0.044,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'SAR' => [
            'code' => 'SAR',
            'symbol' => 'SAR ',
            'name' => 'Saudi Riyal',
            'country' => 'Saudi Arabia',
            'flag' => '🇸🇦',
            'rate' => 0.045,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'QAR' => [
            'code' => 'QAR',
            'symbol' => 'QAR ',
            'name' => 'Qatari Riyal',
            'country' => 'Qatar',
            'flag' => '🇶🇦',
            'rate' => 0.043,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'KWD' => [
            'code' => 'KWD',
            'symbol' => 'KD ',
            'name' => 'Kuwaiti Dinar',
            'country' => 'Kuwait',
            'flag' => '🇰🇼',
            'rate' => 0.0037,
            'decimals' => 3,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'BHD' => [
            'code' => 'BHD',
            'symbol' => 'BD ',
            'name' => 'Bahraini Dinar',
            'country' => 'Bahrain',
            'flag' => '🇧🇭',
            'rate' => 0.0045,
            'decimals' => 3,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'OMR' => [
            'code' => 'OMR',
            'symbol' => 'OMR ',
            'name' => 'Omani Rial',
            'country' => 'Oman',
            'flag' => '🇴🇲',
            'rate' => 0.0046,
            'decimals' => 3,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'CHF' => [
            'code' => 'CHF',
            'symbol' => 'CHF ',
            'name' => 'Swiss Franc',
            'country' => 'Switzerland',
            'flag' => '🇨🇭',
            'rate' => 0.0105,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'SGD' => [
            'code' => 'SGD',
            'symbol' => 'S$',
            'name' => 'Singapore Dollar',
            'country' => 'Singapore',
            'flag' => '🇸🇬',
            'rate' => 0.016,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'HKD' => [
            'code' => 'HKD',
            'symbol' => 'HK$',
            'name' => 'Hong Kong Dollar',
            'country' => 'Hong Kong',
            'flag' => '🇭🇰',
            'rate' => 0.093,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'NZD' => [
            'code' => 'NZD',
            'symbol' => 'NZ$',
            'name' => 'New Zealand Dollar',
            'country' => 'New Zealand',
            'flag' => '🇳🇿',
            'rate' => 0.020,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'KRW' => [
            'code' => 'KRW',
            'symbol' => '₩',
            'name' => 'South Korean Won',
            'country' => 'South Korea',
            'flag' => '🇰🇷',
            'rate' => 16.2,
            'decimals' => 0,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'SEK' => [
            'code' => 'SEK',
            'symbol' => 'kr ',
            'name' => 'Swedish Krona',
            'country' => 'Sweden',
            'flag' => '🇸🇪',
            'rate' => 0.126,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'NOK' => [
            'code' => 'NOK',
            'symbol' => 'kr ',
            'name' => 'Norwegian Krone',
            'country' => 'Norway',
            'flag' => '🇳🇴',
            'rate' => 0.128,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'DKK' => [
            'code' => 'DKK',
            'symbol' => 'kr ',
            'name' => 'Danish Krone',
            'country' => 'Denmark',
            'flag' => '🇩🇰',
            'rate' => 0.082,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'PLN' => [
            'code' => 'PLN',
            'symbol' => 'zł',
            'name' => 'Polish Zloty',
            'country' => 'Poland',
            'flag' => '🇵🇱',
            'rate' => 0.047,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'MXN' => [
            'code' => 'MXN',
            'symbol' => 'MX$',
            'name' => 'Mexican Peso',
            'country' => 'Mexico',
            'flag' => '🇲🇽',
            'rate' => 0.22,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'BRL' => [
            'code' => 'BRL',
            'symbol' => 'R$',
            'name' => 'Brazilian Real',
            'country' => 'Brazil',
            'flag' => '🇧🇷',
            'rate' => 0.065,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'TRY' => [
            'code' => 'TRY',
            'symbol' => '₺',
            'name' => 'Turkish Lira',
            'country' => 'Turkey',
            'flag' => '🇹🇷',
            'rate' => 0.40,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'ZAR' => [
            'code' => 'ZAR',
            'symbol' => 'R ',
            'name' => 'South African Rand',
            'country' => 'South Africa',
            'flag' => '🇿🇦',
            'rate' => 0.22,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'THB' => [
            'code' => 'THB',
            'symbol' => '฿',
            'name' => 'Thai Baht',
            'country' => 'Thailand',
            'flag' => '🇹🇭',
            'rate' => 0.42,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'MYR' => [
            'code' => 'MYR',
            'symbol' => 'RM ',
            'name' => 'Malaysian Ringgit',
            'country' => 'Malaysia',
            'flag' => '🇲🇾',
            'rate' => 0.056,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'IDR' => [
            'code' => 'IDR',
            'symbol' => 'Rp ',
            'name' => 'Indonesian Rupiah',
            'country' => 'Indonesia',
            'flag' => '🇮🇩',
            'rate' => 190.0,
            'decimals' => 0,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'PHP' => [
            'code' => 'PHP',
            'symbol' => '₱',
            'name' => 'Philippine Peso',
            'country' => 'Philippines',
            'flag' => '🇵🇭',
            'rate' => 0.68,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'VND' => [
            'code' => 'VND',
            'symbol' => '₫',
            'name' => 'Vietnamese Dong',
            'country' => 'Vietnam',
            'flag' => '🇻🇳',
            'rate' => 298.0,
            'decimals' => 0,
            'symbol_position' => 'after',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'TWD' => [
            'code' => 'TWD',
            'symbol' => 'NT$',
            'name' => 'New Taiwan Dollar',
            'country' => 'Taiwan',
            'flag' => '🇹🇼',
            'rate' => 0.38,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'ILS' => [
            'code' => 'ILS',
            'symbol' => '₪',
            'name' => 'Israeli Shekel',
            'country' => 'Israel',
            'flag' => '🇮🇱',
            'rate' => 0.044,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'EGP' => [
            'code' => 'EGP',
            'symbol' => 'E£ ',
            'name' => 'Egyptian Pound',
            'country' => 'Egypt',
            'flag' => '🇪🇬',
            'rate' => 0.58,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'CZK' => [
            'code' => 'CZK',
            'symbol' => 'Kč',
            'name' => 'Czech Koruna',
            'country' => 'Czech Republic',
            'flag' => '🇨🇿',
            'rate' => 0.28,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'HUF' => [
            'code' => 'HUF',
            'symbol' => 'Ft',
            'name' => 'Hungarian Forint',
            'country' => 'Hungary',
            'flag' => '🇭🇺',
            'rate' => 4.35,
            'decimals' => 0,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'RON' => [
            'code' => 'RON',
            'symbol' => 'lei',
            'name' => 'Romanian Leu',
            'country' => 'Romania',
            'flag' => '🇷🇴',
            'rate' => 0.055,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'BGN' => [
            'code' => 'BGN',
            'symbol' => 'лв',
            'name' => 'Bulgarian Lev',
            'country' => 'Bulgaria',
            'flag' => '🇧🇬',
            'rate' => 0.021,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ],
        'PKR' => [
            'code' => 'PKR',
            'symbol' => 'Rs ',
            'name' => 'Pakistani Rupee',
            'country' => 'Pakistan',
            'flag' => '🇵🇰',
            'rate' => 3.32,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'BDT' => [
            'code' => 'BDT',
            'symbol' => '৳',
            'name' => 'Bangladeshi Taka',
            'country' => 'Bangladesh',
            'flag' => '🇧🇩',
            'rate' => 1.40,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'LKR' => [
            'code' => 'LKR',
            'symbol' => 'Rs ',
            'name' => 'Sri Lankan Rupee',
            'country' => 'Sri Lanka',
            'flag' => '🇱🇰',
            'rate' => 3.60,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'NPR' => [
            'code' => 'NPR',
            'symbol' => 'Rs ',
            'name' => 'Nepalese Rupee',
            'country' => 'Nepal',
            'flag' => '🇳🇵',
            'rate' => 1.60,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'NGN' => [
            'code' => 'NGN',
            'symbol' => '₦',
            'name' => 'Nigerian Naira',
            'country' => 'Nigeria',
            'flag' => '🇳🇬',
            'rate' => 18.5,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'KES' => [
            'code' => 'KES',
            'symbol' => 'KSh ',
            'name' => 'Kenyan Shilling',
            'country' => 'Kenya',
            'flag' => '🇰🇪',
            'rate' => 1.55,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'GHS' => [
            'code' => 'GHS',
            'symbol' => 'GH₵',
            'name' => 'Ghanaian Cedi',
            'country' => 'Ghana',
            'flag' => '🇬🇭',
            'rate' => 0.18,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'MAD' => [
            'code' => 'MAD',
            'symbol' => 'MAD ',
            'name' => 'Moroccan Dirham',
            'country' => 'Morocco',
            'flag' => '🇲🇦',
            'rate' => 0.12,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'CLP' => [
            'code' => 'CLP',
            'symbol' => 'CLP$',
            'name' => 'Chilean Peso',
            'country' => 'Chile',
            'flag' => '🇨🇱',
            'rate' => 11.2,
            'decimals' => 0,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'COP' => [
            'code' => 'COP',
            'symbol' => 'COL$',
            'name' => 'Colombian Peso',
            'country' => 'Colombia',
            'flag' => '🇨🇴',
            'rate' => 48.5,
            'decimals' => 0,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'ARS' => [
            'code' => 'ARS',
            'symbol' => 'ARS$',
            'name' => 'Argentine Peso',
            'country' => 'Argentina',
            'flag' => '🇦🇷',
            'rate' => 11.4,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => '.',
            'dec_point' => ','
        ],
        'PEN' => [
            'code' => 'PEN',
            'symbol' => 'S/. ',
            'name' => 'Peruvian Sol',
            'country' => 'Peru',
            'flag' => '🇵🇪',
            'rate' => 0.045,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_sep' => ',',
            'dec_point' => '.'
        ],
        'KZT' => [
            'code' => 'KZT',
            'symbol' => '₸',
            'name' => 'Kazakhstani Tenge',
            'country' => 'Kazakhstan',
            'flag' => '🇰🇿',
            'rate' => 5.75,
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_sep' => ' ',
            'dec_point' => ','
        ]
    ];
}

// Handle currency switch requests via GET or POST (?currency=USD)
if (isset($_GET['currency']) && !empty($_GET['currency'])) {
    $requested = strtoupper(trim($_GET['currency']));
    $defs = get_currency_definitions();
    if (isset($defs[$requested])) {
        $_SESSION['currency'] = $requested;
        setcookie('shopping_currency', $requested, time() + (86400 * 30), "/"); // 30 days
    }
}

/**
 * Get active currency metadata
 * @return array
 */
function get_current_currency() {
    $defs = get_currency_definitions();
    $code = 'USD'; // Modern default
    
    if (isset($_SESSION['currency']) && isset($defs[$_SESSION['currency']])) {
        $code = $_SESSION['currency'];
    } elseif (isset($_COOKIE['shopping_currency']) && isset($defs[$_COOKIE['shopping_currency']])) {
        $code = $_COOKIE['shopping_currency'];
        $_SESSION['currency'] = $code;
    }
    
    return $defs[$code];
}

/**
 * Convert a base INR price to the active regional currency numeric value
 * @param float|int $basePrice
 * @return float
 */
function convert_price($basePrice) {
    $curr = get_current_currency();
    $val = floatval($basePrice) * $curr['rate'];
    return round($val, $curr['decimals']);
}

/**
 * Convert and format price with appropriate symbol, separators, and decimals
 * @param float|int $basePrice
 * @param bool $showCode Whether to append currency code e.g. " ($24.99 USD)"
 * @return string
 */
function format_price($basePrice, $showCode = false) {
    $curr = get_current_currency();
    $numeric = floatval($basePrice) * $curr['rate'];
    $formattedNum = number_format($numeric, $curr['decimals'], $curr['dec_point'], $curr['thousands_sep']);
    
    if ($curr['symbol_position'] === 'after') {
        $out = $formattedNum . ' ' . $curr['symbol'];
    } else {
        $out = $curr['symbol'] . $formattedNum;
    }
    
    if ($showCode) {
        $out .= ' ' . $curr['code'];
    }
    
    return $out;
}

/**
 * Get list of all supported currencies for dropdown
 * @return array
 */
function get_currency_list() {
    return get_currency_definitions();
}
?>
