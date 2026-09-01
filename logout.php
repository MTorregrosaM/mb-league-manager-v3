<?php
	
	try {
	
		$_SESSION["autorizado"] = 0;
		header("Location: login.php");
	
	}catch(Exception $e){
		return null;	 
	}

?>
