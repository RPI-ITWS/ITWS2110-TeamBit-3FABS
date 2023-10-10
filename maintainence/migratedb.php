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

        ?> 
    </body>
</html>