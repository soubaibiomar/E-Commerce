import os
import subprocess

def fix_images():
    print("=== SYNCHRONIZING PRODUCT IMAGES IN MARIADB ===")
    
    # We will query and update via docker exec shopping_db mariadb
    base_dir = "shopping/admin/productimages"
    sql_statements = []

    # Clean up duplicate trailing rows if any (id > 21)
    sql_statements.append("DELETE FROM products WHERE id > 21;")

    for pid in range(1, 22):
        p_path = os.path.join(base_dir, str(pid))
        if not os.path.exists(p_path):
            continue
        
        files = [f for f in os.listdir(p_path) if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp'))]
        if not files:
            continue

        # Sort so img_main.jpg comes first if it exists
        img1 = None
        img2 = None
        img3 = None

        if "img_main.jpg" in files:
            img1 = "img_main.jpg"
            img2 = "img_angle.jpg" if "img_angle.jpg" in files else (files[1] if len(files) > 1 else "img_main.jpg")
            img3 = "img_detail.jpg" if "img_detail.jpg" in files else (files[2] if len(files) > 2 else "img_main.jpg")
        else:
            img1 = files[0]
            img2 = files[1] if len(files) > 1 else files[0]
            img3 = files[2] if len(files) > 2 else files[0]

        sql = f"UPDATE products SET productImage1='{img1}', productImage2='{img2}', productImage3='{img3}' WHERE id={pid};"
        sql_statements.append(sql)

    full_sql = "\n".join(sql_statements)
    with open("sql/fix_images.sql", "w", encoding="utf-8") as f:
        f.write(full_sql)

    print(f"Generated {len(sql_statements)} SQL updates in sql/fix_images.sql.")

    # Execute inside docker container
    cmd = 'Get-Content sql/fix_images.sql -Raw | docker exec -i shopping_db mariadb -u shopping_user -pshopping_pass shopping'
    res = subprocess.run(["powershell", "-Command", cmd], capture_output=True, text=True)
    print("Execution output:", res.stdout, res.stderr)

    # Verify
    verify_cmd = 'docker exec -i shopping_db mariadb -u shopping_user -pshopping_pass shopping -e "SELECT id, productName, productImage1 FROM products;"'
    v_res = subprocess.run(["powershell", "-Command", verify_cmd], capture_output=True, text=True)
    print("\nVerified Products Table:")
    print(v_res.stdout)

if __name__ == "__main__":
    fix_images()
