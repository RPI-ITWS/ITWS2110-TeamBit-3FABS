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

        $db = new mysqli("localhost","team5project","team5project","team5project");
        if ($db->connect_error) {
            echo '<p class="failure">Connection failed: ' . $db->connect_error . "</p>";
            exit;
        } else {
            echo '<p class="success">Successfully connected to the database.</p>';
        }
        
        $db->query("CREATE TABLE IF NOT EXISTS metadata (id INT NOT NULL PRIMARY KEY, value VARCHAR(255) NOT NULL)");
        $db->query("INSERT IGNORE INTO metadata (id, value) VALUES (1, '0')");
        $migrationVersionResult = $db->query('SELECT value "migration_version" FROM metadata WHERE id = 1');
        // Return result as integer
        $migrationVersion = intval($migrationVersionResult->fetch_assoc()['migration_version']);
        echo '<p class="success">Current migration version: ' . $migrationVersion . '</p>';
        $maxMigrationVersion = 1; # TODO: Update this as we add more migrations
        if ($migrationVersion < 1) {
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
        }
        if ($migrationVersion != $maxMigrationVersion) {
            $db->query("UPDATE metadata SET value = " . $maxMigrationVersion . " WHERE id = 1");
            echo '<p class="success">Successfully migrated to version ' . $maxMigrationVersion . '</p>';
        } else {
            echo '<p class="warning">Database is already up to date.</p>';
        }
        ?> 
    </body>
</html>