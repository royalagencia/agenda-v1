<?php
if(file_exists("../fewfewgfunctions.php")){ //excluir esse código
	include_once("../../set/Du-Art_functions.php");
}
$users = new Usuarios;

if(isset($GET['bloq'])){
	$u = $users->dados_usuario($GET['bloq']);
	if($u['status'] == 'Bloqueado'){
		$k = true;
	}else{
		$k = false;
	}
	if($users->bloquear_usuario($GET['bloq'],$k)){
		?><script>modal('<?=$txt['alert'][13]?>','<?=$txt['alert'][14]?>');</script><?php
		
	}else{
		?><script>modal('<?=$txt['alert'][0]?>','<?=$txt['alert'][6]?>');</script><?php
	}
}
if(isset($GET['excluir'])){
	if($users->excluir_usuario($GET['excluir'])){
		?><script>modal('<?=$txt['alert'][13]?>','<?=$txt['alert'][14]?>');</script><?php
	}else{
		?><script>modal('<?=$txt['alert'][0]?>','<?=$txt['alert'][6]?>');</script><?php
	}
}
if(isset($GET['Tornar_Admin'])){
	if($users->tornar_admin($GET['Tornar_Admin'])){
		?><script>modal('<?=$txt['alert'][13]?>','<?=$txt['alert'][14]?>');</script><?php
	}else{
		?><script>modal('<?=$txt['alert'][0]?>','<?=$txt['alert'][6]?>');</script><?php
	}
}
$html = array();
$html['regs'] = '';
$busca = $users->listar_usuarios(false,true,35,$PAGE);
if($busca){
	foreach($busca['page'] as $item){
		$border = $item['tipo'] == 'admin' ? '3px solid #f60' : '1px solid #dee2e6';
		$item['admin-label'] = $item['tipo'] == 'admin' ? '<b style="color:#f60">Administrador</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '';
		
		$item['k'] = $item['status']=='Bloqueado'?'K':'k';
		$item['border'] = $border;
		
		$html['regs'] .= renderView('card-reg-user',$item);
			
	}
	$html['regs'] .= check_pagination($busca['full'],$PAGE,$GET_STRING,35);
	
}else{
	$html['regs'] .= '<h2 class="col-12 font-18 text-center mb-5">Nenhum Registro Encontrado</h2>';
}
$BODY['content'] = renderView('page-usuarios',$html);
echo renderView('body',$BODY);
?>