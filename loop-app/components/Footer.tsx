import React from 'react';
import { Box, Sparkles, Shield, Cpu, Globe } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="border-t border-slate-800 bg-slate-950/90 text-slate-400 text-xs py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div className="space-y-3 md:col-span-2">
          <div className="flex items-center gap-2">
            <div className="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white">
              <Box className="w-4 h-4" />
            </div>
            <span className="text-base font-extrabold text-white">Loop Engineering</span>
          </div>
          <p className="max-w-md text-slate-400 text-xs leading-relaxed">
            Next-Generation eCommerce storefront powered by Next.js 14, Three.js interactive 3D WebGL Studio, Prisma ORM, and n8n AI Agent Workflow Automation.
          </p>
        </div>

        <div>
          <h4 className="font-bold text-slate-200 uppercase tracking-wider text-[11px] mb-3">Core Stack</h4>
          <ul className="space-y-2">
            <li className="flex items-center gap-1.5"><Cpu className="w-3.5 h-3.5 text-indigo-400" /> Next.js 14 App Router</li>
            <li className="flex items-center gap-1.5"><Box className="w-3.5 h-3.5 text-cyan-400" /> Three.js 3D WebGL Studio</li>
            <li className="flex items-center gap-1.5"><Sparkles className="w-3.5 h-3.5 text-purple-400" /> n8n AI Agent Webhooks</li>
            <li className="flex items-center gap-1.5"><Globe className="w-3.5 h-3.5 text-emerald-400" /> 52+ Dynamic Currencies</li>
          </ul>
        </div>

        <div>
          <h4 className="font-bold text-slate-200 uppercase tracking-wider text-[11px] mb-3">Guarantees</h4>
          <ul className="space-y-2">
            <li className="flex items-center gap-1.5"><Shield className="w-3.5 h-3.5 text-emerald-400" /> 100% Verified Technical Data</li>
            <li>Real-time Global Express Logistics</li>
            <li>24/7 AI Shopping Assistance</li>
          </ul>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 mt-8 pt-6 border-t border-slate-900 text-center text-slate-500 text-[11px]">
        &copy; {new Date().getFullYear()} Loop Engineering. All rights reserved. Co-existing seamlessly with PHP/MySQL storefront.
      </div>
    </footer>
  );
}
