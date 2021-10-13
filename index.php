<?php
include_once("set/dados_db.php");
include_once("set/config.php");
@session_start();
require_once("set/phpmailer/class.phpmailer.php");
include_once("set/Du-Art_functions.php");


$URL_ = strstr($_SERVER['REQUEST_URI'],'?') ? strstr($_SERVER['REQUEST_URI'],'?',true) : $_SERVER['REQUEST_URI'];
if($URL_ <> '/'){
	$URI = $_SERVER['REQUEST_URI'];
	$GET_STRING = strstr($URI,'?');
	$GET = _GET($GET_STRING);
	$PAGE = isset($GET['page']) ? intval($GET['page']) : 1;
	if($GET_STRING){
		$DIR = strstr($URI,'?',true);
	}else{
		$DIR = $URI;
	}
	
	$DIR = trim($DIR,'/');
	$cont = substr_count($DIR, '/');
	define('DIR_COUNT', $cont);
	$arr_tst = explode('/',$DIR);
	$sys['page'] = ucwords(str_replace('-',' ',$arr_tst[$cont]));
	$MODAL__ = false;
	check_session_pages($DIR);
	
}else{
	$MODAL__ = false;
	$GET_STRING = strstr($_SERVER['REQUEST_URI'],'?');
	$GET = _GET($GET_STRING);
	$PAGE = isset($GET['page']) ? intval($GET['page']) : 1;
	$DIR = 'index';
	$sys['page'] = '';
	check_session_pages($DIR);
}
if(isset($GET['token-app-id'])){
	$User = new Usuarios;
	$_SESSION['token-app-id'] = $GET['token-app-id'];
	if($User->id_machine($GET['token-app-id'])){
		header("LOCATION: /");
		exit;
	}
}
function FILTRO_DIR($DIR){
	
	$AB = DIR_COUNT;
	if(isset($AB) and $AB > 0){
		$loop = 0;
		$str = '';
		while($loop < $AB){
			$str .= '../';
			$loop++;
		}
		return $str.$DIR;
	}else{
		return $DIR;
	}
}
function getDefaultMenu(){
	if(!get_connect()){
		return '';
	}
	$Menu = Config::get_menu($_SESSION['tipo'] ?? 'externo');
	$result = renderView('menu-item',array("link"=>"/","texto"=>"Início"));
	if($Menu){
		foreach($Menu as $item){
			$result .= renderView('menu-item',$item);
		}
	}
	if(isset($_SESSION['tipo'])){
		$result .= renderView('menu-item',array("link"=>"/sair","texto"=>"Sair"));
	}
	return $result;
}
function getHtmlHeader($Array=false){
	$Vars = array(
		"extra"=>"",
		"class"=>"bg_color_2 shadow px-5 header-new",
		"class-logo"=>"",
		"logo-link"=>"/img/logo.svg",
		"class-menu"=>"text-right",
		"extra-content"=>'',
		"menu"=>getDefaultMenu()
	);
	if($Array && is_array($Array)){
		foreach($Array as $x=>$y){
			if(isset($Vars[$x])){
				$Vars[$x] = $y;
			}
		}
	}
	return renderView('cabecalho',$Vars);
}



$BODY['global'] = $globalConfig;
$BODY['title-page'] = (!empty($sys['page']) ? $sys['page'].' | ' : '').$BODY['global']['sys']['title'];
$BODY['extra-head'] = renderView('style-extra-body',$BODY);
$BODY['top'] = getHtmlHeader(false);
$BODY['content'] = '';
$BODY['bottom'] = '';
$BODY['bottom-script'] = '';

if(file_exists('includes/'.$DIR.'.php')){
	include_once('includes/'.$DIR.'.php');
}else{
	include_once('includes/not-found.php');
}   	
@close_connect();
?>