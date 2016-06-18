<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_POST["search"], $_SESSION["user"])) {
    echo json_encode(searchContacts($mysqli, $_SESSION["user"]["id"], $_POST["search"]));
} else {
	echo "Missing POST variables!";
}