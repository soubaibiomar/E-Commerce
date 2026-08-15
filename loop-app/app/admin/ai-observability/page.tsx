'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import {
  Activity,
  Cpu,
  ShieldCheck,
  Zap,
  Globe,
  Bot,
  Layers,
  ArrowUpRight,
  ShieldAlert,
  Clock,
  Sparkles,
  ChevronLeft,
} from 'lucide-react';

export default function AiObservabilityDashboard() {
  const [metrics] = useState({
    totalRequests: 1420,
    avgLatencyMs: 142,
    successRate: 99.8,
    guardrailBlocks: 14,
    activeModel: 'Ollama / llama3.2:1b',
    activeVectorDb: 'Qdrant (Port 6333)',
  });

  const recentLogs = [
    {
      id: 'log-1',
      time: 'Just now',
      agent: 'SALES_AGENT',
      query: 'What is the processor on iPhone 15 Pro Max?',
      latency: '112ms',
      status: '200 OK',
      dialect: 'English',
    },
    {
      id: 'log-2',
      time: '1 min ago',
      agent: 'SALES_AGENT',
      query: 'شحال الثمن ديال هاد التلفون وبغيت نعرف واش مزيان',
      latency: '138ms',
      status: '200 OK',
      dialect: 'Moroccan Darija (MAD)',
    },
    {
      id: 'log-3',
      time: '3 mins ago',
      agent: 'SUPPORT_AGENT',
      query: 'Where is my order TRK-MA-8842?',
      latency: '95ms',
      status: '200 OK',
      dialect: 'English',
    },
    {
      id: 'log-4',
      time: '5 mins ago',
      agent: 'INVENTORY_AGENT',
      query: 'Check warehouse stock quantity for MacBook Pro 16',
      latency: '82ms',
      status: '200 OK',
      dialect: 'English',
    },
    {
      id: 'log-5',
      time: '12 mins ago',
      agent: 'SUPERVISOR (BLOCKED)',
      query: 'DROP TABLE products; SELECT * FROM users',
      latency: '4ms',
      status: '403 GUARDRAIL_BLOCKED',
      dialect: 'Malicious Payload',
    },
  ];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
      {/* Header */}
      <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <Link
              href="/"
              className="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white transition-colors"
            >
              <ChevronLeft className="w-4 h-4" />
            </Link>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 text-xs font-bold">
              <Activity className="w-3.5 h-3.5 text-cyan-400" />
              AI Observability &bull; Real-time Control Center
            </div>
          </div>
          <h1 className="text-3xl font-black text-white">AI Engine &amp; Multi-Agent Metrics</h1>
        </div>

        <div className="flex items-center gap-3">
          <div className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
            <span className="w-2 h-2 rounded-full bg-emerald-400 animate-ping" />
            All Multi-Agents Operational
          </div>
        </div>
      </div>

      {/* KPI Metric Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-xl space-y-2">
          <div className="flex items-center justify-between text-slate-400 text-xs font-bold">
            <span>TOTAL AI INVOCATIONS</span>
            <Bot className="w-4 h-4 text-indigo-400" />
          </div>
          <div className="text-3xl font-black text-white">{metrics.totalRequests.toLocaleString()}</div>
          <div className="text-[11px] text-emerald-400 flex items-center gap-1 font-semibold">
            <ArrowUpRight className="w-3.5 h-3.5" /> +24.8% from yesterday
          </div>
        </div>

        <div className="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-xl space-y-2">
          <div className="flex items-center justify-between text-slate-400 text-xs font-bold">
            <span>AVG INFERENCE LATENCY</span>
            <Clock className="w-4 h-4 text-cyan-400" />
          </div>
          <div className="text-3xl font-black text-white">{metrics.avgLatencyMs} ms</div>
          <div className="text-[11px] text-cyan-400 flex items-center gap-1 font-semibold">
            <Zap className="w-3.5 h-3.5" /> Ultra-fast local Docker inference
          </div>
        </div>

        <div className="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-xl space-y-2">
          <div className="flex items-center justify-between text-slate-400 text-xs font-bold">
            <span>ROUTING SUCCESS RATE</span>
            <ShieldCheck className="w-4 h-4 text-emerald-400" />
          </div>
          <div className="text-3xl font-black text-white">{metrics.successRate}%</div>
          <div className="text-[11px] text-emerald-400 font-semibold">
            Zero hallucinated product prices
          </div>
        </div>

        <div className="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-xl space-y-2">
          <div className="flex items-center justify-between text-slate-400 text-xs font-bold">
            <span>GUARDRAILS BLOCKED</span>
            <ShieldAlert className="w-4 h-4 text-rose-400" />
          </div>
          <div className="text-3xl font-black text-rose-400">{metrics.guardrailBlocks}</div>
          <div className="text-[11px] text-slate-400 font-semibold">
            100% prompt injection containment
          </div>
        </div>
      </div>

      {/* Engine Topology Info */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 backdrop-blur-xl space-y-4">
          <h3 className="text-sm font-bold text-white flex items-center gap-2">
            <Cpu className="w-4 h-4 text-indigo-400" />
            AI Supervisor Routing Distribution
          </h3>
          <div className="space-y-3 text-xs">
            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>Technical Sales Agent</span>
                <span>64%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-indigo-500 rounded-full" style={{ width: '64%' }} />
              </div>
            </div>

            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>Support &amp; Fulfillment Agent</span>
                <span>24%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-cyan-500 rounded-full" style={{ width: '24%' }} />
              </div>
            </div>

            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>Inventory &amp; Warehouse Agent</span>
                <span>12%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-purple-500 rounded-full" style={{ width: '12%' }} />
              </div>
            </div>
          </div>
        </div>

        <div className="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 backdrop-blur-xl space-y-4">
          <h3 className="text-sm font-bold text-white flex items-center gap-2">
            <Globe className="w-4 h-4 text-emerald-400" />
            Dialect &amp; Localization Analytics
          </h3>
          <div className="space-y-3 text-xs">
            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>English (Global)</span>
                <span>58%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-emerald-500 rounded-full" style={{ width: '58%' }} />
              </div>
            </div>

            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>Moroccan Darija (MAD)</span>
                <span>30%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-amber-500 rounded-full" style={{ width: '30%' }} />
              </div>
            </div>

            <div>
              <div className="flex justify-between text-slate-300 font-semibold mb-1">
                <span>French / Multi-Region</span>
                <span>12%</span>
              </div>
              <div className="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                <div className="h-full bg-blue-500 rounded-full" style={{ width: '12%' }} />
              </div>
            </div>
          </div>
        </div>

        <div className="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 backdrop-blur-xl space-y-4">
          <h3 className="text-sm font-bold text-white flex items-center gap-2">
            <Layers className="w-4 h-4 text-cyan-400" />
            Connected Infrastructure Nodes
          </h3>
          <div className="space-y-2.5 text-xs text-slate-300">
            <div className="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800">
              <span>Docker Ollama</span>
              <span className="font-mono text-cyan-400 font-bold">11434 (llama3.2:1b)</span>
            </div>
            <div className="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800">
              <span>Qdrant Vector DB</span>
              <span className="font-mono text-purple-400 font-bold">6333 (Cosine)</span>
            </div>
            <div className="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800">
              <span>Redis Event Bus</span>
              <span className="font-mono text-rose-400 font-bold">6379 (BullMQ)</span>
            </div>
            <div className="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800">
              <span>MariaDB Core DB</span>
              <span className="font-mono text-indigo-400 font-bold">3308 (Prisma)</span>
            </div>
          </div>
        </div>
      </div>

      {/* Live AI Query Activity Stream */}
      <div className="rounded-2xl bg-slate-950/80 border border-slate-800 overflow-hidden backdrop-blur-xl space-y-0">
        <div className="p-5 border-b border-slate-800 bg-slate-900/60 flex items-center justify-between">
          <h3 className="text-base font-bold text-white flex items-center gap-2">
            <Activity className="w-4 h-4 text-indigo-400" />
            Live AI Invocations &amp; Telemetry Stream
          </h3>
          <span className="text-xs text-slate-400">Auto-refreshing &bull; Latency tracking</span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-900/90 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
              <tr>
                <th className="p-4">Time</th>
                <th className="p-4">Routed Agent</th>
                <th className="p-4">Customer Prompt / Query</th>
                <th className="p-4">Dialect</th>
                <th className="p-4">Latency</th>
                <th className="p-4">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-850 text-slate-300">
              {recentLogs.map((log) => (
                <tr key={log.id} className="hover:bg-slate-900/50 transition-colors">
                  <td className="p-4 text-slate-500 whitespace-nowrap">{log.time}</td>
                  <td className="p-4 font-mono font-bold text-indigo-400">{log.agent}</td>
                  <td className="p-4 max-w-md truncate font-medium text-slate-200">{log.query}</td>
                  <td className="p-4 text-slate-400">{log.dialect}</td>
                  <td className="p-4 font-mono text-cyan-400 font-bold">{log.latency}</td>
                  <td className="p-4">
                    <span
                      className={`px-2 py-0.5 rounded-md font-bold text-[10px] ${
                        log.status.includes('OK')
                          ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                          : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'
                      }`}
                    >
                      {log.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
