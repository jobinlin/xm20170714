$(document).ready(function() {
	function auto_height() {
		if ($("body").width()>1300) {
			var m_height=$(".view-md").height();
			$(".view-md .main-right .info-detail").css('height', m_height-10);
			$(".view-md .main-left .info-detail").css('height', m_height-20);
		}else{
			$(".view-md .info-detail").css('height', 'auto');
		}

		if ($("body").width()<=1400) {
			var h_height=$(".view-hd .main-left").height();
			$(".view-hd .main-left .info-detail").css('height', h_height-25);
			$(".pay-info .info-detail").css('height', 'auto');
		}
		else{
			var h_height=$(".view-hd").height();
			$(".view-hd .info-detail").css('height', h_height-25);
		}
		if($(".view-hd .main-right").length==0){
			$(".view-hd .main-left").css('width','100%');
		}
	}
	auto_height();
	window.onresize=function(){
	  auto_height();
	};
	$(".dis-btn").bind('click', function() {
		$(".mask").addClass('active');
		$(".dis-select").show();
		setTimeout('$(".dis-select").addClass("active")',100);
		var action=$(this).attr('data-href');
		$('.dist_search').unbind("click").bind('click', function() {
			var sKey = $('input[name="dist_skey"]').val();
			sKey = $.trim(sKey);
			if (!sKey) {
				$.showSuccess("请输入搜索关键字");
				return false;
			}
			var query = {'ctl': 'deal_order','act': 'keySearch','key': sKey, 'ajax': 1};
			$.ajax({
				url: AJAX_URL,
				type: "POST",
				data: query,
				dataType: "json",
				success: function(obj) {
					if (obj.status) {
						$('#dist_select').html(obj.html);
						init_ui_select();
					} else {
						$.showSuccess(obj.info);
					}
				}
			});
			return false;
		});
		//分配驿站
		$('.dist_submit').unbind("click").bind('click', function() {
			// 确定按钮事件
			var did = $('select[name="dist_result"]').val();
			if (did == 0) {
				$.showSuccess("请分配一个驿站");
				return false;
			}
			var query = {'did': did, 'ajax': 1};
			$.ajax({
				url: action,
				type: 'POST',
				data: query,
				dataType: 'json',
				success: function(obj) {
					if (obj.status) {
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					} else {
						$.showSuccess(obj.info);
					}
				}
			})
		});
	});
	$(".j-close-mask").bind('click', function() {
		$(".dis-select").removeClass("active");
		setTimeout('$(".mask").removeClass("active")',250);
		setTimeout('$(".dis-select").hide()',250);
	});
	$(".do_refund").bind("click",function(){		
		var action = $(this).attr("data-href");
		var query = new Object();
		query.ajax = 1;
		$.ajax({
			url:action,
			type:"POST",
			data:query,
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.weeboxs.open(obj.html, {boxid:"refund_form",contentType:'text',showButton:false,title:"退款处理",width:530,onopen:function(){
						
						var form = $("#refund_form").find("form[name='refund_form']");
						
						$("#confirm").bind("click",function(){
							var query = $(form).serialize();
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
						$("#refuse").bind("click",function(){
							var query = $(form).serialize();
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
					}});
				}
				else
				{
					alert(obj.info);
				}
				
			}
		});
	});
	$(".do_verify").bind("click",function(){	
		var obj1=this;
		$.showConfirm("确认该项操作吗？",function(){
			var action = $(obj1).attr("data-href");
			var query = new Object();
			query.ajax = 1;
			$.ajax({
				url:action,
				type:"POST",
				data:query,
				dataType:"json",
				success:function(obj){
					if(obj.status)
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
					else
					{
						$.showSuccess(obj.info);
					}
					
				}
			});
		});
	});
	// 发货弹出框事件
	$(".delivery").bind("click",function(e){
		var query = new Object();
		query.ctl = 'deal_order';
		query.act = 'load_delivery_form';
		query.id = $(this).attr('data-id');
		$.ajax({
			url:AJAX_URL,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1000) {
					location.reload();
				} else if(obj.status ==0) {
					$.showErr(obj.info);
				} else {
					$.weeboxs.open(obj.html, {boxid:'delivery_form',contentType:'text',showButton:false, showCancel:false, showOk:false,title:'发货',width:800,height:550,type:'wee',onopen:function(){
						init_ui_button();
						init_ui_select();
						init_ui_textbox();
						var lock = false;
						$("form[name='delivery_form']").bind("submit",function(){
							if (!lock) {
								lock = true;
								var url = $(this).attr("action");
								var query = $(this).serialize();
								$.ajax({
									url:url,
									data:query,
									dataType:"json",
									type:"POST",
									success:function(obj) {
										if(obj.status) {
											$.showSuccess(obj.info,function(){
												$.weeboxs.close("delivery_form");
												location.reload();
											});
										} else {
											$.showErr(obj.info,function() {
												if(obj.jump) {
													location.href = obj.jump;
												}
											});
										}
									}
								});
								lock = false;
							}
							return false;
						});
						
					}});
				}
			}
		});
		e.stopPropagation();
	});
});
function ajax_do_submit(action,query)
{
	$.ajax({
		url:action,
		data:query,
		dataType:"json",
		type:"POST",
		success:function(obj){
			if(obj.status)
			{					
				$.weeboxs.close("refund_form");
				$.showSuccess(obj.info,function(){
					location.reload();
				});
			}
			else
			{
				$.showSuccess(obj.info);
			}
		}
	});
}