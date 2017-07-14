$(document).on("pageInit", "#dist_withdrawal_form", function(e, pageId, $page) {
	$(".ui-textbox").val('');
	$("form[name='withdrawal_form']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>all_money){
			$.toast("提现超额");
			$(this).val(all_money);
		}
	});

	var load_page_count=0;
	
	$("form[name='withdrawal_form']").bind("submit",function(){		
		var money = $("form[name='withdrawal_form']").find("input[name='money']").val();
		var pwd = $("form[name='withdrawal_form']").find("input[name='pwd_verify']").val();
		if(is_bank=="")
		{	
			if(load_page_count==0){
				$.toast("请先绑定银行卡");
				setTimeout(function(){
					load_page($(".load_page"));
					setTimeout(function(){
						load_page_count=0;
					},100);
				},1500);
			}
			
			load_page_count++;
			return false;
		}
		
		if($.trim(pwd)=="")
		{
			$.toast("请输入登录密码");
			return false;
		}
		
		if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
		{
			$.toast("请输入正确的提现金额");
			return false;
		}
		
		var ajax_url = $("form[name='withdrawal_form']").attr("action");
		var query = $("form[name='withdrawal_form']").serialize();
		//console.log(query);
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$(".ui-textbox").val('');
					$.toast("提现申请成功，请等待管理员审核");
					if(obj.jump){
						setTimeout(function(){
							$.router.load(obj.jump, true);
							//location.href = obj.jump;
						},1500);
					}
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								$.router.load(obj.jump, true);
								//location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)$.router.load(obj.jump, true);
					}
					
				}
			}
		});		
		return false;
	});
});
