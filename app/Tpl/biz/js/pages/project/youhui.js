$(function(){	
	/*页面初始化调用*/
	if(youhui_type==1){
		$("#location").show();
	}
	else if(youhui_type==2){
		$("#location").hide();
	}
	
	if(valid_type==2){
		$(".use_day").hide();
		$(".use_time").show();
	}
	else if(valid_type==1){
		$(".use_day").show();
		$(".use_time").hide();
	}
	
	//15个字数的限制
	$("input[name='name']").bind("keyup change",function(){
		if($(this).val().length>15)
		{
			$(this).val($(this).val().substr(0,15));
		}		
	});
	
	$("input[name='youhui_value']").bind("keyup change",function(){
		if(parseInt($(this).val())<=0)
		{
			$(this).val('1');
		}	
	});
	
	$(".natural_number").bind("keyup change",function(){
		if(parseInt($(this).val())<0)
		{
			$(this).val('0');
		}	
	});
	
	$(".natural_number").bind("blur",function(){
		if(parseInt($(this).val())<0)
		{
			$(this).val('0');
		}	
	});
	
	/*日期控件*/
	$("input[name='begin_time']").datetimepicker({format: "Y-m-d H:i"});
	$("input[name='end_time']").datetimepicker({format: "Y-m-d H:i"});	
	
	$("input[name='use_begin_time']").datetimepicker({format: "Y-m-d H:i"});
	$("input[name='use_end_time']").datetimepicker({format: "Y-m-d H:i"});	
	
	$(".youhui_type").bind("click",function(){
		var val=$("input[name='youhui_type']:checked ").val();
		
		if(val==1){
			$("#location").show();
		}
		else if(val==2){
			$("#location").hide();
			$(".location_id_item").prop("checked",false);
			$(".location_item").removeClass("common_cbo_checked").addClass('common_cbo');
		}
	});
	
	$(".valid_type").bind("click",function(){
		var val=$("input[name='valid_type']:checked ").val();
		
		if(val==2){
			$(".use_day").hide();
			$(".use_time").show();
			$("input[name='expire_day']").val('');
		}
		else if(val==1){
			$(".use_day").show();
			$(".use_time").hide();
			$("input[name='use_begin_time']").val('');
			$("input[name='use_end_time']").val('');
		}
	});

	
	/*发布*/
	$("form[name='youhui_publish_form']").submit(function(){
		
		var form = $("form[name='youhui_publish_form']");
		if(check_form_submit()){
			$(".sub_from_btn").html('<button class="ui-button" rel="disabled" type="button">提交</button>');
			init_ui_button();
			var query = $(form).serialize();
			var url = $(form).attr("action");
			$.ajax({
				url:url,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					if(data.status == 0){
						$(".sub_from_btn").html('<button class="ui-button" rel="orange" type="submit">确认提交</button>');
						init_ui_button();
						$.showErr(data.info,function(){
							if(data.jump&&data.jump!="")
							{
								location.href = data.jump;
							}
						});
					}else if(data.status==1){
						$.showSuccess(data.info,function(){window.location = data.jump;});
					}
					return false;
				}
			});
		}
		return false;
	});	


/*JQUERY END*/
});

/*表单提交验证*/
function check_form_submit(){
	
	//团购名称
	if($.trim($("input[name='name']").val())==''){
		$.showErr("请输入优惠券名称",function(){$("input[name='name']").focus();});
		return false;
	}
	
	if(parseInt($("input[name='youhui_value']").val())<=0 || parseInt($("input[name='youhui_value']").val())>999){
		$.showErr("请输入符合要求的面额",function(){$("input[name='name']").focus();});
		return false;
	}
	
	if($("input[name='youhui_type']:checked ")==1 && $(".location_item:checked").length==2){
		$.showErr("实体券必须选择门店",function(){$("#location").focus();});
		return false;
	}
	
	return true;
	
}


