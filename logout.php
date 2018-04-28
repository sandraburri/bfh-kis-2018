<?php
include("_header.php");
session_start();

unset($_SESSION['user']);
unset($_SESSION['functionID']);
unset($_SESSION['staffID']);

header("Location: main.php");
?>
