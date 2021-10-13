<?php

if(isset($_POST['cad'],$_POST['token']) and !check_post($_POST['token'])){
	receive_post($_POST['token']);
	if($_POST['cad']['senha'] == $_POST['cad']['csenha'] && strlen($_POST['cad']['senha']) >= 6){
		if(is_array($_POST['cad'])){		
			$user = new Usuarios;
			if(!$user->check_mail_user($_POST['cad']['email'])){			
				if($user->novo_usuario($_POST['cad'])){
					//$user->entrar($_POST['cad']['email'],$_POST['cad']['senha']);
					recarregar_pagina('/');
				}else{
					mensagem_de_erro();
				}
			}else{
				mensagem_de_erro($txt['alert'][8].'<br>'.$txt['alert'][7]);
			}
		}else{
			mensagem_de_erro();
		}
	}else{
		mensagem_de_erro('As Senhas Não Coincidem, ou não possui 6 caracteres ou mais.');
	}
}

$html['titulo'] = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin' ? 'Cadastro' :'Cadastre-se';

$cad['class-termos-de-uso'] = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin' ? 'force-hide' : '';
$cad['token'] = set_token();

$html['content'] = renderView('page-cadastro',$cad);

$BODY['content'] = renderView('page-generic',$html);

echo renderView('body',$BODY);
?>