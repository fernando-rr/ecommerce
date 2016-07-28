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
	$("#acao").val($(this).html());
	$("#adicionar-produto").click();
	return false;
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
			var obj 	  = jQuery.parseJSON(data);
			var categoria = obj.prod_categoria;
			
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
			
			$("#imagem").val("");
			$("#titulo").val(obj.prod_titulo);
			$("#descricao").val(obj.prod_descricao);
			$("#valor").val(obj.prod_valor).trigger('mask.maskMoney');
			$("#valor-desc").val(obj.prod_valorDesc).trigger('mask.maskMoney');
			$("#quantidade").val(obj.prod_quantidade);
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