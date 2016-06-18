<?php
include_once "db_connect.php";
include_once "logging.php";

if (isset($_POST["email"], $_POST["password"])) {
    $status = login($mysqli, $_POST["email"], $_POST["password"]);
    
    if ($status) {
        echo $status;
    } else {
        echo $status;
    }
}