$(document).on("pageInit", "#biz_coupon_use_log", function(e, pageId, $page) {
	$(document).on('click', '.j-use-search', function() {
		$(".use-search-bar").addClass('open');
	});
	$(".use-search-bar").on('click', '.j-close-use-search', function() {
		$(".use-search-bar").removeClass('open');
	});

	init_list_scroll_bottom();

	$('.search').bind('click', function() {
		var pwd = $.trim($('input[name="coupon_pwd"]').val());
		if (pwd == '') {
			$.toast('请输入要搜索的券码');
			return;
		}
		pwd = pwd.replace(/\s/g,'');
		if (pwd.length!=12) {
			$.toast('请输入有效券码');
			return;
		}
		var param = {
			act: 'search_log',
			coupon_pwd: pwd
		};
		$.ajax({
			url: use_log,
			type:"GET",
			data: param,
			dataType:"JSON",
			success: function(html) {

				$('.j-ajaxlist').html($(html).find('.j-ajaxlist').html());
				init_list_scroll_bottom();
			},
			error: function(err) {
				console.log(err);
			}
		});
		return false;
	});
});