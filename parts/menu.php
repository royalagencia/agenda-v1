<label for="ic" class="icon d-print-none" id="menu-active">q</label>
<input type="checkbox" id="ic">
<div class="menu-sup mb-0 d-print-none">
    <ul class="protege">
    	<a href="/" class=""><li>Início</li></a>
        <?php
		$config = new Config;
		$itens_menu = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'externo';
		$menu = $config->get_menu($itens_menu);
		if($menu){
			foreach($menu as $item){
?>
        <a href="<?=$item['link']?>" class=""><li><?=$item['texto']?></li></a>
<?php
			}
		}
		$link_sair = isset($_SESSION['tipo']) ? '<a href="/sair" class="" title="Sair"><li>Sair</li></a>' : '';
		echo $link_sair;
?>
<!--CONTEUDO-->
    </ul>
</div>
