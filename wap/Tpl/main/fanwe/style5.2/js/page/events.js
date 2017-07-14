$(document).on("pageInit", "#events", function(e, pageId, $page) {
	init_listscroll(".j_ajaxlist_"+cate_id_1,".j_ajaxadd_"+cate_id_1);
	function tab_line() {
		var init_width=$(".m-events-tab a:first-child").width();
		var init_left=$(".m-events-tab a:first-child").offset().left;
		$(".events-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	function item_width() {
	}
	var tab_length =$(".m-events-tab li").length;
	if (tab_length<3) {
		$(".m-events-tab").hide();
	} else if(tab_length<6){
		$(".m-events-tab ul").addClass('flex-box');
		$(".m-events-tab ul li").addClass('flex-1');
	}
	else{
		var w_width=$(window).width();
		var item_width=w_width/5.5;
		$(".m-events-tab li").css('width', item_width);
		$(".m-events-tab ul").css('width', item_width*tab_length);
		$(".m-events-tab ul li").addClass('tab-item');
	}
	tab_line();
	$(".m-events-tab li:first-child").addClass('active');
	$(".m-events-tab a").click(function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		$(".m-events-tab a").removeClass('active');
		$(this).addClass('active');
		$(".m-events-list").hide();
		var item_width=$(this).width();
		var item_left=$(this).offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: item_width,
			left: item_left
		});
		var url=$(this).attr("data-src");
		var cate_id=$(this).attr("cate-id");
		$(".j_ajaxlist_"+cate_id).show();
		$(".content").scrollTop(1); 
		//alert($(".j_ajaxlist_"+cate_id).html());return false;
		//console.log($(".j_ajaxlist_"+cate_id).html());
		if($(".j_ajaxlist_"+cate_id).html()==null){
			//alert(111111);return false;
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			    },
			    error:function()
			    {
			    	
			    	$(".j_ajaxlist_"+cate_id).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			}
        }
	});
});