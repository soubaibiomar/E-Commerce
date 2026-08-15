import type { Metadata } from 'next';
import './globals.css';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import AiShoppingAdvisor from '@/components/AiShoppingAdvisor';

export const metadata: Metadata = {
  title: 'ZeyTech | Next-Gen 3D eCommerce & AI Platform',
  description:
    'Next-Generation eCommerce storefront powered by Next.js 14, Three.js 3D WebGL Studio, Prisma ORM, and n8n AI Agent Workflow Automation.',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className="dark">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link
          href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet"
        />
      </head>
      <body className="min-h-screen flex flex-col bg-[#080c16] text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">
        <Navbar />
        <main className="flex-1">{children}</main>
        <Footer />
        <AiShoppingAdvisor />
      </body>
    </html>
  );
}
