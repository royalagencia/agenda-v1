<?php

if (isset($_POST['deletar'])){
  header("Content-Type: text/plain");
  if ( Agenda::deletar($_POST['deletar']) ){
    echo "OK";
  } else {
    http_response_code(501);
  }
  exit();
}

if(isset($_POST['titulo'], $_POST['info'], $_POST['data'], $_POST['hora'], $_POST['token']) and !(check_post($_POST['token']))){
  receive_post($_POST['token']);
  if (Agenda::novo($_POST['titulo'], $_POST['data'] . " " . $_POST['hora'], $_POST['info'])){
    mensagem_de_sucesso();
  } else {
    mensagem_de_erro();
  }
}

$AGENDA = new Agenda();

$form = array(
  "token"=>set_token(),
  "compromisso"=>""
);

$compromissos = $AGENDA->getRegs();
if ($compromissos){
  
  foreach($compromissos as $compromisso){
    // $card_compromisso['hora'] = $compromisso['data-hora'];
    // $card_compromisso['titulo'] = $compromisso['titulo'];
    // $card_compromisso['info'] = urldecode($compromisso['info']);
    $compromisso['info'] = urldecode($compromisso['info']);
    
    $form['compromisso'] .= renderView('card-compromisso', $compromisso);
    
  }
  
}


$BODY['content'] = renderView('page-index',$form);


echo renderView('body',$BODY);