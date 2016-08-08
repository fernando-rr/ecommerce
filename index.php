<?php session_start(); include "connect.php"; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>E-commerce</title>

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/navbar-fixed-top.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/ecommerce.css" rel="stylesheet">
	</head>

	<body>
		
		<?php include("menu.php"); ?>
		<div id="container" class="container"></div>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.maskMoney.min.js"></script>
		<script src="js/ecommerce.js"></script>
	</body> 
</html>