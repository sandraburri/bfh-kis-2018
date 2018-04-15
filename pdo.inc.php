<?php

if (strpos($_SERVER["CONTEXT_DOCUMENT_ROOT"], "xampp") > 0) {
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'medizininformatik';
    
} else {
    $hostname = 'localhost';
    $username = 'macmanu_groupB';
    $password = 'jERz742j+!GHi87';
    $dbname = 'macmanu_groupB';
}

try{
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
    echo $e->getMessage();
}

?>