<?php
include("../includes/config.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../ze-login.php");
    exit();
}

$username = $_SESSION['username'];

$msg = $error = "";

if (isset($_POST['submit'])) {
    $blogtitle = $_POST['title'];
    $blogdescription = $_POST['description'];
    $blogcategory = $_POST['category'];
    $blogtags = implode(',', $_POST['tags']); // Convert array to string
    $blogquotes = $_POST['quotes'];

    // Handle file uploads
    $uploadedFiles = [];
    $uploadDir = "../uploads/";

    if (isset($_FILES['uploadfile'])) {
        $fileCount = count($_FILES['uploadfile']['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            $filename = $_FILES['uploadfile']['name'][$i];
            $tempname = $_FILES['uploadfile']['tmp_name'][$i];
            $targetFilePath = $uploadDir . $filename;

            if (move_uploaded_file($tempname, $targetFilePath)) {
                $uploadedFiles[] = $filename;
            } else {
                // Handle file upload error
                $error = "Error uploading file: " . $_FILES['uploadfile']['error'][$i];
            }
        }
    }

    if (!empty($uploadedFiles)) {
        // Insert blog info into the database
        $sql = "INSERT INTO bloginfo (blog_title, blog_description, blog_category, blog_tags, blog_quotes, blog_images) 
                VALUES (:title, :description, :category, :tags, :quotes, :FileName)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $blogtitle, PDO::PARAM_STR);
        $stmt->bindParam(':description', $blogdescription, PDO::PARAM_STR);
        $stmt->bindParam(':category', $blogcategory, PDO::PARAM_STR);
        $stmt->bindParam(':tags', $blogtags, PDO::PARAM_STR);
        $stmt->bindParam(':quotes', $blogquotes, PDO::PARAM_STR);

        foreach ($uploadedFiles as $filename) {
            $stmt->bindParam(':FileName', $filename, PDO::PARAM_STR);
            $stmt->execute();
        }

        $lastInsertId = $dbh->lastInsertId();

        if ($lastInsertId) {
            $msg = "Blog info added successfully";
        } else {
            $error = "Something went wrong. Please try again";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<?php include("../includes/dash-head.php") ?>



<body class="g-sidenav-show bg-gray-100">

    <style>
        .selected-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .tag {
            background-color: #cb0c9f;
            color: #fff;
            padding: 5px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tag-close {
            cursor: pointer;
            margin-left: 5px;
        }
    </style>

    <?php include("../includes/dash-sidebar.php") ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?php include("../includes/dash-nav.php") ?>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-md-12">

                    <!-- Dropdown Card Example -->
                    <div class="card shadow mb-4">

                        <!-- Card Body -->
                        <div class="card-body">
                            <?php if ($msg) { ?>
                                <div class="alert alert-success" role="alert">
                                    <strong>Well done! </strong><?php echo htmlentities($msg); ?>
                                </div>
                            <?php } elseif ($error) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                </div>
                            <?php } ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title">Blog Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Blog Description</label>
                                    <textarea type="text" id="editor" class="form-control" name="description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="category">Blog Category</label>
                                    <select class="form-control" name="category" required>
                                        <option disabled="disabled" selected="selected">Choose option</option>
                                        <option>Medical Science</option>
                                        <option>Microbiology</option>
                                        <option>Health and Wellness</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tags">Blog Tags</label>
                                    <div class="selected-tags" id="selectedTags"></div>
                                    <select class="form-control" name="tags[]" id="tagSelect" multiple required>
                                        <option>Microbiome</option>
                                        <option>Medical Research</option>
                                        <option>Prebiotics</option>
                                        <option>Probiotics</option>
                                        <option>Disease Prevention</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="quotes">Blog Quotes</label>
                                    <input type="text" class="form-control" name="quotes">
                                </div>
                                <div class="form-group">
                                    <label for="uploadfile">Blog Images</label>
                                    <input type="file" class="form-control" name="uploadfile[]" id="file-input" accept="image/png, image/jpeg" onchange="preview()" multiple>
                                </div>
                                <div class="form-group">
                                    <button name="submit" class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include("../includes/configurator.php") ?>
    <!-- Core JS Files -->
    <script src="../js/core/popper.min.js"></script>
    <script src="../js/core/bootstrap.min.js"></script>
    <script src="../js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../js/plugins/chartjs.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const editorTextarea = document.querySelector('#editor');

        if (editorTextarea) {
            ClassicEditor
                .create(editorTextarea)
                .catch(error => {
                    console.error(error);
                });
        }
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectedTagsContainer = document.getElementById("selectedTags");
            const tagSelect = document.getElementById("tagSelect");

            tagSelect.addEventListener("change", function() {
                selectedTagsContainer.innerHTML = "";
                const selectedOptions = Array.from(this.selectedOptions);
                selectedOptions.forEach((option) => {
                    const tag = document.createElement("div");
                    tag.className = "tag";
                    tag.innerHTML = `
                        <span>${option.value}</span>
                        <span class="tag-close" onclick="removeTag('${option.value}')">&#10005;</span>
                    `;
                    selectedTagsContainer.appendChild(tag);
                });
            });
        });

        function removeTag(tag) {
            const tagSelect = document.getElementById("tagSelect");
            const optionToRemove = Array.from(tagSelect.options).find(
                (option) => option.value === tag
            );
            optionToRemove.selected = false;

            const selectedTagsContainer = document.getElementById("selectedTags");
            selectedTagsContainer.removeChild(selectedTagsContainer.lastChild);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>