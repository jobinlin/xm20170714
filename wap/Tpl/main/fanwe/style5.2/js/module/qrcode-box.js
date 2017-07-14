function qrcode_box() {
	$(".j-open-qrcode").on('click', function() {
		$(".m-mask").addClass('active');
		$(".m-qrcode-box").addClass('active');
	});
	$(".j-close-qrcode").on('click', function() {
		$(".m-mask").removeClass('active');
		$(".m-qrcode-box").removeClass('active');
	});
}