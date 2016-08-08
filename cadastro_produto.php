<?php
	session_start();
	if(!$_SESSION["logged"])
		header("Location: index.php");
		
	include "connect.php";
	$error = "";
	
	if(isset($_POST["acao"])) {
		$acao = $_POST["acao"];	
		
		if($acao == "Procurar") {
			$id = $_POST["id"];
			$produtos   = mysqli_query($conn, "SELECT * FROM produto WHERE prod_id = $id");
			$categorias = mysqli_query($conn, "SELECT * FROM categoria_produto WHERE catProd_produto = $id");
			
			if($produtos->num_rows > 0) {
				$produto  = mysqli_fetch_object($produtos);
				$produto2 = (array) $produto;
				unset($produto2['prod_imagem']);
				$produto2  = json_encode($produto2);
				
				$produto2  = rtrim($produto2, '}');
				if($categorias->num_rows > 0) {
					while($categoria = mysqli_fetch_object($categorias)) {
						$categoria2[] = $categoria->catProd_categoria;
					}
					
					$produto2 .= ',"prod_categoria":"' . implode(";", $categoria2) . '"}';
				} else {
					$produto2 .= ',"prod_categoria":""}';
				}
				
				print $produto2; 
				exit;
			}
		}

		if($acao != "Deletar") {
			$titulo 	= str_replace("'", "\'", $_POST["titulo"]); $titulo = str_replace('"', '\"', $titulo);
			$descricao 	= str_replace("'", "\'", $_POST["descricao"]); $descricao = str_replace('"', '\"', $descricao);
			$valor 	   	= $_POST["valor"] == "" ? 0 : $_POST["valor"];
			$valor_desc	= $_POST["valor-desc"] == "" ? 0 : $_POST["valor-desc"];
			$quantidade	= $_POST["quantidade"] == "" ? 0 : $_POST["quantidade"];
			$categoria  = isset($_POST["categoria"]) ? $_POST["categoria"] : "null";
		}
		
		if($acao == "Adicionar") {
			if (isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0) {
				$tmpName = $_FILES['imagem']['tmp_name'];
				$fp 	 = fopen($tmpName, 'r');
				$imagem  = fread($fp, filesize($tmpName));
				$imagem  = addslashes($imagem);
				fclose($fp);
				$imagem = "'" . $imagem . "'";
			} else {
				$imagem = "null";
			}
		
			$conn->query("INSERT INTO produto VALUES (NULL, '$descricao', $valor, '$titulo', $valor_desc, $quantidade, $imagem)");
			$id = mysqli_query($conn, "SELECT LAST_INSERT_ID() as ID");
			$id = mysqli_fetch_object($id)->ID;
			
			if($categoria != "null") {
				foreach($categoria as $cat) {
					$conn->query("INSERT INTO categoria_produto VALUES ($cat, $id)");
				}
			}
		} else if($acao == "Salvar") {
			$id = $_POST["optProduto"];
			
			if ($_FILES['imagem']['size'] > 0) {
				$tmpName = $_FILES['imagem']['tmp_name'];
				$fp 	 = fopen($tmpName, 'r');
				$imagem  = fread($fp, filesize($tmpName));
				$imagem  = addslashes($imagem);
				fclose($fp);
				$imagem = "'" . $imagem . "'";
				$imagem = ", prod_imagem = $imagem";
			} else {
				$imagem = "";
			}
			
			$conn->query("	UPDATE produto 
							SET    prod_descricao  = '$descricao',
								   prod_titulo     = '$titulo',
								   prod_valor      = $valor,
								   prod_valorDesc  = $valor_desc,
								   prod_quantidade = $quantidade
								   $imagem
							WHERE prod_id = $id");
			
			$conn->query("DELETE FROM categoria_produto WHERE catProd_produto = $id");
			
			if($categoria != "null") {
				foreach($categoria as $cat) {
					$conn->query("INSERT INTO categoria_produto VALUES ($cat, $id)");
				}
			}
		} else if ($acao == "Deletar") {
			$id = $_POST["id"];
			$conn->query("DELETE FROM categoria_produto WHERE catProd_produto = $id");
			$conn->query("DELETE FROM produto WHERE prod_id = $id");
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
		<form id="uploadForm" action="cadastro_produto.php" method="POST" enctype= "multipart/form-data">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-6 col-md-4">
						<input type="radio" class='optProduto' value="0" checked name="optProduto"> Adicionar novo produto</input><br/><br/>
						<strong>Produtos cadastrados:</strong><br/>
						<?php
						$produtos = mysqli_query($conn, "SELECT prod_id, prod_titulo, prod_imagem FROM produto");
						
						if($produtos->num_rows > 0) {
							while($produto = mysqli_fetch_object($produtos)) {
								print "	<div class='radio'>
											<label><input type='radio' value='$produto->prod_id' class='optProduto' name='optProduto'> <div>$produto->prod_titulo</div></label>
										</div>";
							}
						} else {
							print "Nenhum produto cadastrado";
						}
					?>
					</div>
					<div class="col-xs-6 col-md-4">
						<div class="input-group margin-bottom-sm">
							<span class="input-group-addon"><i class="fa fa-pencil-square-o fa-fw"></i></span>
							<input id="titulo" name="titulo" class="form-control" type="text" placeholder="Título" required/>
							<textarea class="form-control" rows="4" id="descricao" name="descricao" placeholder="Descrição"></textarea>
						</div>
						<div class="input-group margin-bottom-sm">
							<span class="input-group-addon"><i class="fa fa-money fa-fw"></i></span>
							<input id="valor" name="valor" class="form-control" type="text" placeholder="Valor" data-allow-zero="true" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal=","/>
							<span class="input-group-addon"><i class="fa fa-fw"></i></span>
							<input id="valor-desc" name="valor-desc" class="form-control" type="text" placeholder="Valor desc." data-allow-zero="true" data-affixes-stay="true" data-prefix="R$ " data-thousands="." data-decimal=","/>
							<span class="input-group-addon"><i class="fa fa-fw"></i></span>
							<input id="quantidade" name="quantidade" class="form-control" type="number" placeholder="Quantidade" value="0"/>
						</div>
						<div class="input-group margin-bottom-sm">
							<span class="input-group-addon"><i class="fa fa-camera fa-fw"></i></span>
							<input id="imagem" name="imagem" class="form-control" type="file" accept="image/*">
							<div style="margin-left: 27%; margin-top: 40px; margin-bottom: 10px"><img id="preview" src="#" height="180" width="180" style="display: none;"/></div>
						</div>
						<label>Categorias:</label>
						<div class="list-group">
							<?php
								$categorias = mysqli_query($conn, "SELECT cat_id, cat_descricao FROM categoria WHERE cat_pai IS NOT NULL");
								
								if($categorias->num_rows > 0) {
									while($categoria = mysqli_fetch_object($categorias)) {
										print "	<div class='checkbox'>
													<label><input type='checkbox' value='$categoria->cat_id' name='categoria[$categoria->cat_id]'><div>$categoria->cat_descricao</div></label>
												</div>";
									}
								} else {
									print "Nenhuma categoria cadastrada";
								}
							?>
						</div>
						<a class="btn btn-danger" id="deletar-produto" href="#" style="position: absolute; display: none">
						  <i class="fa fa-trash-o" title="Deletar"  aria-hidden="true"></i>
						</a>
						<a href="#adicionar-produto" class="btn btn-primary" style="margin-left: 320px; width: 105px">Adicionar</a>
						<label <?php print ($error == "") ? "style='display: none'" : ""?>><?php print $error; ?></label>
						<input type="submit" id="adicionar-produto" style="display: none"/>
						<input type="text" name="acao" id="acao" style="display: none"/>
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
<script>
	$(function(){
		$("#valor").maskMoney();
		$("#valor-desc").maskMoney();
		$("#titulo").focus();
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					$('#preview').attr('src', e.target.result);
				}

				reader.readAsDataURL(input.files[0]);
			}
		}

		$("#imagem").change(function(){
			readURL(this);
			$('#preview').show();
		});
	});
</script>