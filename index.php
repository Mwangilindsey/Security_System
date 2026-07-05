<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "admin") {
        header("Location: admin_dashboard.php");
        exit();
    }

    if ($_SESSION['role'] == "security") {
        header("Location: security_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Jonathan Gloag Academy Security System</title>

<link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="login-page" id="loginPage">
    <div class="container">
        <h2>Jonathan Gloag Academy</h2>
        <h3>Security System Login</h3>

        <?php if (isset($_GET['error'])) { ?>
            <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>

        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>