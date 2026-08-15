'use client';

import React, { useState, useRef, useEffect } from 'react';
import { Bot, Sparkles, Send, X, RefreshCw, Cpu } from 'lucide-react';

interface AiShoppingAdvisorProps {
  currentProductId?: number;
  currentProductName?: string;
}

interface Message {
  id: string;
  sender: 'user' | 'ai';
  text: string;
  model?: string;
  timestamp: string;
}

export default function AiShoppingAdvisor({
  currentProductId,
  currentProductName,
}: AiShoppingAdvisorProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [input, setInput] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [activeModel, setActiveModel] = useState('llama3');
  const [messages, setMessages] = useState<Message[]>([
    {
      id: '1',
      sender: 'ai',
      text: currentProductName
        ? `👋 Hello! I am your Local Ollama AI Technical Advisor. Ask me anything about **${currentProductName}**, its Fiche Technique, 3D model, or pricing!`
        : `👋 Hello! I am your Local Ollama AI Technical Advisor. I can answer questions about hardware specifications, compare flagship products, or calculate multi-currency pricing with 100% privacy.`,
      model: 'ollama / llama3',
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    },
  ]);

  const messagesEndRef = useRef<HTMLDivElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    if (isOpen) {
      scrollToBottom();
    }
  }, [messages, isOpen]);

  // Stream text helper for smooth typewriter effect
  const typeOutText = (fullText: string, msgId: string, modelName: string) => {
    const words = fullText.split(' ');
    let currentIdx = 0;

    const interval = setInterval(() => {
      currentIdx += 2;
      const partial = words.slice(0, currentIdx).join(' ');

      setMessages((prev) =>
        prev.map((m) =>
          m.id === msgId ? { ...m, text: partial, model: modelName } : m
        )
      );

      if (currentIdx >= words.length) {
        clearInterval(interval);
        setMessages((prev) =>
          prev.map((m) =>
            m.id === msgId ? { ...m, text: fullText, model: modelName } : m
          )
        );
      }
    }, 25);
  };

  const handleSend = async (customPrompt?: string) => {
    const textToSend = customPrompt || input.trim();
    if (!textToSend || isLoading) return;

    const userMsg: Message = {
      id: Date.now().toString(),
      sender: 'user',
      text: textToSend,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };

    setMessages((prev) => [...prev, userMsg]);
    if (!customPrompt) setInput('');
    setIsLoading(true);

    const placeholderId = (Date.now() + 1).toString();
    const aiPlaceholder: Message = {
      id: placeholderId,
      sender: 'ai',
      text: '...',
      model: activeModel,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };

    setMessages((prev) => [...prev, aiPlaceholder]);

    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message: textToSend,
          productId: currentProductId,
        }),
      });

      const data = await res.json();
      const aiReply = data.reply || 'Thank you for your question.';
      const modelSource = data.source || 'ollama-local';
      if (data.model) setActiveModel(data.model);

      typeOutText(aiReply, placeholderId, modelSource);
    } catch (err) {
      setMessages((prev) =>
        prev.map((m) =>
          m.id === placeholderId
            ? {
                ...m,
                text: 'Connection to local AI engine was interrupted. Please ensure Ollama is running (`ollama serve`).',
              }
            : m
        )
      );
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      {/* Floating Launcher Button */}
      {!isOpen && (
        <button
          type="button"
          onClick={() => setIsOpen(true)}
          className="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex items-center gap-2.5 sm:gap-3 px-4 py-3 sm:px-5 sm:py-3.5 min-h-[48px] rounded-full bg-gradient-to-r from-indigo-600 via-purple-600 to-cyan-500 text-white font-bold shadow-2xl shadow-indigo-500/50 hover:scale-105 transition-all duration-300 group border border-white/20"
        >
          <div className="relative">
            <Bot className="w-6 h-6 animate-pulse" />
            <span className="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border border-slate-900" />
          </div>
          <span className="text-sm font-semibold tracking-wide">Ollama Local AI</span>
          <Sparkles className="w-4 h-4 text-cyan-200 group-hover:rotate-12 transition-transform" />
        </button>
      )}

      {/* Floating Chat Window */}
      {isOpen && (
        <div className="fixed bottom-2 right-2 sm:bottom-6 sm:right-6 z-50 w-[calc(100vw-16px)] sm:w-[430px] h-[calc(100dvh-20px)] sm:h-[600px] max-h-[640px] rounded-2xl bg-slate-950/90 backdrop-blur-2xl border border-slate-800 shadow-2xl flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-300">
          {/* Header */}
          <div className="p-4 bg-gradient-to-r from-indigo-950 via-slate-900 to-slate-900 border-b border-slate-800 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-400 p-0.5 flex items-center justify-center shadow-glow">
                <div className="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                  <Bot className="w-5 h-5 text-cyan-400" />
                </div>
              </div>
              <div>
                <h4 className="text-sm font-bold text-white flex items-center gap-1.5">
                  Loop AI Advisor
                  <span className="px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center gap-1">
                    <Cpu className="w-2.5 h-2.5" /> Ollama Local
                  </span>
                </h4>
                <p className="text-[11px] text-emerald-400 flex items-center gap-1">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping" /> Privacy-First &bull; Fiche Technique Grounded
                </p>
              </div>
            </div>
            <button
              type="button"
              onClick={() => setIsOpen(false)}
              className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Quick Prompt Chips */}
          <div className="px-4 py-2.5 bg-slate-900/60 border-b border-slate-800/80 flex items-center gap-1.5 overflow-x-auto no-scrollbar text-xs">
            <button
              type="button"
              onClick={() => handleSend('What are the key technical specifications (Fiche Technique)?')}
              className="px-2.5 py-1 rounded-full bg-slate-800 hover:bg-indigo-600/30 border border-slate-700 text-slate-300 text-[11px] whitespace-nowrap transition-colors"
            >
              📋 Specs Summary
            </button>
            <button
              type="button"
              onClick={() => handleSend('How does pricing and delivery work for this product?')}
              className="px-2.5 py-1 rounded-full bg-slate-800 hover:bg-indigo-600/30 border border-slate-700 text-slate-300 text-[11px] whitespace-nowrap transition-colors"
            >
              💰 Price & Delivery
            </button>
            <button
              type="button"
              onClick={() => handleSend('How can I view and customize the 3D model?')}
              className="px-2.5 py-1 rounded-full bg-slate-800 hover:bg-indigo-600/30 border border-slate-700 text-slate-300 text-[11px] whitespace-nowrap transition-colors"
            >
              🧊 3D Studio
            </button>
          </div>

          {/* Messages Feed */}
          <div className="flex-1 p-4 overflow-y-auto space-y-3 text-sm">
            {messages.map((msg) => (
              <div
                key={msg.id}
                className={`flex gap-2.5 ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}`}
              >
                {msg.sender === 'ai' && (
                  <div className="w-7 h-7 rounded-lg bg-indigo-900/50 border border-indigo-700 flex items-center justify-center shrink-0">
                    <Bot className="w-4 h-4 text-indigo-300" />
                  </div>
                )}
                <div
                  className={`max-w-[82%] px-3.5 py-2.5 rounded-2xl text-xs leading-relaxed ${
                    msg.sender === 'user'
                      ? 'bg-indigo-600 text-white rounded-tr-none'
                      : 'bg-slate-900 border border-slate-800 text-slate-200 rounded-tl-none whitespace-pre-wrap'
                  }`}
                >
                  {msg.text}
                  <div className="flex items-center justify-between gap-2 mt-1.5">
                    {msg.model && (
                      <span className="text-[9px] text-purple-400/80 font-mono">
                        {msg.model}
                      </span>
                    )}
                    <span
                      className={`text-[9px] ${
                        msg.sender === 'user' ? 'text-indigo-200 ml-auto' : 'text-slate-500'
                      }`}
                    >
                      {msg.timestamp}
                    </span>
                  </div>
                </div>
              </div>
            ))}

            {isLoading && (
              <div className="flex gap-2 items-center text-xs text-slate-400 pl-9">
                <RefreshCw className="w-3.5 h-3.5 animate-spin text-purple-400" />
                <span>Ollama AI analyzing Fiche Technique &amp; 3D parameters...</span>
              </div>
            )}
            <div ref={messagesEndRef} />
          </div>

          {/* Input Box */}
          <form
            onSubmit={(e) => {
              e.preventDefault();
              handleSend();
            }}
            className="p-3 bg-slate-900/90 border-t border-slate-800 flex items-center gap-2"
          >
            <input
              type="text"
              value={input}
              onChange={(e) => setInput(e.target.value)}
              placeholder="Ask Ollama about specs, comparisons, finishes..."
              className="flex-1 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500"
            />
            <button
              type="submit"
              disabled={isLoading || !input.trim()}
              className="p-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white disabled:opacity-50 transition-all shadow-glow"
            >
              <Send className="w-4 h-4" />
            </button>
          </form>
        </div>
      )}
    </>
  );
}
