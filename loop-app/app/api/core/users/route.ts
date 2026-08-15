import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';

export async function GET() {
  try {
    const users = await prisma.user.findMany({
      select: {
        id: true,
        name: true,
        email: true,
        role: true,
        contactno: true,
        shippingCity: true,
        regDate: true,
        _count: { select: { orders: true } },
      },
      orderBy: { id: 'asc' },
    });

    // BigInt serialization serializer
    const safeUsers = users.map((u) => ({
      ...u,
      contactno: u.contactno ? String(u.contactno) : null,
    }));

    return NextResponse.json({ total: users.length, users: safeUsers });
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}
