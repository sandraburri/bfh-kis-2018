<?php
include("_header.php");
session_start();

unset($_SESSION['user']);
unset($_SESSION['functionID']);
 header("Location: main.php");
?>
