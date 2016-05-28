<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["id"], $_POST["item_name"], $_POST["lendee"], $_POST["date"], $_SESSION["user"])) {
	echo var_dump($_POST);
	$dt = strtotime($_POST["date"]);
	echo editItem($mysqli, $_POST["id"], $_POST["item_name"], $_POST["lendee"], $dt);
} else {
	echo "Missing POST variables!";
}