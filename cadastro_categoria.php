<?php
	session_start();
	if(!$_SESSION["logged"])
		header("Location: index.php");
		
	include "connect.php";
	$error = "";
	
	function existe_dependente($id) {
		global $conn;
		return mysqli_query($conn, "SELECT * FROM categoria         WHERE cat_pai = $id")->num_rows +
			   mysqli_query($conn, "SELECT * FROM categoria_produto WHERE catProd_categoria = $id")->num_rows;
	}
	
	if(isset($_POST["acao"])) {
		$acao	   = $_POST["acao"];

		if($acao != "Deletar") {
			$descricao = str_replace("'", "\'", $_POST["descricao"]); $descricao = str_replace('"', '\"', $descricao);
			$categoria = isset($_POST["categoria"]) ? (intval(substr($_POST["categoria"], 6))) : 0;  
			$categoria = (!$categoria ? "null" : $categoria);
		}
		
		$id = isset($_POST["optCategoria"]) ? $_POST["optCategoria"] : "null";
		
	
		if($acao == "Salvar") {
			$result = mysqli_query($conn, "SELECT * FROM categoria WHERE cat_pai = $id");
			if(existe_dependente($id)) {
				$conn->query("	UPDATE categoria 
								SET 	cat_descricao = '$descricao'
								WHERE cat_id = $id");
				
				if($categoria != "null") {
					$error = "Não é possível alterar uma categoria pai contendo dependentes.";
				}
			} else {
					$conn->query("	UPDATE categoria 
									SET 	cat_descricao = '$descricao',
											cat_pai		  = $categoria
									WHERE cat_id = $id");
				}
		} else if($acao == "Adicionar") {
			$conn->query("INSERT INTO categoria VALUES (null, $categoria, '$descricao')");
		} else if ($acao == "Deletar") {
			if(!existe_dependente($id)) {
				$conn->query("DELETE FROM categoria WHERE cat_id = $id");
			} else {
				$error = "Não é possível excluir uma categoria contendo dependentes.";
			}
		}
		
	}
?>
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
		<form id="uploadForm" action="cadastro_categoria.php" method="POST">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-6 col-md-4"></div>
					<div class="col-xs-6 col-md-4">
						<div class="input-group margin-bottom-sm">
							<span class="input-group-addon"><i class="fa fa-pencil-square-o fa-fw"></i></span>
							<input id="descricao" class="form-control" name="descricao" type="text" placeholder="Descrição">
						</div>
						<div class="form-group">
							<label for="sel1">Categoria pai:</label>
							<select class="form-control" name="categoria" id="categoria">
								<option selected value="option0">Nenhum</option>
								<?php
									$categorias = mysqli_query($conn, "SELECT cat_id, cat_descricao FROM categoria WHERE cat_pai IS NULL");
									
									if($categorias->num_rows > 0) {
										while($categoria = mysqli_fetch_object($categorias)) {
											print "<option value='option$categoria->cat_id'>$categoria->cat_descricao</option>";
										}
									}
								?>
							</select>
						</div>
						<a class="btn btn-danger" id="deletar-categoria" href="#" style="position: absolute; display: none">
						  <i class="fa fa-trash-o" title="Deletar"  aria-hidden="true"></i>
						</a>
						<a href="#adicionar-categoria" class="btn btn-primary" style="margin-left: 320px; width: 105px">Adicionar</a>
						<div class="list-group">
							<div class="radio">
								<label><input type="radio" class='optCategoria' value="0" checked name="optCategoria">Adicionar nova categoria</label>
							</div>
							<?php
								$categorias = mysqli_query($conn, "SELECT * FROM categoria");
								
								if($categorias->num_rows > 0) {
									while($categoria = mysqli_fetch_object($categorias)) {
										print "	<div class='radio'>
													<label><input type='radio' id='$categoria->cat_pai' value='$categoria->cat_id' class='optCategoria' name='optCategoria'> <div>$categoria->cat_descricao</div></label>
												</div>";
									}
								} else {
									print "Nenhuma categoria cadastrada";
								}
							?>
						</div>
						<label <?php print ($error == "") ? "style='display: none'" : ""?>><?php print $error; ?></label>
						<input type="text" name="acao" id="acao" style="display: none"/>
						<input type="submit" id="adicionar-categoria" style="display: none"/>
					</div>
				</div>
			</div>
		</form>
	</body>
</html>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.maskMoney.min.js"></script>
<script src="js/ecommerce.js"></script>