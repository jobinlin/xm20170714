$(document).on("pageInit", "#dc_location", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	
	var mySwiper = new Swiper('.j-index-banner', {
		speed: 400,
		spaceBetween: 0,
		pagination: '.swiper-pagination',
		autoplay: 2500
	});
	var mySwiper = new Swiper('.j-sort_nav', {
	    speed: 400,
	    spaceBetween: 0
	});
	$(document).on('click', '.j-open-youhui', function() {
		var i_height=$(this).parent().find('.youhui-item').height();
		var t_height=i_height*$(this).parent().find('.youhui-item').length;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().find('.youhui-list').css('max-height', i_height*2);
		} else {
			$(this).addClass('active');
			$(this).parent().find('.youhui-list').css('max-height', t_height);
		}
	});
});