$(document).on("pageInit", "#store_shop", function(e, pageId, $page) {
	$(document).on("click",".dropdown-navlist",function() {
		screen_bar_close();
	});
	$(".m-screen-bar").on("click",".screen-link",function() {
		screen_bar_close();
		$(".screen-link").removeClass('active');
		$(this).addClass('active');
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
			$("#all-goods .goods-type li").eq($(this).attr("data-cid")).addClass('active');
			$("#all-goods .type-detail ul").eq($(this).attr("data-cid")).show();
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
	$("#all-goods").on('click', '.type-detail li a', function() {
		$("#all-goods .type-detail li a").removeClass('active');
		$(this).addClass('active');
		$(".screen-all p").html($(this).find('p').html());
		$(".screen-all").attr('data-cid', $(this).parent().parent().attr("data-id"));
		$(".screen-link").removeClass('active');
	});
	$("#all-goods").on('click', '.type-detail li:first-child a', function() {
		var type_id = $(this).parent().parent().attr('data-id');
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
			$("#area-screen .goods-type li").eq($(this).attr("data-qid")).addClass('active');
			$("#area-screen .type-detail ul").eq($(this).attr("data-qid")).show();
		}
	});
	$("#area-screen").on('click', '.type-detail li a', function() {
		$("#area-screen .type-detail li a").removeClass('active');
		$(this).addClass('active');
		$(".screen-area p").html($(this).find('p').html());
		$(".screen-area").attr('data-qid', $(this).parent().parent().attr("data-id"));
		$(".screen-link").removeClass('active');
	});
	$("#area-screen").on('click', '.type-detail li:first-child a', function() {
		var type_id = $(this).parent().parent().attr('data-id');
		$(".screen-area p").html($("#area-screen .goods-type li").eq(type_id).html());
	});


	$(document).off("click",".j-listchoose");
	$(document).on("click",".j-listchoose",function() {
		var url=$(this).attr("date-href");
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
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					init_list_scroll_bottom();
				};
				if ($("#type-cube").css('display')=='none') {
					$(".m-goods-list ul").addClass('type-list').removeClass('type-cube');
				}
				if ($("#type-list").css('display')=='none') {
					$(".m-goods-list ul").removeClass('type-list').addClass('type-cube');
				}
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
});
