<?php
/*
Robot Du-Art | Dashboard v1.0
*/
$html['opts'] = '';

$format='45';


#INI-OPTS
#D01
$opts[] = array(
	"label"=>"Meus Dados",
	"link"=>"/meus-dados",
	"logo"=>DIR_IMG."icons/vectors/usuario.svg"
);
#D02
$opts[] = array(
	"label"=>"Usuários",
	"link"=>"/usuarios",
	"logo"=>DIR_IMG."icons/vectors/followers.svg"
);
#D03
$opts[] = array(
	"label"=>"Configurações",
	"link"=>"/configuracoes",
	"logo"=>DIR_IMG."icons/vectors/settings.svg"
);
#FIN-OPTS

$format = str_split("$format");
$x = 0;
$z = 0;
foreach($opts as $k=>$item){
	$i = intval($format[$x]);
	if($z < $i){
		$html['opts'] .= renderView('card-dashboard',$item);
		$z++;
	}else{
		if(isset($format[$x+1])){
			$x++;
		}else{
			$x = 0;
		}
		$z = 0;
		$html['opts'] .= '<br>';
		$html['opts'] .= renderView('card-dashboard',$item);
		$z++;
	}
}

$BODY['content'] = renderView('page-dashboard',$html);
echo renderView('body',$BODY);
?>