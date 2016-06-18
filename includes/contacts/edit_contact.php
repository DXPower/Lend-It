<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["id"], $_POST["name"], $_POST["phone_number"], $_POST["email"])) {
	echo editContact($mysqli, $_POST["id"], $_POST["name"], $_POST["phone_number"], $_POST["email"]);
} else {
	echo "Missing POST variables!";
}