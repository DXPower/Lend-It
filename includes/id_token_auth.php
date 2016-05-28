<?php
include_once "logging.php";
include_once "db_connect.php";

session_start();

echo var_dump(error_get_last());

if (isset($_POST["id_token"])) {
	$response = file_get_contents("https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $_POST["id_token"]);
	$data = json_decode($response, true);
	
	if (array_key_exists("email", $data)) {
		login($mysqli, $data);
		echo $response;
	} else {
		logout();
	}
} else {
	logout();
}
