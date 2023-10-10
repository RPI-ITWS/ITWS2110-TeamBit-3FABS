<!DOCTYPE html>
<html>
    <head>
        <title>DB Migration</title>
    </head>
    <body>
        <h1>DB Migration</h1>
        <?php

        $db = new mysqli("localhost","team5project","team5project","team5project");
        if ($db->connect_error) {
            echo "<p>Connection failed: " . $db->connect_error . "</p>\n";
            exit;
        }

        ?> 
    </body>
</html>