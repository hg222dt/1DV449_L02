<?php

// get the specific message
function getMessages() {

	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages ORDER BY `serial` ASC";
	
	$result;
	$stm;	
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	$result = json_encode($result);
	
	if($result)
		return $result;
	else
	 	return false;
}

function pollDatabase($highestMessageId) {

	session_write_close();

	$endTime = time() + 20;
	$db = null;

	while(time() <= $endTime) {

		try {
			$db = new PDO("sqlite:db.db");
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOEception $e) {
			die("Del -> " .$e->getMessage());
		}
		
		$q = "SELECT * FROM messages WHERE `serial` > $highestMessageId ORDER BY `serial` ASC";
		
		$result;
		$stm;	
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
		}
		catch(PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}

		$result = json_encode($result);
		
		$decodedResult = json_decode($result, true);

		if(count($decodedResult) > 0) {
			return $result;
		}
		else {
			sleep(3);
		}

	}

	return false;
}