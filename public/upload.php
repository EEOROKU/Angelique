<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // File upload path
    $target_dir = "img/products/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (isset($_FILES["image"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" 
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";

            // Collect form data
            $product_id = htmlspecialchars($_POST['product_id']);
            $brand = htmlspecialchars($_POST['brand']);
            $product_name = htmlspecialchars($_POST['product_name']);
            $price = (float) htmlspecialchars($_POST['price']);
            $image_name = basename($_FILES["image"]["name"]);

            // Create product array
            $product = [
                "id" => $product_id,
                "img" => $image_name,
                "brand" => $brand,
                "name" => $product_name,
                "price" => $price
            ];

            // Load existing data from JSON file
            $json_file = 'products.json';
            $products = [];
            if (file_exists($json_file)) {
                $products = json_decode(file_get_contents($json_file), true) ?? [];
            }

            // Append new product and save back to JSON file
            $products[] = $product;
            file_put_contents($json_file, json_encode($products, JSON_PRETTY_PRINT));

            echo "Product details saved successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Upload</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="img/Logo.png">
</head>
<body>
    
    <div class="login">
        <a href="index.html"><img src="img/Logo.png" width="500px" height="500px" class="logo" alt=""></a>

        <h1>Product Upload</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="product_id">Product ID:</label>
            <input type="text" name="product_id" id="product_id" required>
            <br><br>

            <label for="brand">Brand Name:</label>
            <input type="text" name="brand" id="brand" required>
            <br><br>

            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" id="product_name" required>
            <br><br>

            <label for="price">Price:</label>
            <input type="number" name="price" id="price" required>
            <br><br>

            <label for="image">Product Image:</label>
            <input type="file" name="image" id="image" required>
            <br><br>

            <input id="submit" type="submit" value="Upload">
            <br><br>
        </form>
        <div id="exit" >
        <i class="fas fa-arrow-alt-circle-right"></i>
        <a href="logout.php">Logout</a>
        </div>
    </div>
    

</body>
</html>
