$(document).on("pageInit", "#biz_shop_verify", function(e, pageId, $page) {
	$(".biz-link-bar").on('click', '.j-qrcode', function() {
		if(app_index == 'wap'){
			$.toast("手机浏览器暂不支持，请下载APP");
		}
	});
	$(".biz-manager-bar").on('click', '.j-unopen', function() {
		$.toast("暂未开放");
	});
	$(".biz-manager-bar").on('click', '.store_pay_unopen', function() {
		$.toast("没有操作权限");
	});
	$(".to-qrcode").on('click', function() {
		if(is_store_payment==1){
			if(open_store_payment_count>0){
				
			}else{
				$.toast("不存在支持到店买单的门店");
				return false;
			}
		}else{
			$.toast("该商户不支持到店买单");
			return false;
		}
	});


/* 消费券验证 */
	var pre_coupon_pwd="";
	$("input[name='qr_code']").keyup(function(){
		var coupon_pwd = $(this).val();
		var code_len = coupon_pwd.length;
		var code_rule = /^[0-9]{12}$/;

		if(pre_coupon_pwd == coupon_pwd){

		}else{
			pre_coupon_pwd = coupon_pwd;
			if(code_len == 12){
				if(!code_rule.test(coupon_pwd)){
					$.toast('您输入的券码无效');
				}
				else{
					$.post(index_check_url, { "coupon_pwd": coupon_pwd },function(data){
						if (data.status){
							$(".code-input").val("");
							location.href = data.jump+'&coupon_pwd='+coupon_pwd;
						}else{
							$.toast(data.info);
						}
					}, "json");
				}
			}else if (code_len > 12){
				$.toast('您输入的券码无效');
			}
		}
	});


});


