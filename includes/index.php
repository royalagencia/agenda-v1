<?php

if(isset($_POST['titulo'])){
  header("content-type: text/plain");
  print_r($_POST);
  exit;
}

$form = array();

$BODY['content'] = renderView('page-index',$form);


echo renderView('body',$BODY);
?>
