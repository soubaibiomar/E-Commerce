'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ShoppingBag, Layers, Box, Cpu, Activity, Menu, X } from 'lucide-react';
import CurrencySelector from './CurrencySelector';

export default function Navbar() {
  const [currency, setCurrency] = useState('USD');
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const handleCurrencyChange = (code: string) => {
    setCurrency(code);
    if (typeof window !== 'undefined') {
      localStorage.setItem('preferredCurrency', code);
      window.dispatchEvent(new Event('currencyChange'));
    }
  };

  return (
    <header className="sticky top-0 z-40 w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-xl">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        {/* Brand Logo */}
        <Link href="/" className="flex items-center gap-3 group">
          <div className="w-8 h-8 rounded-lg bg-slate-900 border border-slate-700/80 flex items-center justify-center text-blue-400 group-hover:border-blue-500/50 transition-colors">
            <Box className="w-4 h-4" />
          </div>
          <div className="flex flex-col">
            <span className="text-sm sm:text-base font-bold tracking-tight text-white flex items-center gap-1">
              Zey<span className="text-blue-400 font-extrabold">Tech</span>
            </span>
            <span className="text-[9px] font-mono font-medium text-slate-400 -mt-1 tracking-widest uppercase">Commerce OS</span>
          </div>
        </Link>

        {/* Desktop Navigation Links */}
        <nav className="hidden md:flex items-center gap-6 text-xs font-medium text-slate-300">
          <Link href="/#catalog" className="hover:text-white transition-colors flex items-center gap-1.5">
            <Layers className="w-3.5 h-3.5 text-slate-400" /> Catalog
          </Link>
          <Link href="/#3d-studio" className="hover:text-white transition-colors flex items-center gap-1.5">
            <Box className="w-3.5 h-3.5 text-slate-400" /> 3D Studio
          </Link>
          <Link href="/admin/ai-generator" className="hover:text-blue-300 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200">
            Admin AI
          </Link>
          <Link href="/admin/ai-observability" className="hover:text-blue-300 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200">
            <Activity className="w-3.5 h-3.5 text-blue-400" /> Ops Center
          </Link>
        </nav>

        {/* Right Tools: Currency, Cart & Mobile Menu Toggle */}
        <div className="flex items-center gap-2 sm:gap-3">
          <CurrencySelector
            currentCurrency={currency}
            onCurrencyChange={handleCurrencyChange}
          />

          <Link
            href="/cart"
            className="flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 sm:px-3.5 sm:py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-tactile transition-all min-h-[38px]"
          >
            <ShoppingBag className="w-4 h-4" />
            <span className="hidden xs:inline">Cart</span>
          </Link>

          {/* Mobile Menu Hamburger */}
          <button
            type="button"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white min-h-[38px] min-w-[38px] flex items-center justify-center transition-colors"
            aria-label="Toggle Navigation Menu"
          >
            {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>
        </div>
      </div>

      {/* Mobile Drawer Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden border-t border-slate-800/80 bg-slate-950/95 backdrop-blur-2xl px-4 py-4 space-y-2 animate-in fade-in slide-in-from-top-2 duration-200">
          <Link
            href="/#catalog"
            onClick={() => setMobileMenuOpen(false)}
            className="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm font-medium text-slate-200 hover:text-white min-h-[44px]"
          >
            <Layers className="w-4 h-4 text-slate-400" /> Catalog Flagships
          </Link>
          <Link
            href="/#3d-studio"
            onClick={() => setMobileMenuOpen(false)}
            className="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm font-medium text-slate-200 hover:text-white min-h-[44px]"
          >
            <Box className="w-4 h-4 text-slate-400" /> 3D Interactive Studio
          </Link>
          <Link
            href="/admin/ai-generator"
            onClick={() => setMobileMenuOpen(false)}
            className="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm font-medium text-slate-200 min-h-[44px]"
          >
            Admin AI Spec Generator
          </Link>
          <Link
            href="/admin/ai-observability"
            onClick={() => setMobileMenuOpen(false)}
            className="flex items-center gap-3 px-3.5 py-3 rounded-xl bg-slate-900/60 border border-slate-800 text-sm font-medium text-blue-300 min-h-[44px]"
          >
            <Activity className="w-4 h-4 text-blue-400" /> Ops Center &amp; Telemetry
          </Link>
        </div>
      )}
    </header>
  );
}