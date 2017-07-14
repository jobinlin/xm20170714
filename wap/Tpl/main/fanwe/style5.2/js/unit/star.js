$(document).ready(function(){
	$(".start-num").each(function(){
		var num = (parseInt($(this).attr('data')) / 5) * 100;
		$(this).width(num + "%");
	});
});