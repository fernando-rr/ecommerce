<?php

	include "connect.php";
	
	if(isset($_GET["id"])) {
		$id = $_GET["id"];
		$imagem = mysqli_query($conn, "SELECT prod_imagem FROM produto WHERE prod_id = $id");
		$imagem = base64_encode(mysqli_fetch_object($imagem)->prod_imagem);
		
		if($imagem == "") {
			$imagem = base64_encode(file_get_contents('img/no-image-found.png'));
		}
		
		print $imagem;
	}
?>