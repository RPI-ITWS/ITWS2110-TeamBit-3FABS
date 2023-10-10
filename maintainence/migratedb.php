<!DOCTYPE html>
<html>
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

        function printTrace($e) {
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

        try {
            $db = new mysqli("localhost","team5project","team5project","team5project");
        } catch (Exception $e) {
            echo '<p class="failure">Caught exception during DB connection:</p>';
            printTrace($e);
            exit;
        }
        if ($db->connect_error) {
            echo '<p class="failure">Connection failed: ' . $db->connect_error . "</p>";
            exit;
        } else {
            echo '<p class="success">Successfully connected to the database.</p>';
        }
        try {
            $db->query("CREATE TABLE IF NOT EXISTS metadata (id INT NOT NULL PRIMARY KEY, value VARCHAR(255) NOT NULL)");
            $db->query("INSERT IGNORE INTO metadata (id, value) VALUES (1, '0')");
            $migrationVersionResult = $db->query('SELECT value "migration_version" FROM metadata WHERE id = 1');
            // Return result as integer
            $migrationVersion = intval($migrationVersionResult->fetch_assoc()['migration_version']);
            echo '<p class="success">Current migration version: ' . $migrationVersion . '</p>';
        } catch (Exception $e) {
            echo '<p class="failure">Caught exception during metadata query:</p>';
            printTrace($e);
            exit;
        }
        $maxMigrationVersion = 2; # TODO: Update this as we add more migrations
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