<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["name"], $_POST["phone_number"], $_POST["email"], $_SESSION["user"])) {
	$id = createContact($mysqli, $_SESSION["user"]["id"], $_POST["name"], $_POST["phone_number"], $_POST["email"]);
	echo $id;
} else {
    echo var_dump($_SESSION);
	echo "Missing POST variables!";
}