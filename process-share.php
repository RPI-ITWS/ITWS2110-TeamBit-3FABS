<?php
require "./helpers/sessions.php";

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
    // Check if user is logged in
    if (!checkSessionValidity()) {
        echo "You must log in to create a post"
        exit;
    }
    $userInfo = getCurrentUserInfo();
    if ($userInfo == NULL) {
        echo "You must log in to create a post"
        exit;
    }
    // Check if file is uploaded
    if (isset($_POST['img'])) {
        // Check file size (the value is in bytes, so 2MB is 2097152 bytes)
        if ($_FILES['img']['size'] > 2097152) {
            echo "Sorry, your file is too large. It should be less than 2MB.";
            exit;
        }

        // // Check if it's an image file
        // $check = getimagesize($_FILES['img']['tmp_name']);
        // if($check === false) {
        //     echo "File is not an image.";
        //     exit;
        // } 
        $dataURL = $_POST['img'];
        $alt_text = trim($_POST['alt_text']);
        $caption = trim($_POST['caption']);

        list($type, $dataURL) = explode(';', $dataURL);
        list(, $dataURL) = explode(',', $dataURL);
        $data = base64_decode($dataURL);

        $filename = uniqid() . '.png';
        $target_file = $target_dir . $filename;

        // Additional validations or processing can be added here

        // Move uploaded file to a designated directory (optional)
        if (file_put_contents($target_file, $data)) {
            echo "The file has been uploaded.";
            $author_id = $userInfo['id'];
            $primary_comment_id = 1;
            $image_url = $target_file;

            $stmt = $conn->prepare("INSERT INTO posts (image_url, author_id, primary_comment_id, alt_text, caption) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiss", $image_url, $author_id, $primary_comment_id, $alt_text, $caption);

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
