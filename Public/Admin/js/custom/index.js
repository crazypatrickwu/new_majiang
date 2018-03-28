$(function(){
	//登陆页面
   $('.submit_btn').click(function(){
		if($('#username').val() == '' && $('#password').val() == '') {
			
			$('.nousername').fadeIn();
			$('.nopassword').hide();
			return false;	
		}
		if($('#username').val() != '' && $('#password').val() == '') {
			$('.nopassword').fadeIn().find('.userlogged h4, .userlogged a span').text($('#username').val());
			$('.nousername,.username').hide();
			return false;
		}
	});
    //$('input:checkbox').uniform();
	$('#username').attr('placeholder','Username');
	$('#password').attr('placeholder','Password');
})
								
	

