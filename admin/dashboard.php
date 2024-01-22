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
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Blog Posts</p>
                    <h5 class="font-weight-bolder mb-0">
                      <?php echo $numberOfBlogs; ?>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php foreach ($blogs as $blog) : ?>
                            <div class="swiper-slide">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="d-flex flex-column h-100">
                                            <h5 class="font-weight-bolder"><?php echo $blog['title']; ?></h5>
                                            <?php
                                            // Reduce the description to desired format
                                            $formattedDescription = substr($blog['description'], 0, strpos($blog['description'], ".", 150) + 1);
                                            ?>
                                            <p class="mb-5"><?= $formattedDescription; ?></p>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 ms-auto text-center mt-5 mt-lg-0">
                                        <div class="bg-gradient-primary border-radius-lg h-100">
                                            <div class="position-relative d-flex align-items-center justify-content-center h-100">
                                                <img class="w-100 position-relative z-index-2 pt-4" src="../uploads/<?php echo $blog['images']; ?>" alt="rocket">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
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
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
</script>
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