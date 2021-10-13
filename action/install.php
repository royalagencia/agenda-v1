<?php
include_once("../set/dados_db.php");
include_once("../set/config.php");
@session_start();
include_once("../set/Du-Art_functions.php");
$idioma = 'pt';
include_once("../set/lang/".$idioma.'.php');
if(isset($_POST['db'],$_POST['user'],$_POST['pass'],$_POST['host'])){
	$array = array($_POST['db'],$_POST['user'],$_POST['pass'],$_POST['host']);

	

	$file = file("../set/dados_db.php");
	$newFile = array();

	foreach($file as $l=>$i){
		if(strstr($i,'$db_config[\'host\'] = ')){
			$newFile[$l] = '$db_config[\'host\'] = \''.$_POST['host']."';\n";
			continue;
		}
		if(strstr($i,'$db_config[\'user\'] = ')){
			$newFile[$l] = '$db_config[\'user\'] = \''.$_POST['user']."';\n";
			continue;
		}
		if(strstr($i,'$db_config[\'pass\'] = ')){
			$newFile[$l] = '$db_config[\'pass\'] = \''.$_POST['pass']."';\n";
			continue;
		}
		if(strstr($i,'$db_config[\'db\'] = ')){
			$newFile[$l] = '$db_config[\'db\'] = \''.$_POST['db']."';\n";
			continue;
		}

		$newFile[$l] = $i;


	}

	$fp = fopen("../set/dados_db.php","w+");
	
	foreach($newFile as $linha){
		fwrite($fp, $linha);
	}

	fclose($fp);
	echo 'Realizado!<br><br><br>';
}
?>
<form action="" method="post">
	<label>Host</label>
	<input type="text" name="host" value="localhost" required><br><br>

	<label>User</label>
	<input type="text" name="user" value="root" required><br><br>

	<label>Pass</label>
	<input type="text" name="pass" value=""><br><br>

	<label>DB</label>
	<input type="text" name="db" value="" required><br><br>

	<button>Configurar</button>

</form>