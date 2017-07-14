$(document).ready(function(){
	
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
	
});