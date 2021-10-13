<?php
if(isset($_SESSION['nome'])){
	$log = true;
}else{
	$log = false;
}
?>

<div class="w-100 p-2 cabecalho-principal bg_color_2 position-relative d-print-none protege" style="overflow:hidden;background:transparent">
    <div id="header" class="flex-row bg-transparent position-relative">
    	
        <h1 class="text-center protege font-30 color_2" style="font-family:'Open Sans','Calibri'; letter-spacing:-3px; font-weight:600"><img class="protege" src="<?=DIR_IMG?>logo.svg" alt="<?=$sys['title']?>, <?=$sys['description']?>" style="max-width:100%;"></h1>
        <font class="assinatura title-master"><?=isset($_SESSION['nome']) ? $_SESSION['nome'] : '&nbsp;'?></font>
        
    </div>
</div>