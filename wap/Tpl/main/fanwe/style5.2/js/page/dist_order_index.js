$(document).on("pageInit", "#dist_order_index", function(e, pageId, $page) {
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j-ajaxlist-"+status,".j-ajaxadd-"+status);
	}
	
	function tab_line() {
		var init_width=$(".biz-shop-order-tab .active span").width();
		var init_left=$(".j-tab-item.active span").offset().left;
		$(".tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	$(".biz-shop-order-tab").on('click','.j-tab-item', function(event) {
		var type=$(this).attr("type");
		
		if($(".content").find(".j-ajaxlist-"+type).length > 0){

			$(".biz-shop-order-tab .j-tab-item").removeClass('active');
			$(this).addClass('active').siblings().removeClass('active');
			
			$(".content .m-biz-shop-order-list").removeClass('active');
			$(".content").find(".j-ajaxlist-"+type).addClass('active').siblings().removeClass('active');
			tab_line();
			
			$(".content").scrollTop(1); 
		    if( $(".content").scrollTop()>0 ){
		    	infinite(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
		    }
			
		}else{
		
			$(".j-tab-item").removeClass('active');
			$(this).addClass('active');
			var item_width=$(this).find('span').width();
			var item_left=$(this).find('span').offset().left;
			$(".tab-line").css({
				width: item_width,
				left: item_left
			});
			var url=$(this).attr("data-href");
			
			$.ajax({
				url:url,
				type:"POST",
				success:function(html)
				{
					$(".content").append($(html).find(".content").html());
					$(".j-ajaxlist-"+type).addClass('active').html($(html).find(".j-ajaxlist-"+type).html()).siblings().removeClass('active');
			
					if ($(html).find(".j-ajaxadd-"+type).length==0) {
						return;
					}else{
						$(".content").scrollTop(1); 
					    if( $(".content").scrollTop()>0 ){
							$(document).off('infinite', '.infinite-scroll-bottom');
							init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
					    }
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
		}
	});
	var swiperm = new Swiper(".j-order-shop-img", {
	    scrollbarHide: true,
	    slidesPerView: 'auto',
	    freeMode: false,
	});
});