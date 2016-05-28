<?php
session_start();

function doesUserEmailExist($mysqli, $email) {
	if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?")) {
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->fetch();
		
		if (!$result) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
	
	return "Failure!";
}

function doesUserExist($mysqli, $id) {
	if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE id = ?")) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->fetch();
		
		if (!$result) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
	
	return "Failure!";	
}

function getUserID($mysqli, $email) {
	if (doesUserEmailExist($mysqli, $email)) {
		if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?")) {
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			
			return $id;
		} else {
			return -2;
		}
	} else {
		return -1;
	}
}

function createUser($mysqli, $email) {
	if ($stmt = $mysqli->prepare("INSERT INTO users SET email = ?")) {
		$stmt->bind_param("s", $email);
		$stmt->execute();
	}
}