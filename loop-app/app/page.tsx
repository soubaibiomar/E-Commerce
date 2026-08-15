import React from 'react';
import Link from 'next/link';
import { prisma } from '@/lib/prisma';
import Product3DStudio from '@/components/Product3DStudio';
import {
  Layers,
  ArrowRight,
  ShieldCheck,
  Globe,
  Cpu,
  Box,
} from 'lucide-react';

export const dynamic = 'force-dynamic';
export const revalidate = 0;

export default async function HomePage() {
  let products: any[] = [];
  try {
    products = await prisma.product.findMany({
      take: 8,
      orderBy: { id: 'desc' },
      include: {
        categoryRel: true,
        subCategoryRel: true,
      },
    });
  } catch (err) {
    console.warn('Prisma query warning during build:', err);
  }

  const heroProduct = products[0] || null;

  return (
    <div className="space-y-24 pb-24 atmospheric-depth">
      {/* ================================================================= HERO SECTION ================================================================= */}
      <section className="relative pt-10 sm:pt-16 pb-12 overflow-hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            {/* Left Content */}
            <div className="lg:col-span-6 space-y-8">
              <div className="space-y-4">
                <div className="text-xs uppercase tracking-[0.25em] font-semibold text-slate-400">
                  ZeyTech &bull; Flagship Engineering &bull; OS 2.0
                </div>

                <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.1]">
                  Precision Tech. <br />
                  <span className="font-serif italic font-normal text-slate-300">
                    Interactive 3D.
                  </span>
                </h1>

                <p className="text-base text-slate-300 max-w-xl leading-relaxed font-normal">
                  Experience flagship electronics and precision gear through authentic 360-degree WebGL inspection, grounded technical specifications, and intelligent conversational assistance.
                </p>
              </div>

              <div className="flex flex-wrap items-center gap-4 pt-2">
                <a
                  href="#catalog"
                  className="px-7 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-tactile hover:shadow-tactile-hover flex items-center gap-2.5 transition-all transform hover:-translate-y-0.5"
                >
                  <Layers className="w-4 h-4" /> Explore Catalog
                </a>
                <a
                  href="#3d-studio"
                  className="px-7 py-4 rounded-xl glass-surface hover:bg-slate-800/80 text-slate-200 font-semibold text-sm flex items-center gap-2.5 transition-all"
                >
                  <Box className="w-4 h-4 text-blue-400" /> Launch 3D Studio
                </a>
              </div>

              {/* Trust Badges */}
              <div className="pt-8 border-t border-slate-800/80 grid grid-cols-3 gap-4 text-xs text-slate-400">
                <div className="flex items-center gap-2.5">
                  <Globe className="w-4 h-4 text-blue-400 shrink-0" />
                  <span>52+ Currencies</span>
                </div>
                <div className="flex items-center gap-2.5">
                  <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
                  <span>Verified Fiche Specs</span>
                </div>
                <div className="flex items-center gap-2.5">
                  <Cpu className="w-4 h-4 text-slate-300 shrink-0" />
                  <span>Local Privacy AI</span>
                </div>
              </div>
            </div>

            {/* Right: Live Interactive 3D Model Studio */}
            <div id="3d-studio" className="lg:col-span-6">
              <Product3DStudio
                productType="smartphone"
                productName={heroProduct?.productName || 'Flagship Titanium'}
                initialColor="#94a3b8"
              />
            </div>
          </div>
        </div>
      </section>

      {/* ================================================================= PRODUCT CATALOG ================================================================= */}
      <section id="catalog" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div className="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 border-b border-slate-800 pb-6">
          <div>
            <div className="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400 mb-2">
              Curated Selection
            </div>
            <h2 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">The Modern Catalog</h2>
          </div>
          <p className="text-xs text-slate-400 max-w-sm leading-relaxed">
            All devices include full 360-degree tactile 3D models and verified technical datasheets.
          </p>
        </div>

        {/* Product Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.map((p) => {
            const price = p.productPrice || 0;
            const oldPrice = p.productPriceBeforeDiscount || 0;
            const discount = oldPrice > price ? Math.round(((oldPrice - price) / oldPrice) * 100) : 0;

            return (
              <div
                key={p.id}
                className="group relative rounded-2xl glass-surface hover:glass-surface-elevated overflow-hidden flex flex-col transition-all duration-300 hover:shadow-tactile-hover hover:-translate-y-1"
              >
                {discount > 0 && (
                  <div className="absolute top-3 left-3 z-10 px-2.5 py-0.5 rounded-md bg-rose-600/90 text-white text-[10px] font-bold tracking-wider uppercase shadow-sm">
                    -{discount}%
                  </div>
                )}

                <div className="absolute top-3 right-3 z-10 px-2 py-0.5 rounded-md bg-slate-900/90 border border-slate-700/60 text-slate-300 text-[10px] font-semibold flex items-center gap-1">
                  <Box className="w-3 h-3 text-blue-400" /> 3D
                </div>

                <div className="relative h-56 w-full p-6 flex items-center justify-center bg-gradient-to-b from-slate-950/40 to-transparent">
                  {p.productImage1 ? (
                    <img
                      src={`/admin/productimages/${p.id}/${p.productImage1}`}
                      alt={p.productName || 'Product'}
                      className="max-h-full max-w-full object-contain drop-shadow-xl transition-transform duration-300 group-hover:scale-105"
                    />
                  ) : (
                    <Box className="w-16 h-16 text-slate-700" />
                  )}
                </div>

                <div className="p-5 flex-1 flex flex-col justify-between space-y-4">
                  <div className="space-y-1.5">
                    <div className="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">
                      {p.productCompany || p.categoryRel?.categoryName}
                    </div>
                    <h3 className="text-sm font-bold text-white line-clamp-2 leading-snug group-hover:text-blue-300 transition-colors">
                      {p.productName}
                    </h3>
                  </div>

                  <div className="pt-3.5 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                      <div className="text-base font-extrabold text-white">
                        ${price.toLocaleString()} <span className="text-[10px] text-slate-400 font-normal">USD</span>
                      </div>
                      {oldPrice > price && (
                        <div className="text-xs text-slate-500 line-through">
                          ${oldPrice.toLocaleString()}
                        </div>
                      )}
                    </div>

                    <Link
                      href={`/products/${p.id}`}
                      className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-blue-600 text-white text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                    >
                      <span>Inspect</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </section>
    </div>
  );
}