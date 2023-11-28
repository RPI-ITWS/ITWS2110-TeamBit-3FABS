<?php

$servername = "localhost";
$database = "team5project";
$username = "root";
$password = "team5";

$conn = mysqli_connect($servername, $username, $password, $database);
$target_dir = "images/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file is uploaded
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        // Check file size (the value is in bytes, so 2MB is 2097152 bytes)
        if ($_FILES['img']['size'] > 2097152) {
            echo "Sorry, your file is too large. It should be less than 2MB.";
            exit;
        }

        // Check if it's an image file
        $check = getimagesize($_FILES['img']['tmp_name']);
        if($check === false) {
            echo "File is not an image.";
            exit;
        }

        // Additional validations or processing can be added here

        // Move uploaded file to a designated directory (optional)
        $target_file = $_POST['photo'];
        list($type, $target_file) = explode(';', $target_file);
        list(, $target_file)      = explode(',', $target_file);
        $target_file = base64_decode($target_file);
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["img"]["name"])). " has been uploaded.";
            $author_id = 1;
            $primary_comment_id = 1;
            $alt_text = $_POST['alt_text'];
            $image_url = $target_file;

            $stmt = $conn->prepare("INSERT INTO posts (image_url, author_id, primary_comment_id, alt_text) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $image_url, $author_id, $primary_comment_id, $alt_text);

            $stmt->execute();
            $stmt->close();

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file was uploaded or there was an error in the upload.";
    }
}

mysqli_close($conn);
?>
