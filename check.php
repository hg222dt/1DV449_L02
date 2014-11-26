<?php
require_once("sec.php");

// check tha POST parameters
$u = $_POST['username'];
$p = $_POST['password'];




// Check if user is OK
if(isset($u) && isset($p) && userVerify($u, $p)) {


	// set the session
	sec_session_start();
	$_SESSION['username'] = $u;
	$_SESSION['login_string'] = hash('sha512', "123456" +$u);
	$_SESSION['csrfToken'] = base64_encode( openssl_random_pseudo_bytes(32));
	
	header("Location: mess.php"); 

}
else {

	header("Location: index.php");

	// To bad
	//header('HTTP/1.1 401 Unauthorized');
	//die("could not call");
}

/*
$mess = new Mess();

//header("Location: mess.php"); 

$messPage = $mess->showMessPage();

echo $messPage;
*/