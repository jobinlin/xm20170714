$(document).on("pageInit", "#dist_order_view", function(e, pageId, $page) {
	$(".do_delivery").bind("click",function(){
		var action=$(this).attr("action");
		$.confirm('确认发货吗？', function () {
			$.ajax({
				url:action,
				dataType:"json",
				type:"POST",
				success:function(obj){
					console.log(obj);
					if(obj.status==1){
						$.toast("发货成功");
						$(".logistics-code").val('');
						$("#remark").val('');
						$(".j-goods-item").find('input').attr("checked",false);
						if(obj.jump){
							setTimeout(function(){
								location.reload();
							},1500);
						}
					}else if(obj.status==0){
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
				}
			});
		});
	});
});