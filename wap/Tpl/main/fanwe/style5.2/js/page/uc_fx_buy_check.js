$(document).on("pageInit", "#uc_fx_buy_check", function(e, pageId, $page) {
	$('.fee_count').hide();
	init_payment_input();
	//init_pay_btn();
	
	$(".u-sure-pay").bind("click",function(){
		var is_ajax = 1;
		var query = new Object();

		//全额支付
		if($("#all_account_money").hasClass("active"))
		{
			query.all_account_money = 1;
		}
		else
		{
			query.all_account_money = 0;
		}

		//支付方式
		var payment = $("input[name='payment']:checked").val();
		if(!payment)
		{
			payment = 0;
		}
		query.payment = payment;
		query.order_id = order_id;
		query.is_ajax = is_ajax;
		query.act = "pay_done";
		$.ajax({
			url: custom_ajax_url,
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
								//console.log(str);
								//$.showErr(str);
								App.pay_sdk(str);
							} catch (ex) {

								$.toast(ex);
								//window.location.reload();
								$.loadPage(location.href);
							}
						}else{
							var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

							try {
								App.open_type(pay_json);
								$.confirm('已支付完成？', function () {
//		   							$.showIndicator();
//			   					      setTimeout(function () {
//			   					      	  window.location.reload();
//			   					          $.hideIndicator();
//			   					    }, 500);
									$.loadPage(location.href);

								},function(){
//	   							$.showIndicator();
//		   					      setTimeout(function () {
//		   					      	  window.location.reload();
//		   					          $.hideIndicator();
//		   					    }, 500);
									$.loadPage(location.href);
									// $.toast('cancel');
								});
							} catch (ex) {
								$.toast(ex);
								$.loadPage(location.href);
								//window.location.reload();
							}
						}
					}



				}else{
					
					$.toast(data.info);
				}
			},
			error:function(ajaxobj)
			{

			}
		});
	});
	
});

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
	var ready_pay = Number(total) + Number(payment_fee);
	$('.ready_pay').text(ready_pay.toFixed(2));
}