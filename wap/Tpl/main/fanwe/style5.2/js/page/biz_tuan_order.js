$(document).on("pageInit", "#biz_tuan_order", function(e, pageId, $page) {
	init_list_scroll_bottom();
	var swiperm = new Swiper(".j-order-shop-img", {
	    scrollbarHide: true,
	    slidesPerView: 'auto',
	    freeMode: false,
	});
});