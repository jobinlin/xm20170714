/**
 * Created by Administrator on 2016/9/7.
 */

$(document).on("pageInit", "#pay", function(e, pageId, $page) {
	count_order_total();
	function count_order_total_change(){
		$("input[name='all_account_money']").unbind('change');
		$("input[name='all_account_money']").bind("change",function () {


			if($("#all_account_money").hasClass("active")){
				$("#all_account_money").removeClass("active");
			}else{
				$("#all_account_money").addClass("active");
			}
			$("input[name='payment']").prop("checked",false);
			count_order_total();

		});

		$(".payment").unbind("click");
		$(".payment").bind("click",function(){
			$("input[name='payment']").prop("checked",false);
			$(this).siblings("input[name='payment']").prop("checked",true);
			$("#all_account_money").removeClass("active");
			count_order_total();
		});
		$(".u-sure-pay.j_pay_button").unbind("click");
		$(".u-sure-pay.j_pay_button").bind("click",function(){

			submit_order($(this));

		});
	}
	function count_order_total()
	{
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
		var rel=$("input[name='payment']:checked").attr("rel");
		query.payment = payment;
		query.rel = rel;
		query.id = order_id;
		query.is_ajax = is_ajax;
		query.act = "pay";
		$.ajax({
			url: CART_URL,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(".content").html(data.html);
				count_order_total_change();
			},
			error:function(ajaxobj)
			{
//    			if(ajaxobj.responseText!='')
//    			alert(LANG['REFRESH_TOO_FAST']);
			}
		});
	}
	function submit_order(obj)
	{
		
		$(obj).removeClass('j_pay_button');
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
		var rel=$("input[name='payment']:checked").attr("rel");
		query.payment = payment;
		query.rel = rel;
		query.id = order_id;
		query.is_ajax = is_ajax;
		query.act = "order_done";
		$.ajax({
			url: CART_URL,
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
								$(obj).addClass('j_pay_button');
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
					$(obj).addClass('j_pay_button');
				}

			},
			error:function(ajaxobj)
			{
				$(obj).addClass('j_pay_button');

			}
		});
	}
	

	
});

