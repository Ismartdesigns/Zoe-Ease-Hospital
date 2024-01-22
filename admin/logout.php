<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>

    <!-- Add your CSS styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .logout-content {
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
        }

        .btn {
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: 1px solid #007bff;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: #ccc;
            border-color: #ccc;
        }

        .btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>

    <?php
    if (isset($_GET['logout'])) {
        logoutUser($dbh);
    }

    function logoutUser($dbh)
    {
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
    ?>

    <div class="logout-content">
        <h2>Logout</h2>
        <p>Are you sure you want to logout?</p>
        <a class="btn" href="?logout">Logout</a>
        <a class="btn btn-cancel" href="javascript:void(0);" onclick="cancelLogout()">Cancel</a>
    </div>

    <script>
        function cancelLogout() {
            // Go back to the previous page
            window.history.back();
        }
    </script>

</body>

</html>
