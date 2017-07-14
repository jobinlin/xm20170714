$(document).ready(function(){
	
});
//关闭订单
function close_order(id)
{
	var query = new Object();
	query.ctl = "deal_order";
	query.act = "cancel";
	query.data_id = id;
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
}