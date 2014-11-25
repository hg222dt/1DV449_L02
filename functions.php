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


		//Stippa av strängen med olika taggar. strip_tags o trim

		$name = strip_tags($name);
		$message = strip_tags($message);
		

		if(strcmp($_GET['CSRFToken'], $_SESSION['csrfToken'])) {
			addToDB($message, $name);
		} else {
			echo "Det funkade inte";
			die();
		}
		header("Location: test/debug.php");
    }
    elseif($_GET['function'] == 'getMessages') {
  	   	echo(getMessages());
    }
}