<?php
require_once './helpers/db.php';
require_once './helpers/heading.php';

$username = $_GET['username'];

$userQuery = $db->prepare("SELECT * FROM users WHERE username = :username");
$userQuery->execute(['username' => $username]);
$profileUserInfo = $userQuery->fetch(PDO::FETCH_ASSOC);

$postQuery = $db->prepare("SELECT * FROM posts WHERE author_id = :userId ORDER BY created_at");
$postQuery->execute(['userId' => $profileUserInfo['id']]);
$posts = $postQuery->fetchAll(PDO::FETCH_ASSOC);

generate_header();
?>
<section class="profile">
    <article class="about">
        <img src="./images/download.png" alt="User Avatar">
        <h1>Sample</h1>
        <p>Sample profile</p>
    </article>
    <aside class="posts">
        <?php foreach ($posts as $post) : ?>
            <img src="<?php echo htmlspecialchars(urlFor('./images/' . $post['image_url'])); ?>" alt="<?php echo htmlspecialchars($post['alt_text']); ?>">
        <?php endforeach; ?>
    </aside>
</section>
<?php generate_footer(); ?>