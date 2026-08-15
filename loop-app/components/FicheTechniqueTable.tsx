'use client';

import React from 'react';
import { CheckCircle2, Cpu, FileText, Layers, ShieldCheck } from 'lucide-react';

interface FicheTechniqueTableProps {
  specificationsString?: string | null;
  productName: string;
}

export default function FicheTechniqueTable({
  specificationsString,
  productName,
}: FicheTechniqueTableProps) {
  let specsObj: Record<string, string> = {};

  try {
    if (specificationsString) {
      specsObj = JSON.parse(specificationsString);
    }
  } catch (err) {
    console.warn('Failed to parse specifications JSON:', err);
  }

  const entries = Object.entries(specsObj);

  if (entries.length === 0) {
    return (
      <div className="p-8 text-center rounded-2xl bg-slate-900/50 border border-slate-800 text-slate-400">
        <FileText className="w-10 h-10 mx-auto mb-2 text-slate-600" />
        <p className="text-sm">Verified technical specifications are being finalized by manufacturer.</p>
      </div>
    );
  }

  return (
    <div className="rounded-2xl bg-slate-950/60 border border-slate-800/80 overflow-hidden backdrop-blur-xl shadow-xl">
      {/* Table Header */}
      <div className="p-5 bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h3 className="text-base font-bold text-white flex items-center gap-2">
            <Layers className="w-4 h-4 text-indigo-400" />
            Fiche Technique Complète &bull; {productName}
          </h3>
          <p className="text-xs text-slate-400">Hardware parameters, dimensions, materials, and manufacturer certifications.</p>
        </div>
        <div className="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
          <ShieldCheck className="w-3.5 h-3.5" />
          Verified Manufacturer Data
        </div>
      </div>

      {/* Specifications Rows */}
      <div className="divide-y divide-slate-800/60 text-sm">
        {entries.map(([key, value], idx) => (
          <div
            key={key}
            className={`grid grid-cols-1 md:grid-cols-3 gap-3 p-4 transition-colors hover:bg-slate-900/40 ${
              idx % 2 === 0 ? 'bg-slate-900/20' : 'bg-transparent'
            }`}
          >
            <div className="font-semibold text-slate-200 flex items-center gap-2 text-xs md:text-sm">
              <CheckCircle2 className="w-3.5 h-3.5 text-indigo-400 shrink-0" />
              {key}
            </div>
            <div className="md:col-span-2 text-xs md:text-sm text-slate-300 leading-relaxed">
              {value}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
