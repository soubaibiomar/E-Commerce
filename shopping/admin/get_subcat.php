<?php
include('include/config.php');

if (!empty($_POST["cat_id"])) {
    $id = intval($_POST['cat_id']);
    $subcategories = db_fetch_all("SELECT id, subcategory FROM subcategory WHERE categoryid=?", [$id], "i");
    echo '<option value="">Select Subcategory</option>';
    foreach ($subcategories as $row) {
        echo '<option value="' . e($row['id']) . '">' . e($row['subcategory']) . '</option>';
    }
}
?>