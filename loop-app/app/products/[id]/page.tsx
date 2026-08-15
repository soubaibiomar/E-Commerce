import React from 'react';
import { notFound } from 'next/navigation';
import { prisma } from '@/lib/prisma';
import Link from 'next/link';
import {
  Sparkles,
  ShoppingBag,
  Heart,
  ShieldCheck,
  Truck,
  RotateCw,
  Box,
  Star,
  Layers,
  ChevronRight,
  Bot,
} from 'lucide-react';
import Product3DStudio from '@/components/Product3DStudio';
import FicheTechniqueTable from '@/components/FicheTechniqueTable';

export const dynamic = 'force-dynamic';
export const revalidate = 0;

interface ProductPageProps {
  params: { id: string };
}

export default async function ProductDetailsPage({ params }: ProductPageProps) {
  const productId = parseInt(params.id, 10);
  if (isNaN(productId)) {
    notFound();
  }

  let product: any = null;
  try {
    product = await prisma.product.findUnique({
      where: { id: productId },
      include: {
        categoryRel: true,
        subCategoryRel: true,
        reviews: { orderBy: { id: 'desc' } },
      },
    });
  } catch (err) {
    console.warn('Prisma product query error during build:', err);
  }

  if (!product) {
    notFound();
  }

  const price = product.productPrice || 0;
  const oldPrice = product.productPriceBeforeDiscount || 0;
  const discount = oldPrice > price ? Math.round(((oldPrice - price) / oldPrice) * 100) : 0;

  const pNameLower = (product.productName || '').toLowerCase();
  let modelType = product.productModel || 'smartphone';
  if (!product.productModel) {
    if (pNameLower.includes('macbook') || pNameLower.includes('xps') || pNameLower.includes('laptop')) {
      modelType = 'laptop';
    } else if (pNameLower.includes('headphone') || pNameLower.includes('sony wh') || pNameLower.includes('earbud')) {
      modelType = 'headphone';
    } else if (pNameLower.includes('watch')) {
      modelType = 'watch';
    } else if (pNameLower.includes('ps5') || pNameLower.includes('playstation') || pNameLower.includes('switch')) {
      modelType = 'console';
    } else if (pNameLower.includes('chair') || pNameLower.includes('aeron')) {
      modelType = 'chair';
    } else if (pNameLower.includes('book') || pNameLower.includes('habits') || pNameLower.includes('money')) {
      modelType = 'book';
    }
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Breadcrumbs */}
      <nav className="flex items-center gap-2 text-xs text-slate-400 font-semibold">
        <Link href="/" className="hover:text-white transition-colors">Home</Link>
        <ChevronRight className="w-3.5 h-3.5" />
        <span>{product.categoryRel?.categoryName || 'Catalog'}</span>
        <ChevronRight className="w-3.5 h-3.5" />
        <span className="text-white truncate max-w-xs">{product.productName}</span>
      </nav>

      {/* Main Showcase Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {/* Left Column: Interactive 3D Model Studio */}
        <div className="lg:col-span-6 space-y-4">
          <Product3DStudio
            productType={modelType}
            productName={product.productName || 'Flagship Product'}
            initialColor="#94a3b8"
          />

          {/* 3D Features Mini Strip */}
          <div className="grid grid-cols-3 gap-3 text-center text-xs">
            <div className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300">
              <Box className="w-4 h-4 mx-auto mb-1 text-cyan-400" />
              <div className="font-bold text-white">360° Free Orbit</div>
              <div className="text-[10px] text-slate-500">Drag &amp; Zoom</div>
            </div>
            <div className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300">
              <RotateCw className="w-4 h-4 mx-auto mb-1 text-indigo-400" />
              <div className="font-bold text-white">PBR Shaders</div>
              <div className="text-[10px] text-slate-500">5 Material Colors</div>
            </div>
            <div className="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-300">
              <Sparkles className="w-4 h-4 mx-auto mb-1 text-purple-400" />
              <div className="font-bold text-white">Studio Lighting</div>
              <div className="text-[10px] text-slate-500">Neon &amp; Sunset</div>
            </div>
          </div>
        </div>

        {/* Right Column: Product Info & Order Panel */}
        <div className="lg:col-span-6 space-y-6">
          <div className="space-y-2">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
              {product.productCompany} &bull; {product.categoryRel?.categoryName}
            </div>
            <h1 className="text-2xl sm:text-3xl font-black text-white leading-tight">
              {product.productName}
            </h1>

            {/* Rating Stars */}
            <div className="flex items-center gap-3 pt-1">
              <div className="flex items-center gap-1 text-amber-400">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="w-4 h-4 fill-amber-400" />
                ))}
              </div>
              <span className="text-xs font-semibold text-slate-400">
                ({product.reviews?.length || 5} Verified Customer Reviews)
              </span>
            </div>
          </div>

          {/* Pricing Box */}
          <div className="p-5 rounded-2xl bg-slate-900/70 border border-slate-800 backdrop-blur-xl flex items-center justify-between gap-4">
            <div>
              <div className="text-xs text-slate-400 font-semibold mb-0.5">Price (USD Base)</div>
              <div className="text-3xl font-black text-white flex items-baseline gap-2">
                ${price.toLocaleString()}
                {oldPrice > price && (
                  <span className="text-sm text-slate-500 line-through font-normal">
                    ${oldPrice.toLocaleString()}
                  </span>
                )}
              </div>
            </div>

            {discount > 0 && (
              <div className="px-3.5 py-1.5 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-black">
                Save ${(oldPrice - price).toLocaleString()} ({discount}% OFF)
              </div>
            )}
          </div>

          {/* AI Advisor Prompt Box */}
          <div className="p-4 rounded-xl bg-gradient-to-r from-indigo-950/40 via-purple-950/30 to-slate-900 border border-indigo-500/30 flex items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white shrink-0 shadow-glow">
                <Bot className="w-5 h-5" />
              </div>
              <div className="text-xs">
                <div className="font-bold text-white flex items-center gap-1.5">
                  Ask Multi-Agent AI about this Product
                  <span className="px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 text-[10px]">AI</span>
                </div>
                <div className="text-slate-400 text-[11px]">Instant answers regarding Fiche Technique, battery, and comparisons.</div>
              </div>
            </div>
          </div>

          {/* Description Snippet */}
          <div className="text-xs sm:text-sm text-slate-300 leading-relaxed">
            {product.productDescription}
          </div>

          {/* Action Buttons */}
          <div className="flex flex-wrap items-center gap-4 pt-2">
            <button
              type="button"
              className="flex-1 px-8 py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-black text-sm shadow-glow flex items-center justify-center gap-2 transition-all transform hover:-translate-y-0.5"
            >
              <ShoppingBag className="w-5 h-5" />
              Add to Cart &bull; ${price.toLocaleString()}
            </button>
            <button
              type="button"
              className="p-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-rose-400 hover:text-rose-300 transition-colors"
              title="Save to Wishlist"
            >
              <Heart className="w-5 h-5" />
            </button>
          </div>

          {/* Trust Guarantees */}
          <div className="grid grid-cols-2 gap-4 pt-4 border-t border-slate-800 text-xs text-slate-400">
            <div className="flex items-center gap-2.5">
              <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>100% Genuine with Official Manufacturer Warranty</span>
            </div>
            <div className="flex items-center gap-2.5">
              <Truck className="w-4 h-4 text-cyan-400 shrink-0" />
              <span>Express Tracked Courier Shipping Worldwide</span>
            </div>
          </div>
        </div>
      </div>

      {/* Fiche Technique Section */}
      <section className="space-y-6 pt-6">
        <FicheTechniqueTable
          specificationsString={product.specifications}
          productName={product.productName || 'Product'}
        />
      </section>
    </div>
  );
}
