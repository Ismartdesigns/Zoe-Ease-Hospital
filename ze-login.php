<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Zoe-Ease Blog Login</title>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap'>
    <link rel="stylesheet" href="css/login.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon" />
    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>

<?php
    include('includes/config.php');

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM admin WHERE username = :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            // Passwords match
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Redirecting to the dashboard...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    window.location.href = 'admin/dashboard.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Login',
                    text: 'Please check your username and password.',
                    confirmButtonText: 'Try Again'
                });
            </script>";
        }
    }
?>


    <!-- partial:index.partial.html -->
    <form method="post" action="">
    <div class="screen-1">
        <img class="logo" src="img/logo-dark.png" alt="Zoe-Ease" width="300" viewbox="0 0 640 480" xml:space="preserve">
        
            <div class="email">
                <label for="email">Username</label>
                <div class="sec-2">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="username" placeholder="Username" />
                </div>
            </div>
            <div class="password">
                <label for="password">Password</label>
                <div class="sec-2">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input class="pas" type="password" name="password" id="passwordInput" placeholder="············" />
                    <ion-icon class="show-hide" id="showHideBtn" name="eye-outline"></ion-icon>
                </div>
            </div>
            <button class="login" name="login">Login </button>
        
    </div>
</form>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const showHideBtn = document.getElementById('showHideBtn');

        showHideBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            showHideBtn.name = type === 'password' ? 'eye-outline' : 'eye-off-outline';
        });
    </script>
    <!-- partial -->
</body>

</html>