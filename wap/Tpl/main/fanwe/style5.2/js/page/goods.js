
$(document).on("pageInit", "#goods", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist",".j-ajaxadd");
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
	$(".screen-item a").click(function(){
		$(".m-screen-list").removeClass('active');
		$(".arrow-up").hide();
		$(".arrow-down").show();
	});
	$(".m-screen-bar").on("click",".screen-item a",function(){
		$(".m-screen-list").find('.mask').removeClass('mask-active');
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".m-screen-list").removeClass('active');
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
		} else {
			$(this).addClass('active');
		}
		if ($(this).hasClass('active')) {
			$(".screen-brand").removeClass('active');
			$(".brand-screen").removeClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			screen_open();
			$("#all-goods").addClass('active');
		} else {
			screen_close();
			$("#all-goods").removeClass('active');
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
	//品牌
	$(".m-screen-bar").on("click",".screen-brand",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
		$(".screen-all").removeClass('active');
		$("#all-goods").removeClass('active');
		if ($(this).hasClass('active')) {
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			$(".m-screen-list").addClass('active');
			$(".brand-screen").addClass('active');
			$(".content").css('overflow', 'hidden');
			$(".m-screen-list").find('.mask').addClass('mask-active');
		} else {
			$(".m-screen-list").find('.mask').removeClass('mask-active');
			$(".brand-screen").removeClass('active');
			$(".content").css('overflow', 'auto');
			$(".m-screen-list").removeClass('active');
		}
	});
	$(".m-screen-list").on("click",".brand-screen li",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
	});
	$(".m-screen-list").on("click",".brand-reset",function() {
		$(".brand-screen li").removeClass('active');
	});
	$(".m-screen-list").on("click",".brand-comfirm",function() {
		var ids = '';
		$(".screen-brand").removeClass('active');
		$(".brand-screen").find('.active').each(function(){
		    ids += $(this).attr("data-id")+",";
		  });
		ids = ids.substring(0,ids.length-1);
		url = $(this).attr('date-href');
		$(this).attr('date-href', url);
		if(ids!=''){
			url +='&bid='+ids;
			$(this).attr('date-href', url);
		}
	});
	//价格
	$(".m-screen-bar").on("click",".screen-price",function() {
		$(this).addClass('active');
		if ($(this).find(".arrow-up").hasClass('active')) {  //降序
			$(this).find(".arrow-up").removeClass('active');
			$(this).find(".arrow-down").addClass('active');
		} else {  //升序
			$(this).find(".arrow-down").removeClass('active');
			$(this).find(".arrow-up").addClass('active');
		}
	});
	//销量
	$(".m-screen-bar").on("click",".screen-sales",function() {
		$(".arrow-up").removeClass('active');
		$(".arrow-down").removeClass('active');
	});
	//背景
	$(".m-screen-list").on("click",".m-screen-list .mask",function() {
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".screen-brand").removeClass('active');
		$(".content").css('overflow', 'auto');
		//$(".screen-item a").removeClass('active');
		$(".m-screen-list").find('.mask').removeClass('mask-active');
		$(".m-screen-list").removeClass('active');
		$(".brand-screen li").removeClass('active');
		$(".brand-screen").removeClass('active');
	});
	$(document).on("click",".j-listchoose",function() {
		var url=$(this).attr("date-href");
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-pp").html($(html).find(".j-pp").html());
				$(".j-xl").html($(html).find(".j-xl").html());
				$("#all-goods").html($(html).find("#all-goods").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					$(document).off('infinite', '.infinite-scroll-bottom');
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
//	//品牌搜索
//	var all_brand=new Array();
//	$.each(brand_list,function(i,obj){
//		if(obj.id > 0){
//			all_brand.push(obj.id);
//		}	
//	});
//
//	
//	$(".brand-screen .brand-comfirm").bind("click",function(){
//		var brand_arr=new Array();
//		$(".brand-screen .flex-1 li").each(function(){
//			if($(this).hasClass("active")){
//				var data_id = $(this).attr("data-id");
//				if(data_id==0){	
//					brand_arr = all_brand;
//					return false;
//				}else{
//					brand_arr.push(data_id);
//				}
//			}	
//		});
//		
//	});
//	
	
});