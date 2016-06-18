<?php
include '../users/functions.php';
include '../contacts/functions.php';

function addItem($mysqli, $user, $itemName, $contact, $date, $optionals) {
	if (doesUserExist($mysqli, $user)) {
		if ($stmt = $mysqli->prepare("INSERT INTO items SET user_id = ?, item_name = ?, contact_id = ?, date = ?")) {
			$dt = date("Y-m-d", $date);
			$stmt->bind_param("isis", $user, $itemName, $contact, $dt);
			$stmt->execute();
			
            $insertId = $mysqli->insert_id;
            $final = array("id" => $insertId, 
						"item_name" => $itemName,
                        "contact" => getContact($mysqli, $contact),
						"date" => $dt,
                        "checked_in" => false);
            
            // Checks if the image exists then will upload it if it does
            if (isset($optionals["image"])) {
                $uploadDir = '../../img/uploads/';
                $fileName = basename(tempnam($uploadDir, "")) . ".png";

                if (move_uploaded_file($optionals["image"]["tmp_name"], $uploadDir . $fileName)) {
                    if ($stmt2 = $mysqli->prepare("UPDATE items SET image = ? WHERE id = ?")) {
                        $stmt2->bind_param("si", $fileName, $insertId);
                        $stmt2->execute();
                        
                        $final["image"] = $fileName;
                    }
                }
            }
            
			return $final;
		} else {
			return "Faulty MYSQLI statement";
		}
	} else {
		return "User does not exist";
	}
}

function getItems($mysqli, $user) {
	if (doesUserExist($mysqli, $user)) {
		if ($stmt = $mysqli->prepare("SELECT id, item_name, contact_id, date, image, checked_in FROM items WHERE user_id = ? ORDER BY id DESC LIMIT 10")) {
			$stmt->bind_param("i", $user);
			$stmt->execute();
			$stmt->bind_result($id, $itemName, $contact, $date, $image, $checkedIn);
            $stmt->store_result();
            
			$result = array();
			
			while ($stmt->fetch()) {
				$row = array("id" => $id,
							"item_name" => $itemName,
                            "contact" => getContact($mysqli, $contact),
							"date" => $date,
							"checked_in" => $checkedIn);
                
                if (!is_null($image)) $row["image"] = $image; else $row["image"] = "";
                
				array_push($result, $row);
			}
			
			return $result;
		}
	}
}

function checkinItem($mysqli, $id) {
	if ($stmt = $mysqli->prepare("UPDATE items SET checked_in = TRUE, date_checked_in = now() WHERE id = ?")) {
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

function editItem($mysqli, $id, $itemName, $date) {
	if ($stmt = $mysqli->prepare("UPDATE items SET item_name = ?, date = ? WHERE id = ?")) {
		$dt = date("Y-m-d", $date);
		$stmt->bind_param("ssi", $itemName, $dt, $id);
		$stmt->execute();
	} else {
		return "Faulty MYSQLI statement!";
	}
}

function getFirstLogIndex($mysqli, $user) {
	if ($stmt = $mysqli->prepare("SELECT id FROM items WHERE user_id = ? ORDER BY id ASC LIMIT 1")) {
		$stmt->bind_param("i", $user);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		
		return $id;
	} else {
		return $mysqli->error;
	}
}

function getLastLogIndex($mysqli, $user) {
	if ($stmt = $mysqli->prepare("SELECT id FROM items WHERE user_id = ? ORDER BY id DESC LIMIT 1")) {
		$stmt->bind_param("i", $user);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		
		return $id;
	}
}

function getLog($mysqli, $start, $user) {
	if ($stmt = $mysqli->prepare("SELECT id, item_name, contact_id, date, date_created, date_checked_in, checked_in FROM items WHERE user_id = ? ORDER BY id DESC LIMIT ?, 10")) {
		$stmt->bind_param("ii", $user, $start);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id, $itemName, $contact, $dateExpired, $dateCreated, $dateCheckedIn, $checkedIn);
		
		$result = array();
		
		$result["first"] = getFirstLogIndex($mysqli, $user);
		$result["last"] = getLastLogIndex($mysqli, $user);
		
		while ($stmt->fetch()) {
			$fDC = new DateTime($dateCreated);
			$row = array("id" => $id,
						 "item_name" => $itemName,
						 "contact" => getContact($mysqli, $contact),
						 "date_expired" => $dateExpired,
						 "date_created" => $fDC->format("Y-m-d"),
						 "date_checked_in" => $dateCheckedIn,
						 "checked_in" => $checkedIn);
			
			array_push($result, $row);
			
			// The javascript checks if these are true and disables the next/back buttons appropriately
			if ($id == $result["first"]) {
				$result["first"] = true;
			} 
			
			if ($id == $result["last"]) {
				$result["last"] = true;
			}
		}
		
		return $result;
	} else {
		return "Faulty MYSQLI statement!";
	}
}