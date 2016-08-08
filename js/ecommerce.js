$('a[href="#area-restrita"]').click(function(){
	$("#container").addClass("center-div");
	$("#container").load("login.php");
});

$('body').on('click','a[href="#entrar"]',function(){
	$.post("auth.php",
    {
        login: $("#login").val(),
        senha: $("#senha").val()
    },
    function(data){
        if(data == "sucess") {
			window.location.replace("index.php");
		} else {
			$(".input-group").addClass("has-error");
			$("#error-msg").show();
		}
    });
});

$('body').on('click','a[href="#adicionar-categoria"]',function(){
	$("#acao").val($(this).html());
	$("#adicionar-categoria").click();
	return false;
});

$('body').on('click','a[href="#adicionar-produto"]',function(){
	if($("#valor").maskMoney('unmasked')[0] >= $("#valor-desc").maskMoney('unmasked')[0]) {
		$("#acao").val($(this).html());
		$("#adicionar-produto").click();
		return false;
	} else {
		alert("O valor do desconto não pode ser maior que o valor do produto");
	}
});

function showAllOptions(){
	$("option").each(function(){
		$(this).show();
	});
};

$('body').on('click','.optCategoria',function(){
	var pai = parseInt($(this).attr("id"));
	var id  = parseInt($(this).val());
	id 		= isNaN(id) ? 0 : id;
	pai 	= isNaN(pai) ? 0 : pai;
	
	showAllOptions();
	if(id) {
		$("#descricao").val(jQuery.trim($(this).next().html()));
		$('option[value="option' + pai + '"]').prop('selected', 'selected').change();
		$('a[href="#adicionar-categoria"]').html("Salvar");
		$('option[value="option' + id + '"]').hide();
		$("#descricao").focus();
		$("#deletar-categoria").show();
	} else {
		$("#descricao").val("");
		$('option[value="option0"]').prop('selected', 'selected').change();
		$('a[href="#adicionar-categoria"]').html("Adicionar");
		$("#descricao").focus();
		$("#deletar-categoria").hide();
	}
	
});

$('body').on('click','.optProduto',function(){
	var id 	= parseInt($(this).val());
	$('input[type="checkbox"]').prop("checked", "");
	if(id) {
		$.post("cadastro_produto.php",
		{
			acao: "Procurar",
			id: id
		},
		function(data){
			var produto   = jQuery.parseJSON(data);
			var categoria = produto.prod_categoria;
			
			if(categoria.length > 2) {
				categoria = categoria.split(";");
				
				$.each(categoria, 	function(key, value){
										$('input[type="checkbox"][value="'+ value +'"]').prop("checked", "checked");
									})
			} else if(categoria != "") {
				$('input[type="checkbox"][value="'+ categoria +'"]').prop("checked", "checked");
			}
			
			$.get("imgView.php",
			{
				id: id
			}, function(data){
				if(data == "") {
					$('#preview').attr('src', "img/no-image-found.png");
				} else {
					$('#preview').attr('src', "data:image/jpeg;base64," + data);
				}
				$('#preview').show();
			});
			
			$("#titulo").val(produto.prod_titulo);
			$("#descricao").val(produto.prod_descricao);
			$("#valor").maskMoney('mask', parseFloat(produto.prod_valor));
			$("#valor-desc").maskMoney('mask', parseFloat(produto.prod_valorDesc));
			$("#quantidade").val(produto.prod_quantidade);
			$("#titulo").focus();
		});
		
		$("a[href='#adicionar-produto']").html("Salvar");
		$("#titulo").focus();
		$("#deletar-produto").show();
	} else {
		$("#imagem").val("");
		$("#uploadForm").get(0).reset();
		$('#preview').hide();
		
		
		$("a[href='#adicionar-produto']").html("Adicionar");
		$("#titulo").focus();
		$("#deletar-produto").hide();
	}
});

$('body').on('click','#deletar-categoria',function(){
	var id = parseInt($("input[type='radio']:checked").val());
	
	if(confirm("Deseja realmente deletar?")) {
		$.post("cadastro_categoria.php",
		{
			acao: "Deletar",
			optCategoria: id
		},
		function(data){
			$(".container-fluid").html(data); return;
			window.location.replace("cadastro_categoria.php");
		});
	}
});

$('body').on('click','#deletar-produto',function(){
	var id = parseInt($("input[type='radio']:checked").val());
	if(confirm("Deseja realmente deletar?")) {
		$.post("cadastro_produto.php",
		{
			acao: "Deletar",
			id: id
		},
		function(data){
			window.location.replace("cadastro_produto.php");
		});
	}
});

$("#uploadForm").submit(function(){
	$("#valor").val($("#valor").maskMoney('unmasked')[0]);
	$("#valor-desc").val($("#valor-desc").maskMoney('unmasked')[0]);
});

var submit = false;

$("#enviar-paypal").submit(function(e){
	
	if(!submit) {
		e.preventDefault();
		
		$.post("carrinho.php", {
			acao: "Finalizar"
		},
		function(data){
			submit = true;
			$("#enviar-paypal").submit();
		});
	}
});

$('input[type="checkbox"][name="selecao-categoria"]').click(function(){
	var categoria = [];
	if($(this).val() == 0) {
		$('input[type="checkbox"]').prop("checked", "");
		$(this).prop("checked", "checked");
	} else {
		$('input[type="checkbox"][value="0"]').prop("checked", "");
	}
	
	if($('input[type="checkbox"]:checked').length) {
	
		$('input[type="checkbox"]:checked').each(function(){
			categoria.push($(this).val());
		});
		
		$.post("produtos.php",
			{
				acao: "Filtrar",
				categoria: categoria
			},
			function(data){
				if(data == "0") {
					$("#lista-produto").html("Nenhum produto encontrado");
					return;
				}
				
				var produtos = jQuery.parseJSON(data);
				var html     = ""
				if(produtos.length) {
					html = '<ul class="thumbnail-list">';
					
					$.each(produtos, function(){
						var produto = $(this)[0];
						
						html += '<li><a href="produtos.php?acao=getProduto&id=' + produto.prod_id + '"><img name="' + produto.prod_id + 
													'" height="200" width="200"></a><h4>' + produto.prod_titulo + 
													'</h4><div class="product-price">';
											
						if(produto.prod_valorDesc > 0) {
							
							$("#val").maskMoney('mask', parseFloat(produto.prod_valor));										
							html += '<span class="cut-price product-desc"> ' + $("#val").val()
							+ '</span>';
						}
						
						$("#val").maskMoney('mask', parseFloat(produto.prod_valorDesc > 0 ? (produto.prod_valor - produto.prod_valorDesc).toFixed(2) : produto.prod_valor));
						html += '<span class="normal-price product-desc"> ' + $("#val").val() + '</span></div></li>';
						
					});
					
					html += '</ul>';
					$("#lista-produto").html(html);
					
					$("#lista-produto img").each(function(){
						var id = $(this).attr("name");
						var img = $(this);
						
						$.get("imgView.php", {
							id: id
						}, function(data){
							img.attr("src", "data:image/jpeg;base64," + data);
						});
					});
				}			
			});
	} else {
		$('input[type="checkbox"][value="0"]').click();
	}
});

$("#adicionar-carrinho").click(function(){
	var id = $(this).attr("name");
	
	if(confirm("Deseja adicionar este produto ao carrinho?")){
		$.post("carrinho.php", {
			acao: "Adicionar",
			id: id
		},
		function(data){
			window.location.replace("carrinho.php");
		});
	}
});

$('body').on('click','a[href="#deletar-produto-carrinho"]',function(){
	var id 	  = $(this).prev().prev().prev().attr("name");
	
	if(confirm("Deseja remover este produto do carrinho?")){
		$.post("carrinho.php", {
			acao: "Remover",
			id: id
		},
		function(data){
			window.location.replace("carrinho.php");
		});
	}
});