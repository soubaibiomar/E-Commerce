import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';

export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const query = (searchParams.get('q') || '').trim().toLowerCase();
    const category = searchParams.get('category');
    const maxPrice = Number(searchParams.get('maxPrice')) || 0;

    const allProducts = await prisma.product.findMany({
      where: { productAvailability: 'In Stock' },
      include: { categoryRel: true, subCategoryRel: true },
    });

    if (!query && !category && !maxPrice) {
      return NextResponse.json(allProducts);
    }

    // Hybrid Scoring Algorithm
    const scoredProducts = allProducts.map((p) => {
      let score = 0;
      const name = (p.productName || '').toLowerCase();
      const company = (p.productCompany || '').toLowerCase();
      const desc = (p.productDescription || '').toLowerCase();
      const specs = (p.specifications || '').toLowerCase();
      const catName = (p.categoryRel?.categoryName || '').toLowerCase();

      if (query) {
        if (name.includes(query)) score += 10;
        if (company.includes(query)) score += 8;
        if (specs.includes(query)) score += 5;
        if (desc.includes(query)) score += 3;
        if (catName.includes(query)) score += 4;
      } else {
        score = 1;
      }

      if (category && catName.includes(category.toLowerCase())) {
        score += 5;
      }

      if (maxPrice > 0 && (p.productPrice || 0) <= maxPrice) {
        score += 2;
      }

      return { product: p, score };
    });

    const results = scoredProducts
      .filter((item) => item.score > 0)
      .sort((a, b) => b.score - a.score)
      .map((item) => item.product);

    return NextResponse.json({
      query,
      totalMatches: results.length,
      results,
    });
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}
