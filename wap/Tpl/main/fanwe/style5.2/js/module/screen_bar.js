function screen_bar() {
	$(document).on("click",".dropdown-navlist",function() {
		screen_bar_close();
	});
	if ($(document).find('.screen-all')) {
		$(".screen-all").attr({
			'data-cid': $("#all-goods .type-detail .active").attr('data-cid'),
			'data-tid': $("#all-goods .type-detail .active").attr('data-tid')
		});
	} else {
		return;
	}
	if ($(document).find('.screen-area')) {
		$(".screen-area").attr({
			'data-qid': $("#area-screen .type-detail .active").attr('data-qid')
		});
	} else {
		return;
	}
	$(".m-screen-bar").on("click",".screen-link",function() {
		screen_bar_close();
		$(".screen-link").removeClass('active');
		$(this).addClass('active');
		$(".m-screen-bar").attr('data-type', $(this).attr('data-type'));
	});
	//筛选
	//标签
	$(".m-screen-bar").on("click",".screen-item a",function(){
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".m-screen-list").removeClass('active');
		$(".goods-type li").removeClass('active');
	});
	//全部
	function screen_open() {
		$(".content").css('overflow', 'hidden');
		$(".m-screen-list").addClass('active');
	}
	function screen_close() {
		$(".content").css('overflow', 'auto');
		$(".m-screen-list").removeClass('active');
	}
	$(".m-screen-bar").on("click",".screen-all",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#all-goods").removeClass('active');
		} else {
			$(this).addClass('active');
			$(".screen-area").removeClass('active');
			$("#area-screen").removeClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			screen_open();
			$("#all-goods").addClass('active');
			$("#all-goods .goods-type li").eq($(this).attr("data-id")).addClass('active');
			$("#all-goods .type-detail ul").eq($(this).attr("data-id")).show();
		}
	});
	$(".m-screen-list").on("click",".goods-type li",function() {
		$(".goods-type li").removeClass('active');
		$(this).addClass('active');
		$(".type-detail ul").hide();
		if ($(".goods-type li").hasClass('active')) {
			var type_id = $(this).attr('data-id');
			$(this).parent().parent().find(".type-detail ul").eq(type_id).show();
		}
	});
	$("#all-goods").on('click', '.type-detail li', function() {
		$("#all-goods .type-detail li").removeClass('active');
		$(this).addClass('active');
		$(".screen-all p").html($(this).find('.flex-1').html());
		$(".screen-all").attr('data-id', $(this).parent().attr("data-id"));
		$(".screen-all").attr('data-cid', $(this).attr("data-cid"));
		$(".screen-all").attr('data-tid', $(this).attr("data-tid"));
		$(".screen-link").removeClass('active');
	});
	$("#all-goods").on('click', '.type-detail li:first-child', function() {
		var type_id = $(this).parent().attr('data-id');
		$(".screen-all p").html($("#all-goods .goods-type li").eq(type_id).html());
	});
	//全城
	$(".m-screen-bar").on("click",".screen-area",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#area-screen").removeClass('active');
		} else {
			$(this).addClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			$(".screen-all").removeClass('active');
			$("#all-goods").removeClass('active');
			screen_open();
			$("#area-screen").addClass('active');
			$(".goods-type li").removeClass('acitve');
			$("#area-screen .goods-type li").eq($(this).attr("data-id")).addClass('active');
			$("#area-screen .type-detail ul").eq($(this).attr("data-id")).show();
		}
	});
	$("#area-screen").on('click', '.type-detail li', function() {
		$("#area-screen .type-detail li").removeClass('active');
		$(this).addClass('active');
		$(".screen-area p").html($(this).find('p').html());
		$(".screen-area").attr('data-id', $(this).parent().attr("data-id"));
		$(".screen-area").attr('data-qid', $(this).attr("data-qid"));
		$(".screen-link").removeClass('active');
	});
	$("#area-screen").on('click', '.type-detail li:first-child', function() {
		var type_id = $(this).parent().attr('data-id');
		$(".screen-area p").html($("#area-screen .goods-type li").eq(type_id).html());
	});
	$(document).on("click",".j-listchoose",function() {
		var c_id = $(".screen-all").attr('data-cid');
		var t_id = $(".screen-all").attr('data-tid');
		var q_id = $(".screen-area").attr('data-qid');
		var order_type = $(".m-screen-bar").attr('data-type');
		var url = sitename+'/wap/index.php?ctl='+ctl_name+'&cate_id='+c_id+'&tid='+t_id+'&qid='+q_id+'&order_type='+order_type;
		if(key!=''){
			url+='&keyword='+key;
		}
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-pj").html($(html).find(".j-pj").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-zj").html($(html).find(".j-zj").html());
				$(".j-zx").html($(html).find(".j-zx").html());
				$("#all-goods").html($(html).find("#all-goods").html());
				$("#area-screen").html($(html).find("#area-screen").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
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
		}, 800);
		screen_bar_close();
	});
	$(document).on("click",".screen-link",function() {
		var url = $(this).attr('date-href');
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-pj").html($(html).find(".j-pj").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-zj").html($(html).find(".j-zj").html());
				$(".j-zx").html($(html).find(".j-zx").html());
				$(".m-screen-list").html($(html).find(".m-screen-list").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
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
		}, 800);
		screen_bar_close();
	});
}

function screen_bar_close() {
	$(".m-screen-list").removeClass('active');
	$(".content").css('overflow', 'auto');
	$(".arrow-up").hide();
	$(".arrow-down").show();
	$(".screen-area").removeClass('active');
	$(".screen-all").removeClass('active');
}