import re

with open(r"d:\Online Shopping\sql\shopping.sql", "r", encoding="utf-8", errors="ignore") as f:
    full_sql = f.read()

with open(r"d:\Online Shopping\scratch\update_catalog.sql", "r", encoding="utf-8") as f:
    new_catalog_sql = f.read()

# We can append the catalog updates before COMMIT or at end of sql
if "COMMIT;" in full_sql:
    full_sql = full_sql.replace("COMMIT;", new_catalog_sql + "\nCOMMIT;")
else:
    full_sql += "\n" + new_catalog_sql

with open(r"d:\Online Shopping\sql\shopping.sql", "w", encoding="utf-8") as f:
    f.write(full_sql)

print("[OK] Updated sql/shopping.sql seed file with modern catalog.")
