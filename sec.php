<?php

/**
Just som simple scripts for session handling
*/
function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params(3600, $cookieParams["path"], $cookieParams["domain"], $secure, false);
        $httponly = true; // This stops javascript being able to access the session id.
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
}

function checkUser() {
	if(!session_id()) {
		sec_session_start();
	}

	if (isset($_SESSION['userAgent']) && $_SESSION['userAgent'] !== $_SERVER["HTTP_USER_AGENT"]) {
	
		header("Location: index.php"); 
	
	}

	if(!isset($_SESSION["username"])) {
		header('Location: index.php');
	}

	$user = getUser($_SESSION["username"]);
	$un = $user[0]["username"];

	if(isset($_SESSION['login_string'])) {
		if($_SESSION['login_string'] !== hash('sha512', "123456" + $un) ) {
			header('Location: index.php');
		}
	}
	else {
		header('Location: index.php');
	}
	return true;
}

function isUser($u, $p) {

	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT id FROM users WHERE username = '$u' AND password = '$p'";

	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
		if(!$result) {
			return false;
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	return true;
	
}


function userVerify($username, $inputPassword) {

	$hashedPasswordOnDatabase = getUserPasswordFromDB($username);

	if($hashedPasswordOnDatabase == false) {
		return false;
	}

	$verified = password_verify($inputPassword, $hashedPasswordOnDatabase);

	if($verified) {
		return true;
	}
	return false;
}

function getUserPasswordFromDB($username) {
	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT password FROM users WHERE username = '$username'";

	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();

		if(empty($result)) {
			return false;
		}

	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	return $result[0]['password'];
}


function getUser($user) {
	$db = null;

	try {
		$db = new PDO("sqlite:db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEception $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT * FROM users WHERE username = '$user'";

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

	return $result;
}

function logout() {

	unset($_SESSION['username']);
    unset($_SESSION['csrfToken']);
    unset($_SESSION['userLoggedIn']);
    session_destroy();
	header('Location: index.php');
}

