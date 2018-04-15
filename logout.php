<?php
include("_header.php");
session_start();

unset($_SESSION['user']);

?>
<h1>Logout done</h1>
<a href="main.php">Zurück zur Loginseite</a>