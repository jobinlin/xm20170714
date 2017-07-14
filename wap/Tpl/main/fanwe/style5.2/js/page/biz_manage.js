$(document).on("pageInit", "#biz_manage", function(e, pageId, $page) {
	$(".biz-manage-list").on('click', '.j-unauth', function() {
		$.toast("没有操作权限");
	});
	dc_popup($(".j-open-dc"),$(".j-dc-popup"));
	dc_popup($(".j-open-rs"),$(".j-rs-popup"));
	$(".j-close-popup").on('click', function() {
		$(".dc-mask").removeClass('active');
		$(".dc-popup").removeClass('active');
	});
	//打开弹层
	function dc_popup(dc_open,popup) {
		dc_open.on('click', function() {
			$(".dc-mask").addClass('active');
			popup.addClass('active');
			/* Act on the event */
		});
	}
});