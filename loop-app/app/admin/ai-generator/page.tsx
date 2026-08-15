'use client';

import React, { useState } from 'react';
import { Sparkles, Bot, Check, Copy, RefreshCw, Cpu, Layers, Box, ArrowRight } from 'lucide-react';

export default function AdminAiGeneratorPage() {
  const [productName, setProductName] = useState('Apple Vision Pro 2');
  const [brand, setBrand] = useState('Apple');
  const [category, setCategory] = useState('Spatial Computing / AR VR');
  const [keyPoints, setKeyPoints] = useState('Dual 4K micro-OLED, M3 + R2 chips, spatial audio, lightweight titanium');
  const [isLoading, setIsLoading] = useState(false);
  const [result, setResult] = useState<any>(null);
  const [copied, setCopied] = useState(false);

  const handleGenerate = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!productName || isLoading) return;

    setIsLoading(true);
    setResult(null);

    try {
      const res = await fetch('/api/n8n/generate-specs', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ productName, brand, category, keyPoints }),
      });

      const data = await res.json();
      setResult(data);
    } catch (err) {
      alert('Failed to connect to AI generator.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleCopy = () => {
    if (!result) return;
    navigator.clipboard.writeText(JSON.stringify(result.specifications, null, 2));
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
      {/* Page Title */}
      <div className="space-y-2 text-center max-w-2xl mx-auto">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-300 text-xs font-bold">
          <Sparkles className="w-3.5 h-3.5 text-purple-400" />
          Admin AI Suite &bull; n8n Webhook Workflow
        </div>
        <h1 className="text-3xl sm:text-4xl font-black text-white">
          Fiche Technique &amp; Copy Generator
        </h1>
        <p className="text-xs sm:text-sm text-slate-400">
          Automate the creation of verified technical specification sheets, marketing descriptions, and 3D model configs using n8n AI agent automation.
        </p>
      </div>

      {/* Generator Form & Result Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Form Column */}
        <div className="lg:col-span-5 rounded-2xl bg-slate-950/80 border border-slate-800 p-6 backdrop-blur-xl space-y-5">
          <h3 className="text-base font-bold text-white flex items-center gap-2">
            <Cpu className="w-4 h-4 text-indigo-400" />
            Product Input Parameters
          </h3>

          <form onSubmit={handleGenerate} className="space-y-4 text-xs">
            <div>
              <label className="block text-slate-300 font-bold mb-1.5">Product Title / Model</label>
              <input
                type="text"
                value={productName}
                onChange={(e) => setProductName(e.target.value)}
                placeholder="e.g. Sony WH-1000XM6"
                required
                className="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-slate-300 font-bold mb-1.5">Brand / Maker</label>
                <input
                  type="text"
                  value={brand}
                  onChange={(e) => setBrand(e.target.value)}
                  placeholder="e.g. Sony"
                  className="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                />
              </div>
              <div>
                <label className="block text-slate-300 font-bold mb-1.5">Category</label>
                <input
                  type="text"
                  value={category}
                  onChange={(e) => setCategory(e.target.value)}
                  placeholder="e.g. Audio"
                  className="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                />
              </div>
            </div>

            <div>
              <label className="block text-slate-300 font-bold mb-1.5">Key Highlights / Features</label>
              <textarea
                value={keyPoints}
                onChange={(e) => setKeyPoints(e.target.value)}
                rows={3}
                placeholder="Key chips, battery, materials, sensor info..."
                className="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-bold text-xs shadow-glow flex items-center justify-center gap-2 transition-all disabled:opacity-50"
            >
              {isLoading ? (
                <>
                  <RefreshCw className="w-4 h-4 animate-spin text-cyan-200" />
                  Generating via n8n AI Agent...
                </>
              ) : (
                <>
                  <Sparkles className="w-4 h-4 text-cyan-200" />
                  Auto-Generate Fiche Technique
                </>
              )}
            </button>
          </form>
        </div>

        {/* Results Column */}
        <div className="lg:col-span-7 rounded-2xl bg-slate-950/80 border border-slate-800 p-6 backdrop-blur-xl space-y-6">
          <div className="flex items-center justify-between">
            <h3 className="text-base font-bold text-white flex items-center gap-2">
              <Bot className="w-4 h-4 text-cyan-400" />
              Generated AI Specifications Output
            </h3>

            {result && (
              <button
                type="button"
                onClick={handleCopy}
                className="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-700 text-xs font-semibold text-slate-200 flex items-center gap-1.5 transition-colors"
              >
                {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                {copied ? 'Copied JSON!' : 'Copy JSON'}
              </button>
            )}
          </div>

          {result ? (
            <div className="space-y-5 animate-in fade-in duration-300">
              {/* Marketing Copy */}
              <div className="p-4 rounded-xl bg-slate-900/80 border border-slate-800 text-xs text-slate-300 leading-relaxed">
                <div className="text-[11px] font-bold text-indigo-400 uppercase tracking-wider mb-1">
                  Marketing Overview
                </div>
                {result.marketingDescription}
              </div>

              {/* Fiche Technique Specs Preview */}
              <div className="rounded-xl border border-slate-800 overflow-hidden text-xs">
                <div className="p-3 bg-slate-900 font-bold text-slate-200 border-b border-slate-800 flex items-center justify-between">
                  <span>Structured Fiche Technique</span>
                  <span className="text-[10px] text-emerald-400 font-mono">JSON Validated</span>
                </div>
                <div className="divide-y divide-slate-800/80">
                  {Object.entries(result.specifications || {}).map(([k, v]) => (
                    <div key={k} className="grid grid-cols-3 p-3 bg-slate-950/40">
                      <div className="font-semibold text-slate-300">{k}</div>
                      <div className="col-span-2 text-slate-400">{String(v)}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ) : (
            <div className="h-64 flex flex-col items-center justify-center text-center text-slate-500 space-y-2">
              <Bot className="w-12 h-12 text-slate-700" />
              <p className="text-xs">Fill in the product details on the left and click generate.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
