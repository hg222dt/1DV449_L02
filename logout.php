<?php

	require_once('sec.php');
	sec_session_start();

	if(isset($_POST['logout'])) {
	    //logout();

	    unset($_SESSION['username']);
	    unset($_SESSION['csrfToken']);
	    unset($_SESSION['userLoggedIn']);
	    session_destroy();
		header('Location: index.php');
	}

?>