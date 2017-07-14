$(document).on("pageInit", "#uc_fxwithdraw", function(e, pageId, $page) {
	/*$(".bank-select").click(function() {
		$(".select-bank").addClass('active');
	});*/
	$(".mask").click(function() {
		$(".select-bank").removeClass('active');
	});
	$(".bank-list li").click(function() {
		$(".bank-list li .iconfont").removeClass('selected');
		$(this).find('.iconfont').addClass('selected');
		$(".bank-select .bank-info").html($(this).find('.bank-info').html());
		$(".select-bank").removeClass('active');
		$("input[name='bank_id']").val($(this).attr("bank_id"));
	});
	
	$(".select-bank .add-bank").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$(".select-bank .close-btn").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$("form[name='withdraw']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>fx_money){
			$.toast("提现超额");
			$(this).val(fx_money);
		}
	});

	var wfeeObj = $('input.withdraw-rate');
	if (wfeeObj) {
		$('input[name=money]').on('input propertychange', function() {
			var money = parseFloat($('input[name="money"]').val());
			if (!money) {
				wfeeObj.val('');
				return false;
			}
			if (money > 0) {
				var rate = parseFloat(wfeeObj.attr('rate-data'));
				var fee = Math.ceil((money * rate) / 10) / 100;
				if (fee < 0) {
					fee = 0;
				}
				wfeeObj.val(fee);
			}
		});
	}

	
	$("form[name='withdraw']").bind("submit",function(){		
		var bank_id = $("form[name='withdraw']").find("input[name='bank_id']").val();
		var money = $("form[name='withdraw']").find("input[name='money']").val();
		var pwd = $("form[name='withdraw']").find("input[name='pwd']").val();
		if($.trim(pwd)=="")
		{
			$.toast("请输入登录密码");
			return false;
		}

		if($.trim(bank_id)==""||isNaN(bank_id)||parseFloat(bank_id)<0)
		{
			$.toast("请选择提现账户");
			return false;
		}
		if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
		{
			$.toast("请输入正确的提现金额");
			return false;
		}
		
		if(fx_money<parseFloat(money)){
			$.toast("提现超额");
			return false;
		}
		
		var ajax_url = $("form[name='withdraw']").attr("action");
		var query = $("form[name='withdraw']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$.toast("提现申请成功，请等待管理员审核");
					if(obj.url){
						setTimeout(function(){
							location.href = obj.url;
						},1000);
					}
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.url){
							setTimeout(function(){
								location.href = obj.url;
							},1000);
						}
					}
					else
					{
						if(obj.url)location.href = obj.url;
					}
					
				}else{
					
				}
			}
		});		
		return false;
	});

	function init_bank(){
		var bank_name=$(".bank").find(".checked").attr("bank_name");
		var bank_id=$(".bank").find(".checked").attr("rel");
		$("input[name='bank_name']").val(bank_name);
		$("input[name='bank_id']").val(bank_id);
	}


});
