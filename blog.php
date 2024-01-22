<?php
include("includes/config.php");

$postsPerPage = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

function getAllBlogs($dbh, $offset, $postsPerPage)
{
	$sql = "SELECT * FROM bloginfo LIMIT :offset, :postsPerPage";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
	$stmt->bindParam(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
	$stmt->execute();

	$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $blogs;
}

function getTotalPages($dbh, $postsPerPage)
{
	$stmt = $dbh->query("SELECT COUNT(*) FROM bloginfo");
	$totalPosts = $stmt->fetchColumn();
	$totalPages = ceil($totalPosts / $postsPerPage);

	return $totalPages;
}

$blogs = getAllBlogs($dbh, $offset, $postsPerPage);
$totalPages = getTotalPages($dbh, $postsPerPage);

?>
<!DOCTYPE html>
<html lang="zxx">
<?php include('includes/head.php') ?>

<body id="top">

	<?php include('includes/header.php') ?>



	<section class="page-title bg-1">
		<div class="overlay"></div>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="block text-center">
						<span class="text-white">Our blog</span>
						<h1 class="text-capitalize mb-5 text-lg">Blog articles</h1>

						<!-- <ul class="list-inline breadcumb-nav">
            <li class="list-inline-item"><a href="index.php" class="text-white">Home</a></li>
            <li class="list-inline-item"><span class="text-white">/</span></li>
            <li class="list-inline-item"><a href="#" class="text-white-50">Our blog</a></li>
          </ul> -->
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section blog-wrap">
		<div class="container">
			<?php if (!empty($blogs)) : ?>
				<div class="row">
					<?php foreach ($blogs as $blog) : ?>
						<div class="col-lg-8">
							<div class="row">
								<div class="col-lg-12 col-md-12 mb-5">
									<div class="blog-item">
										<div class="blog-thumb">
											<img src="uploads/<?php echo $blog['blog_images']; ?>" alt="" class="img-fluid ">
										</div>

										<div class="blog-item-content">
											<div class="blog-item-meta mb-3 mt-4">
												<span class="text-muted text-capitalize mr-3"><i class="icofont-comment mr-2"></i>0 Comments</span>
												<span class="text-black text-capitalize mr-3"><i class="icofont-calendar mr-1"></i>
													<?php
													$formattedDate = date("F, jS Y", strtotime($blog['date']));
													?> <?= $formattedDate; ?>
												</span>
											</div>

											<h2 class="mt-3 mb-3"><a href="blog-post.php?id=<?= $blog['id']; ?>"><?php echo $blog['blog_title']; ?></a></h2>

											<?php
											$formattedDescription = substr($blog['blog_description'], 0, strpos($blog['blog_description'], ".", 150) + 1);
											?>
											<p class="mb-4"><?= $formattedDescription; ?></p>

											<a href="blog-post.php?id=<?= $blog['id']; ?>" class="btn btn-main btn-icon btn-round-full">Read More <i class="icofont-simple-right ml-2  "></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="sidebar-wrap pl-lg-4 mt-5 mt-lg-0">
								<div class="sidebar-widget search  mb-3 ">
									<h5>Search Here</h5>
									<form action="#" class="search-form">
										<input type="text" class="form-control" placeholder="search">
										<i class="ti-search"></i>
									</form>
								</div>


								<div class="sidebar-widget latest-post mb-3">
									<h5>Popular Posts</h5>

									<div class="py-2">
										<span class="text-sm text-muted"><?php
																			$formattedDate = date("F, jS Y", strtotime($blog['date']));
																			?> <?= $formattedDate; ?></span>
										<h6 class="my-2"><a href="blog-post.php?id=<?= $blog['id']; ?>"><?php echo $blog['blog_title']; ?></a></h6>
									</div>
								</div>

								<div class="sidebar-widget category mb-3">
									<h5 class="mb-4">Categories</h5>

									<?php
									$categories = array_unique(array_column($blogs, 'blog_category'));

									foreach ($categories as $category) {
										$categoryCount = array_count_values(array_column($blogs, 'blog_category'))[$category];
									?>
										<li class="align-items-center">
											<a href="#"><?php echo $category; ?></a>
											<span>(<?php echo $categoryCount; ?>)</span>
										</li>
									<?php } ?>
								</div>

								<div class="sidebar-widget tags mb-3">
									<h5 class="mb-4">Tags</h5>

									<?php
									$allTags = implode(',', array_column($blogs, 'blog_tags'));
									$tags = array_unique(explode(',', $allTags));

									foreach ($tags as $tag) {
									?>
										<a href="#"><?php echo $tag; ?></a>
									<?php } ?>
								</div>



								<div class="sidebar-widget schedule-widget mb-3">
									<h5 class="mb-4">Time Schedule</h5>

									<ul class="list-unstyled">
										<li class="d-flex justify-content-between align-items-center">
											<a href="#">Monday - Friday</a>
											<span>9:00 - 17:00</span>
										</li>
										<li class="d-flex justify-content-between align-items-center">
											<a href="#">Saturday</a>
											<span>9:00 - 16:00</span>
										</li>
										<li class="d-flex justify-content-between align-items-center">
											<a href="#">Sunday</a>
											<span>Closed</span>
										</li>
									</ul>

									<div class="sidebar-contatct-info mt-4">
										<p class="mb-0">Need Urgent Help?</p>
										<h3>+23-4565-65768</h3>
									</div>
								</div>

							</div>
						</div>
					<?php endforeach; ?>
				</div>


				<div class="row mt-5">
					<div class="col-lg-8">
						<nav class="pagination py-2 d-inline-block">
							<div class="nav-links">
								<?php for ($i = 1; $i <= $totalPages; $i++) : ?>
									<a class="page-numbers <?php echo ($i === $page) ? 'current' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
								<?php endfor; ?>
								<a class="page-numbers" href="?page=<?php echo $page + 1; ?>"><i class="icofont-thin-double-right"></i></a>
							</div>
						</nav>
					</div>
				</div>


			<?php else : ?>
				<article class="blog-post">
					<p>No blogs available.</p>
				</article>
			<?php endif; ?>
		</div>
	</section>

	<!-- footer Start -->
	<?php include('includes/footer.php') ?>


	<!-- 
    Essential Scripts
    =====================================-->


	<?php include('includes/scripts.php') ?>

</body>

</html>