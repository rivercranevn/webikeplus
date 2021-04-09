<?php
	header('Content-Type: application/json');
	$strApple = @file_get_contents('https://321c0ba9e4d9.ngrok.io/.well_known/apple-app');	
	echo $strApple; 
	exit;
?>
