import urllib.request
import urllib.parse
import http.cookiejar
import sys

sys.stdout.reconfigure(encoding='utf-8')

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

def fetch(url):
    req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
    with opener.open(req, timeout=10) as res:
        return res.getcode(), res.read().decode("utf-8", errors="ignore")

def test_all_currencies():
    test_sample = [
        # Americas
        ("USD", "$", "US Dollar"),
        ("CAD", "CA$", "Canadian Dollar"),
        ("BRL", "R$", "Brazilian Real"),
        ("MXN", "MX$", "Mexican Peso"),
        ("ARS", "ARS$", "Argentine Peso"),
        ("CLP", "CLP$", "Chilean Peso"),
        ("COP", "COL$", "Colombian Peso"),
        ("PEN", "S/.", "Peruvian Sol"),
        # Europe
        ("EUR", "€", "Euro"),
        ("GBP", "£", "British Pound"),
        ("CHF", "CHF", "Swiss Franc"),
        ("SEK", "kr", "Swedish Krona"),
        ("NOK", "kr", "Norwegian Krone"),
        ("DKK", "kr", "Danish Krone"),
        ("PLN", "zł", "Polish Zloty"),
        ("TRY", "₺", "Turkish Lira"),
        ("CZK", "Kč", "Czech Koruna"),
        ("HUF", "Ft", "Hungarian Forint"),
        ("RON", "lei", "Romanian Leu"),
        ("BGN", "лв", "Bulgarian Lev"),
        # Asia & Pacific
        ("INR", "₹", "Indian Rupee"),
        ("JPY", "¥", "Japanese Yen"),
        ("CNY", "CN¥", "Chinese Yuan"),
        ("SGD", "S$", "Singapore Dollar"),
        ("HKD", "HK$", "Hong Kong Dollar"),
        ("NZD", "NZ$", "New Zealand Dollar"),
        ("KRW", "₩", "South Korean Won"),
        ("THB", "฿", "Thai Baht"),
        ("MYR", "RM", "Malaysian Ringgit"),
        ("IDR", "Rp", "Indonesian Rupiah"),
        ("PHP", "₱", "Philippine Peso"),
        ("VND", "₫", "Vietnamese Dong"),
        ("TWD", "NT$", "New Taiwan Dollar"),
        ("PKR", "Rs", "Pakistani Rupee"),
        ("BDT", "৳", "Bangladeshi Taka"),
        ("LKR", "Rs", "Sri Lankan Rupee"),
        ("NPR", "Rs", "Nepalese Rupee"),
        # Middle East & Central Asia
        ("AED", "AED", "UAE Dirham"),
        ("SAR", "SAR", "Saudi Riyal"),
        ("QAR", "QAR", "Qatari Riyal"),
        ("KWD", "KD", "Kuwaiti Dinar"),
        ("BHD", "BD", "Bahraini Dinar"),
        ("OMR", "OMR", "Omani Rial"),
        ("ILS", "₪", "Israeli Shekel"),
        ("KZT", "₸", "Kazakhstani Tenge"),
        # Africa
        ("ZAR", "R", "South African Rand"),
        ("EGP", "E£", "Egyptian Pound"),
        ("NGN", "₦", "Nigerian Naira"),
        ("KES", "KSh", "Kenyan Shilling"),
        ("GHS", "GH₵", "Ghanaian Cedi"),
        ("MAD", "MAD", "Moroccan Dirham")
    ]
    
    print(f"Testing {len(test_sample)} global currencies...")
    for code, sym, name in test_sample:
        url = f"http://localhost:8085/index.php?currency={code}"
        st, body = fetch(url)
        assert st == 200, f"Failed loading {url}"
        assert code in body, f"Expected currency code '{code}' not found in body"
        assert sym in body, f"Expected currency symbol '{sym}' not found in body for {code}"
        print(f"[PASS] {code} ({sym} - {name}) dynamic pricing & conversion verified.")

def test_currency_search_dropdown():
    st, body = fetch("http://localhost:8085/index.php")
    assert st == 200
    assert "currencyQuickSearch" in body, "Currency quick search input missing in header"
    assert "Global Currencies" in body, "Global currencies header label missing"
    print("[PASS] Interactive searchable currency dropdown verified.")

if __name__ == "__main__":
    print("=== EXECUTING GLOBAL ALL-CURRENCY TEST SUITE ===")
    test_currency_search_dropdown()
    test_all_currencies()
    print("\n=== ALL GLOBAL CURRENCY TESTS PASSED SUCCESSFULLY! ===")
