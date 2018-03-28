function popbox(main_html,closeTime){
    $('body').prepend("<div class=\"bg\"></div><div class=\"pop_main\"></div>");
    var ctr_main=$(".pop_main");
	var boxhtml="<div class=\'box\'><span class=\'close\'><em></em></span>"+ main_html +"</div>";
	ctr_main.html(boxhtml);
	var w=ctr_main.width();
	var h=ctr_main.height();
	ctr_main.css({"left":($(window).width()-w)/2,"top":($(window).height()-h)/2});
	$(".bg").fadeIn(300);
	ctr_main.fadeIn(300);
	if(closeTime>0){
		setTimeout(function(){$(".pop_main").fadeOut(200,function(){$(".pop_main,.bg").remove()});},closeTime);
	}
	
}
$('body').on("click",".pop_main .close,.bg",function() {
	$(".pop_main").fadeOut(200,function(){$(".pop_main,.bg").remove()});
	
});
//外部调用关闭函数
function close_pop(){
	$(".pop_main").fadeOut(200,function(){$(".pop_main,.bg").remove()});
}
