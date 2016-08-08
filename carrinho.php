<?php
	session_start();
	include "connect.php";
	
	if(isset($_POST["acao"])) {
		$acao = $_POST["acao"];
		
		if($acao == "Adicionar") {			
			$_SESSION["carrinho"][$_POST["id"]] = "";
		} else if($acao == "Remover") {
			unset($_SESSION["carrinho"][$_POST["id"]]);
		} else if($acao == "Finalizar") {
			foreach($_SESSION["carrinho"] as $id => $val) {
				unset($_SESSION["carrinho"][$id]); 
				$conn->query("	UPDATE produto
								SET prod_quantidade = prod_quantidade-1
								WHERE prod_id = $id" );
			}
			exit;
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
		<link href="css/produto.css" rel="stylesheet">
		<link href="css/jquery.modal.css" rel="stylesheet">
	</head>

	<body>
		<?php include("menu.php"); ?>
		<div class="container" id="container">
			<div class="row">
				<div id="lista-carrinho" class="col-sm-8 col-md-9">
					<ul class="thumbnail-list">
					<?php 	if(count($_SESSION["carrinho"]) == 0) {
								print "<li>Nenhum produto adicionado</li>";
							} else {
								foreach($_SESSION["carrinho"] as $id => $val) {
									$produto = mysqli_fetch_object(mysqli_query($conn, "SELECT prod_id, prod_titulo, prod_descricao, prod_valor, prod_valorDesc, prod_quantidade
																						FROM produto 
																						WHERE prod_id = $id"));
									
									$produtos[] = $produto;
									
									print '	<li>
												<img name="' . $produto->prod_id . '" height="200" width="200" />
												<h4>' . $produto->prod_titulo . '</h4>
												<div class="product-price">
													<span class="normal-price">R$ ' . number_format($produto->prod_valor - $produto->prod_valorDesc, 2, ',', '.') . '</span>
												</div>
												<a class="btn btn-danger" href="#deletar-produto-carrinho">
													<i class="fa fa-trash-o" title="Deletar"  aria-hidden="true"></i>
												</a>
											</li>';
								}
							}
					?>
					</ul>
				</div>
			</div>
			<?php 	if(count($_SESSION["carrinho"]) > 0) {?>
			<a class="btn btn-success" href="#modal" rel="modal:open">
				Finalizar compra
			</a>
			<?php } ?>
			
			<div id="modal" style="display:none; width: 600px;">
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
						  <td class="active"><label>Produto</label></td>
						  <td class="active"><label>Valor</label></td>
						</tr>
						<?php 
							if(count($_SESSION["carrinho"])) {
								$total = 0;
								
								foreach($produtos as $produto) { 
									$total += ($produto->prod_valor - $produto->prod_valorDesc);
						?>
									<tr>
										<td><?php print $produto->prod_titulo; ?></td>
										<td>R$ <?php print number_format($produto->prod_valor - $produto->prod_valorDesc, 2, ',', '.'); ?></td>
									</tr>
						<?php 	
								}
							}
						?>
						<tr>
							<td class="active"><label>Total</label></td>
							<td class="active"><label>R$ <?php print number_format($total, 2, ',', '.'); ?></label></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center">
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" id="enviar-paypal">
									<input type="hidden" name="cmd" value="_xclick">
									<input type="hidden" name="amount" value="<?php print $total; ?>">
									<input type="hidden" name="item_name" value="Finalizar compra e-commerce">
									<input type="hidden" name="business" value="GXKAJJT67Q7Q6">
									<input type="hidden" name="lc" value="PT">
									<input type="hidden" name="button_subtype" value="services">
									<input type="hidden" name="no_note" value="0">
									<input type="hidden" name="no_shipping" value="2">
									<input type="hidden" name="currency_code" value="BRL">
									<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
									<input type="image" src="https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - A forma mais fácil e segura de efetuar pagamentos online!">
									<img alt="" border="0" src="https://www.paypalobjects.com/pt_PT/i/scr/pixel.gif" width="1" height="1">
								</form>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.maskMoney.min.js"></script>
<script src="js/jquery.modal.js"></script>
<script src="js/ecommerce.js"></script>
<script src="JS/jquery.modal.js" type="text/javascript" charset="utf-8"></script>
<script>
	$(function() {
		$("#lista-carrinho img").each(function(){
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