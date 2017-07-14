/**
 * Created by Administrator on 2016/9/7.
 */

$(document).on("pageInit", "#dc_order_pay", function(e, pageId, $page) {

	$('.fee_count').hide();
	init_payment_input();
	init_pay_btn();
	function init_payment_input(){
		$("input[name='all_account_money']").live("change",function () {

			if($("#all_account_money").hasClass("active")){
				$("#all_account_money").removeClass("active");
			}else{
				$("#all_account_money").addClass("active");
				$("input[name='payment']").prop("checked",false);
			}
			//count_buy_total();
			$('.fee_count').hide();
			$('.fee_count .payment_fee').text(0);
			local_count()
		});
		
		$(".payment").live("click",function(){
			$("input[name='payment']").prop("checked",false);
			$(".payment").removeClass('active');
			$(this).siblings("input[name='payment']").prop("checked",true);
			$(this).addClass("active");

			$("#all_account_money").removeClass("active");
			$("input[name='all_account_money']").prop("checked",false);
			var fee = Number($('.active .fee_amount').text());
			if (fee > 0) {
				$('.fee_count .payment_fee').text(fee.toFixed(2));
				$('.fee_count').show();
			} else {
				$('.fee_count .payment_fee').text(0);
				$('.fee_count').hide();
			}
			local_count()
		});
	}

	function local_count() {
		var total= $('.total_count').text().replace(",","");
		var payment_fee= $('.payment_fee').text().replace(",","");
		var discount= 0; // $('.discount').text().replace(",","");
		var ready_pay = Number(total) - Number(discount) + Number(payment_fee);
		$('.ready_pay').text(ready_pay.toFixed(2));
	}

	function init_pay_btn(){
	    $(".u-sure-pay").bind("click",function(){
	    	var all_account_money = 0; // 是否余额支付
			var payment = 0;
			//全额支付
			if($("#all_account_money").hasClass("active")) {
				all_account_money = 1;
			} else { // 其它支付方式
				payment = $("input[name='payment']:checked").val();
			}

			if (all_account_money == 0 && payment == 0) {
				$.toast('请选择一个支付方式');
				return;
			}
			var query = {
				'payment': payment, 
				'all_account_money': all_account_money,
				'id': order_id,
				'act': 'order_done'
			};
	        $.ajax({
				url: ORDER_AJAX,
				data:query,
				type: "POST",
				dataType: "json",
				success: function(data){
					if(data.status==1){
						if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
							if(data.pay_status==1){
								$.router.load(data.jump, true);
							}else{
								location.href=data.jump;
							}
						} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
							 $.router.load(data.jump, true);

						} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
							if(data.online_pay==3){
								try {

									var str = pay_sdk_json(data.sdk_code);
									App.pay_sdk(str);
								} catch (ex) {

									$.toast(ex);
									$.loadPage(location.href);
								}
							}else{
								var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

								try {
									App.open_type(pay_json);
									$.confirm('已支付完成？', function () {
										$.loadPage(location.href);

									},function(){
										$.loadPage(location.href);

									});
								} catch (ex) {
									$.toast(ex);
									$.loadPage(location.href);
								}
							}
						}
					}else{
						
						$.alert(data.info);
					}
				},
				error:function(ajaxobj) {

				}
			});
	    });
	};
});

