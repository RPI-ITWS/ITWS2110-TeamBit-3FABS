<!DOCTYPE html>
<html lang="en">
    <head>
        <title>DB Migration</title>
        <style>
            .success {
                color: green;
            }
            .failure {
                color: red;
            }
            .warning {
                color: orange;
            }
        </style>
    </head>
    <body>
        <h1>DB Migration</h1>
        <?php
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        function printTrace(Throwable $e) {
            $message = $e->getMessage();
            $trace = $e->getTraceAsString();
            echo <<<EOD
            <pre class="failure">
            Exception: <b>$message</b>

            Stack trace (most recent call first):
            $trace
            </pre>
            EOD;
        }

        set_exception_handler(function (Throwable $e) {
            echo '<p class="failure">Caught exception:</p>';
            printTrace($e);
            exit;
        });

        try {
            $db = new PDO('mysql:host=localhost;dbname=team5project', 'team5project', 'team5project');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            echo '<p class="failure">Caught exception during DB connection:</p>';
            printTrace($e);
            exit;
        }
        echo '<p class="success">Successfully connected to the database.</p>';
        try {
            $db->query("CREATE TABLE IF NOT EXISTS metadata (id INT NOT NULL PRIMARY KEY, value VARCHAR(255) NOT NULL)");
            $db->query("INSERT IGNORE INTO metadata (id, value) VALUES (1, '0')");
            $migrationVersionResult = $db->query('SELECT value "migration_version" FROM metadata WHERE id = 1');
            // Return result as integer
            $migrationVersion = intval($migrationVersionResult->fetch()['migration_version']);
            echo '<p class="success">Current migration version: ' . $migrationVersion . '</p>';
        } catch (Exception $e) {
            echo '<p class="failure">Caught exception during metadata query:</p>';
            printTrace($e);
            exit;
        }
        $maxMigrationVersion = 11; # TODO: Update this as we add more migrations
        if ($migrationVersion < 1) {
            try {
                $db->query('
                    CREATE TABLE IF NOT EXISTS users (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(64) NOT NULL UNIQUE,
                        email VARCHAR(255) UNIQUE DEFAULT NULL,
                        first_name VARCHAR(255) DEFAULT NULL,
                        last_name VARCHAR(255) DEFAULT NULL,
                        is_admin BOOLEAN NOT NULL DEFAULT FALSE
                    )
                ');
                $db->query('
                    CREATE TABLE IF NOT EXISTS posts (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        image_url VARCHAR(255) NOT NULL,
                        author_id INT NOT NULL,
                        FOREIGN KEY (author_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                CREATE TABLE IF NOT EXISTS comments (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        post_id INT NOT NULL,
                        author_id INT NOT NULL,
                        FOREIGN KEY (post_id) REFERENCES posts(id),
                        FOREIGN KEY (author_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        content TEXT NOT NULL
                    )
                ');
                $db->query('
                    CREATE TABLE IF NOT EXISTS sessions (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id),
                        token VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        expires_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #1:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 2) {
            try {
                $db->query('
                    CREATE INDEX idx_comments_post_id ON comments (post_id);
                ');
                $db->query('
                    CREATE INDEX idx_comments_author_id ON comments (author_id);
                ');
                $db->query('
                    CREATE INDEX idx_posts_author_id ON posts (author_id);
                ');
                $db->query('
                    ALTER TABLE comments ADD COLUMN parent_comment_id INT DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE comments ADD FOREIGN KEY (parent_comment_id) REFERENCES comments(id);
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #2:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 3) {
            try {
                // Add new table for likes
                $db->query('
                    CREATE TABLE IF NOT EXISTS likes (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        post_id INT NOT NULL,
                        author_id INT NOT NULL,
                        FOREIGN KEY (post_id) REFERENCES posts(id),
                        FOREIGN KEY (author_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                    CREATE INDEX idx_likes_post_id ON likes (post_id);
                ');
                $db->query('
                    CREATE INDEX idx_likes_author_id ON likes (author_id);
                ');
                // Add new table for comment likes
                $db->query('
                    CREATE TABLE IF NOT EXISTS comment_likes (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        comment_id INT NOT NULL,
                        author_id INT NOT NULL,
                        FOREIGN KEY (comment_id) REFERENCES comments(id),
                        FOREIGN KEY (author_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                    CREATE INDEX idx_comment_likes_comment_id ON comment_likes (comment_id);
                ');
                // Add new table for follows
                $db->query('
                    CREATE TABLE IF NOT EXISTS follows (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        follower_id INT NOT NULL,
                        followee_id INT NOT NULL,
                        FOREIGN KEY (follower_id) REFERENCES users(id),
                        FOREIGN KEY (followee_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                    CREATE INDEX idx_follows_follower_id ON follows (follower_id);
                ');
                $db->query('
                    CREATE INDEX idx_follows_followee_id ON follows (followee_id);
                ');
                // Add new table for blocked users
                $db->query('
                    CREATE TABLE IF NOT EXISTS blocks (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        blocker_id INT NOT NULL,
                        blockee_id INT NOT NULL,
                        FOREIGN KEY (blocker_id) REFERENCES users(id),
                        FOREIGN KEY (blockee_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                    CREATE INDEX idx_blocks_blocker_id ON blocks (blocker_id);
                ');
                // Add new table for hashtags
                $db->query('
                    CREATE TABLE IF NOT EXISTS hashtags (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL UNIQUE
                    )
                ');
                // Add new table for post-hashtag relationships
                $db->query('
                    CREATE TABLE IF NOT EXISTS post_hashtags (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        post_id INT NOT NULL,
                        hashtag_id INT NOT NULL,
                        FOREIGN KEY (post_id) REFERENCES posts(id),
                        FOREIGN KEY (hashtag_id) REFERENCES hashtags(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                $db->query('
                    CREATE INDEX idx_post_hashtags_post_id ON post_hashtags (post_id);
                ');
                $db->query('
                    CREATE INDEX idx_post_hashtags_hashtag_id ON post_hashtags (hashtag_id);
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #3:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 4) {
            try {
                // Add profile_image_path to users table
                $db->query('
                    ALTER TABLE users ADD COLUMN profile_image_path VARCHAR(255) DEFAULT NULL;
                ');
                // Add primary_comment to posts table
                $db->query('
                    ALTER TABLE posts ADD COLUMN primary_comment_id INT DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE posts ADD FOREIGN KEY (primary_comment_id) REFERENCES comments(id);
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #4:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 5) {
            try {
                // Remove first and last name columns on users table and add display name
                $db->query('
                    ALTER TABLE users DROP COLUMN first_name;
                ');
                $db->query('
                    ALTER TABLE users DROP COLUMN last_name;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN display_name VARCHAR(255) DEFAULT NULL;
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #5:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 6) {
            try {
                // Remove first and last name columns on users table and add display name
                $db->query('
                    CREATE TABLE IF NOT EXISTS post_reports (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        reported_post_id INT NOT NULL,
                        reporter_id INT NOT NULL,
                        message TEXT NOT NULL,
                        FOREIGN KEY (reported_post_id) REFERENCES posts(id),
                        FOREIGN KEY (reporter_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        acted_on TIMESTAMP DEFAULT NULL,
                        moderator_id INT NOT NULL,
                        FOREIGN KEY (moderator_id) REFERENCES users(id),
                        accepted BOOLEAN DEFAULT NULL,
                        moderator_message TEXT DEFAULT NULL
                    )
                ');
                $db->query('
                    CREATE INDEX idx_post_reports_reported_post_id ON post_reports (reported_post_id);
                ');
                $db->query('
                    CREATE INDEX idx_post_reports_reporter_id ON post_reports (reporter_id);
                ');
                $db->query('
                    CREATE INDEX idx_post_reports_moderator_id ON post_reports (moderator_id);
                ');
                $db->query('
                    CREATE TABLE IF NOT EXISTS comment_reports (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        reported_comment_id INT NOT NULL,
                        reporter_id INT NOT NULL,
                        message TEXT NOT NULL,
                        FOREIGN KEY (reported_comment_id) REFERENCES comments(id),
                        FOREIGN KEY (reporter_id) REFERENCES users(id),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        acted_on TIMESTAMP DEFAULT NULL,
                        moderator_id INT NOT NULL,
                        FOREIGN KEY (moderator_id) REFERENCES users(id),
                        accepted BOOLEAN DEFAULT NULL,
                        moderator_message TEXT DEFAULT NULL
                    )
                ');
                $db->query('
                    CREATE INDEX idx_comment_reports_reported_comment_id ON comment_reports (reported_comment_id);
                ');
                $db->query('
                    CREATE INDEX idx_comment_reports_reporter_id ON comment_reports (reporter_id);
                ');
                $db->query('
                    CREATE INDEX idx_comment_reports_moderator_id ON comment_reports (moderator_id);
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #6:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 7) {
            try {
                // Add new columns to user table
                $db->query('
                    ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN website VARCHAR(255) DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN password_hash VARCHAR(128) DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN password_salt VARCHAR(128) DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE posts ADD COLUMN alt_text VARCHAR(255) DEFAULT NULL;
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #7:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 8) {
            try {
                // Make email mandatory and unique
                $db->query('
                    ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NOT NULL UNIQUE;
                ');
                // Make email verified default to false and do not allow null
                $db->query('
                    ALTER TABLE users MODIFY COLUMN email_verified BOOLEAN NOT NULL DEFAULT FALSE;
                ');
                // Remove email verification token from users table
                $db->query('
                    ALTER TABLE users DROP COLUMN email_verification_token;
                ');
                // Add new table for email verification tokens
                $db->query('
                    CREATE TABLE IF NOT EXISTS email_verification_tokens (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id),
                        token VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        expires_at TIMESTAMP NOT NULL
                    )
                ');
                // Make other timestamp columns not null
                $db->query('
                    ALTER TABLE posts MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE comments MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE comments MODIFY COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE sessions MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE sessions MODIFY COLUMN expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE likes MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE comment_likes MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE follows MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE blocks MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE post_hashtags MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE post_reports MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE post_reports MODIFY COLUMN acted_on TIMESTAMP DEFAULT NULL;
                ');
                $db->query('
                    ALTER TABLE comment_reports MODIFY COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
                ');
                $db->query('
                    ALTER TABLE comment_reports MODIFY COLUMN acted_on TIMESTAMP DEFAULT NULL;
                ');
                // Add new table for password reset tokens
                $db->query('
                    CREATE TABLE IF NOT EXISTS password_reset_tokens (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id),
                        token VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        expires_at TIMESTAMP NOT NULL
                    )
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #8:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 9) {
            try {
                // Make display name not null and make it use the username by default
                $db->query('
                    ALTER TABLE users MODIFY COLUMN display_name VARCHAR(255) NOT NULL DEFAULT "username";
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #9:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 10) {
            try {
                // Add caption to posts table
                $db->query('
                    ALTER TABLE posts ADD COLUMN caption TEXT DEFAULT NULL;
                ');
                // Remove parent comment ID from posts table
                // Drop foreign key first
                $db->query('
                    ALTER TABLE posts DROP FOREIGN KEY posts_ibfk_2;
                ');
                $db->query('
                    ALTER TABLE posts DROP COLUMN primary_comment_id;
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #10:</p>';
                printTrace($e);
                exit;
            }
        }
        if ($migrationVersion < 11) {
            try {
                // Add updated at to posts table
                $db->query('
                    ALTER TABLE posts ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
                ');
                // Copy post creation time to updated at for now
                $db->query('
                    UPDATE posts SET updated_at = created_at;
                ');
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during migration #11:</p>';
                printTrace($e);
                exit;
            }

        }
        if ($migrationVersion != $maxMigrationVersion) {
            try {
                $db->query("UPDATE metadata SET value = " . $maxMigrationVersion . " WHERE id = 1");
                echo '<p class="success">Successfully migrated to version ' . $maxMigrationVersion . '</p>';
            } catch (Exception $e) {
                echo '<p class="failure">Caught exception during metadata update:</p>';
                printTrace($e);
                exit;
            }
        } else {
            echo '<p class="warning">Database is already up to date.</p>';
        }
        ?> 
    </body>
</html>
