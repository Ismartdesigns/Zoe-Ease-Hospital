<?php
include("../includes/config.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

function getAllBlogs($dbh)
{
    $sql = "SELECT * FROM bloginfo";
    $stmt = $dbh->query($sql);

    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $blogs;
}

$blogs = getAllBlogs($dbh);

$numberOfBlogs = count($blogs);

$username = $_SESSION['username'];
?>


<!DOCTYPE html>
<html lang="en">

<?php include("../includes/dash-head.php") ?>



<body class="g-sidenav-show  bg-gray-100">
    <?php include("../includes/dash-sidebar.php") ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?php include("../includes/dash-nav.php") ?>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Blog Posts</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <?php foreach ($blogs as $blog) : ?>
                                <div class="table-responsive p-0">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <img src="../uploads/<?php echo $blog['images']; ?>" class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo $blog['title']; ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?php echo $blog['category']; ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <?php
                                                    // Format the date as "Month, Day Year"
                                                    $formattedDate = date("F, jS Y", strtotime($blog['date']));
                                                    ?>
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $formattedDate; ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                                            Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("../includes/configurator.php") ?>
    <!--   Core JS Files   -->
    <script src="../js/core/popper.min.js"></script>
    <script src="../js/core/bootstrap.min.js"></script>
    <script src="../js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../js/plugins/chartjs.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>