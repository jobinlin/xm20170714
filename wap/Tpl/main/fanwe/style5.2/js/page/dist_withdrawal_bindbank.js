$(document).on("pageInit", "#dist_withdrawal_bindbank", function(e, pageId, $page) {

	$("#btn").bind("click",function(){
		var phone=$("#phonenumer").val();
		if(phone==""){
			$.toast("请到PC端绑定手机");
		}
	});
	
	$("form[name='add_card']").bind("submit",function(){		
		var bank_name = $("form[name='add_card']").find("input[name='bank_name']").val();
		var bank_account = $("form[name='add_card']").find("input[name='bank_account']").val();
		var bank_user = $("form[name='add_card']").find("input[name='bank_user']").val();
		var sms_verify = $("form[name='add_card']").find("input[name='sms_verify']").val();		
		if($.trim(bank_name)=="")
		{
			$.toast("请输入开户行名称");
			return false;
		}
		if($.trim(bank_account)=="")
		{
			$.toast("请输入开户行账号");
			return false;
		}
		if($.trim(bank_user)=="")
		{
			$.toast("请输入开户人真实姓名");
			return false;
		}
		if($.trim(sms_verify)=="")
		{
			$.toast("请输入短信验证码");
			return false;
		}
		
		var ajax_url = $("form[name='add_card']").attr("action");
		var query = $("form[name='add_card']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$.toast(obj.info);	
					setTimeout(function(){
						location.href = obj.jump;
					},1500);
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)location.href = obj.jump;
					}
					
				}
				else{
					
				}
			}
		});		
		return false;
	});
});
