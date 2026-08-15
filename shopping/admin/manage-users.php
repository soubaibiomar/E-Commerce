<?php
session_start();
include('include/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
} else {
    $currentTime = date('d-m-Y h:i:s A');

    // Delete user
    if (isset($_GET['del'])) {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            db_query("DELETE FROM users WHERE id = ?", [$id], "i");
            $_SESSION['delmsg'] = "User deleted successfully!";
        }
    }

    // Handle form submission for Add/Edit
    if (isset($_POST['submit'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contactno = trim($_POST['contactno'] ?? '');
        $shippingAddress = trim($_POST['shippingAddress'] ?? '');
        $shippingCity = trim($_POST['shippingCity'] ?? '');
        $shippingState = trim($_POST['shippingState'] ?? '');
        $shippingPincode = intval($_POST['shippingPincode'] ?? 0);
        $billingAddress = trim($_POST['billingAddress'] ?? '');
        $billingCity = trim($_POST['billingCity'] ?? '');
        $billingState = trim($_POST['billingState'] ?? '');
        $billingPincode = intval($_POST['billingPincode'] ?? 0);

        if ($id > 0) {
            // Update user
            db_query("UPDATE users SET name=?, email=?, contactno=?, shippingAddress=?, shippingCity=?, shippingState=?, shippingPincode=?, billingAddress=?, billingCity=?, billingState=?, billingPincode=? WHERE id=?",
                [$name, $email, $contactno, $shippingAddress, $shippingCity, $shippingState, $shippingPincode, $billingAddress, $billingCity, $billingState, $billingPincode, $id],
                "ssssssisssii"
            );
            $_SESSION['updatemsg'] = "User updated successfully!";
        } else {
            // Add new user
            $defaultPwd = hash_password('User@123');
            db_query("INSERT INTO users (name, email, contactno, password, shippingAddress, shippingCity, shippingState, shippingPincode, billingAddress, billingCity, billingState, billingPincode, regDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$name, $email, $contactno, $defaultPwd, $shippingAddress, $shippingCity, $shippingState, $shippingPincode, $billingAddress, $billingCity, $billingState, $billingPincode, $currentTime],
                "ssssssisssiis"
            );
            $_SESSION['addmsg'] = "User added successfully!";
        }
    }

    // Fetch user data for editing
    $user = null;
    if (isset($_GET['edit'])) {
        $editId = intval($_GET['edit']);
        $user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$editId], "i");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Users</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
</head>
<body>
<?php include('include/header.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php'); ?>
            <div class="span9">
                <div class="content">

                    <div class="module">
                        <div class="module-head">
                            <h3>Manage Users</h3>
                        </div>
                        <div class="module-body">

                            <!-- Display Messages -->
                            <?php if (isset($_SESSION['delmsg']) && !empty($_SESSION['delmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Done!</strong> <?php echo e($_SESSION['delmsg']); $_SESSION['delmsg'] = ""; ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($_SESSION['addmsg']) && !empty($_SESSION['addmsg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Well done!</strong> <?php echo e($_SESSION['addmsg']); $_SESSION['addmsg'] = ""; ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($_SESSION['updatemsg']) && !empty($_SESSION['updatemsg'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Great!</strong> <?php echo e($_SESSION['updatemsg']); $_SESSION['updatemsg'] = ""; ?>
                                </div>
                            <?php } ?>

                            <!-- Add/Edit Form -->
                            <form method="post">
                                <input type="hidden" name="id" value="<?php echo e($user['id'] ?? ''); ?>">
                                <div class="control-group">
                                    <label>Name</label>
                                    <input type="text" name="name" value="<?php echo e($user['name'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Contact No</label>
                                    <input type="text" name="contactno" value="<?php echo e($user['contactno'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Shipping Address</label>
                                    <textarea name="shippingAddress" required><?php echo e($user['shippingAddress'] ?? ''); ?></textarea>
                                </div>
                                <div class="control-group">
                                    <label>Shipping City</label>
                                    <input type="text" name="shippingCity" value="<?php echo e($user['shippingCity'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Shipping State</label>
                                    <input type="text" name="shippingState" value="<?php echo e($user['shippingState'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Shipping Pincode</label>
                                    <input type="text" name="shippingPincode" value="<?php echo e($user['shippingPincode'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Billing Address</label>
                                    <textarea name="billingAddress" required><?php echo e($user['billingAddress'] ?? ''); ?></textarea>
                                </div>
                                <div class="control-group">
                                    <label>Billing City</label>
                                    <input type="text" name="billingCity" value="<?php echo e($user['billingCity'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Billing State</label>
                                    <input type="text" name="billingState" value="<?php echo e($user['billingState'] ?? ''); ?>" required>
                                </div>
                                <div class="control-group">
                                    <label>Billing Pincode</label>
                                    <input type="text" name="billingPincode" value="<?php echo e($user['billingPincode'] ?? ''); ?>" required>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Save User</button>
                            </form>

                            <!-- Users Table -->
                            <br><br>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact No</th>
                                        <th>Shipping Address</th>
                                        <th>Billing Address</th>
                                        <th>Reg Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $allUsers = db_fetch_all("SELECT * FROM users ORDER BY id DESC");
                                    $cnt = 1;
                                    foreach ($allUsers as $row) {
                                    ?>
                                        <tr>
                                            <td><?php echo e($cnt); ?></td>
                                            <td><?php echo e($row['name']); ?></td>
                                            <td><?php echo e($row['email']); ?></td>
                                            <td><?php echo e($row['contactno']); ?></td>
                                            <td><?php echo e($row['shippingAddress'] . ", " . $row['shippingCity'] . ", " . $row['shippingState'] . " - " . $row['shippingPincode']); ?></td>
                                            <td><?php echo e($row['billingAddress'] . ", " . $row['billingCity'] . ", " . $row['billingState'] . " - " . $row['billingPincode']); ?></td>
                                            <td><?php echo e($row['regDate']); ?></td>
                                            <td>
                                                <a href="manage-users.php?edit=<?php echo e($row['id']); ?>">Edit</a> | 
                                                <a href="manage-users.php?del=1&id=<?php echo e($row['id']); ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php'); ?>

<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>
<?php } ?>
