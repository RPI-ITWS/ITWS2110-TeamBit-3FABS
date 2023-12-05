<?php
require './helpers/heading.php';
generate_header();
?>
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
                        echo '<p>Account created successfully!</p>';
                        createSession($db->lastInsertId());
                        if (isset($_SESSION['login_redirect'])) {
                            $redirect = $_SESSION['login_redirect'];
                            echo '<p>You are being redirected to the previous page.</p>';
                        } else {
                            $redirect = urlFor('/');
                            echo '<p>You are being redirected to the home page.</p>';
                        }
                        echo '<meta http-equiv="refresh" content="2;url=' . $redirect . '">';
                    } else {
                        echo "ERROR: Could not execute $sql. " . var_export($stmt->errorInfo(), true);
                    }
                }
            }
        }
    }
    ?>
</div>
<?php generate_footer(); ?>