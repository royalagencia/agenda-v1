<?php
class Usuarios{
	public static function novo_usuario($array,$foto=false){
		if(is_array($array)){
			foreach($array as $i=>$d){
				$dados[$i] = FILTRO($d);
			}
			$DB['nome'] = FILTRO_nome($dados['nome']);
			$DB['senha'] = FILTRO_senha($dados['senha']);
			$DB['email'] =strtolower($dados['email']);
			$DB['tipo'] = 'normal';
			$DB['data'] = date_time_now();
			#CAMPOS_EXTRAS#
			$DB['status'] = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin' ? 'Ativo' : 'Ativo';
			$hash = insertDB('users',$DB,'hash');
			if($hash){
				#USER_FOTO#
				regLog('0100 - '.$DB['nome'].' < '.$DB['email'].' > Se Cadastrou',0);
				return true;
			}else{
				regLog('0101 - Erro ao tentar cadastrar '.$DB['nome'].' < '.$DB['email'].' >',3);
				return false;
			}
		}else{
			regLog('0103 - Erro de requisição ao tentar cadastrar '.$DB['nome'].' < '.$DB['email'].' >',3);
			return false;
		}
	}
	public static function entrar($email,$senha){
		$email = FILTRO($email);
		$senha = FILTRO_senha($senha);
		$busca = get_data_single("SELECT * FROM `yul__users` WHERE `email` = '$email' AND `senha` = '$senha' AND `status` = 'Ativo'");
		if($busca){
			@session_start();
			$key_user = $busca['hash'];
			$_SESSION['ID'] = $busca['ID'];
			$_SESSION['nome'] = $busca['nome'];
			$_SESSION['email'] = $busca['email'];
			$_SESSION['tipo'] = $busca['tipo'];
			$_SESSION['key'] = $busca['hash'];
			$_SESSION['dados'] = $busca;
			$_SESSION['token'] = set_token();
			regLog('1010 - '.$busca['nome'].' < '.$busca['email'].' > Entrou',0);
			if(isset($_SESSION['token-app-id'])){
				self::inserir_id($_SESSION['token-app-id'],$busca['hash']);
			}
			return true;
		}else{
			regLog('1011 - Dados de Login Inválidos: '.$email,0);
			return false;
		}
	}
	public static function entrar_hash($key){
		$key = FILTRO($key);
		$busca = get_data_single("SELECT * FROM `yul__users` WHERE `hash` = '$key' AND `status` = 'Ativo'");
		if($busca){
			@session_start();
			$key_user = $busca['hash'];
			$_SESSION['ID'] = $busca['ID'];
			$_SESSION['nome'] = $busca['nome'];
			$_SESSION['email'] = $busca['email'];
			$_SESSION['tipo'] = $busca['tipo'];
			$_SESSION['key'] = $busca['hash'];
			$_SESSION['dados'] = $busca;
			$_SESSION['token'] = set_token();
			regLog('1010 - '.$busca['nome'].' < '.$busca['email'].' > Entrou',0);
			if(isset($_SESSION['token-app-id'])){
				self::inserir_id($_SESSION['token-app-id'],$busca['hash']);
			}
			return true;
		}else{
			return false;
		}
	}

	public static function id_machine($COD){
		if(strstr($COD,'---')){
	        $COD = explode('---',$COD)[0];
	    }
		$COD = FILTRO_senha($COD);
		$busca = get_data_single("SELECT * FROM `yul__app-token` WHERE `code` = '$COD'");
		if($busca){
			$in = intval(strtotime("now"));
			$ip = get_ip();
			if(self::entrar_hash($busca['user'])){
				query("UPDATE `yul__app-token` SET `last_used` = $in, `ip` = '$ip' WHERE `code` = '$COD'");
				return true;
			}else{
				return false;
			}
		}else{
			regLog('1021 - Erro ao buscar app token',0);
			return false;
		}
	}
	public static function inserir_id($COD,$UserID){
		if(strstr($COD,'---')){
	        $C = explode('---',$COD);
	        $DB['firebase-token'] = $C[1];
	        $DB['code'] = FILTRO_senha($C[0]);
	    }else{
	        $DB['code'] = FILTRO_senha($COD);
	    }
		$UserID = FILTRO($UserID);
		$in = intval(strtotime("now"));
		$DB['user'] = $UserID;
		$DB['last_used'] = $in;
		if(insertDB('app-token',$DB,'ip')){
			regLog('1020 - Token inserido com sucesso para o user '.($_SESSION['email']??$UserID),0);
			return true;
		}else{
			regLog('1023 - Erro ao tentar inserir token para: '.$UserID,0);
			return false;
		}
	}

	public static function sair(){
		if(isset($_SESSION['nome'],$_SESSION['email'])){
			regLog('1018 - '.$_SESSION['nome'].' < '.$_SESSION['email'].' > Saiu',0);
		}
		@session_start();
		if(isset($_SESSION['token-app-id'])){
	        $token = FILTRO_senha($_SESSION['token-app-id']);
	        query("DELETE FROM `yul__app-token` WHERE `code` = '$token'");
	    }
		session_destroy();
	}

	public static function aprovar_usuario($key){
		$key = FILTRO($key);
		$user = self::dados_usuario($key);
		if($user){
			$DB['status'] = 'Ativo';
			if(updateDB('users',$DB,$user['hash'])){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function listar_usuarios($filtrosArray=false,bool $pagination=false,int $itens_p_pagina=35,int $pagina_atual=1){
		if($filtrosArray){
			$str = 'WHERE ';
			foreach($filtrosArray as $a=>$b){
				$str .= '`'.$a.'` LIKE \'%'.$b.'%\'***@@***';
			}
			$str = trim($str,'***@@***');
			$str = str_replace('***@@***',' AND ',$str);
		}else{
			$str = '';
		}
		$busca = get_data("SELECT * FROM `yul__users` $str ORDER BY `nome` ASC",$pagination,$itens_p_pagina,$pagina_atual);
		return $busca;
	}



	public static function bloquear_usuario(string $ID_Usuario, bool $desbloquear=false){
		$busca = self::dados_usuario($ID_Usuario);
		if($busca){
			if($desbloquear){
				$DB['status'] = 'Ativo';

			}else{
				$DB['status'] = 'Bloqueado';
			}
			return updateDB('users',$DB,$busca['hash']);
		}else{
			return false;
		}
	}
	public static function excluir_usuario($ID_Usuario){
		$busca = self::dados_usuario($ID_Usuario);
		if($busca){
			$DB['status'] = 'Excluído';
			return updateDB('users',$DB,$busca['hash']);
		}else{
			return false;
		}
	}
	public static function dados_usuario($ID_Usuario){
		$str = is_hash($ID_Usuario) ? ' `hash` = '."'$ID_Usuario'" : ' `ID` = '."'$ID_Usuario'";
		$SQL = "SELECT * FROM `yul__users` WHERE $str";
		$busca = get_data_single($SQL);
		return $busca?$busca:false;
	}
	public static function tornar_admin($ID){
		$busca = self::dados_usuario($ID);
		if($busca && $_SESSION['tipo'] == 'admin'){
			$DB['tipo'] = ($busca['tipo'] == 'normal' ? 'admin' : 'normal');
			return updateDB('users',$DB,$busca['hash']);
		}else{
			return false;
		}
	}
	public static function check_mail_user($email){
		$email = FILTRO($email);
		$busca = get_data_single("SELECT * FROM `yul__users` WHERE `email` = '$email'");
		if($busca){
			return $busca;
		}else{
			return false;
		}
	}
	public static function check_user($cpf,$email){
		$cpf = FILTRO($cpf);
		$email = FILTRO($email);
		$busca = get_data_single("SELECT * FROM `yul__users` WHERE `cpf` = '$cpf' OR `email` = '$email'");
		if($busca){
			return true;
		}else{
			return false;
		}
	}
	public static function alterar_senha($key_user,$senha){
		$key_user = FILTRO($key_user);
		$senha = FILTRO_senha($senha);
		$N = new Notification;
		$u = self::dados_usuario($key_user);
		$DB['senha'] = $senha;

		if(updateDB('users',$DB,$u['hash'])){
			@$N->send_email_notif_html(1,1,$key_user,'');
			return true;
		}else{
			return false;
		}
	}
	public static function alterar_email($key_user,$email){
		$key_user = FILTRO($key_user);
		$busca = self::dados_usuario($key_user);
		$email = FILTRO($email);
		if($busca){
			$DB['email'] = $email;
			return updateDB('users',$DB,$busca['hash']);
		}else{
			return false;
		}
	}
	public static function consultar_cpf($CPF){
		$CPF = FILTRO($CPF);
		$busca = get_data_single("SELECT * FROM `yul__users` WHERE `cpf` = '$CPF'");
		if($busca){
			return $busca;
		}else{
			return false;
		}
	}
	public static function esqueci_minha_senha($user){
		$cod = time().rand(1001,9999);
		$key = set_token();
		$data = date("Y-m-d H:i:s");
		$exp = date("Y-m-d H:i:s",strtotime("+2 hours"));
		$busca = self::dados_usuario($user);

		if($busca){
			query("UPDATE `yul__code_pass` SET `status` = 'Expirado' WHERE `user_key` = '$user'");
			if(query("INSERT INTO `yul__code_pass` VALUES (NULL,'$user','$cod','$data','$exp','Ativo','$key')")){
				$obj = new Notification;
				if($obj->send_email_notif_html(2,2,$user,'Acesse o Link Para Redefinir a Sua Senha: <a href="'.ENDERECO_SITE.'/redefinir-minha-senha?key='.$key.'">'.ENDERECO_SITE.'/redefinir-minha-senha?key='.$key.'</a>')){
					//$this->novo_log('103 - '.$busca['nome'].' ('.$busca['hash'].') Solicitou Alteração de Senha');
					regLog('1030 - Recuperação de senha enviada para: '.$busca['nome'].' < '.$busca['email'].' >',4);
					return true;
				}else{
					query("UPDATE `yul__code_pass` SET `status` = 'Expirado' WHERE `user_key` = '$user'");
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function check_cod_pass($KEY){
		$KEY = FILTRO($KEY);
		$busca = get_data_single("SELECT * FROM `yul__code_pass` WHERE `hash` = '$KEY' AND `status` = 'Ativo'");
		if($busca){
			return $busca;
		}else{
			return false;
		}
	}
	public static function redefinir_senha($senha,$key){
		$key = FILTRO($key);
		$cod = self::check_cod_pass($key);
		if($cod){
			if(self::alterar_senha($cod['user_key'],$senha)){
				self::status_code_pass($key,'Utilizado');
				return true;
			}
		}else{
			return false;
		}
	}
	public static function status_code_pass($key,$status){
		$key = FILTRO($key);
		$status = FILTRO($status);
		$busca = get_data("SELECT * FROM `yul__code_pass` WHERE `hash` = '$key'");
		if($busca){
			if(query("UPDATE `yul__code_pass` SET `status` = '$status' WHERE `hash` = '$key'")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

class Config{

	public static function get_config($ID){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = '$ID'");
		return $busca ? $busca['valor'] : false;;
	}
	public static function set_config(string $ID,$Config,bool $json=false){
		if($json && is_array($Config)){
			$Config = json_encode($Config,JSON_UNESCAPED_UNICODE);
		}else{
			$Config = $Config;
		}
		if(query("UPDATE `yul__settings` SET `valor` = '$Config' WHERE `config` = '$ID'")){
			return true;
		}else{
			return false;
		}
	}
	public static function set_restricao_de_acesso($DIR,$nivel){
		$array['DIR'] = $DIR;
		$array['acesso'] = $nivel;
		$x = self::get_restricao_de_acesso();
		$x[] = $array;
		$json = json_encode($x);
		if(query("UPDATE `yul__settings` SET `valor` = '$json' WHERE `config` = 'acesso_restrito'")){
			return true;
		}else{
			return false;
		}
	}
	public static function get_restricao_de_acesso(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'acesso_restrito'");
		if($busca and !empty($busca['valor'])){
			return json_decode($busca['valor'],true);
		}else{
			return false;
		}
	}
	public static function delete_restricao_de_acesso($index){
		$arr = self::get_restricao_de_acesso();
		if(isset($arr[$index])){
			unset($arr[$index]);
			$json = json_encode($arr);
			if(query("UPDATE `yul__settings` SET `valor` = '$json' WHERE `config` = 'acesso_restrito'")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function email_system(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'email_system'");
		return $busca ? $busca['valor'] : false;
	}
	public static function alt_email_system(string $email){
		if(query("UPDATE `yul__settings` SET `valor` = '$email' WHERE `config` = 'email_system'")){
			return true;
		}else{
			return false;
		}
	}
	public static function get_menu($tipo=false){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'itens_menu'");
		if($busca and !empty($busca['valor'])){
			$array = json_decode($busca['valor'],true);
			foreach($array as $index=>$item){
				if($tipo){
					if($item['tipo'] == $tipo){
						unset($item['tipo']);
						$result[$index] = $item;
					}
				}else{
					$result[$index] = $item;
				}
			}
			return isset($result) ? $result : false;
		}else{
			return false;
		}
	}
	public static function new_item_menu($text,$link,$tipo){
		$busca = self::get_menu();
		if($busca){
			$array['texto'] = FILTRO_nome($text);
			$array['link'] = $link;
			$array['tipo'] = $tipo;
			$busca[count($busca)] = $array;
			if(query("UPDATE `yul__settings` SET `valor` = '".json_encode($busca, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."' WHERE `config` = 'itens_menu'")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function excluir_item_menu($texto,$link,$tipo){
		$busca = self::get_menu();
		if($busca){

			foreach($busca as $index=>$item){
				if($item['texto'] == $texto && $item['link'] == $link && $item['tipo'] == $tipo){
					unset($busca[$index]);
				}else{
					$array[] = $item;
				}
			}

			if(query("UPDATE `yul__settings` SET `valor` = '".json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."' WHERE `config` = 'itens_menu'")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function InstallExternalSMTP(array $DADOS){
		$file = file(__DIR__."/config.php");

		$escrita['user'] = 'define(\'GUSER\',\''.$DADOS['user'].'\');'."\n";
		$escrita['pass'] = 'define(\'GPWD\',\''.$DADOS['pass'].'\');'."\n";
		$escrita['host'] = 'define(\'SMTP_HOST\',\''.$DADOS['host'].'\');'."\n";
		$escrita['port'] = 'define(\'SMTP_PORT\','.$DADOS['port'].');'."\n";

		foreach($file as $n=>$l){
			if(strstr($l,'define(\'GUSER\',')){
				$file[$n] = $escrita['user'];
				unset($escrita['user']);
				continue;
			}
			if(strstr($l,'define(\'GPWD\',')){
				$file[$n] = $escrita['pass'];
				unset($escrita['pass']);
				continue;
			}
			if(strstr($l,'define(\'SMTP_HOST\',')){
				$file[$n] = $escrita['host'];
				unset($escrita['host']);
				continue;
			}
			if(strstr($l,'define(\'SMTP_PORT\',')){
				$file[$n] = $escrita['port'];
				unset($escrita['port']);
				continue;
			}
		}
		if(isset($escrita) && !empty($escrita)){
			foreach($escrita as $write){
				$file[] = $write;
			}
		}
		escrever_arquivo(__DIR__."/config.php",join('',$file),true);
	}
}
class Notification{

	private $email_system;
	private $ExternalSMTP;

	function __construct(){
		$this->email_system = Config::email_system();
		$smtp = Config::get_config('external-smtp');
		$this->ExternalSMTP = $smtp && $smtp == 'true' ? true : false;
	}

	function send_email_notif(int $IDassunto,int $ID_Message,$ID_User=false,string $More_Message=''){

		$lang = _LANG_;
		$mensagem = $lang['emails']['mensagens'][$ID_Message];
		$mensagem = $mensagem."\n\n".$More_Message;
		$assunto = $lang['emails']['assuntos'][$IDassunto];
		if($ID_User){
			$busca = Usuarios::dados_usuario($ID_User);
			if($busca){
				$destino = $busca['email'];
			}else{
				return false;
			}
		}else{
			$destino = $this->email_system;
		}
		if($this->ExternalSMTP){
			$sys = _SYS_;
			smtpmailer($destino, $this->email_system, $sys['title'],$assunto, $mensagem,false);
		}else{
			$email_headers = implode ( "\n",array ( 'From: '.$sys['title'].'<'.$this->email_system.'>', "Reply-To: $this->email_system", "Return-Path: $this->email_system","MIME-Version: 1.0","X-Priority: 3","Content-Type: text/plain; charset=UTF-8" ) );
			return mail ($destino, $assunto, $mensagem, $email_headers)? true : false;
		}
	}
	function send_email_notif_html(int $IDassunto,int $ID_Message,$ID_User=false,string $More_Message=''){

		$lang = _LANG_;
		$sys = _SYS_;
		$assunto = $lang['emails']['assuntos'][$IDassunto];
		$mensagem = $lang['emails']['mensagens'][$ID_Message].'<br><br>'.$More_Message;

		$vars['color'] = $GLOBALS['color__2'];
		$vars['logo'] = DIR_IMG.'logo-banner.png';
		$vars['title'] = '';
		$vars['site'] = $sys['site'];
		$vars['text-color'] = '#333333';
		$email['header'] = renderView('header-email',$vars);
		$vars['text-color'] = '#ffffff';
		$email['footer'] = renderView('footer-email',$vars);
		$email['texto'] = $mensagem;
		$email['titulo'] = $assunto;

		$mensagem = renderView('email',$email);

		if($ID_User){
			$busca = Usuarios::dados_usuario($ID_User);
			if($busca){
				$destino = $busca['email'];
			}else{
				return false;
			}
		}else{
			$destino = $this->email_system;
		}
		//$mensagem = nl2br($mensagem);
		$origem = $this->email_system;


		if($this->ExternalSMTP){
			$sys = _SYS_;
			return smtpmailer($destino, $this->email_system, $sys['title'],$assunto, $mensagem,true);
		}else{
			$email_headers = implode ( "\n",array ( 'From: '.$sys['title'].'<'.$origem.'>',"Content-Type: text/html; charset=UTF-8" ) );
			return mail ($destino, $assunto, $mensagem, $email_headers)? true : false;
		}
	}
}



class Social{
	public static function get_facebook(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca['facebook'] : false;
	}
	public static function get_whatsapp(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca['whatsapp'] : false;
	}
	public static function get_twitter(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca['twitter'] : false;
	}
	public static function get_linkedin(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca['linkedin'] : false;
	}
	public static function get_youtube(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca['linkedin'] : false;
	}
	public static function get_all(){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$busca = $busca ? json_decode($busca['valor'],true) : false;
		return $busca ? $busca : false;
	}
	public static function set_social(string $name,string $contact){
		$busca = get_data_single("SELECT * FROM `yul__settings` WHERE `config` = 'social_contact'");
		$name = preg_replace("/ /", '',$name);
		$name = strtolower($name);
		if($busca){
			$busca = json_decode($busca['valor'],true);
			$busca[$name] = $contact;
			$json = json_encode($busca);
			if(query("UPDATE `yul__settings` SET `valor` = '$json' WHERE `config` = 'social_contact'")){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

class Log{
	function novo_log($text){
		if(isset($_SESSION['tipo'])){
			$user['Key'] = $_SESSION['key'];
			$user['Nome'] = $_SESSION['nome'];
			$user['CPF'] = $_SESSION['dados']['cpf'];
			$origem['Usuário'] = $user;
		}else{
			$user['Sistema'] = $_SERVER['REQUEST_URI'];
			$origem['Sys'] = $user;
		}
		$ip = get_ip();
		$k = set_token();
		$dt = date_time_now();
		$json = json_encode($origem,FLAGS('json'));
		if(query("INSERT INTO `__log` VALUES ('','$text','$json','$dt','$ip','$k')")){
			return true;
		}else{
			return false;
		}
	}
	function buscar_log($code=false,$user=false,$dataArray=false){
		$SQL = "SELECT * FROM `__log` ";
		if($user||$dataArray||$code){
			$SQL .= 'WHERE ';
			$yy = '';
			if($code){
				$yy .= "`text` LIKE '$code -%' AND ";
			}
			if($user){
				$i = '"Key":"'.$user.'"';
				$yy .= "(`text` LIKE '%$user%' OR `origem` LIKE '%$i%') AND ";
			}
			if($dataArray){
				$d1 = $dataArray[0] ? date('Y-m-d H:i:s',strtotime($dataArray[0])) : false;
				$d2 = $dataArray[1] ? date('Y-m-d H:i:s',strtotime($dataArray[1])) : false;
				$a='';
				$b='';
				if($d1){
					$a = "`data` > '$d1' AND ";
				}
				if($d2){
					$b = "`data` < '$d2' AND ";
				}
				$yy .= $a.$b;
			}
			$yy = trim($yy,' AND ');
			$SQL .= $yy;
		}
		return get_data($SQL);
	}
}

//===================== Objetos Especiais ===================

class Backup{
	private $db;
	private $tables;
	private $file = array();

	function __construct($db=false){
		if($db){
			$this->db = $db;
		}else{
			$db = get_db();
			$this->db = $db['db'];
		}
		$this->tables = $this->set_tables($this->db);
		$this->set_drop();
		$this->set_creates();
		$this->set_inserts();
	}
	function set_tables($db){
		$tables = get_data("SHOW TABLES FROM $db");
		foreach($tables as $IN => $KY){
			foreach($KY as $AA => $BB){
				$array[$IN]= $BB;
			}
		}
		return $array;
	}
	function get_tables(){
		return $this->tables;
	}
	private function set_creates(){
		$array = array();
		foreach($this->tables as $table){
			$create = get_data_single("SHOW CREATE TABLE `$table`");
			$array[] = $create['Create Table'].";\n";
		}
		$array[]= "\n\n";
		$this->file = array_merge($this->file,$array);
	}
	private function set_inserts(){
		foreach($this->tables as $table){
			$this->make_insert($table);
		}
	}
	private function make_insert($table){
		$busca = get_data("SELECT * FROM `$table`");
		$array = array();
		if($busca){
			foreach($busca as $i){
				$vls = '';
				foreach($i as $f=>$v){
					if(is_string($v)){
						$vls .= "'$v',";
					}else if(is_null($v)){
						$vls .= "NULL,";
					}else{
						$vls .= "$v,";
					}
				}
				$vls = $this->filtro(trim($vls,','));
				$array[] = "INSERT INTO `$table` VALUES ($vls);\n";
			}
			$array[]= "\n\n";
			$this->file = array_merge($this->file,$array);
		}
	}
	private function set_drop(){
		$array = array();
		foreach($this->tables as $table){
			$array[] = "DROP TABLE IF EXISTS `$table`;\n";
		}
		$array[]= "\n\n";
		$this->file = array_merge($this->file,$array);
	}
	function output($path=__DIR__.'/../DB.sql'){
		$fp = fopen($path,"w+");
		foreach($this->file as $line){
			fwrite($fp,$line);
		}
		fclose($fp);
	}
	private function filtro($str){
		return str_replace('"','\"',$str);
	}
}


class SyncDB{
	private $file;
	private $commands = array();
	function __construct($path=__DIR__.'/../DB.sql'){
		if(file_exists($path)){
			$this->file = file_get_contents($path);
		}else{
			echo 'Arquivo DB Inexistente!';
			return;
		}
		$this->set_commands();
	}
	private function set_commands(){
		preg_match_all('/(INSERT|DROP|CREATE)[\w\W]{1,}(\;)/U',$this->file,$array);
		foreach($array[0] as $command){
			array_push($this->commands,str_replace(';','',$command));
		}
	}
	function exe(){
		foreach($this->commands as $SQL){
			query($SQL);
		}
	}
}

/**
 *
 */
class Agenda{
	private $regs;
	function __construct()
	{
		$now = date("Y-m-d H:i:s", strtotime("now -4 hours"));
		$now2 = date("Y-m-d H:i:s", strtotime("now -15 days "));
		$lista = get_data("SELECT * FROM `yul__agenda` WHERE `data-hora` >= '$now'");
		if($lista){
			$this->regs = $lista;
		}
	}
}


#ADICIONAL_CONTENT#

////================================ FINAL dos objetos ============


//===================== Funções Essenciais =====================

function smtpmailer($para, $de, $de_nome, $assunto, $corpo,$isHTML=false) {
	$mail = new PHPMailer();
	$mail->IsSMTP();		// Ativar SMTP
	$mail->SMTPDebug = 0;		// Debugar: 1 = erros e mensagens, 2 = mensagens apenas
	$mail->SMTPAuth = true;		// Autenticação ativada
//	$mail->SMTPSecure = 'ssl';	// SSL REQUERIDO pelo GMail
	$mail->Host = SMTP_HOST;	// SMTP utilizado
	$mail->Port = SMTP_PORT;  		// A porta 587 deverá estar aberta em seu servidor
	$mail->Username = GUSER;
	$mail->Password = GPWD;
	$mail->SetFrom($de, $de_nome);
	$mail->IsHTML($isHTML);
	$mail->Subject = $assunto;
	$mail->CharSet = 'UTF-8';
	$mail->Body = $corpo;
	$mail->AddAddress($para);
	if(!$mail->Send()) {
		return false;
	} else {
		return true;
	}
}
function regLog(string $text,int $priority=3){
	$DB = array(
	"log"=>$text,
	"prioridade"=>$priority
	);
	if(insertDB('logs',$DB,'data,ip,hash')){
		return true;
	}else{
		return false;
	}
}
function write_array(string $string,$array){
	preg_match_all('/({%)[\W\w]{1,}(%})/U',$string,$indexes);
	$indexes = $indexes[0];
	if($array){
		foreach($array as $item){
			$txt = $string;
			foreach($indexes as $x){
				$y = preg_replace('/({%)|(%})/','',$x);
				if(strstr($y,'->')){
					$blc = explode('->',$y);
					$func = false;
					if(count($blc)==2 || count($blc)==4){
						$func = $blc[count($blc)-1];
					}
					if(count($blc) > 2){
						$busca = verItemDb($blc[1],$item[$blc[0]]);
						$sub = $busca[$blc[2]];
					}else{
						$sub = $item[$blc[0]];
					}
					if($func){
						$sub = $func($sub);
					}
				}else{
					$sub = $item[$y];
				}
				$txt = str_replace($x,$sub,$txt);
			}
			return $txt;
		}
	}
}
function str_var(string $string,array $vars){
	$txt = $string;
	preg_match_all('/({%)[\W\w]{1,}(%})/U',$string,$indexes);
	$indexes = $indexes[0];
	foreach($indexes as $x){
		$y = preg_replace('/({%)|(%})/','',$x);

		if(strstr($y,'->')){
			$blc = explode('->',$y);
			$func = false;
			if(count($blc)==2 || count($blc)==4){
				$func = $blc[count($blc)-1];
			}
			if(isset($vars[$blc[0]])){
				if(count($blc) > 2){
					$busca = verItemDb($blc[1],$vars[$blc[0]]);
					$sub = $busca[$blc[2]];
				}else{
					$sub = $vars[$blc[0]];
				}
				if($func){
					$sub = $func($sub);
				}
				$txt = str_replace($x,$sub,$txt);
			}
		}else if(isset($vars[$y])){
			$txt = str_replace($x,$vars[$y],$txt);
		}

	}
	return $txt;
}

function getListDb(string $table,$filtrosArray=false,$mod='=',$orderBy=false,$pagination=false,int $itens_p_pagina=35,int $pagina_atual=1){
	if($filtrosArray){
		$str = 'WHERE ';
		foreach($filtrosArray as $a=>$b){
			if($mod == 'LIKE'){
				$str .= '`'.$a.'` LIKE \'%'.$b.'%\'***@@***';
			}else{
				$str .= '`'.$a.'` '.$mod.' \''.$b.'\'***@@***';
			}
		}
		$str = trim($str,'***@@***');
		$str = str_replace('***@@***',' AND ',$str);
	}else{
		$str = '';
	}

	$order = '';
	if($orderBy){
		if(strstr($orderBy,' ')){
			$oT = explode(' ',$orderBy)[0];
			$oP = explode(' ',$orderBy)[1];
		}else{
			$oT = $orderBy;
			$oP = '';
		}
		$order = "ORDER BY `$oT` $oP";
	}
	$SQL = "SELECT * FROM `yul__$table` $str $order";
	return get_data($SQL,$pagination,$itens_p_pagina,$pagina_atual);
}
function verItemDb($table,$key){
	$WHERE = strlen($key) == strlen(set_token()) ? "WHERE `hash` = '$key'" : "`ID` = '".intval($key)."'";
	$SQL = "SELECT * FROM `yul__$table` $WHERE";
	return get_data_single($SQL);
}
function updateDB(string $table,array $ArrayDados,$key){
	$WHERE = strlen($key) == strlen(set_token()) ? "WHERE `hash` = '$key'" : "`ID` = '".intval($key)."'";
	$log = array();
	$DESCRIBE = checkFieldsDB($table);

	if(!$DESCRIBE){ return false;}
	foreach($ArrayDados as $field => $valor){
		if(isset($DESCRIBE[$field])){
			if(query("UPDATE `yul__$table` SET `$field`='$valor' $WHERE")){
				$log[$field] = true;
			}else{
				$log[$field] = false;
			}
		}else{
			$log[$field] = true;
		}
	}

	$false = 0;
	foreach($log as $i){
		if(!$i){ $false++;}
	}
	if($false == count($ArrayDados)){
		$result = false;
	}else if($false == 0){
		$result = true;
	}else{
		$result = $log;
	}
	return $result;
}

function AutoInsert($data){
	switch($data){
		case 'data':
			return date_time_now();
		case 'hash':
			return set_token();
		case 'ip':
			return get_ip();
		default:
			return false;

	}
}
function insertDB(string $table,array $ArrayDados,$Auto=false){
	$AutoHash = false;
	if($Auto){
		$Auto = str_replace(' ','',$Auto);
		if(!empty($Auto)){
			if(strstr($Auto,',')){
				$Autos = explode(',',$Auto);
			}else{
				$Autos = array($Auto);
			}
			if(in_array('hash',$Autos)){
				$AutoHash = true;
			}
			foreach($Autos as $i){
				if(AutoInsert($i)){
					$ArrayDados[$i] = AutoInsert($i);
				}
			}
		}
	}

	$values = '(';
	$campos = '(';
	foreach($ArrayDados as $field => $valor){
		$campos .= "`$field`,";
		$values .= "'$valor',";
	}
	$values = trim($values,',').')';
	$campos = trim($campos,',').')';
	$SQL = "INSERT INTO `yul__$table` $campos VALUES $values";

	if(query($SQL)){
		return $AutoHash ? $ArrayDados['hash'] : true;
	}else{
		return false;
	}
}
function checkFieldsDB($table){
	$busca = get_data("DESCRIBE `yul__$table`");
	if($busca){
		$dados = array();
		foreach($busca as $i){
			$dados[$i['Field']] = $i['Null'] == 'NO' ? false : true;
		}
		return $dados;
	}else{
		return false;
	}
}



function getView($view){
	if(file_exists(__DIR__."/../views/$view.html")){
		return file_get_contents(__DIR__."/../views/$view.html");
	}else{
		return false;
	}
}
function renderView(string $view,$vars){
	$view = getView($view);
	if($view && strstr($view,'{%') && strstr($view,'%}')){
		if(is_array($vars)){
			$txt = $view;
			preg_match_all('/({%)[\W\w]{1,}(%})/U',$view,$indexes);
			$indexes = $indexes[0];
			foreach($indexes as $x){
				$y = preg_replace('/({%)|(%})/','',$x);
				$init = 0;
				if(strstr($y,'>')){

					$A = explode('>',$y);

					foreach($A as $a1=>$a2){
						if($a1 == 0){
							$result = $vars;
						}

						$a3 = str_split($a2);
						if($a3[count($a3)-1] == '-'){
							$ind = substr($a2,0,strlen($a2)-1);
							if(in_array(substr($A[$a1+1],-1,1),array("-",":"))){
								$tbl = substr($A[$a1+1],0,strlen($A[$a1+1])-1);
							}else{
								$tbl = $A[$a1+1];
							}
							$busca = verItemDb($tbl,$result[$ind]);

							$result = $busca;
						}else if($a3[count($a3)-1] == ':'){
							$func = $A[$a1+1];
							$ind = substr($a2,0,strlen($a2)-1);
							if(in_array(substr($func,-1,1),array("-",":"))){
								$func = substr($func,0,strlen($func)-1);
							}
							if(isset($result[$ind])){
								$result = $func($result[$ind]);
							}
						}else if(isset($A[$a1+1])){
							if(isset($A[$a1-1]) && in_array(substr($A[$a1-1],-1,1),array("-",":"))){
								continue;
							}
							if(isset($result[$a2])){
								if($a2 == 'json'){
									$result = json_decode($result[$a2],true);
								}else{
									$result = $result[$a2];
								}
							}
						}else{
							if(isset($result[$a2])){
								if(!in_array(substr($A[$a1-1],-1,1),array("-",":"))){
									$result = $result[$a2];
								}
							}
						}
					}
					if(is_array($result) || is_null($result)){
						$result = '';
					}
					$txt = str_replace($x,$result,$txt);
				}else{
					if(isset($vars[$y])){
						$txt = str_replace($x,$vars[$y],$txt);
					}
				}

			}
			return $txt;

		}else if(is_string($vars)){
			return preg_replace('/({%)[\w\W]{1,}(%})/U',$vars,$view);
		}else{
			return $view;
		}
	}else{
		return $view;
	}
}

function delTree($dir) {
 $files = array_diff(scandir($dir), array('.','..'));
  foreach ($files as $file) {
	(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
  }
  return rmdir($dir);
}

function replace(array $ArrayReplaces,string $string){
	foreach($ArrayReplaces as $search=>$replace){
		$string = str_replace($search,$replace,$string);
	}
	return $string;
}
function read($file){
	if(file_exists($file)){
		$fp = fopen($file,"r");
		$read = fread($fp,filesize($file));
		fclose($fp);
		return $read;
	}else{
		return false;
	}
}
function escrever_arquivo(string $file,string $string,bool $init=false){
	$fp = fopen($file,($init ? "w+" : "a"));
	fwrite($fp,$string);
	fclose($fp);
}
function listarDiretorio($dir,int $nivel = 1,array $result=array()){
	$str = '';
	for($x=0;$x<$nivel;$x++){
		$str .= '/*';
	}
	$HaveNext = false;
	if(is_dir($dir) || strstr($dir,'*')){
		foreach(glob($dir.$str,GLOB_MARK) as $file){
			if(substr($file,-1,1) <> '\\'){
				$result[] = $file;
			}else{
				$HaveNext = true;
			}
		}
	}
	if($HaveNext){
		return listarDiretorio($dir,$nivel+1,$result);
	}else{
		return $result;
	}
}



function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
    );

    return strtr($string, $table);
}
function FILTRO_CODE($dado){
	$dado = normalize($dado);
	$dado = strtolower($dado);
	return $dado;
}
function FILTRO_FILENAME($dado){
	$dado = normalize($dado);
	$dado = str_replace(' ', '-', $dado);
	$dado = strtolower($dado);
	return $dado;
}

function javascript($script='/* script */'){
	$GLOBALS['BODY']['bottom-script'] =  "\n".$script."\n";
}

function recarregar_pagina($link='/'){
	$GLOBALS['BODY']['bottom-script'] =  "\n".'window.location.href = "'.$link.'";'."\n";
}
function mensagem_de_erro($mensagem=false){
	$mensagem = $mensagem ? $mensagem : 'Ops, não foi possível realizar esta operação.<br>Verifique os campos e tente novamente.';
	$GLOBALS['BODY']['bottom-script'] = "\n".'modal("Erro","'.$mensagem.'");'."\n";
}
function mensagem_de_sucesso($mensagem=false){
	$mensagem = $mensagem ? $mensagem : 'Operação realizada com sucesso!';
	$GLOBALS['BODY']['bottom-script'] =  "\n".'modal("Sucesso!","'.$mensagem.'");'."\n";
}
function location_time($link='/'){
	$GLOBALS['BODY']['bottom-script'] = "\n".'location_time("'.$link.'");'."\n";
}
function mensagem_com_location($mensagem,$link){
	$mensagem = $mensagem ? $mensagem : 'Operação realizada com sucesso!';
	$GLOBALS['BODY']['bottom-script'] = "\n".'modal("Sucesso!","'.$mensagem.'");location_time("'.$link.'");'."\n";
}
function upload($dir,$arquivo,$name=false){
	if(is_array($arquivo)){

		$pasta = $dir;
		$nome_imagem = $arquivo['name'];
		// pega a extensão do arquivo
		$ext = strtolower(strrchr($nome_imagem,"."));
		if($name){
			$nome_atual = $name.$ext;
		}else{
			$nome_atual = $nome_imagem;
		}
		$tmp = $arquivo['tmp_name']; //caminho temporário da imagem

		@unlink($pasta.$nome_atual);

		 // ============= Envio da imagem ============

		if(move_uploaded_file($tmp,$pasta.$nome_atual)){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
function upload_image($dir,$arquivo,$name=false,array $sizes=array(0,0)){
	if(is_array($arquivo)){
		switch($arquivo['type']){
			case 'image/jpeg':
				$img_temp = imagecreatefromjpeg($arquivo['tmp_name']);
				break;
			case 'image/png':
				$img_temp = imagecreatefrompng($arquivo['tmp_name']);
				break;
			default:
				return false;
				break;
		}
		$pasta = $dir;
		$nome_imagem = $arquivo['name'];
		if($name){
			$nome_atual = $name.'.png';
		}else{
			$nome_atual = $nome_imagem;
		}

		@unlink($pasta.$nome_atual);

		 // ============= Redimensionando a imagem ============
	    $old_size['w'] = imagesx($img_temp);
		$old_size['h'] = imagesy($img_temp);

		if($sizes[0] == 0 || $sizes[1] == 0){
			$area = 545000;
			$size_area = intval($old_size['w'] * $old_size['h']);
			if($size_area > $area){
				$A = $size_area/$area;
				$A = sqrt($A);
				$sizes[0] = intval($old_size['w'] / $A);
				$sizes[1] = intval($old_size['h'] / $A);
			}else{
				$sizes[0] = $old_size['w'];
				$sizes[1] = $old_size['h'];
			}
		}
		$default_w = intval($sizes[0]);
		$default_h = intval($sizes[1]);
		$new_img = imagecreatetruecolor($default_w,$default_h);
		imagesavealpha($new_img, true);


		// obtém uma cor
		$start_x = 40;
		$start_y = 50;
		$color_index = imagecolorat($img_temp, $start_x, $start_y);

		// torna legível
		$color_tran = imagecolorsforindex($img_temp, $color_index);



		// Qual é?
//		print_r($color_tran);
		$trans_colour = imagecolorallocatealpha($new_img, $color_tran['red'],$color_tran['green'], $color_tran['blue'], 127);
	    imagefill($new_img,0,1, $trans_colour);


		$prop = $sizes[0] / $sizes[1];
		$prop2 = $old_size['w'] / $old_size['h'];
		if($prop2 >= 1){
			$sizes[0] = $prop2 * $default_h;
			$sizes[1] = $default_h;
			$cut_w = $sizes[0] >= $default_w ? floor(($sizes[0] - $default_w) / 2) : 0;
			$cut_h = 0;
			$i_w = $sizes[0] < $default_w ? (($default_w - $sizes[0]) / 2) : 0;
			$i_h = 0;
			$w = $cut_w ? ($cut_w / $default_h) * $old_size['h'] : 0;
			$h = 0;
		}else{
			$sizes[1] = ($old_size['h'] / $old_size['w']) * $default_w;
			$sizes[0] = $default_w;
			$cut_h = $sizes[1] > $default_h ? floor(($sizes[1] - $default_h) / 2) : 0;
			$cut_w = 0;
			$i_w = 0;
			$i_h = $sizes[1] < $default_h ? (($default_h - $sizes[1]) / 2) : 0;
			$w = 0;
			$h = $cut_h ? ($cut_h / $default_w) * $old_size['w'] : 0;
		}
		imagecopyresampled($new_img,$img_temp,$i_w,$i_h,$w,$h,$sizes[0],$sizes[1],$old_size['w'],$old_size['h']);
		if(imagepng($new_img,$pasta.$nome_atual)){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
function check_session_pages($DIRETORIO){
	if(get_connect()){
		$array = Config::get_restricao_de_acesso();
		$result = '';
		if($array){
			foreach($array as $k => $v){
				if($DIRETORIO == $v['DIR']){
					$result = $v['acesso'];
				}
			}

			if(!empty($result)){
				@include_once("set/session_".$result.".php");
			}

		}else{
			return false;
		}
	}else{
		return false;
	}
}
function check_session_pages_modal($DIRETORIO){
	$array = Config::get_restricao_de_acesso();
	$result = '';
	if($array){
		foreach($array as $k => $v){
			if($DIRETORIO == $v['DIR']){
				$result = $v['acesso'];
			}
		}

		if(!empty($result) && !strstr($result,$_SESSION['tipo']) && $_SESSION['tipo']<>'admin'){
			include_once("../includes/acesso-negado.php");
			exit;
		}

	}else{
		return false;
	}
}

function check_post($token){
	if(isset($_SESSION['requests'][$token])){
		return true;
	}else{
		return false;
	}
}

function receive_post($token){
	if(isset($_SESSION['requests'])){
		unset($_SESSION['requests']);
	}
	$_SESSION['requests'][$token] = true;
}

function reset_post_session(){
	if(isset($_SESSION['requests'])){
		unset($_SESSION['requests']);
	}
}

function get_data(string $SQL, bool $Pagination=false, int $Itens_per_page=30, int $CurrentPage=1){
	$query1 = query($SQL);
	if(mysqli_num_rows($query1)){
		while($row_reg = mysqli_fetch_assoc($query1)){
			$fetch1[] = $row_reg;
		}
		if($Pagination){
			$ini = ($CurrentPage - 1) * $Itens_per_page;
			$SQL2 = $SQL." LIMIT $ini,$Itens_per_page";
			$query2 = query($SQL2);
			while($row_reg = mysqli_fetch_assoc($query2)){
				$fetch2[] = $row_reg;
			}
			$result['full'] = $fetch1;
			$result['page'] = $fetch2;

			return $result;
		}else{
			return $fetch1;
		}
	}else{
		return false;
	}
}

function get_data_single(string $SQL){
	$query = query($SQL);
	if(mysqli_num_rows($query)){
		return mysqli_fetch_assoc($query);
	}else{
		return false;
	}
}
function check_pagination(array $array_regs_amount, int $current_page, string $current_GET, int $amount_p_page=30){
	$current_GET = str_replace('?','',$current_GET);
	$current_GET = str_replace('page='.$current_page,'',$current_GET);
	$current_GET = trim($current_GET,'&');
	$current_GET = strstr($current_GET,'=')&&strlen($current_GET) > 1?$current_GET.'&' : '';

	$amount = count($array_regs_amount);
	if($amount > $amount_p_page){
		$pages = $amount / $amount_p_page;
		$total_pages = ceil($pages);
		$content = '<div class="col-12 text-center p-3 mt-2 formAlt">';
		if($current_page > 1){
			$content .= '<a href="'.strstr($_SERVER['REQUEST_URI'],'?',true).'?'.$current_GET.'page='.($current_page-1).'"><button class="float-left">Anterior</button></a>';
		}
		if($current_page < $total_pages){
			$content .='<a href="'.strstr($_SERVER['REQUEST_URI'],'?',true).'?'.$current_GET.'page='.($current_page+1).'"><button class="float-right">Próximo</button></a>';
		}
		$content .= '</div>';

		return $content;

	}else{
		return '';
	}
}


// ============ Geradores de Dados ==============

function set_token(){
	$time = time().date('YmdHis');
	$number = rand(1,1000000);
	return md5(md5(md5(md5(md5(md5(md5(md5(md5(md5($number.$time))))))))));
}
function date_time_now(){
	return date('Y-m-d H:i:s');
}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
// ============ Filtros de Dados ==============

function FILTRO_nome($dado){
	$dado = preg_replace("/[0-9%'\"><;?|\/]/", '',$dado);
	$dado = mb_strtolower($dado, 'UTF-8');
	$dado = mb_convert_case($dado, MB_CASE_TITLE, "UTF-8");;
	return $dado;
}

function FILTRO_idade($data_nasc){
	$time__ = strtotime($data_nasc);
	$dt[0] = intval(date('d',$time__));
	$dt[1] = intval(date('m',$time__));
	$dt[2] = intval(date('Y',$time__));
	if($dt[1] < intval(date('m'))){
		$calc = true;
	}else if($dt[1] == intval(date('m'))){
		if($dt[0] <= intval(date('d'))){
			$calc = true;
		}else{
			$calc = false;
		}
	}else{
		$calc = false;
	}
	$idade = intval(date('Y')) - $dt[2];
	return intval($calc ? $idade : $idade - 1);
}

function FILTRO_data($dado,int $mode=1,$format=false){


	$date = new DateTime($dado);
	$zone = new DateTimeZone('America/Cuiaba');
	$date->setTimezone($zone);
	$resultado = false;
	switch($mode){
		case 1:
		  	$resultado = $date->format('d/m/Y');
			break;
		case 2:
		  	$resultado = strftime("%d / %B / %G &nbsp;&nbsp; | &nbsp;&nbsp;  %A", strtotime($date->format('Y-m-d')));
			$resultado = ucwords(FILTRO_utf8($resultado));
			break;
		case 3:
			if($format){
				$resultado = $date->format($format);
			}
			break;
		case 4:
			if($format){
				$resultado = FILTRO_utf8(strftime($format, strtotime($date->format('Y-m-d H:i:s'))));
			}
			break;

	}
	return $resultado;
}

function FILTRO_utf8($dado){
	return utf8_encode($dado);
}
function FILTRO_text_funcional($dado,$NoSpace_NoUppercase=false){
	$dado = FILTRO($dado);
	$trocas = array("a"=>array("à","á","â","ã"),"e"=>array("è","é","ê"),"i"=>array("ì","í","î"),"o"=>array("ò","ó","ô","õ"),"u"=>array("ú","ù","û"),"A"=>array("À","Á","Â","Ã"),"E"=>array("È","É","Ê"),"I"=>array("Ì","Í","Î"),"O"=>array("Ò","Ó","Ô","Õ"),"U"=>array("Ú","Ù","Û"),"c"=>array("ç"),"C"=>array("Ç"));
	$new_dado = '';
	foreach($trocas as $k => $i){
		foreach($i as $o){
			$dado = str_replace($o,$k,$dado);
		}
	}
	if($NoSpace_NoUppercase){
		if(strstr($dado,' ')){
			$dado = str_replace(' ','-',$dado);
		}
		$dado = strtolower($dado);
	}
	return $dado;
}

function FILTRO_data_inv($dado){
	$dado = preg_replace('/[^[:digit:]_]/', '/',$dado);
	$dado1 = substr($dado,0,2);
	$dado2 = substr($dado,3,2);
	$dado3 = substr($dado,6,4);
	$dado = "$dado3-$dado2-$dado1";
	return $dado;
}
function FILTRO_number($dado){
	return preg_replace('/[^[:digit:]\-\.\,_]/','',$dado);
}
function FILTRO_moneyBRL($valor){
	$valor = doubleval($valor);
	return "R$ ".number_format($valor,2,',','.');
}
function FILTRO_crypt($valor){
	$valor = doubleval($valor);
	return number_format($valor,8,'.',',');
}
function FILTRO($dado){
	$dado = preg_replace("/[%'\"><;?|]/", '',$dado);
	return $dado;
}
function FILTRO123($dado){
	$dado = preg_replace("/[0-9]/", '',$dado);
	return $dado;
}
function FILTRO_DATA_FORMAT($dado){
	if(!is_string($dado) || strlen($dado)< 5){
		return '-----';
	}
	$dia = substr($dado,8,2);
	$mes = substr($dado,5,2);
	$ano = substr($dado,0,4);
	$hora = substr($dado, 10);
	return $dia.'/'.$mes.'/'.$ano.$hora;
}
function FILTRO_HORA($dado){
	$hora = substr($dado, 10);
	$hora = substr($hora, 1,5);
	return $hora;
}
function FILTRO_senha($senha){
	$senha = hash('sha256', md5($senha));
	return $senha;
}
function is_hash($string){
	if(ctype_xdigit($string) && (strlen($string) >= 16)){
		return true;
	}else{
		return false;
	}
}
function consulta_query_db(string $querySQL, bool $paginacao=true,int $page_atual=1, int $itens_p_page=25){

		$conexao = get_connect();

		$page_inicio = ($page_atual - 1) * $itens_p_page;
		$page_final = $itens_p_page;

		$query = mysqli_query($conexao,$querySQL." LIMIT $page_inicio,$page_final");

		$query2 = mysqli_query($conexao,$querySQL);


		if(mysqli_num_rows($query2) > 0){
			if($paginacao == true){
				while($result = mysqli_fetch_assoc($query)){
					$array['page'][] = $result;
				}
			}else{
				while($result2 = mysqli_fetch_assoc($query2)){
					$array['completo'][] = $result2;
				}
			}
			return $array;
		}else{
			return false;
		}

}

function br2nl($html){
	return preg_replace('/\<br \/\>/', "\t", $html);
}

function _GET($string){
	if(is_string($string) and (strlen($string) > 2)){
		if(strstr($string,'?')){
			$string = str_replace('?','',$string);
		}
		if(strstr($string,'&')){
			$filtro1 = explode('&',$string);
			foreach($filtro1 as $item){
				$index = strstr($item,'=',true);
				$value = strstr($item,'=');
				$value = str_replace('=','',$value);
				$result[$index] = $value;
			}
		}else{
			$index = strstr($string,'=',true);
			$value = strstr($string,'=');
			$value = str_replace('=','',$value);
			$result[$index] = $value;
		}
		return $result;
	}else{
		return false;
	}
}
function FILTRO_tirar_acentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}
function FLAGS($string){
	switch($string){
		case 'json':
			return JSON_UNESCAPED_UNICODE;
			break;
		default:
			return false;
			break;
	}
}
function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
function formatTime(int $segundos){
	if($segundos < 15){
		$segundos = 1;
	}
	$a = array("minuto","hora","dia");
	switch($segundos){
		case $segundos >= 86400:
			$E = $a[2];
			$V = floor($segundos/86400);
			break;
		case $segundos >= 3600:
			$E = $a[1];
			$V = floor($segundos/3600);
			break;
		case $segundos >= 60:
			$E = $a[0];
			$V = floor($segundos/60);
			break;
		default:
			$E = $a[0];
			$V = 'Menos de 1';
	}
	$p = !is_string($V) && $V > 1 ? 's' : '';
	return "$V $E$p";
}
?>
