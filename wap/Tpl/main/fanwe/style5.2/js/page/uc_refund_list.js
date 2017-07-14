

$(document).on("pageInit", "#uc_refund_list", function(e, pageId, $page) {

	init_list_scroll_bottom();

	$(document).on('click', '.refund_view', function() {
		$.router.load($(this).attr('data-src'));
	})
});