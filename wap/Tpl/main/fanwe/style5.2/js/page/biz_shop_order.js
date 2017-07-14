$(document).on("pageInit", "#biz_shop_order", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-0",".j-ajaxadd-0");
	init_listscroll(".j-ajaxlist-1",".j-ajaxadd-1");
	init_listscroll(".j-ajaxlist-2",".j-ajaxadd-2");
	function tab_line() {
		var init_width=$(".biz-shop-order-tab .active span").width();
		var init_left=$(".j-tab-item.active span").offset().left;
		$(".tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	$(".biz-shop-order-tab").on('click', '.j-tab-item', function(event) {
		var type=$(this).attr("type");
		
		if($(".content").find(".j-ajaxadd-"+type).length > 0){

			$(".biz-shop-order-tab .j-tab-item").removeClass('active');
			$(this).addClass('active').siblings().removeClass('active');
			
			$(".content .m-biz-shop-order-list").removeClass('active');
			$(".content").find(".j-ajaxlist-"+type).addClass('active').siblings().removeClass('active');
			tab_line();
			init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
		}else{
		

			$(document).off('infinite', '.infinite-scroll-bottom');
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
					
					$(".j-ajaxlist-"+type).addClass('active').html($(html).find(".j-ajaxlist-"+type).html()).siblings().removeClass('active');
			
					if ($(html).find(".j-ajaxadd-"+type).length==0) {
						return;
					}else{
						init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
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