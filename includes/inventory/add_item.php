<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["item_name"], $_POST["lendee"], $_POST["date"], $_SESSION["user"])) {
	$dt = strtotime($_POST["date"]);
	echo json_encode(addItem($mysqli, $_SESSION["user"]["id"], $_POST["item_name"], $_POST["lendee"], $dt));
} else {
	echo "Missing POST variables!";
}