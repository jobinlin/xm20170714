$(document).on("pageInit", "#dc_table_list", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	$("#dc_table_list").on('click', '.j-open-select', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-mask").removeClass('active');
		} else {
			$(".j-open-select").removeClass('active');
			$(this).addClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-select").eq($(this).index()).addClass('active');
			$(".dc-mask").addClass('active');
		}
	});
	/*$(".dc-dp-dis").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		param_location();
	});*/
	$('.dc-dp-dis').on('click', '.j-ajaxchoose', function() {
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		param_location();
	})
	/*$(".dc-cate-list").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		param_location();
	});*/
	$('.dc-cate-list').on('click', '.j-ajaxchoose', function() {
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		param_location();
	})
	/*$(".dc-area").find('.j-ajaxchoose').click(function() {
		$(".dc-area").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var qid=$(this).attr('data-id');
		$("input[name='qid']").val(qid);
		var aid=$(this).parents('ul').attr('data-id');
		$("input[name='aid']").val(aid);
		var dc_area=$(this).find(".select-tit").html();
		$(".now-area-name").html(dc_area);
		param_location();
	});*/
	$('.dc-area').on('click', '.j-ajaxchoose', function() {
		$(".dc-area").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var qid=$(this).attr('data-id');
		$("input[name='qid']").val(qid);
		var aid=$(this).parents('ul').attr('data-id');
		$("input[name='aid']").val(aid);
		var dc_area=$(this).find(".select-tit").html();
		$(".now-area-name").html(dc_area);
		param_location();
	})
	$("#dc_table_list").on('click', '.j-area', function() {
		$(".j-area").removeClass('active');
		$(this).addClass('active');
		$(".area-list").removeClass('active');
		$(".area-list").eq($(this).index()).addClass('active');
		/* Act on the event */
	});
	$("#dc_table_list").on('click', '.j-close-select', function() {
		$(".j-open-select").removeClass('active');
		$(".dc-select").removeClass('active');
		$(".dc-mask").removeClass('active');
		// param_location();
	});
	function param_location(){
		var query = $("form[name='dc_location_param']").serialize();
		var ajax_url = $("form[name='dc_location_param']").attr("action");
		$(".content").scrollTop(0);
		$(document).off('infinite', '.infinite-scroll-bottom');
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"html",
			type:"POST",
			success:function(html){
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".dc-cate-list").html($(html).find('.dc-cate-list').html());
				$(".dc-dp-dis").html($(html).find('.dc-dp-dis').html());
				$(".dc-area").html($(html).find('.dc-area').html());
				init_list_scroll_bottom();
			}
		});
		return false;
	}

});
