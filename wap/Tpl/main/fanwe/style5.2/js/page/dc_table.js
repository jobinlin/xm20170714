$(document).on("pageInit", "#dc_table", function(e, pageId, $page) {

	$('a.rs-btn').on('click', function() {
		if (location_close) {
			$.toast('店铺休息中,暂停预订');
			return;
		} else {
			$.router.load($(this).attr('data-url'), true);
		}
	})

	$(".j-rs-day").on('click', function() {
		$(".j-rs-day").removeClass('active');
		$(this).addClass('active');
		$(".shop-rs-list").removeClass('active');
		$(".shop-rs-list").eq($(this).index()).addClass('active');
	});


	//增加收藏
	$('.add_location_collect').bind('click',function(){
		add_location_collect_function($(this));
        if ($(this).hasClass('collected')) {
            $(this).removeClass('collected');
        } else {
            $(this).addClass('collected');
        }
	});
});