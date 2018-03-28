/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2015-02-01 21:55:28
 * @version $Id$
 */
$(function(){
   $('.togglemenu').click(function(){
		if(!$(this).hasClass('togglemenu_collapsed')) {
			
			//if($('.iconmenu').hasClass('vernav')) {
			if($('.vernav').length > 0) {
				if($('.vernav').hasClass('iconmenu')) {
					$('body').addClass('withmenucoll');
					$('.iconmenu').addClass('menucoll');
				} else {
					$('body').addClass('withmenucoll');
					$('.vernav').addClass('menucoll').find('ul').hide();
				}
			} else if($('.vernav2').length > 0) {
			//} else {
				$('body').addClass('withmenucoll2');
				$('.iconmenu').addClass('menucoll2');
			}
			
			$(this).addClass('togglemenu_collapsed');
			
			$('.iconmenu > ul > li > a').each(function(){
				var label = $(this).text();
				$('<li><span>'+label+'</span></li>')
					.insertBefore($(this).parent().find('ul li:first-child'));
			});
		} else {
			
			//if($('.iconmenu').hasClass('vernav')) {
			if($('.vernav').length > 0) {
				if($('.vernav').hasClass('iconmenu')) {
					$('body').removeClass('withmenucoll');
					$('.iconmenu').removeClass('menucoll');
				} else {
					$('body').removeClass('withmenucoll');
					$('.vernav').removeClass('menucoll').find('ul').show();
				}
			} else if($('.vernav2').length > 0) {	
			//} else {
				$('body').removeClass('withmenucoll2');
				$('.iconmenu').removeClass('menucoll2');
			}
			$(this).removeClass('togglemenu_collapsed');	
			
			$('.iconmenu ul ul li:first-child').remove();
		}
	});
    //登陆页面
   $('.submit_btn').click(function(){
		if($('#username').val() == '' && $('#password').val() == '') {
			
			$('.nousername').fadeIn();
			$('.nopassword').hide();
			return false;	
		}
		if($('#username').val() != '' && $('#password').val() == '') {
			$('.nopassword').fadeIn().find('.userlogged h4, .userlogged a span').text($('#username').val());
			//$('.nousername,.username').hide();
			return false;
		}
	});
})

	