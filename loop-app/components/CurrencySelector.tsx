'use client';

import React, { useState, useEffect } from 'react';
import { CURRENCIES, CurrencyInfo } from '@/lib/currency';
import { Globe, Search, ChevronDown, Check } from 'lucide-react';

interface CurrencySelectorProps {
  currentCurrency: string;
  onCurrencyChange: (code: string) => void;
}

export default function CurrencySelector({
  currentCurrency,
  onCurrencyChange,
}: CurrencySelectorProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [search, setSearch] = useState('');

  const activeCurr = CURRENCIES[currentCurrency] || CURRENCIES.USD;

  const filteredCurrencies = Object.values(CURRENCIES).filter((c) =>
    c.code.toLowerCase().includes(search.toLowerCase()) ||
    c.name.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="relative">
      <button
        type="button"
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 text-xs font-semibold text-slate-200 transition-all"
      >
        <Globe className="w-3.5 h-3.5 text-indigo-400" />
        <span>{activeCurr.code} ({activeCurr.symbol})</span>
        <ChevronDown className="w-3.5 h-3.5 text-slate-400" />
      </button>

      {isOpen && (
        <>
          <div className="fixed inset-0 z-40" onClick={() => setIsOpen(false)} />
          <div className="absolute right-0 mt-2 z-50 w-64 rounded-2xl bg-slate-950/95 border border-slate-800 backdrop-blur-2xl shadow-2xl p-2 animate-in fade-in zoom-in-95 duration-150">
            {/* Search Filter */}
            <div className="relative mb-2">
              <Search className="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-500" />
              <input
                type="text"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Search 52+ currencies..."
                className="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            {/* List */}
            <div className="max-h-56 overflow-y-auto space-y-1 text-xs">
              {filteredCurrencies.map((c) => (
                <button
                  key={c.code}
                  type="button"
                  onClick={() => {
                    onCurrencyChange(c.code);
                    setIsOpen(false);
                    setSearch('');
                  }}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-xl transition-all ${
                    currentCurrency === c.code
                      ? 'bg-indigo-600/30 text-indigo-300 font-bold border border-indigo-500/40'
                      : 'hover:bg-slate-900 text-slate-300'
                  }`}
                >
                  <span className="flex items-center gap-2">
                    <span className="font-mono font-bold text-slate-400">{c.code}</span>
                    <span className="text-slate-200 truncate max-w-[120px]">{c.name}</span>
                  </span>
                  <span className="text-indigo-400 font-bold">{c.symbol}</span>
                </button>
              ))}
            </div>
          </div>
        </>
      )}
    </div>
  );
}
