$(document).ready(function(){
	$(".check_delivery[ajax='true']").bind("click",function(){
		return false;
	});
	$(".check_delivery[ajax='true']").hover(function(){
		var id = "delivery_box_"+$(this).attr("rel");
		$("#"+id).stopTime();
		var dom = $(this);
		if($("#"+id).length>0)
		{
			$("#"+id).show();
		}
		else
		{
			var html = "<div id='"+id+"' class='check_delivery_pop'><div class='loading'></div></div>";
			var box = $(html);
			$("body").append(box);
			$(box).css({"position":"absolute","left":$(dom).position().left-80,"top":$(dom).position().top+20,"z-index":10});
			$.ajax({
				url:$(dom).attr("action"),
				type:"POST",
				dataType:"json",
				success:function(obj){
					if(obj.status)
					{
						$(box).html(obj.html);
					}
					else
					{
						$(box).remove();
					}
				}
			});
		}
		$("#"+id).hover(function(){
			$("#"+id).stopTime();
			$("#"+id).show();
		},function(){
			$("#"+id).oneTime(300,function(){
				$("#"+id).remove();
			});
		});
	},function(){
		var id = "delivery_box_"+$(this).attr("rel");
		if($("#"+id).length>0)
		{
			$("#"+id).oneTime(300,function(){
				$("#"+id).remove();
			});
		}
		
	});

	// 发票弹窗
	$(".invoice-info").bind('click', function() {
		var ivop = $('.ivop').html();
		var ivoc = $('.ivoc').html();
		var ivot = $('.ivot').html();
		var html = '<div class="info_table"><table class="form_teble_box"><tr><td>发票类型</td><td>普通发票</td></tr><tr><td>发票抬头</td><td>'+ivop+'</td></tr>';
		if (ivot) {
			html += '<tr><td>纳税人识别码</td><td>'+ivot+'</td></tr>';
		}
		html += '<tr><td>发票内容</td><td>'+ivoc+'</td></tr></table></div>';
		$.weeboxs.open(html, {
			boxid: 'invoice_form',
			contentType: 'text',
			showButton: false,
			showCancel: false,
			showOk: false,
			title: '发票信息',
			width:500,
			height:200,
			type: 'wee',
		})
	});
	
	// 发货弹出框事件
	$("div.do_delivery").bind("click",function(e){
		var query = new Object();
		query.act = 'load_delivery_form';
		query.id = $(this).parent('div').attr('data-id');
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

	// 详情弹出框事件
	$("div.order_detail").bind("click",function(e){
		var query = new Object();
		query.act = 'load_order_detail';
		query.id = $(this).parent('div').attr('data-id');
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
					$.weeboxs.open(obj.html, {boxid:'order_detail',contentType:'text',showButton:false, showCancel:false, showOk:false,title:'订单详情',width:800,height:550,type:'wee',onopen:function(){
						init_ui_button();
						init_ui_select();
						init_ui_textbox();
						do_recive();
					}});
				}
			}
		});
		e.stopPropagation();
	});
	
	// 强制收货
	function do_recive() {
		$(".do_verify_delivery").bind("click",function(){
			var query = new Object();
			query.act = "do_verify_delivery";
			var params = $(this).attr("rel");
			query.param = params;
			$.ajax({
				url:AJAX_URL,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1000)
					{
						location.reload();
					}
					else if(obj.status ==0)
					{
						$.showErr(obj.info);
					}
					else
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
				}
			});
		});
	}
	
	
	
	$(".do_refund_coupon").bind("click",function(){
		var id = $(this).attr("rel");
		$.showConfirm("确定同意退款操作吗？",function(){
			var query = new Object();
			query.act = "do_refund_coupon";
			query.id = id;
			$.ajax({
				url:AJAX_URL,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1000)
					{
						location.reload();
					}
					else if(obj.status ==0)
					{
						$.showErr(obj.info);
					}
					else
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
				}
			});
		});
	});
	
	$(".do_refuse_coupon").bind("click",function(){
		var id = $(this).attr("rel");
		$.showConfirm("确定拒绝退款操作吗？",function(){
			var query = new Object();
			query.act = "do_refuse_coupon";
			query.id = id;
			$.ajax({
				url:AJAX_URL,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1000)
					{
						location.reload();
					}
					else if(obj.status ==0)
					{
						$.showErr(obj.info);
					}
					else
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
				}
			});
		});
	});
	
	/*$(".do_refund_item").bind("click",function(){
		var id = $(this).attr("rel");
		$.showConfirm("确定同意退款操作吗？",function(){
			var query = new Object();
			query.act = "do_refund_item";
			query.id = id;
			$.ajax({
				url:AJAX_URL,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1000)
					{
						location.reload();
					}
					else if(obj.status ==0)
					{
						$.showErr(obj.info);
					}
					else
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
				}
			});
		});
	});
	
	$(".do_refuse_item").bind("click",function(){
		var id = $(this).attr("rel");
		$.showConfirm("确定拒绝退款操作吗？",function(){
			var query = new Object();
			query.act = "do_refuse_item";
			query.id = id;
			$.ajax({
				url:AJAX_URL,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1000)
					{
						location.reload();
					}
					else if(obj.status ==0)
					{
						$.showErr(obj.info);
					}
					else
					{
						$.showSuccess(obj.info,function(){
							location.reload();
						});
					}
				}
			});
		});
	});*/

	var refuse_item = function(id) {
		$("button.do_refuse_item").bind("click",function(){
			$.showConfirm("确定拒绝退款操作吗？",function(){
				var query = new Object();
				query.act = "do_refuse_item";
				query.id = id;
				$.ajax({
					url:AJAX_URL,
					data:query,
					dataType:"json",
					type:"POST",
					success:function(obj){
						if(obj.status==1000)
						{
							location.reload();
						}
						else if(obj.status ==0)
						{
							$.showErr(obj.info);
						}
						else
						{
							$.showSuccess(obj.info,function(){
								location.reload();
							});
						}
					}
				});
			});
		});
	};

	var refund_item = function(id) {
		$("button.do_refund_item").bind("click",function(){
			$.showConfirm("确定同意退款操作吗？",function(){
				var query = new Object();
				query.act = "do_refund_item";
				query.id = id;
				var money = $('input[name="refund_money"]').val();
				if (money <= 0) {
					money = 0;
				} else {
					var maxm = $('input[name="max_money"]').val();
					if (Number(money) > Number(maxm)) {
						$.showErr('退款金额不能超出实付金额');
						return false;
					}
				}
				query.refund_money = money;
				query.memo = $('textarea[name="memo"]').val();
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
							$.showSuccess(obj.info,function(){
								location.reload();
							});
						}
					}
				});
			});
		});
	};
	
	$('.refund_handle').bind('click', function() {
		// 获取信息
		var data_id = $(this).attr('rel');
		var query = {
			data_id: data_id,
			act: 'refund_init',
		};
		// 验证数据有效性
		$.ajax({
			url: AJAX_URL,
			data: query,
			type: 'get',
			dataType: 'json',
			success: function(result) {
				if (result.status == 1) {
					$.weeboxs.open(
						result.html,
						{boxid: '', contentType: 'text', showButton: false, title: '退款处理', width: 500, type: 'wee', onopen: function() {
							init_ui_button();
							init_ui_textbox();
							refuse_item(data_id);
							refund_item(data_id);
						}}
					)
				} else if(result.status==1000) {
					location.reload();
				} else {
					$.showErr(result.info);
				}
			}
		})
	})
});