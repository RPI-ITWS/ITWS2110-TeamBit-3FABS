<?php
$db = new PDO('mysql:host=localhost;dbname=team5project', 'team5project', 'team5project');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>