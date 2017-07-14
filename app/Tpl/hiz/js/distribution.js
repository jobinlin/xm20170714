
$(document).ready(function(){

	function dist_init() {
		bindAbleFunc();
		bindDelFunc();
		dist_search();
	}

	dist_init();

	function dist_search() {
		$('.dist_search').unbind('click').on('click', function() {
			var search_key = $.trim($('.dist_key').val());
			if (search_key == '') {
				return;
			}
			var param = {'dist_key': search_key};
			$.ajax({
				url:dist_ajax_url,
				data:param,
				type:"POST",
				success: function(html) {
					$(".j-ajax-content").html($(html).find(".j-ajax-content").html());
					dist_init();
				},
				error: function() {
					$.showErr("网络异常", reload);
				}
			})
		});
	}

	function bindAbleFunc() {
		$('.dist_disabled').on('click', function() {
			var param = getParam(this);
			distAjaxFunc(param, switchText, this);
		});
	}

	function switchText(obj) {
		var text = $.trim($(obj).html());
		if (text == '禁用') {
			$(obj).html('正常');
		} else {
			$(obj).html('禁用');
		}
	}

	function bindDelFunc() {
		$(document).on('click', '.dist_del', function() {
			var _this = this;
			$.showConfirm('确定要删除吗', function() {
				var param = getParam(_this);
				distAjaxFunc(param, distDel, _this);
			})
		})
	}

	function distDel(obj) {
		$(obj).parents('tr').remove();
	}

	function getParam(obj) {
		var param = {};
		param.id = $(obj).attr('data-id');
		param.act = $(obj).attr('data-act');
		return param;
	}

	function distAjaxFunc(param, callback, arg) {
		$.ajax({
			url: dist_ajax_url,
			data: param,
			type: "POST",
			dataType: "json",
			success: function(obj) {
				if (obj.status) {
					if (typeof callback == 'function') {
						callback(arg, obj);
					} else {
						reload();
					}
				} else {
					$.showErr(obj.info);
				}
			},
			error: function() {
				$.showErr("网络异常", reload);
			}
		})
	}

	function reload() {
		window.location.reload();
	}
})