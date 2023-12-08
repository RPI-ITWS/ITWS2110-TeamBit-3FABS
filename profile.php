<?php
require_once './helpers/db.php';
require_once './helpers/heading.php';

$username = $_GET['username'];

$userQuery = $db->prepare("SELECT * FROM users WHERE username = :username");
$userQuery->execute(['username' => $username]);
$profileUserInfo = $userQuery->fetch(PDO::FETCH_ASSOC);

if ($profileUserInfo === null) {
    http_response_code(404);
    die("User not found");
}

$postQuery = $db->prepare("SELECT * FROM posts WHERE author_id = :userId ORDER BY created_at DESC");
$postQuery->execute(['userId' => $profileUserInfo['id']]);
$posts = $postQuery->fetchAll(PDO::FETCH_ASSOC);

if (checkSessionValidity()) {
    $userBlockedPosterQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $userBlockedPosterQuery->execute(['blockerId' => $_SESSION["userId"], 'blockeeId' => $profileUserInfo['id']]);
    $userBlockedPoster = $userBlockedPosterQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    $posterBlockedUserQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $posterBlockedUserQuery->execute(['blockerId' => $profileUserInfo['id'], 'blockeeId' => $_SESSION["userId"]]);
    $posterBlockedUser = $posterBlockedUserQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    if ($userBlockedPoster || $posterBlockedUser) {
        http_response_code(403);
        die("You are not allowed to view this profile");
    }
}

generate_header();
?>
<article class="about">
    <!-- <img src="default.png" alt="User Avatar"> -->
    <h1><?php echo htmlspecialchars($profileUserInfo["display_name"]); ?></h1>
    <p>This is the profile page for <?php echo htmlspecialchars($profileUserInfo["display_name"]); ?>. (<?php echo htmlspecialchars($profileUserInfo["username"]); ?>)</p>
    <?php if (checkSessionValidity()) : ?>
        <?php if ($_SESSION['userId'] == $profileUserInfo['id']) : ?>
            <!-- <a href="<?php echo urlFor('/settings'); ?>"><button>Edit Profile</button></a> -->
        <?php else : ?>
            <form action="<?php echo urlFor('/api_block.php'); ?>" method="get">
                <input type="hidden" name="blockee_id" value="<?php echo $profileUserInfo['id']; ?>">
                <input type="submit" value="Block">
            </form>
        <?php endif; ?>
    <?php endif; ?>

</article>
<aside class="posts">
    <?php foreach ($posts as $post) : ?>
        <a href="<?php echo urlFor("/posts/" . $post['id']); ?>">
            <img src="<?php echo htmlspecialchars(urlFor('/' . $post['image_url'])); ?>" alt="<?php echo htmlspecialchars($post['alt_text']); ?>">
        </a>
    <?php endforeach; ?>
</aside>
<?php generate_footer(); ?>