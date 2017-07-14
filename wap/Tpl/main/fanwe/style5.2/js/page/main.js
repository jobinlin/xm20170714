$(document).on("pageInit", "#main", function(e, pageId, $page) {
	init_auto_load_data();
	/*首页广告图轮播*/
	var mySwiper = new Swiper('.j-index-banner', {
	    speed: 400,
	    spaceBetween: 0,
	    pagination: '.swiper-pagination',
     	autoplay: 2500
	});
	/*商家设置头部列表*/
	var mySwiper = new Swiper('.j-sort_nav', {
	    speed: 400,
	    spaceBetween: 0,
	    pagination: '.sort-pagination'
	});
	var mySwiper = new Swiper('.j-index-lb', {
	    speed: 400,
	    spaceBetween: 0
	});
/*地址定位
if($.fn.cookie("cancel_geo")!=1)
	{
		if(navigator.geolocation)
		{
			 var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
			 navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
		}
	}
	*/
if($.fn.cookie("cancel_geo")!=1){
	position();
}
});


