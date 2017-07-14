$(document).on("pageInit", "#help_search", function(e, pageId, $page) {
	clear_input($('.search-input'),$('.j-clear'));


	var origkey = '';
	$('.help-search-btn').bind('click', function() {
		var skey = $.trim($('.search-input').val());
		if (!skey) {
			$.toast('搜索关键字不能为空');
			return false;
		}
		if (skey == origkey) {
			return false;
		}
		origkey = skey;

		var query = {'keyword': skey};
		$.ajax({
			url: searchurl,
			data: query,
			dataType: "json",
			type: "post",
			success: function(data){
				if (data.status) {
					var list = data.list;
					var html = '';
					for (var key in list) {
						html += '<li class="b-line"><a href="'+list[key].wap_url+'" class="flex-box">'+
						'<p class="flex-1 bar-tit">'+list[key].title+'</p><div class="iconfont">&#xe607;</div></a></li>';
					}
					$('.bar-list').html(html);
				} else {
					$.toast(data.info);
				}
			}
		});
	});

});