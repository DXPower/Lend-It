<?php
include_once '../db_connect.php';
include_once 'functions.php';

if (isset($_POST["start"], $_SESSION["user"])) {
	$items = getLog($mysqli, $_POST["start"], $_SESSION["user"]["id"]);
	echo json_encode($items);
} else {
	echo "Missing POST variables!";
}