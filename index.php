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
                <img src="./images/3fabs.png" alt="1-Bit Logo">
            </figure>
            <article class="right">
                <h1>Join the dither revolution.</h1>
                <p>Log in, or create a new account.</p>
                <div class="buttonWrap">
                <button class="login" onclick="location.href = '<?php echo urlFor('/login.php') ?>'"><h2>Log In</h2></button>
                <button class="signup" onclick="location.href = '<?php echo urlFor('/create_acc.php') ?>'"><h2>Create Account</h2></button>
                </div>
            </article>
        </section>
    </main>
</body>
</html>