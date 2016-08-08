<?php
	session_start();
	include "connect.php";
	
	if(isset($_POST["acao"])) {
		$acao = $_POST["acao"];
		if($acao == "Filtrar") {
			$categoria = $_POST["categoria"];
			
			
			if(count($categoria) >= 1 && $categoria[0] != 0) {
				$categoria = " WHERE catProd_categoria IN (" . implode(", ", $categoria) . ")";
			} else {
				$categoria = "";
			}
			
			$sql = "SELECT catProd_produto
					FROM categoria_produto" . $categoria;
			
			$produtos = mysqli_query($conn, $sql);
			
			if($categoria == "") {
				$produtos2 = mysqli_fetch_all(mysqli_query($conn, "SELECT prod_id, prod_titulo, prod_descricao, prod_valor, prod_valorDesc FROM produto"),MYSQLI_ASSOC);
			} elseif($produtos->num_rows) {
				while($produto =  mysqli_fetch_object($produtos)) {
					$produtos2[] = mysqli_fetch_object(mysqli_query($conn, "SELECT prod_id, prod_titulo, prod_descricao, prod_valor, prod_valorDesc
																			FROM produto 
																			WHERE prod_id = " . $produto->catProd_produto));
				}
			} else {
				print "0"; exit;
			}
			
			print json_encode($produtos2);
		}
		exit;
	} else if(isset($_GET["acao"])){
		$acao = $_GET["acao"];
		
		if($acao == "getProduto") {
			$id = $_GET["id"];
			$produto = mysqli_fetch_object(mysqli_query($conn, "SELECT prod_id, prod_titulo, prod_descricao, prod_valor, prod_valorDesc, prod_quantidade
																			FROM produto 
																			WHERE prod_id = " . $id));																			
		}
	} else {
		$acao = "";
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
		<link href="css/produto.css" rel="stylesheet">
	</head>

	<body>
		<?php include("menu.php"); ?>
		<div class="container" id="container">
			<div class="row">
				<?php if($acao == "getProduto") { ?>
					<div class="item-container">	
						<div class="container">	
							<div class="col-md-12">
								<div class="col-md-3 service-image-left">
									<center>
										<img name="<?php print $produto->prod_id; ?>" height="200" width="200"/>
									</center>
								</div>
							</div>
								
							<div class="col-md-7">
								<div class="product-title"><?php print $produto->prod_titulo; ?></div>
								<div class="product-desc"><?php print $produto->prod_descricao; ?></div>								
								<hr>
								<?php if($produto->prod_valorDesc > 0) { ?>
									<div class="product-price cut-price">R$ <?php print number_format($produto->prod_valor, 2, ',', '.'); ?></div>
								<?php } ?>
								
								<div class="product-price">R$ <?php print number_format(($produto->prod_valor - $produto->prod_valorDesc), 2, ',', '.'); ?></div>
								
								<?php if($produto->prod_quantidade > 0) { ?>
									<div class="product-stock">Disponível</div>
								<?php } else { ?>
									<div class="product-out-stock">Indisponível</div>
								<?php } ?>
								<hr>
								<div class="btn-group cart">
									<button type="button" id="adicionar-carrinho" name="<?php print $produto->prod_id; ?>" class="btn <?php print ($produto->prod_quantidade > 0) ? "btn-success" : "btn-danger";?>" <?php if($produto->prod_quantidade == 0 || isset($_SESSION["carrinho"][$produto->prod_id])) { ?>disabled="disabled" <?php } ?>>
										<?php print isset($_SESSION["carrinho"][$produto->prod_id]) ? "Adicionado" : "Adicionar"; ?> ao carrinho
									</button>
								</div>
							</div>
						</div> 
					</div>
				<?php } else { ?>
					<div class="col-sm-4 col-md-3">
						<div id="panel">
							<div class="well">
								<strong>Categorias</strong>

								<div class='checkbox'>
									<label><input type='checkbox' value='0' name="selecao-categoria" checked>Todos</label>
									<?php
										$categorias_pai = mysqli_query($conn, "SELECT * FROM categoria WHERE cat_pai IS NULL");
										
										if($categorias_pai->num_rows) {
											while($pai = mysqli_fetch_object($categorias_pai)) {
												$categorias = mysqli_query($conn, "SELECT * FROM categoria WHERE cat_pai = " . $pai->cat_id);
												if($categorias->num_rows){										
													print '<hr/>' . $pai->cat_descricao . '<br/><br/>';
													
													while($categoria = mysqli_fetch_object($categorias)) {
														print '<label><input type="checkbox" value="' . $categoria->cat_id . '" name="selecao-categoria">' . $categoria->cat_descricao . '</label><br/>';												
													}
												}
											}
										}
									?>
								</div>
							</div>
						</div>
					</div>
					<div id="lista-produto" class="col-sm-8 col-md-9"></div>
				<?php } ?>
			</div>
		</div>
	<input type="text" id="val" style="display: none"/>
	</body>
</html>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.maskMoney.min.js"></script>
<script src="js/ecommerce.js"></script>
<script>
	$(function() {
		$("#val").maskMoney({prefix:'R$ ', thousands:'.', decimal:',', allowZero:true});
		$('#list').click(function(event){event.preventDefault();$('#products .item').addClass('list-group-item');});
		$('#grid').click(function(event){event.preventDefault();$('#products .item').removeClass('list-group-item');$('#products .item').addClass('grid-group-item');});
		$('input[type="checkbox"][value="0"]').click();
		
		$("div img").each(function(){
			var id = $(this).attr("name");
			var img = $(this);
			
			$.get("imgView.php", {
				id: id
			}, function(data){
				img.attr("src", "data:image/jpeg;base64," + data);
			});
		});
	});
</script>