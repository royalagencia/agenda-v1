/* ============ Formatando Dados ================ */
var cont = 0;
function number_format($elem){
	var AssimSera = '';
	var format = $($elem).attr('data-format');
	var numb = $elem.value;
	var leg = numb.length;
	var obj = document.getElementById('demo');
	var arr_f = format.match(/[\d\W\w\s]/g);
	var arr_n = numb.match(/\d/g);
	arr_f.forEach(function( i, k){
		if(i == 'd' && cont <= leg){
			if(arr_n[cont]){
				AssimSera += arr_n[cont];
				cont = cont+1;
			}
		}else{
			if(arr_n[cont]){
				AssimSera += i;
			}
		}
		$elem.value = AssimSera;
	});
	cont=0;
	
}
var cont = 0;
function set_format($elem,$format){
		if($format == 'number'){
		$Ptt = /\d/g;
	}else if($format == 'alpha'){
		$Ptt = /[A-z]/g;
	}else{
		$Ptt = /\w/g;
	}
		
	var AssimSera = '';
	var format = $($elem).attr('data-format');;
	var numb = $elem.value;
	var leg = numb.length;
	var arr_f = format.match(/[\d\W\w\s]/g);
	var arr_n = numb.match($Ptt);
	arr_f.forEach(function( i, k){
		if(i == 'd' && cont <= leg){
			if(arr_n[cont]){
				AssimSera += arr_n[cont];
				cont = cont+1;
			}
		}else{
			if(arr_n[cont]){
				AssimSera += i;
			}
		}
		$elem.value = AssimSera;
	});
	cont=0;
}
function cpf_cnpj($elem){
	var AssimSera = '';
	var format = $elem.value.length > 14 ? 'dd.ddd.ddd/dddd-dd' : 'ddd.ddd.ddd-dd';
	var numb = $elem.value;
	var leg = numb.length;
	var obj = document.getElementById('demo');
	var arr_f = format.match(/[\d\W\w\s]/g);
	var arr_n = numb.match(/\d/g);
	arr_f.forEach(function( i, k){
		if(i == 'd' && cont <= leg){
			if(arr_n[cont]){
				AssimSera += arr_n[cont];
				cont = cont+1;
			}
		}else{
			if(arr_n[cont]){
				AssimSera += i;
			}
		}
		$elem.value = AssimSera;
	});
	cont=0;
	
}
var MODAL_OPEN = false;
var PAGE_MODAL_OPEN = false;
var CONFIRM_MODAL_OPEN = false;
function close_modal(){
	MODAL_OPEN = false;
	CONFIRM_MODAL_OPEN = false;
	$('div.frame-modal').removeClass('bounceInDown');
	$('div.frame-modal').addClass('bounceOutUp');
	$('div.fundo-modal').fadeOut(1500);
	setTimeout(function (){
		$('div.fundo-modal').addClass('force-hide');
		$('div.frame-modal').removeClass('bounceOutUp');
	},1500);
}
function close_page_modal(){
	PAGE_MODAL_OPEN = false;
	$('div.frame-page-modal').removeClass('bounceInDown');
	$('div.frame-page-modal').addClass('bounceOutUp');
	$('div.fundo-page-modal').fadeOut(1500);
	setTimeout(function (){
		$('div.fundo-page-modal').addClass('force-hide');
		$('div.frame-page-modal').removeClass('bounceOutUp');
	},1500);
}
$('a.page-scroll').bind('click', function(event) {
	var $anchor = $(this);
	$('html, body').stop().animate({
		scrollTop: $($anchor.attr('href')).offset().top
	}, 1500, 'easeInOutExpo');
	event.preventDefault();
});
$(window).scroll(function () {
  if ($(this).scrollTop() > 250) {
	  $('#back-top').fadeIn();
  } else {
	  $('#back-top').fadeOut();
  }
});

function modal($title,$text){
	$('#title-modal').html($title);
	$('#text-modal').html($text);
	$('div.fundo-modal').hide();
	$('div.fundo-modal').removeClass('force-hide');
	$('div.fundo-modal').fadeIn();
	$('div.frame-modal').addClass('bounceInDown');
	MODAL_OPEN = true;
	setTimeout(function (){
		close_modal();
	},20000);
	
}
function page_modal($link,$GET){
	$.post('action/modal.php',{"page":$link,"GET":$GET},function($info){
		$('#content-page-modal').html($info);
		$('div.fundo-page-modal').hide();
		$('div.fundo-page-modal').removeClass('force-hide');
		$('div.fundo-page-modal').fadeIn();
		$('div.frame-page-modal').addClass('bounceInDown');
	});
	PAGE_MODAL_OPEN = true;
}
function modal_direct($link,$GET){
	$.get('action/modal.php',{"page":$link,"GET":$GET},function($info){
		$('#content-page-modal').html($info);
	});
}
function confirm_modal($href){
	CONFIRM_MODAL_OPEN = true;
	modal("Tem certeza disso?",'Tem certeza que de deseja fazer isso?<br>Talvez essa operação não possa ser desfeita.<br><br><a href="' + $href +'"><button class="new_btn__">Sim</button></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onClick="close_modal()" class="link_hover">Não</a>');
	
		$(document).keypress(function(e) {
            if(e.keyCode == 13 && CONFIRM_MODAL_OPEN){
				window.location.href = $href;
			}
        });
		return false;
}
function location_time($LINK){
	setTimeout(function(){
		window.location.href = $LINK;
	},3000)
}
function fixed__($elem){
	var x = parseFloat($elem.value);
	$elem.value = x.toFixed(2);
}
function location__($link){
	window.location.href = $link;
}
$(document).ready(function(e) {
    $('.fixed__js').change(function(){fixed__(this);});
	$(document).keyup(function(e) {
        if(e.keyCode == 27){
			if(MODAL_OPEN || CONFIRM_MODAL_OPEN){
				close_modal();
			}else if(PAGE_MODAL_OPEN){
				close_page_modal();
			}
		}
    });
});