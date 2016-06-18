<?php
include_once '../db_connect.php';
include_once 'functions.php';
include_once '../contacts/functions.php';

session_start();

if (isset($_POST["item_name"], $_POST["lendee"], $_POST["date"], $_SESSION["user"])) {
    $contact;
    
    if (isset($_POST["contact_id"])) {
        $contact = $_POST["contact_id"];
    } else if (isset($_POST["lendee"], $_POST["phone_number"], $_POST["email"])) {
        $contact = createContact($mysqli, $_SESSION["user"], $_POST["lendee"], $_POST["phone_number"], $_POST["email"]);
    } else {
        echo "Invalid contact information";
        return;
    }

    if (!is_null($contact)) {
        $dt = strtotime($_POST["date"]);
        $optionals = array();

        if (isset($_FILES["checkout_image"])) {
            $optionals["image"] = $_FILES["checkout_image"];
        }

        echo json_encode(addItem($mysqli, $_SESSION["user"]["id"], $_POST["item_name"], $contact, $dt, $optionals));
    }
} else {
	echo "Missing POST variables!";
}