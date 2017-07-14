$(document).on("pageInit", "#shop", function(e, pageId, $page) {
	init_auto_load_data();
	var mySwiper = new Swiper('.j-index-banner', {
		speed: 400,
		spaceBetween: 0,
		pagination: '.swiper-pagination',
		autoplay: 2500
	});
	var mySwiper = new Swiper('.j-index-lb', {
	    speed: 400,
	    spaceBetween: 0,
		autoplay: 2500
	});
/*商家设置头部列表*/
var mySwiper = new Swiper('.j-sort_nav', {
    speed: 400,
    spaceBetween: 0
});

});