<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require './helpers/heading.php';
generate_header();
?>
        <section class="indexWrap">
            <figure class="left">
                <img src="./images/3fabs.png" alt="1-Bit Logo">
            </figure>
            <article class="right">
                <h1>Join the dither revolution.</h1>
                <p>Log in, or create a new account.</p>
                <div class="buttonWrap">
                <?php         ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL); ?>
                <button class="login" onclick="location.href = '<?php echo urlFor('/login.php') ?>'"><h2>Log In</h2></button>
                <button class="signup" onclick="location.href = '<?php echo urlFor('/create_acc.php') ?>'"><h2>Create Account</h2></button>
                </div>
            </article>
        </section>
<?php generate_footer(); ?>