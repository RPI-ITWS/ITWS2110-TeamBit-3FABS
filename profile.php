<?php
require_once './helpers/db.php';
require_once './helpers/heading.php';

$username = $_GET['username'];

$userQuery = $db->prepare("SELECT * FROM users WHERE username = :username");
$userQuery->execute(['username' => $username]);
$userInfo = getCurrentUserInfo();

$postQuery = $db->prepare("SELECT * FROM posts WHERE author_id = :userId ORDER BY created_at DESC");
$postQuery->execute(['userId' => $userInfo['id']]);
$posts = $postQuery->fetchAll(PDO::FETCH_ASSOC);

if ($userInfo !== null) {
    $userBlockedPosterQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $userBlockedPosterQuery->execute(['blockerId' => $_SESSION["userId"], 'blockeeId' => $author['id']]);
    $userBlockedPoster = $userBlockedPosterQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    $posterBlockedUserQuery = $db->prepare("SELECT COUNT(*) FROM blocks WHERE blocker_id = :blockerId AND blockee_id = :blockeeId");
    $posterBlockedUserQuery->execute(['blockerId' => $author['id'], 'blockeeId' => $_SESSION["userId"]]);
    $posterBlockedUser = $posterBlockedUserQuery->fetch(PDO::FETCH_NUM)[0] > 0;
    if ($userBlockedPoster || $posterBlockedUser) {
        http_response_code(403);
        die("You are not allowed to view this profile");
    }
}

generate_header();
?>
        <!--<p>Where you can either log in or see your profile, and the top "login" thing would become your name if you sign in.</p>
        <p>You should also be able to copy the link up top to share your profile.</p>-->
        <article class="about">
            <!-- <img src="default.png" alt="User Avatar"> -->
            <h1>
                <?php echo htmlspecialchars($username); ?>
            </h1>
            <p>This is the profile page for
                <?php echo htmlspecialchars($username); ?>

            </p>
        </article>
        <aside class="posts">
            <?php foreach ($posts as $post): ?>
                <img src="<?php echo htmlspecialchars(urlFor('/' . $post['image_url'])); ?>"
                    alt="<?php echo htmlspecialchars($post['alt_text']); ?>">
            <?php endforeach; ?>
        </aside>
<?php generate_footer(); ?>