$(document).on("pageInit", "#index", function(e, pageId, $page) {
	// 初始化回到头部
	

	headerScroll();/*导航条变化*/
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
    spaceBetween: 0
});
/*方维头条*/
var swiper = new Swiper('.j-headlines', {
        pagination: '',
        direction: 'vertical',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        mousewheelControl: true,
        autoplay: 2000,
        loop: true
    });
/*首页小轮播*/
var mySwiper = new Swiper('.j-index-lb', {
    speed: 400,
    spaceBetween: 0,
    autoplay: 2500
});
/*跑马灯*/
var swiper = new Swiper('.j-horse-lamp', {
    scrollbarHide: true,
    slidesPerView: 'auto',
    centeredSlides: false,
    grabCursor: true
});

if($.fn.cookie("cancel_geo")!=1){
	position();
}
});
