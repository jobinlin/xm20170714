$(document).ready(function(){
	init_getpassword_panel();
	
	$('.ui-textbox').bind('blur',function(){
		var val = $(this).val();
		var tit = $(this).attr('data');
		if(tit=='图片验证码'){
			
		}
		else if(val.length == 0){
			form_tip($(this),tit + '不能为空');
			return false;
		}else{
			form_tip_clear($(this));
			return false;
		}
	});
	$('#p_submit').click(function(){
		var is_check = true;
		$('.ui-textbox').each(function(index) {
			if($(this).val().length == 0){
				is_error = 1;	
				error_msg = "请输入"+$(this).attr('data');
				//$.showErr("请输入"+$(this).attr('data'));
				if(is_error == 1){
					$(".msg_tip .msg_content").html(error_msg);
					$(".msg_tip").addClass("sysmsg_error");
					$(".msg_tip .status").addClass("s_error");
					$(".msg_tip").show();
				}
				is_check = false;
				return false;
			}
		});
		if(is_check){
			//$('#getpassword_form').submit();
			if($.trim($("#verify_code").val())=="")
			{
				is_error = 1;	
				error_msg = "请输入验证码";
				if(is_error == 1){
					$(".msg_tip .msg_content").html(error_msg);
					$(".msg_tip").addClass("sysmsg_error");
					$(".msg_tip .status").addClass("s_error");
					$(".msg_tip").show();
				}
				//$.showErr("请输入验证码");
				return false;
			}
			var url = $("#getpassword_form").attr("action");
			var query = $("#getpassword_form").serialize();
			//alert(query);return false;
			$.ajax({
				url: url,
				type: "POST",
				data:query,
				dataType: "json",
				success: function(obj){
					if(obj.status)
	    		    {
						$.showSuccess(obj.info,function(){
							location.href = obj.jump;		
						});	
						//$.showSuccess("修改成功");
	    		    }
	    		    else
	    		    {
	    		    	$.showErr(obj.info);
	    		    }
				}
			});
			
			return false;
		}
	});
});

/*初始化取回密码表单*/
function init_getpassword_panel()
{	
	
	//验证码刷新
	$("#getpassword_form img.verify").live("click",function(){
		$(this).attr("src",$(this).attr("rel")+"?"+Math.random());
	});
	$("#getpassword_form .refresh_verify").live("click",function(){
		var img = $(this).parent().find("img.verify");
		$(img).attr("src",$(img).attr("rel")+"?"+Math.random());
	});
	
	
	$("#getpassword_form").each(function(k,getpassword_form){
		if(!$(getpassword_form).find("input[name='verify_code']").attr("bindblur"))
		{
			$(getpassword_form).find("input[name='verify_code']").attr("bindblur",true);
			$(getpassword_form).find("input[name='verify_code']").bind("blur",function(){
				var txt = $(this).val();
				var ipt = $(this);
				if($.trim(txt)=="")
				{
					
				}
				else
				{
					//验证图片验证码
					ajax_check_field("verify_code",txt,0,ipt);
				}
			});
		}
	});
	
	
	var allow_ajax_check = true;
	function ajax_check_field(field,value,user_id,ipt)
	{
		if(!allow_ajax_check)return;
		var query = new Object();
		query.act = "check_field";
		query.field = field;
		query.value = value;
		query.user_id = user_id;
		$.ajax({
			url:AJAX_URL,
			dataType: "json",
			data:query,
	        type:"POST",
	        global:false,
			success:function(data)
			{
			    if(!data.status)			    		   
			    {
			    	if(data.field)
			    	{
			    		form_err(ipt,data.info);
			    	}
			    	else{
			    		$.showErr(data.info);
			    	}
			    	
			    }
			    else
			    {
			    	form_success(ipt,data.info);
			    }
			}
		});
	}
	
	//发短信的按钮事件
	init_sms_btn();

	$(".ph_verify_btn").bind("click",function(){	
		if($(this).attr("rel")=="disabled")return false;

		var form = $("form[name='getpassword']");				
		var btn = $(this);
		var query = new Object();
		query.act = "send_sms_code";
		var mobile = $(form).find("input[name='user_mobile']").val();

		if($.trim(mobile)=="")
		{
			form_tip($(form).find("input[name='user_mobile']"),"请输入手机号");
			return false;
		}
		if(!$.checkMobilePhone(mobile))
		{
			form_err($(form).find("input[name='user_mobile']"),"手机号格式不正确");
			return false;
		}
		query.mobile = $.trim(mobile);
		query.verify_code = $.trim($(form).find("input[name='verify_code']").val());
		//发送手机验证登录的验证码
		$.ajax({
    		url:AJAX_URL,
    		dataType: "json",
    		data:query,
            type:"POST",
            global:false,
    		success:function(data) {
    		    if(data.status) {
    		    	init_sms_code_btn(btn,data.lesstime);
    		    	IS_RUN_CRON = true;
    		    	//$(form).find("img.verify").click();
    		    } else {
    		    	if(data.field)
    		    	{
    		    		form_err($(form).find("input[name='"+data.field+"']"),data.info);
    		    		$(".refresh_verify").trigger("click");
    		    	}
    		    	else{
    		    		$.showErr(data.info);
    		    	}
    		    	
    		    }
    		}
    	});
	});
		
}

function form_err(ipt,txt){
	$(ipt).parent().parent().find(".form_tip").html("<span class='error'>"+txt+"</span>");
}
function form_success(ipt,txt){
	if(txt!="")
	$(ipt).parent().parent().find(".form_tip").html("<span class='success'>"+txt+"</span>");
	else
		$(ipt).parent().parent().find(".form_tip").html("<span class='success'>&nbsp;</span>");
}
function form_tip(ipt,txt){
   try{
       $(ipt).parent().parent().find(".form_tip").html("<span class='tip'>"+txt+"</span>");
   }catch(e){
       console.log(e);
   }

}
function form_tip_clear(ipt)
{
	$(ipt).parent().parent().find(".form_tip").html("");
}

