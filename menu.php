<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div id="navbar" class="navbar-collapse collapse">
			<!--<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Produtos <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#">Action</a></li>
						<li><a href="#">Another action</a></li>
						<li><a href="#">Something else here</a></li>
						<li role="separator" class="divider"></li>
						<li class="dropdown-header">Nav header</li>
						<li><a href="#">Separated link</a></li>
						<li><a href="#">One more separated link</a></li>
					</ul>
				</li>
			</ul>!-->
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
		</div>
	</div>
</nav>