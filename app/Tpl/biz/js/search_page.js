$(document).ready(function(){
	$(".pages").find("a").bind("click",function(){
		var search_form = $("form[name='search_form']");
		var url = $(this).attr("href");
		if(search_form.length>0)
		{
			search_form.attr("action",url);
			search_form.submit();
			return false;
		}
		else
		{
			return true;
		}
		
	});
	
	reinit_side_nav_cur();

});

function reinit_side_nav_cur(){
    $(".nav_item_title").parent().find("dd").hide();
    $(".side_nav .nav_item dd").find(".current").parent().show();
}