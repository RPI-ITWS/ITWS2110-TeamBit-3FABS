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
        <div id="accountMessage"></div>
    </main>
</body>


    <?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // servername => localhost
        // username => root
        // password => empty
        // database name => staff
        $conn = mysqli_connect("localhost", "root", "team5", "team5project");

        // Check connection
        if ($conn === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        // Taking all 2 values from the form data(input)
        $id = null;
        $user_name = mysqli_real_escape_string($conn, $_REQUEST["username"]);
        $password = mysqli_real_escape_string($conn, $_REQUEST["password"]);
        





        //First check if username exist

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $result = $stmt->get_result();  

        if (mysqli_num_rows($result) == 0) {
            echo '<script>document.getElementById("accountMessage").innerHTML = "No account with this username exists";</script>';
            mysqli_close($conn);
        }
        else{
            //Account exists
            while ($row = $result->fetch_assoc()) {
                $hashed_password = $row['password_hash']; // Assuming 'password' is the column name for hashed passwords
                $salt = $row['password_salt'];
                $salted = $salt.$password;
                $salted_hashed = hash('sha512', $salted);

                // Verify the provided password against the hashed password in the database
                if ($salted_hashed == $hashed_password) {
                    // Passwords match, user authenticated
                    
                    echo '<script>document.getElementById("accountMessage").innerHTML = "Password Match!";</script>';
                    // Perform further actions or grant access
                } else {
                    // Passwords don't match
                    echo '<script>document.getElementById("accountMessage").innerHTML = "Invalid Password.";</script>';
                }


        }
    }
}
        // Ensure the usernmae they wish to use is free
         ?>


    
</html>