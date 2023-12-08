<?php
require './helpers/heading.php';
generate_header();
?>
<section class="indexWrap">
    <figure class="left">
        <img src="<?php echo urlFor('/images/3fabs.png') ?>" alt="1-Bit Logo">
    </figure>
    <article class="right">
        <h1>Join the dither revolution.</h1>
        <p>Log in, or create a new account.</p>
        <div class="buttonWrap">
            <button class="login" onclick="location.href = '<?php echo urlFor('/login.php') ?>'">
                <h2>Log In</h2>
            </button>
            <button class="signup" onclick="location.href = '<?php echo urlFor('/create_acc.php') ?>'">
                <h2>Create Account</h2>
            </button>
        </div>
    </article>
</section>
<?php generate_footer(); ?>