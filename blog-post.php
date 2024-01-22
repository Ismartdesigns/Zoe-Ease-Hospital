<?php
include("includes/config.php");

function getAllBlogs($dbh)
{
	$sql = "SELECT *, DATE_FORMAT(date, '%M %e, %Y') AS formatted_date FROM bloginfo";
	$stmt = $dbh->query($sql);

	$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $blogs;
}

function getBlogComments($dbh, $blogId)
{
	$sql = "SELECT * FROM blogcomments WHERE blog_id = :blogId AND parent_comment_id IS NULL ORDER BY created_at DESC";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':blogId', $blogId, PDO::PARAM_INT);
	$stmt->execute();

	$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $comments;
}

function getReplies($dbh, $parentCommentId)
{
	$sql = "SELECT * FROM blogcomments WHERE parent_comment_id = :parentCommentId ORDER BY created_at";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':parentCommentId', $parentCommentId, PDO::PARAM_INT);
	$stmt->execute();

	$replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $replies;
}

// Process Comment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-comment'])) {
	$blogId = $_POST['blog_id'];
	$parentCommentId = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;
	$name = $_POST['name'];
	$email = $_POST['email'];
	$comment = $_POST['comment'];

	$sql = "INSERT INTO blogcomments (blog_id, parent_comment_id, name, email, comment) VALUES (:blogId, :parentCommentId, :name, :email, :comment)";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':blogId', $blogId, PDO::PARAM_INT);
	$stmt->bindParam(':parentCommentId', $parentCommentId, PDO::PARAM_INT);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':email', $email, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->execute();
}

$blogs = getAllBlogs($dbh);

if (!empty($blogs)) {
	$currentBlogId = isset($_GET['id']) ? $_GET['id'] : $blogs[0]['id'];
	$currentIndex = array_search($currentBlogId, array_column($blogs, 'id'));

	if ($currentIndex === false) {
		$currentBlog = $blogs[0];
	} else {
		$currentBlog = $blogs[$currentIndex];
	}

	$blogComments = getBlogComments($dbh, $currentBlogId);
}
?>
<!DOCTYPE html>
<html lang="zxx">
<?php include('includes/head.php') ?>

<body id="top">

<style>
        .comment-reply-form {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }

        .comment-reply-form.show-form {
            max-height: 500px; /* Adjust the maximum height as needed */
        }
    </style>

	<?php include('includes/header.php') ?>



	<section class="page-title bg-1">
		<div class="overlay"></div>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="block text-center">
						<span class="text-white">News details</span>
						<h1 class="text-capitalize mb-5 text-lg"><?php echo $currentBlog['blog_title']; ?></h1>

						<!-- <ul class="list-inline breadcumb-nav">
            <li class="list-inline-item"><a href="index.html" class="text-white">Home</a></li>
            <li class="list-inline-item"><span class="text-white">/</span></li>
            <li class="list-inline-item"><a href="#" class="text-white-50">News details</a></li>
          </ul> -->
					</div>
				</div>
			</div>
		</div>
	</section>



	<section class="section blog-wrap">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="row">
						<div class="col-lg-12 mb-5">
							<div class="single-blog-item">
								<img src="uploads/<?php echo $currentBlog['blog_images']; ?>" alt="" class="img-fluid">

								<div class="blog-item-content mt-5">
									<div class="blog-item-meta mb-3">
										<span class="text-muted text-capitalize mr-3"><i class="icofont-comment mr-2"></i>5 Comments</span>
										<span class="text-black text-capitalize mr-3"><i class="icofont-calendar mr-2"></i> <a href="#" class="effect-ajax"><?php echo $currentBlog['formatted_date']; ?></a></span>
									</div>

									<h2 class="mb-4 text-md"><a href="javascript:void"><?php echo $currentBlog['blog_title']; ?></a></h2>

									<blockquote class="quote">
										<?php echo $currentBlog['blog_quotes']; ?>
									</blockquote>

									<p class="lead mb-4"><?php echo $currentBlog['blog_description']; ?></p>

									<div class="mt-5 clearfix">
										<ul class="float-left list-inline tag-option">
											<li class="list-inline-item">
												<?php
												$allTags = implode(',', array_column($blogs, 'blog_tags'));
												$tags = array_unique(explode(',', $allTags));

												foreach ($tags as $tag) {
												?>
													<a href="#"><?php echo $tag; ?></a>
												<?php } ?>
											</li>
										</ul>

										<ul class="float-right list-inline">
											<li class="list-inline-item"> Share: </li>
											<li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-facebook" aria-hidden="true"></i></a></li>
											<li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-twitter" aria-hidden="true"></i></a></li>
											<li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-pinterest" aria-hidden="true"></i></a></li>
											<li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-linkedin" aria-hidden="true"></i></a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-12">
                            <div class="comment-area mt-4 mb-5">
                                <h4 class="mb-4"><?php echo count($blogComments); ?> Comments on <?php echo $currentBlog['blog_title']; ?></h4>
                                <ul class="comment-tree list-unstyled">
                                    <?php foreach ($blogComments as $comment) : ?>
                                        <li class="mb-5">
                                            <div class="comment-area-box">
                                                <div class="comment-thumb float-left">
                                                    <!-- Display user image if available -->
                                                </div>
                                                <div class="comment-info">
                                                    <h5 class="mb-1"><?php echo $comment['name']; ?></h5>
                                                    <span class="date-comm">Posted <?php echo date('F j, Y', strtotime($comment['created_at'])); ?></span>
                                                </div>
                                                <div class="comment-meta mt-2">
                                                    <a href="#" class="reply-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                                        <i class="icofont-reply mr-2 text-muted"></i>Reply
                                                    </a>
                                                </div>
                                                <div class="comment-content mt-3">
                                                    <p><?php echo $comment['comment']; ?></p>
                                                </div>

                                                <?php
                                                $replies = getReplies($dbh, $comment['id']);
                                                foreach ($replies as $reply) :
                                                ?>
                                                    <div class="comment-reply mt-3 ml-4">
                                                        <div class="comment-area-box">
                                                            <div class="comment-thumb float-left">
                                                                <!-- Display reply user image if available -->
                                                            </div>
                                                            <div class="comment-info">
                                                                <h5 class="mb-1"><?php echo $reply['name']; ?></h5>
                                                                <span class="date-comm">Posted <?php echo date('F j, Y', strtotime($reply['created_at'])); ?></span>
                                                            </div>
                                                            <div class="comment-content mt-3">
                                                                <p><?php echo $reply['comment']; ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div class="comment-reply-form mt-3 ml-4 reply-form" id="reply-form-<?php echo $comment['id']; ?>">
                                                    <form class="comment-form" method="post">
                                                        <input type="hidden" name="blog_id" value="<?php echo $currentBlogId; ?>">
                                                        <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <input class="form-control" type="text" name="name" id="name" placeholder="Name:" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <input class="form-control" type="text" name="email" id="email" placeholder="Email:" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <textarea class="form-control mb-4" name="comment" id="comment" cols="30" rows="5" placeholder="Reply to this comment" required></textarea>
                                                        <input class="btn btn-main-2 btn-round-full" type="submit" name="submit-comment" value="Submit Reply">
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <form class="comment-form my-5" id="comment-form" method="post">
                                <h4 class="mb-4">Write a comment</h4>
                                <input type="hidden" name="blog_id" value="<?php echo $currentBlogId; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="name" id="name" placeholder="Name:" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email" id="email" placeholder="Email:" required>
                                        </div>
                                    </div>
                                </div>
                                <textarea class="form-control mb-4" name="comment" id="comment" cols="30" rows="5" placeholder="Comment" required></textarea>
                                <input class="btn btn-main-2 btn-round-full" type="submit" name="submit-comment" id="submit_comment" value="Submit Comment">
                            </form>
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
								<span class="text-sm text-muted"><?php echo $currentBlog['formatted_date']; ?></span>
								<h6 class="my-2"><a href="#"><?php echo $currentBlog['blog_title']; ?></a></h6>
							</div>
						</div>

						<div class="sidebar-widget category mb-3">
							<h5 class="mb-4">Categories</h5>

							<ul class="list-unstyled">
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
							</ul>
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
			</div>
		</div>
	</section>


	<!-- footer Start -->
	<?php include('includes/footer.php') ?>


	<!-- 
    Essential Scripts
    =====================================-->


	<?php include('includes/scripts.php') ?>
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            var replyButtons = document.querySelectorAll('.reply-btn');

            replyButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    var commentId = button.getAttribute('data-comment-id');
                    var replyForm = document.getElementById('reply-form-' + commentId);

                    replyForm.classList.toggle('show-form');

                    replyButtons.forEach(function (otherButton) {
                        var otherCommentId = otherButton.getAttribute('data-comment-id');
                        if (otherCommentId !== commentId) {
                            var otherReplyForm = document.getElementById('reply-form-' + otherCommentId);
                            otherReplyForm.classList.remove('show-form');
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>