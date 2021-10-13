<?php
if(file_exists("Du-Art_funwdwqdctions.php")){
	@include_once("../set/Du-Art_functions.php");
}
$C = new Config;

$html = array();

if(isset($_POST['set'],$_POST['token']) and !check_post($_POST['token'])){
	receive_post($_POST['token']);
	if(isset($_POST['set'])){
		if($C->set_restricao_de_acesso($_POST['set'][0],$_POST['set'][1])){
			recarregar_pagina('/configuracoes');
		}else{
			mensagem_de_erro();
		}
	}
}


if(isset($GET['excluir'])){
	if($C->delete_restricao_de_acesso($GET['excluir'])){
		recarregar_pagina('/configuracoes');
	}else{
		mensagem_de_erro();
	}
}
if(isset($_POST['set_menu'],$_POST['token']) and !check_post($_POST['token'])){
	receive_post($_POST['token']);
	if($C->new_item_menu($_POST['set_menu'][0],$_POST['set_menu'][1],$_POST['set_menu'][2])){
		mensagem_de_sucesso();
	}else{
		mensagem_de_erro();
	}
}

if(isset($GET['excluir-item'])){
	$str = urldecode($GET['excluir-item']);
	$array = explode('!!',$str);
	if($C->excluir_item_menu($array[0],$array[1],$array[2])){
		recarregar_pagina('/configuracoes');
	}else{
		mensagem_de_erro();
	}
	
}

$arr = $C->get_restricao_de_acesso();
$html['regs-controle-de-acesso'] = '';
if(empty($arr)){
	$html['regs-controle-de-acesso'] = '<h2 class="col-12 font-10 text-center p-3">Nenhum Registro Encontrado</h2>';
}else{
    foreach($arr as $u=>$i){
		$html['regs-controle-de-acesso'] .= '<div class="row w-100 p-0 border_color mb-1 text-center border-radius-4px">';
		$html['regs-controle-de-acesso'] .= '<b class="font-13 resp-col-6 text-left">'.$i['DIR'].'</b>';
		$html['regs-controle-de-acesso'] .= '<b class="font-13 resp-col-4">'.$i['acesso'].'</b>';
		$html['regs-controle-de-acesso'] .= '<div class="resp-col-2 text-right">';
		$html['regs-controle-de-acesso'] .= '<a href="/configuracoes?excluir='.$u.'" onClick="return confirm_modal(this.href)" class="icon font-13 link_hover">x</a>';
		$html['regs-controle-de-acesso'] .= '</div></div>';

	}
}
$html['token'] = md5(time());
@$arr = $C->get_menu();
$html['regs-menu'] = '';
if(empty($arr)){
	$html['regs-menu'] .= '<div class="row w-100 p-0 border_color mb-1 text-center border-radius-4px bg_color_2 text-white">';
	$html['regs-menu'] .= '<b class="font-11 col-12 text-center">Nenhum Item Encontrado</b></div>';
}else{
	$html['regs-menu'] .= '<div class="row w-100 p-0 border_color mb-1 text-center border-radius-4px bg_color_2 text-white">';
	$html['regs-menu'] .= '<b class="font-10 col-5 text-left">Link</b><b class="font-10 col-3 text-left">Item</b><b class="font-10 col-4">Tipo</b></div>';
	foreach($arr as $u=>$i){
		$html['regs-menu'] .= '<div class="row w-100 p-0 border_color mb-1 text-center border-radius-4px">';
		$html['regs-menu'] .= '<b class="font-13 resp-col-5 text-left">'.$i['link'].'</b>';
		$html['regs-menu'] .= '<b class="font-13 resp-col-3 text-left">'.$i['texto'].'</b>';
		$html['regs-menu'] .= '<b class="font-13 resp-col-3">'.$i['tipo'].'</b>';
		$html['regs-menu'] .= '<div class=" resp-col-1 text-center">';
		$html['regs-menu'] .= '<a href="/configuracoes?excluir-item='.urlencode($i['texto'].'!!'.$i['link'].'!!'.$i['tipo']).'" style="line-height: 2em;" class="icon font-10 link_hover text-right">x</a>';
		$html['regs-menu'] .= '</div></div>';
		
	}
}
$BODY['content'] = renderView('page-configuracoes',$html);
echo renderView('body',$BODY);