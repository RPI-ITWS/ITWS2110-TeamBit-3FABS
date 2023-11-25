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
            <li><a href="./index.php" class="navi selected">HOME</a></li>
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
                <form action="create_acc.php" method="post">
                    <p>
                        <label for="displayname">Display Name (optional)</label>
                        <input type="text" name="displayname" id="displayname">
                    </p>

                    <p>
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email">
                    </p>

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
                require_once './helpers/db.php';
                global $db;

                $display_name = $_REQUEST["displayname"] ?? $_REQUEST["username"];
                $email = $_REQUEST["email"];
                $user_name = $_REQUEST["username"];
                $password = $_REQUEST["password"];
                try {
                    $salt = bin2hex(random_bytes(16));
                } catch (Exception $e) {
                    echo '<p>Sorry, but the computer running this code sucks and should be sent to the local dumpster. If you\'re seeing this, it means that you need to go bribe RPI IT to give us computers that came after the 90s.</p>';
                    die();
                }

                // Check if username exists
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $result = $stmt->execute(['username' => $user_name]);

                if ($result === false) {
                    die("Error executing the query: " . var_export($stmt->errorInfo(), true));
                }

                if ($stmt->rowCount() > 0) {
                    echo '<p>"This username is already in use!"</p>';
                } else {
                    // Check if email exists
                    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
                    $result = $stmt->execute(['email' => $email]);

                    if ($result === false) {
                        die("Error executing the query: " . var_export($stmt->errorInfo(), true));
                    }

                    if ($stmt->rowCount() > 0) {
                        echo '<p>This email is already associated with an account!</p>';
                    } else {
                        $salted = $salt . $password;
                        $hashed = hash('sha512', $salted);
                        $sql = "INSERT INTO users (username, email, display_name, password_hash, password_salt) 
                    VALUES (:username, :email, :display_name, :password_hash, :password_salt)";
                        $stmt = $db->prepare($sql);
                        if ($stmt) {
                            if ($stmt->execute(['username' => $user_name, 'email' => $email, 'display_name' => $display_name, 'password_hash' => $hashed, 'password_salt' => $salt])) {
                                echo '<script>document.getElementById("accountMessage").innerHTML = "Account Created Successfully!";</script>';
                            } else {
                                echo "ERROR: Could not execute $sql. " . var_export($stmt->errorInfo(), true);
                            }
                        }
                    }
                }
            }
            ?>
        </div>
    </main>
</body>

</html>
