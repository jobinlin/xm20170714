/**
 * 
 */
$(document).on("pageInit", "#payment_done", function(e, pageId, $page){
	$(".deal-share").click(function(){
		var url_data=$(this).attr("url");
		var that = this;
		if($("#share").is(':checked')){
			var query = new Object();
			query.act="order_shere";
			query.id = id;
			query.url_data
				$.ajax({
					url:AJAX_URL,
					data:query,
					dataType:"json",
					type:"post",
					global:false,
					success:function(obj){
						if(obj.status){
							if (app_index == 'app') {

							//	var pay_json = '{"id":"830","url":"'+data.jump+'","title":"'+data.title+'"}';
								App.app_detail(type,json_parma);
							} else {

								$.router.load(url_data, true);
							}
						}else{
							$.toast(obj.info);
						}
					}
				});
		}else{

		 	if (app_index == 'app') {
	

		 		if(type > 0){

		 			//App.app_detail(type,json_parma);
		 		}else{
		 			$.router.load(url_data, true);
		 		}
		 	} else {

				$.router.load(url_data, true);
		 	}
		}
	});
});