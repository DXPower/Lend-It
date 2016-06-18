<?php

function createContact($mysqli, $owner, $name, $phone, $email) {
    if ($stmt = $mysqli->prepare("INSERT INTO contacts SET owner_id = ?, name = ?, phone_number = ?, email = ?")) {
        $phone = preg_replace("/[^0-9]/", "", $phone); // Strip any non numeric characters from the number
        $stmt->bind_param("isis", $owner, $name, $phone, $email);
        $stmt->execute();
        
        echo $mysqli->instert_id;
        
        return $mysqli->insert_id;
    } else {
        return "Faulty MYSQLI statement";
    }
}
        
function searchContacts($mysqli, $owner, $n) {
    if ($stmt = $mysqli->prepare("SELECT id, name, phone_number, email FROM contacts WHERE owner_id = ? AND name LIKE CONCAT('%', ?, '%') ORDER BY name ASC")) {
        $stmt->bind_param("is", $owner, $n);
        $stmt->execute();
        $stmt->bind_result($id, $name, $number, $email);
        $result = array();
        
        while ($stmt->fetch()) {
            array_push($result, array("id" => $id,
                                      "name" => $name,
                                      "phone_number" => $number,
                                      "email" => $email));
        }
        
        return $result;
    } else {
        return "Faulty MYSQLI statement";
    }
}
        
function getContact($mysqli, $id) {
    if ($stmt = $mysqli->prepare("SELECT id, name, phone_number, email FROM contacts WHERE id = ?")) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $phone_number, $email);
        $stmt->fetch();
        
        return array("id" => $id,
                     "name" => $name,
                     "phone_number" => $phone_number,
                     "email" => $email);
    } else {
        return $mysqli->error;
    }
}
        
function getContacts($mysqli, $owner) {
    if ($stmt = $mysqli->prepare("SELECT id, name, phone_number, email FROM contacts WHERE owner_id = ? ORDER BY name ASC")) {
        $stmt->bind_param("i", $owner);
        $stmt->execute();
        $stmt->bind_result($id, $name, $number, $email);
        $result = array();
        
        while ($stmt->fetch()) {
            array_push($result, array("id" => $id,
                                      "name" => $name,
                                      "phone_number" => $number,
                                      "email" => $email));
        }
        
        return $result;
    } else {
        return "Faulty MYSQLI statement";
    }
}

function editContact($mysqli, $id, $name, $number, $email) {
    if ($stmt = $mysqli->prepare("UPDATE contacts SET name = ?, phone_number = ?, email = ? WHERE id = ?")) {
        $stmt->bind_param("sssi", $name, $number, $email, $id);
        $stmt->execute();
        
        return true;
    } else {
        return "Faulty MYSQLI statement";
    }
}