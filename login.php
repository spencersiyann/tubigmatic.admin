<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dummy login credentials
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin_logged_in'] = true;

       
        echo "<script>
            window.location.href = 'loading.html';
        </script>";
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TubigMatic Admin Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="logo.png" alt="TubigMatic Logo">
                <h2><span style="color:#006F9E">Tubig</span><span style="color:#3E6606">Matic</span></h2>
            </div>
            <h3>Admin</h3>
            <form method="POST">
                <h7><label for="username">Username<br></label></h7>
                <input type="text" id="username" name="username" required><br>
                
                <h7><label for="password">Password<br></label></h7>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Login</button>

                <?php if (isset($error_message)) : ?>
                    <p id="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('bg_pictures/bg_admin.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .login-container h7 {
            margin-right: 150px;
            font-size: 13px;
        }
        .login-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 435px;
        }
        .login-box h2 {
            text-shadow: 1px 1px rgba(0, 0, 0, 0.5);
        }
        .login-box h3 {
            margin-bottom: 30px;
            font-size: 16px;
        }
        .logo img {
            width: 150px;
            height: auto;
            margin-top: -10px;
            margin-bottom: -10px;
        }
        input {
            width: 200px;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #a8a8a8;
            border-radius: 12px;
        }
        button {
            background-color: #3E6606;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            margin-top: 15px;
            cursor: pointer;
            width: 40%;
        }
        button:hover {
            background-color: rgb(46, 87, 46);
        }
        #error-message {
            color: red;
            font-size: 10px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</body>
</html>
