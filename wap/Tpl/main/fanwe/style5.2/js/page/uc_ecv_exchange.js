$(document).on("pageInit", "#uc_ecv_exchange", function(e, pageId, $page) {
	$(".j-exchange-input").on("change keyup",function(){
		if($(this).val()){
			$(".j-exchange-btn").removeClass('disable');
		}else{
			$(".j-exchange-btn").addClass('disable');
		}
	});
	
	$(".j-exchange-btn").bind("click",function(){
		if(!$(this).hasClass('disable')){
			$("form[name='exchange_form']").submit();
		}
	});
	
	$("form[name='exchange_form']").bind('submit',function(){

		var sn = $(".j-exchange-input").val();
		if(sn==""){
			$.toast("口令不能为空");
			return false;
		}else{
			var form = $("form[name='exchange_form']");
			var url=$(form).attr('action');
			var query = new Object();
			query.sn = sn;
			$.ajax({
				url:url,
				data:query,
				type:'post',
				dataType:'json',
				success:function(obj){
					if(obj.status==1){
						console.log(obj.info);
						$.toast(obj.info);
						setTimeout(function(){
							window.location.reload();
						},1000); 
					}else{
						$.toast(obj.info);
						/*$("input[name='sn']").val("");*/
						if(obj.jump){
							setTimeout(function(){
								window.location.href=obj.jump;
							},1000);
						}
					}
					return false;
				}
			});
		}
		return false;
	});

	
	/*兑换红包功能*/	
	$(".j-receive").on('click',function(){
		var id = $(this).attr('data-id');
		
		var query = new Object();
		query.id = id;
		query.act = 'do_exchange';
		$.ajax({
			url:ajax_url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(obj){
				console.log(obj);
				if(obj.status==1){
					$.toast(obj.info);
					setTimeout(function(){
						window.location.reload();
					},1000); 
				}else{
					$.toast(obj.info);
				}
				return false;
			}
		});

	});
});