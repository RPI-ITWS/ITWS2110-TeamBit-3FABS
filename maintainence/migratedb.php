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
        ?> 
    </body>
</html>