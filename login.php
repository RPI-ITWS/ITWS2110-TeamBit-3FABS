<?php
require './helpers/heading.php';
generate_header("Login", true);
?>
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
                    <input type="password" name="password" id="password">
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
                echo '<p>Invalid Username or Password.</p>'; // Don't leak that the account doesn't exist
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
                        createSession($row["id"]);
                        if (isset($_SESSION['login_redirect'])) {
                            $redirect = $_SESSION['login_redirect'];
                            echo '<p>You are being redirected to the previous page.</p>';
                        } else {
                            $redirect = urlFor('/');
                            echo '<p>You are being redirected to the home page.</p>';
                        }
                        echo '<meta http-equiv="refresh" content="2;url=' . $redirect . '">';
                    } else {
                        // Passwords don't match
                        echo '<p>Invalid Username or Password.</p>';
                    }
                }
            }
        }
        // Ensure the usernmae they wish to use is free
        ?>
    </div>
<?php generate_footer(); ?>