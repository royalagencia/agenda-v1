<?php
if(file_exists("../set/Du-ftyiftyf.php")){ //excluir esse código
	include_once("../set/Du-Art_functions.php");
}
$Usuarios = new Usuarios;
if(isset($GET['key'])){
	$key = $GET['key'];
	$cod = $Usuarios->check_cod_pass($key);
	if($cod){
		
	}
}else{
	recarregar_pagina();
	return;
}


if(isset($_POST['senha'],$_POST['token']) and !check_post($_POST['token'])){
	receive_post($_POST['token']);
	if($Usuarios->redefinir_senha($_POST['senha'],$key)){
		mensagem_com_location('Sua senha foi redefinida com sucesso!','/login');
	}else{
		mensagem_de_erro();
	}
}


$BODY['content'] = renderView('page-redefinir-minha-senha',md5(time()));
echo renderView('body',$BODY);

?>