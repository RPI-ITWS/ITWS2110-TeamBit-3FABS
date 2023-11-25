<!DOCTYPE html>
<html lang="en">
<?php require './helpers/urls.php' ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>1-Bit</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
</head>

<body>
<header>
    <ul>
        <li><a href="./index.php" class="navi">HOME</a></li>
        <li><a href="./browse.html" class="navi">BROWSE</a></li>
        <li><a href="./share.html" class="navi">SHARE</a></li>
        <li><a href="./account.html" class="navi">LOGIN</a></li>
    </ul>
</header>
<main class="content">
    <section class="indexWrap">
        <figure class="left">
            <img src="./images/bit_logo_dither_trans.png" alt="1-Bit Logo">
        </figure>
        <article class="right">
            <form action="login.php" method="post">
                <p>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username">
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="text" name="password" id="password">
                </p>
                <input type="submit" value="Submit">
            </form>
        </article>
    </section>
    <div id="accountMessage">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Create connection
            require_once './helpers/db.php';
            global $db;

            // Taking all 2 values from the form data(input)
            $id = null;
            $user_name = $_REQUEST["username"];
            $password = $_REQUEST["password"];

            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $user_name]);
            $row_count = $stmt->rowCount();

            if ($row_count == 0) {
                echo '<p>No account with this username exists</p>';
            } else {
                //Account exists
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $hashed_password = $row['password_hash']; // Assuming 'password' is the column name for hashed passwords
                    $salt = $row['password_salt'];
                    $salted = $salt.$password;
                    $salted_hashed = hash('sha512', $salted);

                    // Verify the provided password against the hashed password in the database
                    if ($salted_hashed == $hashed_password) {
                        // Passwords match, user authenticated
                        echo '<p>Password Match!</p>';
                        // Perform further actions or grant access
                    } else {
                        // Passwords don't match
                        echo '<p>Invalid Password.</p>';
                    }
                }
            }
        }
        // Ensure the usernmae they wish to use is free
        ?>
    </div>
</main>
</body>
</html>