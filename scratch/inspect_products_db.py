import mysql.connector

try:
    conn = mysql.connector.connect(
        host="127.0.0.1",
        port=3308,
        user="shopping_user",
        password="shopping_secret_password_2026!",
        database="shopping"
    )
    cursor = conn.cursor(dictionary=True)
    
    print("=== CATEGORIES ===")
    cursor.execute("SELECT * FROM category")
    for c in cursor.fetchall():
        print(f"ID: {c['id']}, Name: {c['categoryName']}")

    print("\n=== SUBCATEGORIES ===")
    cursor.execute("SELECT * FROM subcategory")
    for sc in cursor.fetchall():
        print(f"ID: {sc['id']}, CatID: {sc['categoryid']}, Name: {sc['subcategory']}")

    print("\n=== PRODUCTS ===")
    cursor.execute("SELECT id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount, productImage1 FROM products")
    for p in cursor.fetchall():
        print(f"ID: {p['id']}, Name: {p['productName']}, Brand: {p['productCompany']}, Price: {p['productPrice']}, Before: {p['productPriceBeforeDiscount']}, Img1: {p['productImage1']}")

    cursor.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
