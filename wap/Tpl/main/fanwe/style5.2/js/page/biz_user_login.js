$(document).on("pageInit", "#biz_user_login", function(e, pageId, $page) {
	clear_input($('#account_name'),$('.j-name-clear'));
	clear_input($('#account_password'),$(".j-password-clear"));
	$("#login-btn").bind("click",function(){
		var account_name = $.trim($("input[name='account_name']").val());
		var account_password = $.trim($("input[name='account_password']").val());
		var form = $("form[name='user_login_form']");
		if(!account_name){
			$.toast("请填写账户名称");
			return false;
		}
		if(!account_password){
			$.toast("请输入密码");
			return false;
		}

		var query = $(form).serialize();
		var ajaxurl = $(form).attr("action");
		$.ajax({
			url:ajaxurl,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data["status"]==1){
					$.toast(data.info);
					window.setTimeout(function(){
						location.href = data.jump;
					},1500);
				}else{
					$.toast(data.info);
					return false;
				}
			}
			,error:function(){
				$.toast("服务器提交错误");
				return false;
			}
		});
		return false;
	});
});