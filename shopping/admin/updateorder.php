<?php
session_start();
include_once 'include/config.php';

if (empty($_SESSION['alogin'])) { 
    header('location:index.php');
    exit();
} else {
    $oid = intval($_GET['oid'] ?? 0);

    if (isset($_POST['submit2'])) {
        $status = trim($_POST['status'] ?? '');
        $remark = trim($_POST['remark'] ?? '');

        if (!empty($status) && !empty($remark)) {
            db_query("INSERT INTO ordertrackhistory(orderId, status, remark) VALUES(?, ?, ?)", [$oid, $status, $remark], "iss");
            db_query("UPDATE orders SET orderStatus=? WHERE id=?", [$status, $oid], "si");
            echo "<script>alert('Order updated successfully...');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin | Update Order Status</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="anuj.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function f2() {
    window.close();
}
function f3() {
    window.print(); 
}
</script>
</head>
<body>

<div style="margin-left:50px;">
 <form name="updateticket" id="updateticket" method="post"> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr height="50">
      <td colspan="2" class="fontkink2" style="padding-left:0px;"><div class="fontpink2"> <b>Update Order #<?php echo e($oid);?></b></div></td>
    </tr>
    <tr height="30">
      <td class="fontkink1"><b>Order ID:</b></td>
      <td class="fontkink"><?php echo e($oid);?></td>
    </tr>
    <?php 
    $tracks = db_fetch_all("SELECT * FROM ordertrackhistory WHERE orderId=? ORDER BY id ASC", [$oid], "i");
    foreach ($tracks as $row) {
    ?>
      <tr height="20">
      <td class="fontkink1"><b>At Date:</b></td>
      <td class="fontkink"><?php echo e($row['postingDate']);?></td>
    </tr>
     <tr height="20">
      <td class="fontkink1"><b>Status:</b></td>
      <td class="fontkink"><?php echo e($row['status']);?></td>
    </tr>
     <tr height="20">
      <td class="fontkink1"><b>Remark:</b></td>
      <td class="fontkink"><?php echo e($row['remark']);?></td>
    </tr>
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
   <?php } ?>

   <?php 
   $order = db_fetch_one("SELECT orderStatus FROM orders WHERE id=?", [$oid], "i");
   $currentSt = $order['orderStatus'] ?? '';

   if ($currentSt === 'Delivered') { 
   ?>
   <tr><td colspan="2"><b>Product Delivered</b></td></tr>
   <?php } else { ?>
    <tr height="50">
      <td class="fontkink1">Status: </td>
      <td class="fontkink"><span class="fontkink1">
        <select name="status" class="fontkink" required="required">
          <option value="">Select Status</option>
          <option value="in Process">In Process</option>
          <option value="Delivered">Delivered</option>
        </select>
        </span></td>
    </tr>

     <tr>
      <td class="fontkink1">Remark:</td>
      <td class="fontkink" align="justify"><span class="fontkink">
        <textarea cols="50" rows="7" name="remark" required="required"></textarea>
        </span></td>
    </tr>
    <tr>
      <td class="fontkink1">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="fontkink"></td>
      <td class="fontkink">
        <input type="submit" name="submit2" value="Update" size="40" style="cursor: pointer;" /> &nbsp;&nbsp;   
        <input type="button" class="txtbox4" value="Close this Window" onClick="return f2();" style="cursor: pointer;" />
      </td>
    </tr>
<?php } ?>
</table>
 </form>
</div>

</body>
</html>
<?php } ?>