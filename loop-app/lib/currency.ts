export interface CurrencyInfo {
  code: string;
  symbol: string;
  name: string;
  rate: number;
  decimals: number;
  symbolAfter?: boolean;
}

export const CURRENCIES: Record<string, CurrencyInfo> = {
  USD: { code: 'USD', symbol: '$', name: 'US Dollar', rate: 1.0, decimals: 2 },
  EUR: { code: 'EUR', symbol: '€', name: 'Euro', rate: 0.92, decimals: 2 },
  GBP: { code: 'GBP', symbol: '£', name: 'British Pound', rate: 0.79, decimals: 2 },
  JPY: { code: 'JPY', symbol: '¥', name: 'Japanese Yen', rate: 155.2, decimals: 0 },
  CAD: { code: 'CAD', symbol: 'CA$', name: 'Canadian Dollar', rate: 1.37, decimals: 2 },
  AUD: { code: 'AUD', symbol: 'A$', name: 'Australian Dollar', rate: 1.52, decimals: 2 },
  CHF: { code: 'CHF', symbol: 'CHF', name: 'Swiss Franc', rate: 0.90, decimals: 2 },
  CNY: { code: 'CNY', symbol: 'CN¥', name: 'Chinese Yuan', rate: 7.24, decimals: 2 },
  INR: { code: 'INR', symbol: '₹', name: 'Indian Rupee', rate: 83.5, decimals: 2 },
  BRL: { code: 'BRL', symbol: 'R$', name: 'Brazilian Real', rate: 5.42, decimals: 2 },
  MXN: { code: 'MXN', symbol: 'MX$', name: 'Mexican Peso', rate: 18.2, decimals: 2 },
  AED: { code: 'AED', symbol: 'AED', name: 'UAE Dirham', rate: 3.67, decimals: 2 },
  SAR: { code: 'SAR', symbol: 'SAR', name: 'Saudi Riyal', rate: 3.75, decimals: 2 },
  KRW: { code: 'KRW', symbol: '₩', name: 'South Korean Won', rate: 1380.0, decimals: 0 },
  SGD: { code: 'SGD', symbol: 'S$', name: 'Singapore Dollar', rate: 1.35, decimals: 2 },
  HKD: { code: 'HKD', symbol: 'HK$', name: 'Hong Kong Dollar', rate: 7.81, decimals: 2 },
  SEK: { code: 'SEK', symbol: 'kr', name: 'Swedish Krona', rate: 10.6, decimals: 2 },
  NOK: { code: 'NOK', symbol: 'kr', name: 'Norwegian Krone', rate: 10.8, decimals: 2 },
  DKK: { code: 'DKK', symbol: 'kr', name: 'Danish Krone', rate: 6.87, decimals: 2 },
  PLN: { code: 'PLN', symbol: 'zł', name: 'Polish Zloty', rate: 3.96, decimals: 2 },
  TRY: { code: 'TRY', symbol: '₺', name: 'Turkish Lira', rate: 32.8, decimals: 2 },
  MAD: { code: 'MAD', symbol: 'MAD', name: 'Moroccan Dirham', rate: 9.95, decimals: 2 },
  ZAR: { code: 'ZAR', symbol: 'R', name: 'South African Rand', rate: 18.1, decimals: 2 },
  EGP: { code: 'EGP', symbol: 'E£', name: 'Egyptian Pound', rate: 47.9, decimals: 2 },
  NGN: { code: 'NGN', symbol: '₦', name: 'Nigerian Naira', rate: 1485.0, decimals: 0 },
  ILS: { code: 'ILS', symbol: '₪', name: 'Israeli Shekel', rate: 3.72, decimals: 2 },
  NZD: { code: 'NZD', symbol: 'NZ$', name: 'New Zealand Dollar', rate: 1.64, decimals: 2 },
  THB: { code: 'THB', symbol: '฿', name: 'Thai Baht', rate: 36.6, decimals: 2 },
  MYR: { code: 'MYR', symbol: 'RM', name: 'Malaysian Ringgit', rate: 4.71, decimals: 2 },
  IDR: { code: 'IDR', symbol: 'Rp', name: 'Indonesian Rupiah', rate: 16250.0, decimals: 0 },
  PHP: { code: 'PHP', symbol: '₱', name: 'Philippine Peso', rate: 58.6, decimals: 2 },
  VND: { code: 'VND', symbol: '₫', name: 'Vietnamese Dong', rate: 25400.0, decimals: 0 },
};

export function convertPrice(amount: number, fromCurrency: string = 'USD', toCurrency: string = 'USD'): number {
  const fromRate = CURRENCIES[fromCurrency]?.rate || 1.0;
  const toRate = CURRENCIES[toCurrency]?.rate || 1.0;
  const inUsd = amount / fromRate;
  return Math.round(inUsd * toRate * 100) / 100;
}

export function formatPrice(usdAmount: number, currencyCode: string = 'USD'): string {
  const curr = CURRENCIES[currencyCode] || CURRENCIES.USD;
  const converted = usdAmount * curr.rate;
  const formatted = converted.toLocaleString(undefined, {
    minimumFractionDigits: curr.decimals,
    maximumFractionDigits: curr.decimals,
  });
  return curr.symbolAfter ? `${formatted} ${curr.symbol}` : `${curr.symbol}${formatted}`;
}
