<?php

$servername = "localhost";
$database = "team5project";
$username = "root";
$password = "team5";

$conn = mysqli_connect($servername, $username, $password, $database);

<form method="post" action="<?php echo urlFor('/process-share.php') ?>" enctype="multipart/form-data">

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
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["img"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file was uploaded or there was an error in the upload.";
    }
}
?>
