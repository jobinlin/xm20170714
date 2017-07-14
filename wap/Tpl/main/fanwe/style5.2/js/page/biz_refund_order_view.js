$(document).on("pageInit", "#biz_refund_order_view", function(e, pageId, $page) {
	$(".refund-btn").on('click', '.j-refund-agree', function() {
		$.confirm('是否立即退款', function () {
			$.ajax({
				url: $('.j-refund-agree').attr("data-href"),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					console.log(obj);
					if(obj.biz_login_status==0){
						$.router.load(obj.jump,true);
					}else{
						if(obj.status){
							$.toast(obj.info);
							$.loadPage(window.location.href );
						}else{
							$.toast(obj.info);
						}
					}
				},
        	});
		});
	});
	$(".refund-btn").on('click', '.j-refund-refuse', function() {
		$.confirm('是否拒绝退款', function () {
			$.ajax({
				url: $('.j-refund-refuse').attr("data-href"),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.biz_login_status==0){
						$.router.load(obj.jump,true);
					}else{
						if(obj.status){
							$.toast(obj.info);
							$.loadPage(window.location.href );
						}else{
							$.toast(obj.info);
						}
					}
				},
        	});
		});
	});
});