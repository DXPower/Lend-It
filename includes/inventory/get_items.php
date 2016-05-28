<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_SESSION["user"])) {
	$items = getItems($mysqli, $_SESSION["user"]["id"]);
	echo json_encode($items);
} else {
	echo "Missing POST variables!";
}