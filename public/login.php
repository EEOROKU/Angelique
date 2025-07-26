<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: upload.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // Replace 'yourpassword' with the actual password
    if ($password === 'yourpassword') {
        $_SESSION['loggedin'] = true;
        header("Location: upload.php");
        exit;
    } else {
        $error = "Incorrect password. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANGELIQUE BOUTIQUE | Admin Login</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="img/Logo.png">
</head>
<body>
    <div class="login">
        <a href="index.html"><img src="img/Logo.png" width="500px" height="500px" class="logo" alt=""></a>
        <h3>Enter the Password to Access the Admin Page</h3>
        <br>
        <form method="POST" action="">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Submit</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </div>
</body>
</html>
