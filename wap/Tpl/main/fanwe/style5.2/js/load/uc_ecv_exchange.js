
	$(".j-exchange-btn").on("click",function(){

		$("form[name='exchange_form']").submit();

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
	$(".j-exchange").on('click',function(){
		var score = $(this).attr('score-data');
		var id = $(this).attr('data-id');
		//alert(ajax_url);return false; 
		$.confirm("兑换将扣除："+score+"积分,确定兑换吗？",function(){
			var query = new Object();
			query.id = id;
			query.act = 'do_exchange';
			$.ajax({
				url:ajax_url,
				data:query,
				type:'post',
				dataType:'json',
				success:function(obj){
					//console.log(data);
					if(obj.status==1){
						console.log(obj.info);
						$.toast(obj.info);
						setTimeout(function(){
							window.location.reload();
						},1000); 
					}else{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								window.location.href=obj.jump;
							},1000);
						}
					}
					return false;
				}
			});
		});

	});
