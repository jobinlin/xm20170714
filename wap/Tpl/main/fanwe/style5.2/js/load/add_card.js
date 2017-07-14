	time($("#uc_sms_btn"));
	/*$("#uc_sms_btn").bind("click",function(){
		do_send($("#uc_sms_btn"));
	});*/
	var lesstime = 0;
	
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
					$.toast("添加成功");	
					if(obj.url){
						setTimeout(function(){
							window.location.reload();
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
						else{
							setTimeout(function(){
								window.location.reload();
							},3000); 
						}
					}
					else
					{
						if(obj.url)location.href = obj.url;
					}
					
				}
				else{
					
				}
			}
		});		
		return false;
	});
	
	if ($('#phonenumer').val() == '') {
		$("#btn").addClass("noUseful").removeClass("isUseful");
	}
	
	/*手机号码正则验证*/
	
	if($("#phonenumer").length>0){
	    document.getElementById("phonenumer").oninput=function () {
	    	if(parseInt($("#btn").attr("lesstime"))==0){
	    		var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
	
	            var text=$(this).val();
	            if(reg.test(text)){
	                $(".j-sendBtn").addClass("isUseful").removeClass("noUseful");
	                $(".j-sendBtn").prop("disabled",false);
	                /*发送验证码倒计时*/
	                $(".j-sendBtn .isUseful").click(function () {
	                	do_send($("#btn"));
	                });
	            }
	            else {
	                $(".j-sendBtn").addClass("noUseful").removeClass("isUseful");
	                $(".j-sendBtn").prop("disabled", true);
	            }
	    	} 
	    };
	}
	//$("#btn").bind("click",function(){
		//alert("111");
	//	do_send($("#btn"));
	//});
	 
	$("#verify_image_box").find(".verify_close_btn").bind("click",function(){
        $("#verify_image_box").hide();
    });

	//关于短信验证码倒计时
	function init_sms_btn(btn)
	{
		// $(btn).stopTime();
		$(btn).everyTime(1000,function(){
			lesstime = parseInt($(btn).attr("lesstime"));
			lesstime--;
			$(btn).val("重新获取("+lesstime+")");
			$(btn).attr("lesstime",lesstime);
			 
			if(lesstime<=0 || $(btn).attr("lesstime") == 'NaN')
			{
				// $(btn).stopTime();
				$(btn).val("获取验证码");
			}
		});

	}

	function time(obj) {
		wait=parseInt(obj.attr("lesstime"));
	    if (wait == 0) {
	        obj.prop("disabled",false);
	        obj.addClass("isUseful").removeClass("noUseful");
	        obj.val("发送验证码");
	        obj.attr("lesstime",0);
	        $(".j-sendBtn.isUseful").click(function () {
	        	do_send($("#uc_sms_btn"));
	        });
	        $("#uc_sms_btn").attr("verify_code","");
	        //wait = 60;
	    } else {
	        obj.prop("disabled", true);
	        obj.addClass("noUseful").removeClass("isUseful");
	        obj.val("重新发送(" + (wait-1) + ")");
	        obj.attr("lesstime",wait-1);
	        //wait--;
	        setTimeout(function() {
	                time(obj)
	            }, 1000);
	    }
	}
	


	function do_send(btn)
	{
	   var account = $(btn).attr("account");
	   
		if($.trim($("#phonenumer").val())=="" && account!=1)
		{
			$.toast("请输入手机号码");
			return false;
		}
		if(lesstime>0)return;
		var query = new Object();
		query.mobile = $("#phonenumer").val();
		query.act = "send_sms_code";
		query.unique = $(btn).attr("unique");
	    query.account = account;
		query.verify_code = (btn).attr("verify_code");
		$.ajax({
			url:AJAX_URL,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status==1)
				{
					$(btn).attr("lesstime",obj.lesstime);
					time(btn);
					$.toast(obj.info);
					
				}
				else
				{
					if(obj.status==-1)
					{
						console.log(obj);
						$("#verify_image_box .verify_form_box .verify_content").html("");
	                    var html_str = '<div class="v_input_box"><input type="text" class="v_txt" placeholder="图形码" id="verify_image"/><img src="'+obj.verify_image+"&r="+Math.random()+'"  /></div>'+
	                                    '<div class="blank"></div><div class="blank"></div>'+
	                                    '<div class="v_btn_box"><input type="button" class="v_btn" name="confirm_btn" value="确认"/></div>';
	                    $("#verify_image_box .verify_form_box .verify_content").html(html_str);
	                    $(".page-current #verify_image_box").show();
						
						$("#verify_image_box").find("img").bind("click",function(){
							$(this).attr("src",obj.verify_image+"&r="+Math.random());
						});
						$("#verify_image_box").find("input[name='confirm_btn']").bind("click",function(){
							var verify_code = $.trim($("#verify_image_box").find("#verify_image").val());
							if(verify_code=="")
							{
								$.toast("请输入图形验证码");
							}
							else
							{
								$(btn).attr("verify_code",verify_code);
								$("#verify_image_box .verify_form_box .verify_content").html("");
						                                    $("#verify_image_box").hide();
						                                    do_send(btn);
						                                    
							}
						});
						if($(btn).attr("verify_code")&&$(btn).attr("verify_code")!="")
						{
							$.toast(obj.info,function(){
								$(btn).attr("verify_code","")
							});
						}
					}
					else
					{
						$.toast(obj.info);
					}
					
				}
			}
		});
	}