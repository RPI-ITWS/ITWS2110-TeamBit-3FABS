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
                            <label for="displayName">Display Name</label>
                            <input type="text" name="display_name" id="displayName">
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
        <div id="accountMessage"></div>
    </main>
</body>


    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // servername => localhost
        // username => root
        // password => empty
        // database name => staff
        $conn = mysqli_connect("localhost", "root", "", "team5project");

        // Check connection
        if ($conn === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        // Taking all 5 values from the form data(input)
        $id = null;
        $display_name = mysqli_real_escape_string($conn, $_REQUEST["display_name"]);
        if($display_name == ''){
            $display_name = mysqli_real_escape_string($conn, $_REQUEST["username"]);
        }
        $email = mysqli_real_escape_string($conn, $_REQUEST["email"]);
        $user_name = mysqli_real_escape_string($conn, $_REQUEST["username"]);
        $password = mysqli_real_escape_string($conn, $_REQUEST["password"]);
        $salt = bin2hex(random_bytes(16));

        // Ensure the usernmae they wish to use is free
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $result = $stmt->get_result();  

        if (mysqli_num_rows($result) > 0) {
            echo '<script>document.getElementById("accountMessage").innerHTML = "This username is already in use!";</script>';
            mysqli_close($conn);
        }
        else{
            //Ensure email hasnt been used already
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();  

            if (mysqli_num_rows($result) > 0) {
                echo '<script>document.getElementById("accountMessage").innerHTML = "This email is already associated to an account!";</script>';
                mysqli_close($conn);
                
                
            } else {




                $salted =$salt.$password;

                $hashed = hash('sha512', $salted);
                $sql = "INSERT INTO users (id, username, email, is_admin, profile_image_path, display_name, bio, website, password_hash, password_salt, email_verified, email_verification_token) 
                VALUES (NULL, ?, ?, 0, NULL, ?, NULL, NULL, ?, ?, NULL, NULL)";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $user_name, $email, $display_name, $hashed, $salt);
                    if (mysqli_stmt_execute($stmt)) {
                        echo '<script>document.getElementById("accountMessage").innerHTML = "Account Created Successfully!";</script>';
                    } else {
                        echo "ERROR: Could not execute $sql. " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "ERROR: Could not prepare statement. " . mysqli_error($conn);
                }
            }
        }
    } ?>


    
</html>