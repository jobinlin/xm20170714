$(document).on("pageInit", "#uc_charge", function(e, pageId, $page) {

	$("form[name='do_charge']").bind("submit",function(){

		var money = $("input[name='money']").val();
		var payment_id = $("input[name='payment_id']:checked").val();		

		if($.trim(payment_id)==""||isNaN(payment_id)||parseFloat(payment_id)<=0)
		{
			$.alert("请选择支付方式");
			return false;
		}
		
		if($.trim(money)==""||isNaN(money)||parseFloat(payment_id)<=0)
		{
			$.alert("请选择正确的充值金额");
			return false;
		}		
		
		
		var query = $(this).serialize();
		var action = $(this).attr("action");

		$.ajax({
			url:action,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(data){
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
			
				return false;
			}
		});
		
		return false;

	});
	
	
	$(".select_num").bind("click",function(){
		$(".money_number").removeClass("selected");
		$(this).addClass("selected");
		$("input[name='money']").val($(this).attr('data'));
	});
    $(".first_number").trigger("click");
    if(money_number_array_other){
        $(".select_other").picker({
            toolbarTemplate: '<header class="bar bar-nav">\
          <button class="button button-link pull-right close-picker">确定</button>\
          <h1 class="title">请选择金额</h1>\
          </header>',
            cols: [
                {
                    textAlign: 'center',
                    values: money_number_array_other
                }
            ],
            onClose:function(){
                $(".money_number").removeClass("selected");
                $(".select_other").addClass("selected");
                $("input[name='money']").val(intval($(".select_other").val()));
            }
        });
    }
	$(".select_num1").focus(function(){
		$(".select_num").removeClass("selected");
	});
	$(".select_num1").blur(function(){
		//$(".select_num").removeClass("selected");
		$("input[name='money']").val($(this).val());
	});
    function intval(p){
        if(!p)return 0;
        if(typeof p=="number"){
            return parseInt(p);
        }else if(typeof p=="string"){
            return parseInt(p.replace(/[^0-9\.]*/ig,""));
        }
    }
	$(".pay_select").bind("click",function(){
		$(".pay_select .j-selected-icon").removeClass("checked");
		$(this).find(".j-selected-icon").addClass("checked");		
		$(".pay_select").find("input[name='payment_id']").prop("checked",false);
		$(this).find("input[name='payment_id']").prop("checked",true);
	});
});
