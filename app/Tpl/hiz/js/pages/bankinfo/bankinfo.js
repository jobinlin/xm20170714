$(document).ready(function(){

	//验证码刷新
	$(".img_verify_box img.verify").live("click",function(){
		$(this).attr("src",$(this).attr("rel")+"?"+Math.random());
		$(".form_tip").html("");
	});
	$(".img_verify_box .refresh_verify").live("click",function(){
		var img = $(this).parent().find("img.verify");
		$(img).attr("src",$(img).attr("rel")+"?"+Math.random());
		$(".form_tip").html("");
	});

	
	
	$("#withdraw form").bind("submit",function(){

		var bank_num = $("#withdraw form").find("input[name='bank_num']").val();
		var bank_name = $("#withdraw form").find("input[name='bank_name']").val();
		var bank_user = $("#withdraw form").find("input[name='bank_user']").val();
		if($.trim(bank_num)==""||isNaN(bank_num))
		{
			$.showErr("请输入正确的银行账号");
			return false;
		}
		if($.trim(bank_name)=="")
		{
			$.showErr("请输入银行名称");
			return false;
		}
		if($.trim(bank_user)=="")
		{
			$.showErr("请输入开户人姓名");
			return false;
		}
		
		
		var ajax_url = $("#withdraw form").attr("action");
		var query = $("#withdraw form").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){

				if(obj.status==1)
				{
					$.showSuccess(obj.info,function(){
						location.reload();
					});
				}
				else
				{
					$.showErr(obj.info,function(){
						if(obj.jump)
						{
							location.href = obj.jump;
						}
					});
				}
			}
		});
		
		
		return false;
	});
//发短信的按钮事件

	$(".ph_verify_btn").bind("click",function(){	
		if($(this).attr("rel")=="disabled")return false;

		var form = $("form[name='withdraw_form']");				
		var btn = $(this);
		var query = new Object();
		query.act = "biz_sms_code";
		var mobile = $(form).find("input[name='mobile']").val();
		query.mobile=mobile;
		query.verify_code = $(form).find("input[name='verify_code']").val();
		
		query.sms_verify = $.trim($(form).find("input[name='sms_verify']").val());
		
		//发送手机验证登录的验证码
		$.ajax({
    		url:SMS_URL,
    		dataType: "json",
    		data:query,
            type:"POST",
            global:false,
    		success:function(data) {
    		    if(data.status) {
    		    	init_sms_code_btn(btn,data.lesstime);
    		    	IS_RUN_CRON = true;
    		    	$(form).find("img.verify").click();
					
					$(btn).parents(".sms_verify_box").find(".form_tip").show().text("短信已发送至您的绑定手机 "+String(mobile).substr(0,3)+" **** "+String(mobile).substr(7,4));
    		    } else {
					$(form).find("img.verify").click();
    		    	if(data.field)
    		    	{
    		    		form_err($(form).find("input[name='"+data.field+"']"),data.info);
    		    	}
    		    	else
    		    	{
						$.showErr(data.info);
					}
    		    }
    		}
    	});
	});

	
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
	



});