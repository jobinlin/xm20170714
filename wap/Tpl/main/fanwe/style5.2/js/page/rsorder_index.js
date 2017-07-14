$(document).on("pageInit", "#rsorder_index", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	//打开评论
	$(document).on('click', '.j-open-comment', function() {
		$(".img-comment-1").attr("src",$(this).parents('li').find(".img-comment").attr('src'));
		$(".name-comment-1").html($(this).parents('li').find(".name-comment").html());
		$("input[name='order_id_1']").val($(this).parents('li').find("input[name='order_id']").val());
		$("input[name='location_id_1']").val($(this).parents('li').find("input[name='location_id']").val());
		$.popup('.popup-comment');
	});
	//关闭当前弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	});
	$(".comment-stars").on('click', '.j-point', function() {
		$(".j-point").removeClass('active');
		$(this).addClass('active');
		$(this).prevAll().addClass('active');
		$("#star-value").attr('value', $(this).attr('value'));
	});
	
	
	//发表评论
	$('.j-comment-sub').bind('click',function(){
		
    	var is_pass=1;
    	var dp_points=$("#star-value").val();
			if(dp_points==0){
				$.toast('请给出您宝贵的评分！');
				is_pass=0;
				return false;
			}
    	if(is_pass==1){

	    	if($("textarea[name='content']").val()==''){
	    		$.toast('请填写您的宝贵意见！');
	    		is_pass=0;
	    		return false;
	    	}
    	}
    		
		if(is_pass==0){
			return false;
		}


    	var url=$(this).attr('action');
    	
		var query = new Object();
		query.location_id = $("input[name='location_id_1']").val();
		query.order_id = $("input[name='order_id_1']").val();
		 
		query.dp_points=dp_points;

    	query.content = $("textarea[name='content']").val(); 
    	query.is_rs = 1;
     	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json', 
			success:function(data){
			
				if(data.status==1){
				$.showIndicator();
			      setTimeout(function () {
			    	  close_comment();
			      }, 2000);
					
				}else{
					$.toast(data.info);
				}
				
				function close_comment(){
					$.toast(data.info);
					$(".popup-comment").removeClass('modal-in').addClass('modal-out');
					$.hideIndicator();
					$(".j-point").removeClass('active');
					$("#star-value").attr('value', '');
					$("textarea[name='content']").val('');
					var AJAX_URL = data.jump;
					var is_ajax  = 1;
					$.ajax({
						url:AJAX_URL,
						data:{"is_ajax":is_ajax},
						type:'post',
						dataType:'json', 
						success:function(obj){
							$(".infinite-scroll-bottom").html(obj.html);
							init_list_scroll_bottom();//下拉刷新加载
						}
					});
				}
			}
    	});
    	   
          
    });
	
	$(document).on('click','.to-pay',function(){
		var url = $(this).attr('data_url');
		var jump_url = $(this).attr('jump_url');
		var query = new Object();
		query.is_rs = 1;
		$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					location.href = jump_url;
				}else{
					$.toast(data.info);
				}
			}
		});
		
	});
	
});