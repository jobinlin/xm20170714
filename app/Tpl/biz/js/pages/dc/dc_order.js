$(function(){

	$('.pp_pay label.ui-radiobox').live('checked',function(){
		$("form[name='search_form']").submit();
	
	});
	
});
