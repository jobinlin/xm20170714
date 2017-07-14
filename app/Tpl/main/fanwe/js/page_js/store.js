$(document).ready(function(){
	$(".j-check-map").bind('click', function() {
		$(".mask").addClass('active');
		$(".map-box").addClass('active');
	});
	$(".j-close-map").bind('click', function() {
		$(".mask").removeClass('active');
		$(".map-box").removeClass('active');
	});

	$(".j-check-more").live('click', function() {
		if($(".j-check-more").hasClass("up")){
			$(".ajax_add").hide();
			$(".j-check-more").removeClass("up");
			$(".j-check-more").text("查看更多");
		}else{
			if($(".ajax_add").length>0){
				$(".ajax_add").show();
			}else{
				var url=$(this).attr("url");
				var query = new Object();
				query['more'] = 1;
				query['store_id'] = $("input[name='store_id']").val();
				$.ajax({
					url: url,
					data: query,
					dataType: "json",
					type: "post",
					success: function(obj){
						if(obj.html){
							$("#supplier_deal dl").append($(obj.html).find("dl").html());
						}
					}
				});
			}
			$(".j-check-more").addClass("up");
			$(".j-check-more").text("收起");
		}
	});

	$(".more-shop").live('click', function() {
		if($(".more-shop").hasClass("shop_up")){
			$(".ajax_shop_add").hide();
			$(".more-shop").removeClass("shop_up");
			$(".more-shop").text("查看更多");
		}else{
			if($(".ajax_shop_add").length>0){
				$(".ajax_shop_add").show();
			}else{
				var url=$(this).attr("url");
				var query = new Object();
				query['more'] = 1;
				query['store_id'] = $("input[name='store_id']").val();
				$.ajax({
					url: url,
					data: query,
					dataType: "json",
					type: "post",
					success: function(obj){
						console.log(obj.html);
						if(obj.html){
							$("#supplier_shop ul").append($(obj.html).find("ul").html());
						}
					}
				});
			}
			$(".more-shop").addClass("shop_up");
			$(".more-shop").text("收起");
		}
	});

	init_content_nav();
	
	if($("#supplier_deal").length>0)
		init_load_supplier_data($("#supplier_deal"),"store_load_supplier_deal");
	
	if($("#supplier_shop").length>0)
		init_load_supplier_data($("#supplier_shop"),"store_load_supplier_shop");
	
	if($("#supplier_youhui").length>0)
		init_load_supplier_data($("#supplier_youhui"),"store_load_supplier_youhui");
	
	if($("#supplier_event").length>0)
		init_load_supplier_data($("#supplier_event"),"store_load_supplier_event");

	init_store_image();





});



/**
 * 初始化图库功能
 */
function init_store_image()
{
	$("#store_image").bind("click",function(){
		if(STORE_IMAGES.length>0)
		{
			var group = new Array();
			for(i=0;i<STORE_IMAGES.length;i++)
			{
				var item = new Object();
				item.href = STORE_IMAGES[i]['image'];
				item.title = STORE_IMAGES[i]['brief'];
				group.push(item);
			}
			$.fancybox.open(group,{
				prevEffect : 'fade',
				nextEffect : 'fade',
				nextClick : true,
				helpers : {
					thumbs : {
						width  : 50,
						height : 50
					}
				}
			});
		}
	});
}

/**
 * 加载商户相关业务数据，
 * dom: 容器对象
 * act: ajax请求的对应action
 * load_tag:全局的是否已加载的标识变量
 */
function init_load_supplier_data(dom,act)
{
	$.bind_supplier_data_pager = function(query,dom){
		$(dom).find(".pages a").bind("click",function(){
			var ajax_url = $(this).attr("href");
			$(dom).html("<div class='loading'></div>");
			var load_tag = true;
			$(dom).attr("is_load",load_tag);
			$.ajax({
				url:ajax_url,
				data:query,
				dataType:"json",
				type:"post",
				global:false,
				success:function(obj){
					$(dom).html(obj.html);
					$.bind_supplier_data_pager(query,dom,load_tag);
				}				
			});
			
			return false;
		});
	};
	
	
	$.load_supplier_data = function(dom,act){
		var scrolltop = $(window).scrollTop();
		var loadheight = $(dom).offset().top;
		var windheight = $(window).height();
		var load_tag = $(dom).attr("is_load");
		if(!load_tag)
		{			
			if(windheight+scrolltop>=loadheight)
			{
				var query = new Object();
				query.store_id = $(dom).attr("store_id");
				query.act = act;
				$(dom).html("<div class='loading'></div>");
				load_tag = true;
				$(dom).attr("is_load",load_tag);
				$.ajax({
					url:AJAX_URL,
					data:query,
					dataType:"json",
					type:"post",
					global:false,
					success:function(obj){						
						$(dom).html(obj.html);
						$.bind_supplier_data_pager(query,dom);
					}				
				});
			}
		}
		
	};
	$.load_supplier_data(dom,act);
	$(window).bind("scroll", function(e){
		$.load_supplier_data(dom,act);
	});
}





//关于内容页的滚动定位,包含x店通用的点击滚动
function init_content_nav()
{	
	var is_show_fix = false;	
	var content_idx = -1;
	$.reset_nav = function(){
		if($.browser.msie && $.browser.version =="6.0")
		{
			$(".fix-nav").css("top",$(document).scrollTop());
		}	

		var navheight = $("#rel_nav").offset().top;
		var docheight = $(document).scrollTop();		
		if(docheight>navheight)		
		{			
			if(!is_show_fix)
			{	
				is_show_fix = true;
				$(".fix-nav").show();
				$("#rel_nav").css("visibility","hidden");
				if($.browser.msie && $.browser.version =="6.0")
				{						
					$(".fix-nav").css("width",900);			
				}
				else
				{
					$(".fix-nav").css({"top":0,"position":"fixed"});					
					$(".fix-nav").animate({
						width:750
					}, {duration: 200,queue:false });
				}
			}
		}
		else
		{
			if(is_show_fix)
			{
				is_show_fix = false;
				$("#rel_nav").css("visibility","visible");
				if($.browser.msie && $.browser.version =="6.0")
				{
					$(".fix-nav").hide();
					$(".fix-nav").css("width",750);
				}
				else
				{
					$(".fix-nav").css({"top":navheight,"position":"absolute"});
					$(".fix-nav").animate({
						width:750
					}, {duration: 200,queue:false,complete:function(){
						$(".fix-nav").hide();
					}});
				}
				
				
			}
		}
		
		//开始自定定位nav的当前位置	
		var content_boxes = $(".show-content .content_box");
		$(".show-nav").find("li").removeClass("active");
		content_idx = -1;
		for(i=0;i<content_boxes.length;i++)
		{
			var scrollTop = $(document).scrollTop() + 50; 
			var current_top = $(content_boxes[i]).offset().top;//内容盒子高度偏移，预留菜单高度
			var next_top = current_top + 50000;  //下一个高度
			if(i<content_boxes.length-1)
			next_top = $(content_boxes[i+1]).offset().top;	
			if(scrollTop>=current_top&&scrollTop<next_top)
			{
				var rel_id = $(content_boxes[i]).attr("rel");	
				content_idx = rel_id;
				break;
			}
			
		}

		$(".show-nav").find("li[rel='"+content_idx+"']").addClass("active");
	};
	$.reset_nav();	
	$(window).scroll(function(){
		$.reset_nav();
	});	
	
	//滚动至xx定位
	$.scroll_to = function(idx){
		var rel_id = idx;	
		var content_box = $(".show-content .content_box[rel='"+rel_id+"']");
		var top = $(content_box).offset().top-40;
		
		$("html,body").animate({scrollTop:top},"fast","swing",function(){
			content_idx = rel_id;
			$(".show-nav").find("li").removeClass("active");
			$(".show-nav").find("li[rel='"+content_idx+"']").addClass("active");
		});
	};
	//菜单点击
	$(".show-nav").find("li").bind("click",function(){
		
		var rel_id = $(this).attr("rel");	
		$.scroll_to(rel_id);
	});
	

}
//领取优惠券
$(".j-get-youhui").live("click",function(){
	var data_id=$(this).attr("data-id");

	var query = new Object();
	query.act = "download_youhui";
	query.id = data_id;
	$.ajax({
		url:AJAX_URL,
		data:query,
		dataType:"json",
		type:"POST",
		success:function(obj){
			console.log(obj);
			$.showSuccess(obj.info,function(){
				if(obj.change_status == 1){
					$(".j-get-youhui[data-id='"+data_id+"']").unbind("click");
					if(obj.use_status == 1){
						$(".j-get-youhui[data-id='"+data_id+"']").attr("href",obj.jump);
					}else{
						$("li[data-id='"+data_id+"']").addClass("disable");
					}
					$(".j-get-youhui[data-id='"+data_id+"']").html(obj.button_info);
					$(".j-get-youhui[data-id='"+data_id+"']").removeClass("j-get-youhui");
				}
			});
		}
	});

});