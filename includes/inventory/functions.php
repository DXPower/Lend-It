<?php
include "../users/functions.php";

function addItem($mysqli, $user, $itemName, $lendee, $date) {
	if (doesUserExist($mysqli, $user)) {
		if ($stmt = $mysqli->prepare("INSERT INTO items SET user_id = ?, item_name = ?, lendee = ?, date = ?")) {
			$dt = date("Y-m-d", $date);
			$stmt->bind_param("isss", $user, $itemName, $lendee, $dt);
			$stmt->execute();
			
			return array("id" => $mysqli->insert_id, 
						"item_name" => $itemName,
						"lendee" => $lendee,
						"date" => $dt);
		} else {
			return "Faulty MYSQLI statement";
		}
	} else {
		return "User does not exist";
	}
}

function getItems($mysqli, $user) {
	if (doesUserExist($mysqli, $user)) {
		if ($stmt = $mysqli->prepare("SELECT id, item_name, lendee, date, checked_in FROM items WHERE user_id = ? ORDER BY id DESC LIMIT 10")) {
			$stmt->bind_param("i", $user);
			$stmt->execute();
			$stmt->bind_result($id, $itemName, $lendee, $date, $checkedIn);
			
			$result = array();
			
			while ($stmt->fetch()) {
				$row = array("id" => $id,
							"item_name" => $itemName,
							"lendee" => $lendee,
							"date" => $date,
							"checked_in" => $checkedIn);
				array_push($result, $row);
			}
			
			return $result;
		}
	}
}

function checkinItem($mysqli, $id) {
	if ($stmt = $mysqli->prepare("UPDATE items SET checked_in = TRUE WHERE id = ?")) {
        $stmt->bind_param("i", $id);
		$stmt->execute();
	} else {
		return "Faulty MYSQLI statement";
	}
}

function deleteItem($mysqli, $id) {
    if ($stmt = $mysqli->prepare("DELETE FROM items WHERE id = ?")) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else {
        return "Faulty MYSQLI Statement!";
    }
}

function editItem($mysqli, $id, $itemName, $lendee, $date) {
	if ($stmt = $mysqli->prepare("UPDATE items SET item_name = ?, lendee = ?, date = ? WHERE id = ?")) {
		$dt = date("Y-m-d", $date);
		$stmt->bind_param("sssi", $itemName, $lendee, $dt, $id);
		$stmt->execute();
	} else {
		return "Faulty MYSQLI statement!";
	}
}