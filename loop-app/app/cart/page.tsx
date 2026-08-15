'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ShoppingBag, Trash2, ArrowRight, ShieldCheck, ArrowLeft } from 'lucide-react';

export default function CartPage() {
  const [items, setItems] = useState([
    {
      id: 1,
      name: 'Apple iPhone 15 Pro Max',
      price: 1199,
      quantity: 1,
      image: '/admin/productimages/1/img_main.jpg',
    },
  ]);

  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
      <div className="flex items-center gap-3">
        <Link href="/" className="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300">
          <ArrowLeft className="w-4 h-4" />
        </Link>
        <h1 className="text-2xl sm:text-3xl font-black text-white">Your Shopping Cart</h1>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div className="lg:col-span-8 space-y-4">
          {items.map((item) => (
            <div
              key={item.id}
              className="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl flex flex-col sm:flex-row items-center justify-between gap-6"
            >
              <div className="flex items-center gap-4">
                <div className="w-20 h-20 rounded-xl bg-slate-950 p-2 flex items-center justify-center border border-slate-800 shrink-0">
                  <img src={item.image} alt={item.name} className="max-h-full max-w-full object-contain" />
                </div>
                <div>
                  <h4 className="text-sm font-bold text-white">{item.name}</h4>
                  <div className="text-xs text-indigo-400 font-bold mt-0.5">${item.price.toLocaleString()} USD</div>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-bold text-white">
                  <span>Qty: {item.quantity}</span>
                </div>
                <div className="text-base font-black text-white">
                  ${(item.price * item.quantity).toLocaleString()}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Order Summary */}
        <div className="lg:col-span-4 rounded-2xl bg-slate-900/80 border border-slate-800 p-6 backdrop-blur-xl space-y-6 h-fit">
          <h3 className="text-base font-bold text-white">Order Summary</h3>

          <div className="space-y-3 text-xs">
            <div className="flex justify-between text-slate-400">
              <span>Subtotal</span>
              <span className="font-bold text-white">${subtotal.toLocaleString()} USD</span>
            </div>
            <div className="flex justify-between text-slate-400">
              <span>Express Global Shipping</span>
              <span className="font-bold text-emerald-400">FREE</span>
            </div>
            <div className="pt-3 border-t border-slate-800 flex justify-between text-sm font-black text-white">
              <span>Total</span>
              <span className="text-indigo-400">${subtotal.toLocaleString()} USD</span>
            </div>
          </div>

          <button
            type="button"
            className="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-black text-sm shadow-glow flex items-center justify-center gap-2 transition-all"
          >
            <span>Proceed to Checkout</span>
            <ArrowRight className="w-4 h-4" />
          </button>

          <div className="flex items-center gap-2 text-[11px] text-slate-400 justify-center">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
            <span>Encrypted Multi-Currency Payment</span>
          </div>
        </div>
      </div>
    </div>
  );
}
