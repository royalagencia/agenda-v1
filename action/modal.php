<?php
include_once("../set/dados_db.php");
include_once("../set/config.php");
@session_start();
include_once("../set/Du-Art_functions.php");
$idioma = 'pt';
include_once("../set/lang/".$idioma.'.php');
$MODAL__ = true;
if(isset($_REQUEST['page'])){
	$GET = isset($_REQUEST['GET']) ? _GET($_REQUEST['GET']) : '';
	check_session_pages_modal($_REQUEST['page']);
	if(file_exists('../includes/'.$_REQUEST['page'].'.php')){
		include_once('../includes/'.$_REQUEST['page'].'.php');
	}else{
		include_once("../includes/not-found.php");
	}
}