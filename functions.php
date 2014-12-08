<?php
require_once("get.php");
require_once("post.php");
require_once("sec.php");
sec_session_start();


/*
* It's here all the ajax calls goes
*/
if(isset($_GET['function'])) {

	if($_GET['function'] == 'logout') {
		logout();
    } 
    elseif($_GET['function'] == 'add') {
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
		header("Location: test/debug.php");
    }
    elseif($_GET['function'] == 'getMessages') {
  	   	echo(getMessages());
    }
    elseif($_GET['function'] == 'pollDatabase') {
    	echo(pollDatabase($_GET['highestMessageId']));
    }
}