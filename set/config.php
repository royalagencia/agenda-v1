<?php
$idioma = 'pt';


$sys['title'] = 'Agenda'; // Nome do Sistema
$sys['description'] = 'Agenda de Desenvolvimento'; // Descrição do Sistema
$sys['protocolo'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
$sys['site'] = 'agenda.localhost';

define('_SYS_',$sys);
if(isset($_SERVER['HTTP_HOST'])){
	define('ENDERECO_SITE',$sys['protocolo'].'://'.$_SERVER['HTTP_HOST']);
}else{
	define('ENDERECO_SITE',$sys['protocolo'].'://'.$sys['site']);
}
define('DIR_CSS',ENDERECO_SITE.'/css/');
define('DIR_JS',ENDERECO_SITE.'/js/');
define('DIR_IMG',ENDERECO_SITE.'/img/');

setlocale(LC_TIME, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo'); //FUSO HORARIO


#SMTP_CONFIG
define('GUSER', '#USER_SMTP#');
define('GPWD', '#PASS_SMTP#');
define('SMTP_HOST', '#HOST_SMTP#');
define('SMTP_PORT', 0);


if (version_compare(phpversion(), '7.1', '>=')) {
    ini_set( 'serialize_precision', -1 );
}

include_once("lang/".$idioma.'.php');
define('_LANG_',$txt);

$color__1 = '#033036';
$color__2 = '#065963';
$color__3 = '#0dcae3';
$color__4 = '#0ed5f0';
$color__5 = '#0cb3c9';



$globalConfig['sys'] = $sys;
$globalConfig['lang'] = _LANG_;
$globalConfig['endereco-site'] = ENDERECO_SITE;
$globalConfig['cor1'] = $color__1;
$globalConfig['cor2'] = $color__2;
$globalConfig['cor3'] = $color__3;
$globalConfig['cor4'] = $color__4;
$globalConfig['cor5'] = $color__5;

//#FIM#
?>