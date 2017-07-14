$(document).ready(function() {
	$(".open-btn").bind('click', function() {
		if ($(this).hasClass('active')) {
			var is_open = 0;
		} else {
			var is_open = 1;
		}
		delivery_type='dada';
		setting_delivery(delivery_type,is_open,$(this));
		
	});
	$(".j-charge").bind('click', function() {
		$(".charge-switch span").removeClass('active');
		$(this).addClass('active');
		$(".charge").show();
		$(".withdraw").hide();
	});
	$(".j-withdraw").bind('click', function() {
		$(".charge-switch span").removeClass('active');
		$(this).addClass('active');
		$(".withdraw").show();
		$(".charge").hide();
	});
	//AlipayBank
	$("input[name='payment_id']").bind('click',function(){
		var class_name = $(this).attr('class_name');
		if(class_name=='AlipayBank'){
			$(".pay_box").show();
		}else{
			$(".pay_box").hide();
		}
	});
	
	


	$(".refresh_verify").each(function(k,text){
		if(!$(text).attr("bindclick"))
		{			
			$(text).attr("bindclick",true);
			$(text).bind("click",function(){	
				var img = $(text).parent().find("img.verify");
				$(img).attr("src",$(img).attr("rel")+"?"+Math.random());				
			});
		}
	});
	

	$(".ph_verify_btn").bind("click",function(){
		var btn = $(this).siblings("div.ph_verify_btn");
		if($(btn).attr("rel")=="disabled")return false;			
		
		var query = new Object();
		query.act = "send_sms_code";
		var mobile = $(this).attr("mobile");
		query.mobile = $.trim(mobile);
		var verify_code = $(this).parents(".form-wrap ").prev().find( $("input[name='verify_code']")).val();
		query.verify_code = verify_code;

		if(verify_code==''){
			$.showErr('请输入图形验证码');
			return false;
		}
		//发送手机验证登录的验证码
		$.ajax({
    		url:AJAX_URL,
    		dataType: "json",
    		data:query,
            type:"POST",
            global:false,
    		success:function(data)
    		{
    		    if(data.status)
    		    {
    		    	init_sms_code_btn(btn,data.lesstime);
    		    	$(btn).siblings(".form-tip").html('已发送至绑定手机'+mobile);
    		    }
    		    else
    		    {
    		    	$.showErr(data.info);
    		    	var img = $(btn).parents(".form-wrap ").prev().find(".verify");
    		    	$(img).attr("src",$(img).attr("rel")+"?"+Math.random());	

    		    }
    		}
    	});
	});

	$("form[name='charge']").submit(function(){
		
		var money = $(this).find("input[name='money']").val();
		var verify_code = $(this).find("input[name='verify_code']").val();
		var sms_verify = $(this).find("input[name='sms_verify']").val();
		if(money==''){
			$.showErr('请输入充值金额');
			return false;
		}
		if(verify_code==''){
			$.showErr('请输入图片验证码');
			return false;
		}
		if(sms_verify==''){
			$.showErr('请输入验证码');
			return false;
		}
		
		var payment_id=$(this).find("input[name='payment_id']:checked").val();
		var class_name = $(this).find("input[name='payment_id']:checked").attr('class_name');
		var bank_id = $(this).find("input[name='bank_id']").val();
		if(class_name=='AlipayBank' && bank_id==''){
			$.showErr('请选择银行');
			return false;
		}
		
		query= $(this).serialize();
		url =$(this).attr('action');
		//发送手机验证登录的验证码
		$.ajax({
			url:url,
			dataType: "json",
			data:query,
	        type:"POST",
	        global:false,
			success:function(data)
			{
			    if(data.status==1)
			    {
			    	
			    	$.showSuccess(data.info,function(){
			    		location.href=data.jump;
			    	});

			    }else if(data.status==2){
			    	//var pay_url = $(data.html).find("form").attr("action");
			    	$.weeboxs.open(data.html, {boxid:'pay_order_tip',contentType:'text',showButton:false, showCancel:true, showOk:true,title:'前往支付',width:500,type:'wee',height:110,onopen:function(){
						init_ui_button();
						init_ui_radiobox();
						init_ui_textbox();	
						check_order_status(data.order_id);
						},onok:function(){
							
						}});	
			    	
			    }
			    else
			    {
			    	$.showErr(data.info);

			    }
			}
		});
		return false;
		
	});
	
	

	$("form[name='withdraw']").submit(function(){
		
		var money = $(this).find("input[name='money']").val();
		var verify_code = $(this).find("input[name='verify_code']").val();
		var sms_verify = $(this).find("input[name='sms_verify']").val();
		if(money==''){
			$.showErr('请输入提现金额');
			return false;
		}
		if(verify_code==''){
			$.showErr('请输入图片验证码');
			return false;
		}
		if(sms_verify==''){
			$.showErr('请输入验证码');
			return false;
		}
				
		query= $(this).serialize();
		url =$(this).attr('action');

		//发送手机验证登录的验证码
		$.ajax({
			url:url,
			dataType: "json",
			data:query,
	        type:"POST",
	        global:false,
			success:function(data)
			{
			    if(data.status==1)
			    {
			    	
			    	$.showSuccess(data.info,function(){
			    		location.reload();
			    	});

			    }
			    else
			    {
			    	$.showErr(data.info);

			    }
			}
		});
		return false;
		
	});
	

	
});

function check_order_status(order_id){
	var CheckOrderObj = setInterval(function(){
		var query = new Object();
		query.act = "check_order_status";
		query.order_id = order_id;
		$.ajax({
			url:SETTING_URL,
			dataType: "json",
			data:query,
	        type:"POST",
	        global:false,
			success:function(data)
			{
			    if(data.status)			    		   
			    {	
			    	$.weeboxs.close("pay_order_tip");
			    	$.showSuccess(data.info,function(){
			    		location.reload();
			    	});
			    	clearInterval(CheckOrderObj);
			    }
			}
		});
	},1000);
}


function setting_delivery(delivery_type,is_open,btn){
	var query = new Object();
	query.delivery_type = delivery_type;
	query.is_open = is_open;
	query.act = 'setting_delivery';
	$.ajax({
		url:SETTING_URL,
		dataType: "json",
		data:query,
	    type:"POST",
		success:function(data)
		{
			if(data.status){
				
				if(is_open==1){
					$(btn).addClass('active');
				}else{
					$(btn).removeClass('active');
				}
				$(btn).find('input').attr('value', is_open);
			}
			

		}
	});
}

