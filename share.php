<?php
require './helpers/heading.php';
require './helpers/sessions.php';
generate_header();
$loggedInUser = getCurrentUserInfo();
if ($loggedInUser === NULL) {
    header('Location: ./login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>1-Bit</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
</head>

<body>
    <!-- <header>

        <ul>
            <li><a href="./index.php">HOME</a></li>
            <li><a href="/project/browse">BROWSE</a></li>
            <li><a href="./share.html" class="selected">SHARE</a></li>
            <li><a href="./login.php">LOGIN</a></li>
            <li><a href="./create_acc.php">CREATE ACCOUNT</a></li>
        </ul>
    </nav>
</header> -->
<main class="content" id="content">
    <section class="editBar">
       <div class="slidersContainer"> 
           <div class="sliderWrap">
               <p>Threshold:</p>
               <input id="thresholdSlider" class="slider" type="range" min="1" max="255" step="1" value="127"
                   oninput="ditherCall()">
               <p id="thresholdValue">127</p>
           </div>
           <div class="sliderWrap">
               <p>Contrast:</p>
               <input id="contrastSlider" class="slider" type="range" min="0" max="200" step="1" value="100"
                   oninput="ditherCall()">
               <p id="contrastValue">100%</p>
           </div>
       </div>
            <select id="dithering" oninput="ditherCall()">
                <option value="bayer4x4">bayer 4x4</option>
                <option value="bayer2x2">bayer 2x2</option>
                <option value="floydsteinberg">floyd-steinberg</option>
                <option value="atkinson">atkinson</option>
                <option value="none">none</option>
                <option value="randomBayer">random bayer</option>
                <option value="randomAtkinson">random atkinson</option>
                <option value="randomNone">random none</option>
            </select>
        </div>
        <figure>
        <canvas id="display" width="300px" height="300px"></canvas>
        </figure>
        <form method="post" action="process-share.php" enctype="multipart/form-data">
            <label for="img">Select image:</label>
            <input type="file" id="img" name="img" accept="image/*" required>
            <input type="submit" value="Upload Image" name="submit">

            <br>
            <label for="caption">Share Your Thoughts!</label>
            <br>
            <textarea id="caption" name="caption" rows="4" cols="50" placeholder="Enter text here...">
            </textarea>
            <textarea id="alt-text" name="alt-text" rows="4" cols="50" placeholder="Enter alt text here...">
            </textarea>
            <input type="submit" value="post" name="submit">
        </form>  
    </section>  
    </main>

    <script src="./Javascript/Dither.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // var fileInput = document.getElementById('img');
            // var fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
            // if (fileSize > 2) { //change the value??
            //     alert('File size must not exceed 2 MB');
            // }
            
            var picture = document.getElementById('display');
            var caption = document.getElementById('caption').value;
            var altText = document.getElementById('alt-text').value;
            var dataURL = picture.toDataURL();

            var formData = new FormData();
            formData.append('img', dataURL);
            formData.append('caption', caption);
            formData.append('alt_text', altText);

            fetch('process-share.php', {
                method: 'POST',
                body: formData
            })
            .then(response=> {
                alert('File Successfully Uploaded!')
                window.location.reload();
            })
            .catch(error=> {
                alert('There was an error uploading your file.')
            })
        });
    </script>
        
</body>

</html>