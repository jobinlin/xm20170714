$(document).on("pageInit", "#dc_locations_list", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	
	$("#dc_locations_list").on('click', '.j-open-youhui', function() {
		var i_height=$(this).parent().find('.youhui-item').height();
		var t_height=i_height*$(this).parent().find('.youhui-item').length;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().find('.youhui-list').css('max-height', i_height*2);
		} else {
			$(this).addClass('active');
			$(this).parent().find('.youhui-list').css('max-height', t_height);
		}
	});
	$("#dc_locations_list").on('click', '.j-open-select', function() {
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
	
	$("#dc_locations_list").on('click', '.j-fliter-item', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).find('input').prop("checked",false);
		} else {
			$(this).find('input').prop("checked",true);
			$(this).addClass('active');
		}
	});
	
	$(".dc-dp-dis").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		
		param_location();
	});
	
	$(".dc-cate-list").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		
		param_location();
	});
	
	$("#dc_locations_list").on('click', '.j-close-select', function() {
		$(".j-open-select").removeClass('active');
		$(".dc-select").removeClass('active');
		$(".dc-mask").removeClass('active');
		
		param_location();
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
				
				init_list_scroll_bottom();
			}
		});
		return false;
	}

});
