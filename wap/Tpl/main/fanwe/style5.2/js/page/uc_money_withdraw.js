var lesstime = 0;
$(document).on("pageInit", "#uc_money_withdraw", function(e, pageId, $page) {
	$(".bank-select").click(function() {
		if(bank==1){
			$(".select-bank").addClass('active');
		}
	});
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
		if(money>all_money){
			$.toast("提现超额");
			$(this).val(all_money);
		}
	});

	// 绑定删除用户银行卡的事件
	$('.del_bank').bind('click', function() {
		var bank_id = $(this).attr('data-id');
		var ajax_url = $(this).attr('data-action');
		// if_confirm??
		$.ajax({
			url: ajax_url,
			data: {'bank_id':bank_id},
			dataType: 'json',
			success: function(obj) {
				if (obj.status == 1) {
					$.toast('删除成功');
					// 移除前台展示的DOM
				} else {
					$.toast(obj.info);
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


	submit();
	
	function submit(){
		$(".withdraw_submit").bind("click",function(){	
			$(".withdraw_submit").attr('disabled',"true");
			setTimeout(function(){
				$(".withdraw_submit").removeAttr("disabled");
			},3000);
			var bank_id = $("form[name='withdraw']").find("input[name='bank_id']").val();
			var money = $("form[name='withdraw']").find("input[name='money']").val();
			var pwd = $("form[name='withdraw']").find("input[name='pwd']").val();
			if($.trim(pwd)=="")
			{
				$.toast("请输入登录密码");
				return false;
			}

			if($.trim(bank_id)==""||isNaN(bank_id)||parseFloat(bank_id)<=0)
			{
				$.toast("请选择提现账户");
				setTimeout(function(){
					load_page($(".load_page"));
				},1000);
				return false;
			}
			if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
			{
				$.toast("请输入正确的提现金额");
				return false;
			}
			
			var ajax_url = $("form[name='withdraw']").attr("action");
			var query = $("form[name='withdraw']").serialize();
			//console.log(query);
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
							},1500);
						}
					}else if(obj.status==0){
						if(obj.info)
						{
							$.toast(obj.info);
							if(obj.url){
								setTimeout(function(){
									location.href = obj.url;
								},1500);
							}
						}
						else
						{
							if(obj.url)location.href = obj.url;
						}
						
					}
				}
			});		
			return false;
		});
	}
});


