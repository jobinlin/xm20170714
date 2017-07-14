$(document).on("pageInit", "#biz_store_pay_order", function(e, pageId, $page) {
	function stopPropagation(e) {
			if (e.stopPropagation)
				e.stopPropagation();
			else
				e.cancelBubble = true;
		}
	$(document).bind('click', function() {
		$(".m-month-list").removeClass('active');
	});
	$(document).on('click','.j-month-select',function(e) {
		stopPropagation(e);
		$(".m-month-list").addClass('active');
	});


	$(".j-month").unbind();
	$(".j-month").bind('click', function() {
	//$(document).on('click', '.j-month', function() {
		$.showIndicator();
		$.loadPage($(this).attr("data-href"));
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		/*$(".m-month-list").removeClass('active');
		var url=$(this).attr("data-href");
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					return;
				}else{
					init_list_scroll_bottom();
				};
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);*/
	});
});