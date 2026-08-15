<?php
session_start();
include_once 'includes/config.php';
$oid = intval($_GET['oid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order Tracking Details</title>
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

<div style="margin-left:50px; margin-top:20px;">
 <form name="updateticket" id="updateticket" method="post"> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr height="50">
      <td colspan="2" class="fontkink2" style="padding-left:0px;"><div class="fontpink2"> <b>Order Tracking Details !</b></div></td>
    </tr>
    <tr height="30">
      <td class="fontkink1"><b>Order ID:</b></td>
      <td class="fontkink"><?php echo e($oid);?></td>
    </tr>
    <?php 
    $tracks = db_fetch_all("SELECT * FROM ordertrackhistory WHERE orderId=? ORDER BY id ASC", [$oid], "i");
    if (!empty($tracks)) {
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
    <?php 
        } 
    } else {
    ?>
    <tr>
        <td colspan="2">Order Not Processed Yet</td>
    </tr>
    <?php 
    }

    $order = db_fetch_one("SELECT orderStatus FROM orders WHERE id=?", [$oid], "i");
    if ($order && $order['orderStatus'] === 'Delivered') { 
    ?>
    <tr><td colspan="2"><br><b>Product Delivered Successfully</b></td></tr>
    <?php } ?>
    <tr>
      <td colspan="2" style="padding-top: 15px;">
        <input type="button" value="Print Receipt" onClick="return f3();" style="cursor: pointer; padding: 5px 10px;" />
        <input type="button" value="Close Window" onClick="return f2();" style="cursor: pointer; padding: 5px 10px;" />
      </td>
    </tr>
</table>
 </form>
</div>

</body>
</html>