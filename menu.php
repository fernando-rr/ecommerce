<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="produtos.php">Produtos</a>
				</li>
			</ul>
			<?php
				if($_SESSION["logged"] == 1) {
					print '	<ul class="nav navbar-nav">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cadastro<span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="cadastro_categoria.php">Categoria</a></li>
										<li><a href="cadastro_produto.php">Produto</a></li>
									</ul>
								</li>
							</ul>';
				}
			?>
			<ul class="nav navbar-nav navbar-right">
				<?php
					if($_SESSION["logged"] == 1) {
						print '<li class="active"><a href="logout.php">Sair</a></li>';
					} else {
						print '<li class="active"><a href="#area-restrita">Area restrita</a></li>';
					}
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="carrinho.php">Meu carrinho</a>
				</li>
			</ul>
		</div>
	</div>
</nav>