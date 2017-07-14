/**
 * Created by lynn on 2016/11/17.
 * Update by YXM on 2016/11/28. 路由改版
 */
$(document).on("pageInit", "#uc_coupon", function(e, pageId, $page) {
   
    var item_width = $(".j-tab-link[rel='"+eq+"']").width();
	var item_left = $(".j-tab-link[rel='"+eq+"']").offset().left;
	$(".tab-line").css({
		width: item_width,
		left: item_left
	});
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j_ajaxlist_"+status,".j_ajaxadd_"+status);
	}
    
    $(".page").on('click',".j-tab-link",function(){
    	$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		var item_width=$(this).width();
		var item_left=$(this).offset().left;
		$(".tab-line").css({
			width: item_width,
			left: item_left
		});
		if($.trim($("#tab"+rel).html()) == ""){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".tabs").find(".tab").removeClass("active");
					$(".tabs").append($(html).find(".tabs").html());
					$(".content").scrollTop(1);
					if($(".content").scrollTop()>0){
						init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
					}
				},
				error:function()
				{
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			$(".content").scrollTop(1);
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
	});
    
});