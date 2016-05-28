<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["id"])) {
	echo deleteItem($mysqli, $_POST["id"]);
} else {
	echo "Missing POST variables!";
}