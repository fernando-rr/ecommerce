<?php
	include 'connect.php';
	
	$login = $senha = '';
	
	$login = str_replace("'", "",$_POST["login"]); $login = str_replace('"', "",$login);
	$senha = str_replace("'", "",$_POST["senha"]); $senha = str_replace('"', "",$senha);
	
	$query =   "SELECT *
				FROM usuario
				WHERE 	usu_login = '$login' AND
						usu_senha = '$senha'";
	
	if($conn->query($query)->num_rows){
		print "sucess";
		session_start();
		$_SESSION["logged"] = 1;
	} else {
		print "fail";	
		$_SESSION["logged"] = 0;
	} 
?>