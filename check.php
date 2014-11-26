<?php
require_once("sec.php");

// check tha POST parameters
$u = $_POST['username'];
$p = $_POST['password'];




// Check if user is OK
if(isset($u) && isset($p)) {

	$userVerified = userVerify($u, $p);

	if($userVerified) {
		// set the session
		sec_session_start();
		$_SESSION['username'] = $u;
		$_SESSION['login_string'] = hash('sha512', "123456" +$u);
		$_SESSION['csrfToken'] = base64_encode( openssl_random_pseudo_bytes(32));
		$_SESSION['userLoggedIn'] = true;
		$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
		
		header("Location: mess.php"); 
	} else {

		header("Location: index.php");
	}
}
