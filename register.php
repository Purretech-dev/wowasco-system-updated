<?php
session_start();
include 'api/db.php';

$message = '';

if(isset($_POST['register'])){
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        $message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $check_user = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
        if($check_user && $check_user->num_rows > 0){
            $message = "Username or email already exists.";
        } else {
            $insert = $conn->query("INSERT INTO users (full_name, username, email, password) VALUES ('$full_name','$username','$email','$hashed_password')");
            if($insert){
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - WOWASCO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        body {font-family: Arial; background:#eef3f8; margin:0;}
        .register-container {width: 450px; margin: 60px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
        .register-container img {display:block; margin:0 auto 20px; width:120px;}
        h2 {text-align:center; color:#003366; margin-bottom:20px;}
        input[type="text"], input[type="email"], input[type="password"] {width:100%; padding:12px; margin:8px 0; border:1px solid #ccc; border-radius:5px;}
        button {width:100%; padding:12px; background:#f7b731; border:none; color:white; font-weight:bold; border-radius:5px; cursor:pointer;}
        button:hover {background:#e6a120;}
        .message {text-align:center; margin-bottom:10px; color:green;}
        .login-link {text-align:center; margin-top:10px;}
        .login-link a {color:#003366; text-decoration:none;}
        .login-link a:hover {text-decoration:underline;}
    </style>
</head>
<body>
    <div class="register-container">
        <img src="assets/images/wowasco-logo.png" alt="WOWASCO Logo">
        <h2>Create an Account</h2>
        <?php if($message) echo "<div class='message'>$message</div>"; ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Login Here</a>
        </div>
    </div>
</body>
</html>