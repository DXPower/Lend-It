<?php
include_once '../db_connect.php';
include_once 'functions.php';
session_start();

if (isset($_SESSION["user"])) {
    echo json_encode(getContacts($mysqli, $_SESSION["user"]["id"]));
} else {
	echo "Missing POST variables!";
}