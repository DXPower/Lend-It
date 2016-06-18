<?php
include_once "users/functions.php";
session_start();


function login($mysqli, $email, $password) {
    if ($stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        $stmt->bind_result($id, $dbPassword);
        $stmt->fetch();
        
        if ($stmt->num_rows == 1) {
            if (password_verify($password, $dbPassword)) {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION["user"] = array("id" => $id,
                                          "email" => $email);
                $_SESSION["access_token"] = hash('sha512', $agent . $dbPassword);
                
                return true;
            } else {
                return "Passwords do not match";
            }
        } else {
            return "No user found";
        }
    } else {
        return "Faulty MYSQLI statement";
    }
}

function logout() {
	$_SESSION = array();
    session_destroy();
    header('Location: ../index.php');
}

function isLoggedIn($mysqli) {
	if (isset($_SESSION["user"], $_SESSION["access_token"])) {
        $email = $_SESSION["user"]["email"];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $accessToken = $_SESSION["access_token"];
        
        if ($stmt = $mysqli->prepare("SELECT password FROM users WHERE email = ? LIMIT 1")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($password);
            $stmt->fetch();
            
            $loginCheck = hash('sha512', $agent . $password);
            
            if (hash_equals($loginCheck, $accessToken)) {
                return true;
            } else {
                return "Mismatching Access Tokens";
            }
        } else {
            return "Faulty MYSQLI statement";
        }
	} else {
		return "Missing SESSION variables";
	}
}

if(!function_exists('hash_equals'))
{
    function hash_equals($str1, $str2)
    {
        if(strlen($str1) != strlen($str2))
        {
            return false;
        }
        else
        {
            $res = $str1 ^ $str2;
            $ret = 0;
            for($i = strlen($res) - 1; $i >= 0; $i--)
            {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}