<?php
include_once "users/functions.php";
session_start();


function login($mysqli, $data) {
	if (!doesUserEmailExist($mysqli, $data["email"])) {
		createUser($mysqli, $data["email"]);
	}
	
	$_SESSION["user"] = array("id" => getUserID($mysqli, $data["email"]),
							 "email" => $data["email"],
							 "name" => $data["name"],
							 "picture" => $data["picture"]);
	
	//header("Location: http://www.inventory.nerdchill.com/home.php");
}

function logout() {
	$_SESSION["user"] = null;
	unset($_SESSION['access_token']);
	header("Location: ../index.php");
}

function isLoggedIn() {
	if (isset($_SESSION["user"]) && $_SESSION["user"] != null) {
		return true;
	} else {
		return false;
	}
}