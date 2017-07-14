$(document).on("pageInit", "#biz_coupon_check", function(e, pageId, $page) {
	function openSelect(open_btn,open_item) {
		$(open_btn).on('click', function() {
			$(".delivery-mask").addClass('active');
			$(open_item).addClass('active');
		});
		$(".delivery-mask").on('click', function() {
			$(this).removeClass('active');
			$(open_item).removeClass('active');
		});
	}
	function closeSelect(close_item) {
		$(".delivery-mask").removeClass('active');
		$(close_item).removeClass('active');
	}
	//绑定团购数量
	$(".flex-box .coupon_use_count").on("blur",function(){
		var use_count = $(this).val();
		var can_number = $(".coupon-check-count .num").text();
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(parseInt(use_count) > parseInt(can_number)){
			use_count=can_number;
		}
		$(this).val(use_count);
	});
	$(".flex-box .coupon_use_count").on("focus",function(){
		$(this).select();
	});
	//团购券数量
	$(".flex-box .count-disable").on("click",function(){
		var use_count = $(".flex-box .coupon_use_count").val();
		var can_number = $(".coupon-check-count .num").text();
		use_count = parseInt(use_count) - 1;
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(use_count>can_number){
			use_count=can_number;
		}
		$(".flex-box .coupon_use_count").val(use_count);
	});
	$(".flex-box .count-plus").on("click",function(){
		var use_count = $(".flex-box .coupon_use_count").val();
		var can_number = $(".coupon-check-count .num").text();
		use_count = parseInt(use_count) + 1;
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(use_count>can_number){
			use_count=can_number;
		}
		$(".flex-box .coupon_use_count").val(use_count);
	});
	//选择验证门店
	openSelect('.j-shop-select','.shop-select');
	$(".shop-list").on('click', 'li', function() {
		$(".shop-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".shop-cancle").on('click', function() {
		closeSelect('.shop-select');
	});
	$(".shop-confirm").on('click', function() {
		closeSelect('.shop-select');
		$(".j-shop-select .shop-name").html($(".shop-select .active .shop-name").html());
		$(".j-shop-select .shop-id").val($(".shop-select .active .shop-id").val());
	});
	
	//核销提交
	$(".check-confirm").on('click', function() {
		var query = new Object();
		query.location_id = $(".j-shop-select .shop-id").val();
		query.coupon_use_count = $(".flex-box .coupon_use_count").val();
		query.coupon_pwd = coupon_pwd;
		$.ajax({
			url:url,
			data:query,
			dataType: "json",
			type:"post",
			success:function(obj){
				if(obj.status==1){
					$.toast(obj.info);
					setTimeout(function() {
                    	location.href = obj.jump;
                    }, 1500);
				}else{
					$.toast(obj.info);
				}
			},
            error: function() {
                $.toast("网络被风吹走啦~");
           	}
		});
	});
});