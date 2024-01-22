<?php
require "../includes/config.php";
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

$msg = $error = "";

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

$msg = $error = "";

if (isset($_POST['reset'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['cnew_password'];

    $result = changeAdminPassword($dbh, $username, $currentPassword, $newPassword, $confirmPassword);

    if ($result === "Success") {
        $msg = "Password reset successfully.";
    } else {
        $error = $result;
    }
}

function changeAdminPassword($dbh, $username, $currentPassword, $newPassword, $confirmPassword)
{
    $username = trim($username);
    $currentPassword = trim($currentPassword);
    $newPassword = trim($newPassword);
    $confirmPassword = trim($confirmPassword);

    if (empty($username) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        return "All fields are required";
    }

    // Verify if the provided current password is correct
    $sql = "SELECT password FROM admin WHERE username = :username";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data === false || !password_verify($currentPassword, $data["password"])) {
        return "Incorrect current password";
    }

    if ($newPassword !== $confirmPassword) {
        return "New password and confirm password do not match";
    }

    // Update the password with the new one
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateSql = "UPDATE admin SET password = :password WHERE username = :username";
    $updateStmt = $dbh->prepare($updateSql);
    $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);

    if ($updateStmt->execute()) {
        return "Success";
    } else {
        return "Failed to change the password";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include("../includes/dash-head.php"); ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include("../includes/dash-sidebar.php"); ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include("../includes/dash-nav.php"); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Reset Password</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-md-12">

                            <!-- Dropdown Card Example -->
                            <div class="card shadow mb-4">

                                <!-- Card Body -->
                                <div class="card-body">
                                    <?php if ($msg) { ?>
                                        <div class="alert alert-color left-icon-alert" role="alert">
                                            <strong>Well done! </strong><?php echo htmlentities($msg); ?>
                                        </div>
                                    <?php } elseif ($error) { ?>
                                        <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                    <?php } ?>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row row-space">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <label class="label">Current Password</label>
                                                    <input class="input--style-4" type="password" name="current_password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row row-space">
                                            <div class="col-12">
                                                <div class "input-group">
                                                    <label class="label">New Password</label>
                                                    <input class="input--style-4" type="password" name="new_password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row row-space">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <label class="label">Confirm New Password</label>
                                                    <input class="input--style-4" type="password" name="cnew_password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-t-15">
                                            <button name="reset" class="btn btn--radius-2 btn--blue" type="submit">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Content Row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <?php include('../includes/dash-footer.php'); ?>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <?php include('../auth/logout.php'); ?>
    <?php include('../includes/dash-scripts.php'); ?>
</body>
</html>
