'use client';

import React, { useState } from 'react';
import { AlertTriangle, CheckCircle2, MessageSquare, ShieldAlert, Phone, Mail, ArrowRight, UserCheck } from 'lucide-react';

interface EscalationCase {
  id: number;
  channel: 'WHATSAPP' | 'TELEGRAM' | 'WEB';
  customerName: string;
  contact: string;
  query: string;
  reason: string;
  riskLevel: 'HIGH' | 'MEDIUM' | 'LOW';
  status: 'PENDING' | 'RESOLVED' | 'APPROVED';
  amountMAD?: number;
  orderNumber: string;
  timestamp: string;
}

export default function AdminEscalationsPage() {
  const [cases, setCases] = useState<EscalationCase[]>([
    {
      id: 101,
      channel: 'WHATSAPP',
      customerName: 'Karim Alami',
      contact: '+212612345678',
      query: 'سلام عليكم، شريت MacBook Pro ولكن تعطل عليا الإرسال لمدينة طنجة وما وصلنيش',
      reason: 'Delivery Delay Inquiry & High Value Order',
      riskLevel: 'HIGH',
      status: 'PENDING',
      amountMAD: 24999,
      orderNumber: 'ORD-2026-8812',
      timestamp: '10 mins ago',
    },
    {
      id: 102,
      channel: 'TELEGRAM',
      customerName: 'Yassine B.',
      contact: '@yassine_tech',
      query: 'Demande de remboursement de 5,400 MAD pour commande endommagée',
      reason: 'Refund Request > 5,000 MAD threshold (HITL Required)',
      riskLevel: 'HIGH',
      status: 'PENDING',
      amountMAD: 5400,
      orderNumber: 'ORD-2026-8805',
      timestamp: '25 mins ago',
    },
    {
      id: 103,
      channel: 'WEB',
      customerName: 'Sara Mansouri',
      contact: 'sara@gmail.com',
      query: 'واش كاين كود برومو للطلبيات الكبيرة؟ بغيت نشري 3 هواتف Samsung',
      reason: 'VIP Bulk Discount Assistance (ZEYTECH10VIP applied)',
      riskLevel: 'LOW',
      status: 'PENDING',
      amountMAD: 36000,
      orderNumber: 'ORD-2026-8799',
      timestamp: '1 hour ago',
    },
  ]);

  const [filter, setFilter] = useState<'ALL' | 'HIGH' | 'PENDING' | 'RESOLVED'>('ALL');

  const handleAction = (id: number, newStatus: 'RESOLVED' | 'APPROVED') => {
    setCases((prev) =>
      prev.map((c) => (c.id === id ? { ...c, status: newStatus } : c))
    );
  };

  const filteredCases = cases.filter((c) => {
    if (filter === 'HIGH') return c.riskLevel === 'HIGH';
    if (filter === 'PENDING') return c.status === 'PENDING';
    if (filter === 'RESOLVED') return c.status === 'RESOLVED' || c.status === 'APPROVED';
    return true;
  });

  return (
    <div className="min-h-screen bg-[#080c16] text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
      <div className="max-w-7xl mx-auto space-y-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold mb-2">
              <ShieldAlert className="w-3.5 h-3.5" /> Human-in-the-Loop Queue
            </div>
            <h1 className="text-3xl font-black text-white">AI Support Escalation Queue</h1>
            <p className="text-sm text-slate-400 mt-1">
              Live customer conversations escalated by the ZeyTech AI Supervisor requiring human review or financial sign-off.
            </p>
          </div>

          {/* Filter Pills */}
          <div className="flex items-center gap-2 bg-slate-900/80 p-1.5 rounded-xl border border-slate-800">
            {(['ALL', 'HIGH', 'PENDING', 'RESOLVED'] as const).map((tab) => (
              <button
                key={tab}
                onClick={() => setFilter(tab)}
                className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${
                  filter === tab
                    ? 'bg-indigo-600 text-white shadow-md'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800'
                }`}
              >
                {tab}
              </button>
            ))}
          </div>
        </div>

        {/* Escalation Cards Grid */}
        <div className="grid grid-cols-1 gap-6">
          {filteredCases.map((item) => (
            <div
              key={item.id}
              className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-indigo-500/40 backdrop-blur-xl transition-all shadow-lg space-y-4"
            >
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800/60 pb-4">
                <div className="flex items-center gap-3">
                  <span className="text-sm font-bold text-white">Case #{item.id}</span>
                  <span className="text-xs text-slate-400">Order: {item.orderNumber}</span>
                  <span
                    className={`px-2.5 py-0.5 rounded-full text-[11px] font-extrabold ${
                      item.channel === 'WHATSAPP'
                        ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
                        : item.channel === 'TELEGRAM'
                        ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30'
                        : 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30'
                    }`}
                  >
                    {item.channel}
                  </span>
                </div>

                <div className="flex items-center gap-2">
                  <span
                    className={`px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider ${
                      item.riskLevel === 'HIGH'
                        ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
                        : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                    }`}
                  >
                    {item.riskLevel} RISK
                  </span>
                  <span className="text-xs text-slate-500">{item.timestamp}</span>
                </div>
              </div>

              {/* Body */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="md:col-span-2 space-y-3">
                  <div className="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/60 text-slate-200 text-sm font-medium italic">
                    "{item.query}"
                  </div>
                  <div className="text-xs text-slate-400">
                    <strong className="text-slate-300">Escalation Trigger:</strong> {item.reason}
                  </div>
                </div>

                <div className="space-y-2 text-xs text-slate-300 bg-slate-950/40 p-3.5 rounded-xl border border-slate-800/40">
                  <div className="font-bold text-white">{item.customerName}</div>
                  <div className="flex items-center gap-1.5 text-slate-400">
                    <Phone className="w-3.5 h-3.5 text-indigo-400" /> {item.contact}
                  </div>
                  {item.amountMAD && (
                    <div className="text-indigo-400 font-extrabold pt-1">
                      Value: {item.amountMAD.toLocaleString()} MAD
                    </div>
                  )}
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex items-center justify-end gap-3 pt-2">
                {item.status === 'PENDING' ? (
                  <>
                    {item.riskLevel === 'HIGH' && (
                      <button
                        onClick={() => handleAction(item.id, 'APPROVED')}
                        className="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 transition-all"
                      >
                        <UserCheck className="w-3.5 h-3.5" /> Approve Refund
                      </button>
                    )}
                    <button
                      onClick={() => handleAction(item.id, 'RESOLVED')}
                      className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 transition-all"
                    >
                      <CheckCircle2 className="w-3.5 h-3.5" /> Mark Resolved
                    </button>
                  </>
                ) : (
                  <span className="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                    <CheckCircle2 className="w-4 h-4" /> Case {item.status}
                  </span>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
