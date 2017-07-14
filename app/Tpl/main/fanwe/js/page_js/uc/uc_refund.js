$(document).ready(function() {
	$(".j-check-reason").bind('click', function() {
		$(".refund-mask").addClass('active');
		$(".refund-reason-box").addClass('active');
		$(".refund-reason-bd").html($(this).parents('.order-shop').find('.refund-reason').html());
	});
	$(".j-close-reason").bind('click', function() {
		$(".refund-mask").removeClass('active');
		$(".refund-reason-box").removeClass('active');
	});
});