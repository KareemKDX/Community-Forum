<?php
$db = new mysqli("db", "uppgift2user", "password", "uppgift2");

if($db->connect_errno) {
    echo "failed to connect to MySQL: " . $db->connect_error;
}

?>