$(document).on("pageInit", "#uc_youhui", function(e, pageId, $page) {
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
	}
	
	$(".page").on('click',".j-list-choose",function(){
    	$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		$(".j-list-choose").removeClass('active');
	    $(this).addClass('active');
	    tab_line();
		if($.trim($(".j-ajaxlist-"+rel).html()) == ""){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".m-youhui-list").addClass("hide");
					$(".content").append($(html).find(".content").html());
					$(".content").scrollTop(1);
					if($(".content").scrollTop()>0){
						init_listscroll(".j-ajaxlist-"+rel,".j-ajaxadd-"+rel);
					}
				},
				error:function()
				{
					$(".j-ajaxlist-"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			$(".m-youhui-list").addClass("hide");
			$(".j-ajaxlist-"+rel).removeClass("hide");
			$(".content").scrollTop(1);
			if($(".content").scrollTop()>0){
				infinite(".j-ajaxlist-"+rel,".j-ajaxadd-"+rel);
			}
		}
	});
	
    function tab_line() {
	    var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
		      width: init_width,
		      left: init_left
		    });
    }
	tab_line();
	    
	$(".j-youhui").on('click', function() {
	  $(".youhui-link").removeClass('hide');
	  $(".ecv-link").addClass('hide')
	});
	$(".j-ecv").on('click', function() {
	  $(".ecv-link").removeClass('hide');
	  $(".youhui-link").addClass('hide')
	});
	//打开弹层
	$("#uc_youhui").on('click', '.j-support-shop', function() {
	  $(".youhui-mask").addClass('active');
	  $(".support-shop-box").addClass('active');
	  var url=$(this).attr("ajax-url");
	  get_location(url);
	});
	$("#uc_youhui").on('click', '.j-qrcode', function() {
	  $(".youhui-mask").addClass('active');
	  $(".qrcode-box").addClass('active');
	  $(".qrcode-box").find(".youhui-code").html("券码："+$(this).attr("data-sn"));
	  $(".qrcode-box").find(".qrcode img").attr("src",$(this).attr("img-url"));
	  
	  var url=$(this).attr("ajax-url");
	  get_location(url);
	  
	});
	$("#uc_youhui").on('click', '.j-close-mask', function() {
	  $(".youhui-mask").removeClass('active');
	  $(".support-shop-box").removeClass('active');
	  $(".qrcode-box").removeClass('active');
	});
});
function get_location(url){
	$.ajax({
        url:url,
        type:"POST",
        dataType:"json",
        success:function(obj)
        {
        	$(".support-list").empty();
      
        	if(obj.location_info){
        		var length=obj.location_info.length;
        		$(".support-hd").html('本券限以下实体店到店消费使用');
        		var location_li="";
	        	for(var i=0;i<length;i++){
	        		location_li+="<li class='flex-box'>"
									+"<div class='shop-info flex-1 r-line'>"
									+"<a href='"+obj['location_info'][i]['jump']+"'><p class='shop-name'>"+obj['location_info'][i]['name']+"</p></a>"
									+"<p class='shop-address'>"+obj['location_info'][i]['address']+"</p>"
									+"</div><a href='tel:"+obj['location_info'][i]['tel']+"' class='iconfont'>&#xe618;</a></li>";
	        	}
	        	$(location_li).appendTo($(".support-list"));
        	}else{
        		$(".support-hd").html('');
        	}
        }
    });
}