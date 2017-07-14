$(document).ready(function() {
	init_table_nav();
});
//列表切换
function init_table_nav(){
	init_table_line();
	function init_table_line() {
		var l_width = $(".table-nav .active").width();
		var l_left = $(".table-nav .active").offset().left - 180;
		$(".table-line").css({
			width: l_width,
			left: l_left
		});
	}
	$(".table-nav").on('click', '.nav-item',function() {
		$(".table-nav .nav-item").removeClass('active');
		$(this).addClass('active');
		var url=$(this).attr('data-href');
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajax-content").html($(html).find(".j-ajax-content").html());
			},
			error:function()
			{

			}
		});
	});
	$(".table-nav .nav-item").hover(function() {
		var l_width = $(this).width();
		var l_left = $(this).offset().left - 180;
		$(".table-line").css({
			width: l_width,
			left: l_left
		});
	}, function() {
		init_table_line();
	});
}