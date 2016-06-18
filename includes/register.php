<?php
include_once "db_connect.php";
include_once "logging.php";
session_start();

if (isset($_POST["email"], $_POST["password"])) {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING); // Password is already hashed on the client side
    
    if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            echo "Email already exists";
            $stmt->close();
            return;
        }
    } else {
        echo "e1";
            $stmt->close();
        return;
    }
    
    // Creates a salt automatically
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    if ($stmt2 = $mysqli->prepare("INSERT INTO users SET email = ?, password = ?")) {
        $stmt2->bind_param("ss", $email, $hashedPassword);
        $stmt2->execute();
        
        echo "PHP register success";
    }
}