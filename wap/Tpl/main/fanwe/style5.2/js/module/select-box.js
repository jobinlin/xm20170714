function select_box(open,box) {
	open.on('click', function() {
		box.addClass('active');
		$(".m-mask").addClass('active');
	});
	$(".j-close-select").on('click', function() {
		$(".m-select-box").removeClass('active');
		$(".m-mask").removeClass('active');
	});
}