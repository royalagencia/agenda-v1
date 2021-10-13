<?php
function get_db(){
	$db_config['host'] = 'localhost';
	$db_config['user'] = 'dev';
	$db_config['pass'] = '';
	$db_config['db'] = 'db3';
	return $db_config;
}
function get_connect(){
	$db_conf = get_db();
	if(@$con = mysqli_connect($db_conf['host'],$db_conf['user'],$db_conf['pass'],$db_conf['db'])){
		mysqli_query($con,"SET NAMES 'utf8'");
		mysqli_query($con,'SET character_set_connection=utf8');
		mysqli_query($con,'SET character_set_client=utf8');
		mysqli_query($con,'SET character_set_results=utf8');
		return $con;
	}else{
		return false;
	}
}

function check_connect(){
	if(!get_connect()){
		echo '<script>alert("Erro ao conectar ao Banco de Dados")</script>';
    	exit;
	}
}
function close_connect(){
	if(mysqli_close(get_connect())){
		return true;
	}else{
		return false;
	}
}
function query($SQL){
	return mysqli_query(get_connect(),$SQL);
}
function get_table_db($data){
	return md5(hash('SHA256',$data.'_c'));
}
function get_ip(){

	if (isset($_SERVER['HTTP_CLIENT_IP'])){
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	}else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else if(isset($_SERVER['HTTP_X_FORWARDED'])){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	}else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	}else if(isset($_SERVER['HTTP_FORWARDED'])){
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	}else if(isset($_SERVER['REMOTE_ADDR'])){
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	}else{
		$ipaddress = 'UNKNOWN';
	}
	
	return $ipaddress;
}
?>