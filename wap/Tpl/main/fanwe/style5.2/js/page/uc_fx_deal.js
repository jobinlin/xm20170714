$(document).on("pageInit", "#uc_fx_deal", function(e, pageId, $page) {
	
	$(".goods-bd").on('click', '.j-dealed', function() {
		$.toast("您已经代理了此商品");
	});

	init_list_scroll_bottom();
	add_fx_deal();
	data_format_check();

	$('.search').bind('click', function() {
		var fx_search_key = $.trim($('input[name="fx_seach_key"]').val());
		if (fx_search_key == '') {
			$.toast('请输入要搜索的关键字');
			return;
		}
		var param = {
			act: 'deal_fx',
			fx_seach_key: fx_search_key
		};
		$.ajax({
			url: fx_ajax_url,
			data: param,
			success: function(html) {
				$('.j-ajaxlist').html($(html).find('.j-ajaxlist').html());
				init_list_scroll_bottom();
				add_fx_deal();
				data_format_check();
			},
			error: function(err) {
				console.log(err);
			}
		});
	});
	
});

function add_fx_deal() {
	$(".goods-bd").on('click', '.j-deal', function() {
		var that = this;
		var param = {
			act: 'add_user_fx_deal',
			deal_id: $(that).attr('data-id'),
		};

		$.ajax({
			url: fx_ajax_url,
			data: param,
			dataType: 'json',
			success: function(obj) {
				if (obj.status == 1) {
					$.toast(obj.info);
					$(that).unbind('click');
					$(that).addClass('j-dealed').removeClass('j-deal');
					$(that).text('已代理');
					$.toast('代理成功');
					setTimeout(function() {
						($(that).parents('.b-line')).remove();
						if($(".j-ajaxlist li").length==0)
						$(".j-ajaxlist").html('<div class="tipimg no_data">暂无数据</div>');
					}, 2000);
					data_format_check();
				} else if (obj.user_login_status == -1) {
					$.toast(obj.info);
					setTimeout(function() {
						$.router.load(obj.jump);
					}, 2000);
				} else {
					$.toast(obj.info);
				}
			},
			error: function(obj) {
				$.toast('网络异常');
			}
		});
	});
}

function data_format_check() {
	var nodata = '<div class="tipimg no_data">暂无数据</div>';
	var li_len = $('.deal-list').find('li').length;
	if (li_len == 0) {
		if ($('.fx-deal-list').find('.no_data').length == 0) {
			$('.fx-deal-list').html(nodata);
		}
		$('.fx-deal-list .page-load').remove();
	} else if (li_len < 4) {
		$('.fx-deal-list .page-load').remove();
	}
}