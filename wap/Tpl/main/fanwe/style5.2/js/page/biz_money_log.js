$(document).on("pageInit", "#biz_money_log", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-"+type_1,".j-ajaxadd-"+type_1);
	
	function tab_line() {
		var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	
	//分类加载内容
	$(".j-list-choose").on('click', function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		var type=$(this).attr("type");
//		alert(type);
		$(".j-list-choose").removeClass('active');
		$(this).addClass('active');
		$(".biz-money-log").hide();
		tab_line();
		var url=$(this).attr("data-href");
		$(".j-ajaxlist-"+type).show();
		$(".content").scrollTop(1); 
		if($(".j-ajaxlist-"+type).html()==null){
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
			    },
			    error:function()
			    {
			    	
			    	$(".j-ajaxlist-"+type).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
			}
        }

	});
	
});