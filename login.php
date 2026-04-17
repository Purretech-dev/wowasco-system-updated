<?php
session_start();
include 'api/db.php';

$message = '';

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if($result && $result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - WOWASCO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        body {font-family: Arial; background:#eef3f8; margin:0;}
        .login-container {width: 400px; margin: 80px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
        .login-container img {display:block; margin:0 auto 20px; width:120px;}
        h2 {text-align:center; color:#003366; margin-bottom:20px;}
        input[type="text"], input[type="password"] {width:100%; padding:12px; margin:8px 0; border:1px solid #ccc; border-radius:5px;}
        button {width:100%; padding:12px; background:#f7b731; border:none; color:white; font-weight:bold; border-radius:5px; cursor:pointer;}
        button:hover {background:#e6a120;}
        .message {color:red; text-align:center; margin-bottom:10px;}
        .register-link {text-align:center; margin-top:10px;}
        .register-link a {color:#003366; text-decoration:none;}
        .register-link a:hover {text-decoration:underline;}
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/images/wowasco-logo.png" alt="WOWASCO Logo">
        <h2>Login to WOWASCO</h2>
        <?php if($message) echo "<div class='message'>$message</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="register.php">Register Here</a>
        </div>
    </div>
</body>
</html>