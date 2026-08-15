import urllib.request
import json
import xml.etree.ElementTree as ET
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_seo_and_geo():
    print("=== EXECUTING SEO & GEO COMPREHENSIVE VERIFICATION SUITE ===")
    
    # 1. robots.txt
    url_robots = "http://localhost:8085/robots.txt"
    with urllib.request.urlopen(url_robots, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200, f"robots.txt returned HTTP {code}"
        assert "User-agent: *" in body
        assert "Disallow: /admin/" in body
        assert "Sitemap: http://localhost:8085/sitemap.xml" in body
        print("[PASS] robots.txt is valid and enforcing proper crawl budget.")

    # 2. sitemap.php
    url_sitemap = "http://localhost:8085/sitemap.php"
    with urllib.request.urlopen(url_sitemap, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200, f"sitemap.php returned HTTP {code}"
        assert "<urlset" in body
        root = ET.fromstring(body)
        urls = [elem.text for elem in root.findall('.//{http://www.sitemaps.org/schemas/sitemap/0.9}loc')]
        assert len(urls) >= 20, f"Expected >= 20 URLs in sitemap, got {len(urls)}"
        print(f"[PASS] Dynamic XML Sitemap generated with {len(urls)} indexed pages (products, categories, subcategories).")

    # 3. Homepage SEO & Organization Schema
    url_home = "http://localhost:8085/index.php"
    with urllib.request.urlopen(url_home, timeout=10) as res:
        body = res.read().decode('utf-8')
        assert '<meta name="description"' in body
        assert '<meta property="og:title"' in body
        assert '<meta name="twitter:card"' in body
        assert '<link rel="canonical"' in body
        assert 'https://schema.org' in body
        assert '"@type": "Organization"' in body
        assert '"@type": "WebSite"' in body
        print("[PASS] Homepage meta tags, OpenGraph, and Schema.org Organization/WebSite JSON-LD verified.")

    # 4. Product Details Rich Schema.org & GEO
    url_product = "http://localhost:8085/product-details.php?pid=1"
    with urllib.request.urlopen(url_product, timeout=10) as res:
        body = res.read().decode('utf-8')
        assert '<meta property="og:type" content="product">' in body
        assert '<link rel="canonical" href="http://localhost:8085/product-details.php?pid=1">' in body
        assert '"@type": "Product"' in body
        assert '"@type": "Offer"' in body
        assert '"@type": "AggregateRating"' in body
        assert '"@type": "BreadcrumbList"' in body
        assert 'Quick Facts &amp; AI Search Summary (TL;DR)' in body
        print("[PASS] Product Details rich Product schema, Offer, AggregateRating, BreadcrumbList, and Answer-First GEO block verified.")

    # 5. Category Page SEO & Breadcrumbs
    url_cat = "http://localhost:8085/category.php?cid=1"
    with urllib.request.urlopen(url_cat, timeout=10) as res:
        body = res.read().decode('utf-8')
        assert '<meta name="description"' in body
        assert '<link rel="canonical" href="http://localhost:8085/category.php?cid=1">' in body
        assert '"@type": "BreadcrumbList"' in body
        print("[PASS] Category page SEO meta tags and BreadcrumbList schema verified.")

    # 6. Subcategory Page SEO & Breadcrumbs
    url_sub = "http://localhost:8085/sub-category.php?scid=1"
    with urllib.request.urlopen(url_sub, timeout=10) as res:
        body = res.read().decode('utf-8')
        assert '<meta name="description"' in body
        assert '<link rel="canonical" href="http://localhost:8085/sub-category.php?scid=1">' in body
        assert '"@type": "BreadcrumbList"' in body
        print("[PASS] Subcategory page SEO meta tags and BreadcrumbList schema verified.")

    print("\n=== ALL SEO & GEO VERIFICATION TESTS PASSED (6/6) ===")

if __name__ == "__main__":
    test_seo_and_geo()
