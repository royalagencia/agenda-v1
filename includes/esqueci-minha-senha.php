<?php
if(file_exists("../set/Du-ftyiftyf.php")){ //excluir esse código
	include_once("../set/Du-Art_functions.php");
}
$Usuarios = new Usuarios;
if(isset($_POST['email'],$_POST['token']) and !check_post($_POST['token'])){
	receive_post($_POST['token']);
	$user = $Usuarios->check_mail_user($_POST['email']);
	if($user){
		if($Usuarios->esqueci_minha_senha($user['hash'])){
			mensagem_de_sucesso('Você receberá um email com as instruções para redefinir sua senha');
		}else{
			mensagem_de_erro();
		}
	}else{
		mensagem_de_sucesso('Você receberá um email com as instruções para redefinir sua senha!');
	}
}
$BODY['content'] = renderView('page-esqueci-minha-senha',md5(time()));
echo renderView('body',$BODY)
?>