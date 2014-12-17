<?php

	require_once("sec.php");

	sec_session_start();
/**
* Called from AJAX to add stuff to DB
*/


		//if($_GET['function'] == 'add') {

		    $name = $_GET["name"];
			$message = $_GET["message"];
			$csrfTokenForm = trim($_GET["csrfToken"]);

			$name = strip_tags($name);
			$message = strip_tags($message);

			$csrfTokenSession = trim($_SESSION["csrfToken"]);

			if(strcmp($csrfTokenSession, $csrfTokenForm) === 0) {
				addToDB($message, $name);
			} else {
				die();
			}
			
  
    	//}


	function addToDB($message, $user) {

		$db = null;
		
		try {
			$db = new PDO("sqlite:db.db");
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOEception $e) {
			die("Something went wrong -> " .$e->getMessage());
		}
		
		$q = "INSERT INTO messages (message, name) VALUES('$message', '$user')";
		
		$result;
		$stm;
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
			if(!$result) {
				return "Could not send message";
			}
		}
		catch(PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}


		$q = "SELECT * FROM users WHERE username = '" .$user ."'";
		$result;
		$stm;
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
			if(!$result) {
				return "Could not find the user";
			}
		}
		catch(PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}
		// Send the message back to the client
		echo "Message saved by user: " .json_encode($result);
		
	}





/**
* Called from AJAX to add stuff to DB
*/

/*
function addToDB($message, $user) {
	$db = null;
	
	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Something went wrong -> " .$e->getMessage());
	}
	
	$q = "INSERT INTO messages (message, name) VALUES('$message', '$user')";
	
	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
			return "Could not send message";
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}


	$q = "SELECT * FROM users WHERE username = '" .$user ."'";
	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
			return "Could not find the user";
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}
	// Send the message back to the client
	echo "Message saved by user: " .json_encode($result);
	
}

*/

