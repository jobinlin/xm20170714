$(function(){

	$(".print_order").live('click',function(){
		
		//	var printData=$("#print_content").html();
			var printData = document.getElementById("print_content").innerHTML;
			window.document.body.innerHTML = printData;
			window.print();
	});

});




/**
 * 接受订单
 */
function dc_accept(obj){
	    $("#accept_order_tip").remove();
		var id = parseInt($(obj).attr("data-id"));
			var query = new Object();
			query.act = "accept_order";
			query.id = id;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					if(data.status==1){
						if(data.html){
							$("body").append(data.html);
							init_ui_button();
							init_ui_radiobox();					
							$(".edit-box").show();
							
							$(".j-close-edit").bind('click', function() {
								$(".edit-box").hide();
								return false;
							});
							
							$(".j-confirm-edit").unbind('click');
							$(".j-confirm-edit").bind('click', function() {
		
								$(".edit-box").hide();
								var url=$("form[name='accept_order_tip']").attr('action');
								var query = $("form[name='accept_order_tip']").serialize();
								$.ajax({
									url:url,
									data:query,
									type:"post",
									dataType:"json",
									success:function(data){
										if(data.status==1){
											$.showSuccess(data.info,function(){location.reload();});
										}else{
											$.showErr(data.info,function(){location.reload();});
											
										}
									}
								});
								return false;
							});	
						}else{
							$.showSuccess(data.info,function(){location.reload();});
						}

								
					}else{
						$.showErr(data.info,function(){
						if(data.jump){
							location.reload();
						}
						});
						
					}
				}
			});
	
	
}
			
function close_order(obj){
		var id = parseInt($(obj).attr("data-id"));
		var CLOSE_TIP=$(obj).attr("action");
			$.weeboxs.open(CLOSE_TIP, {boxid:'close_tip',contentType:'ajax',showButton:true, showCancel:true, showOk:true,title:'请选择关闭交易原因',width:220,type:'wee',height:150,onopen:function(){
			init_ui_button();
			init_ui_radiobox();
			init_ui_textbox();
			
			$("#close_formx label[name='close_reason']").live("click",function(){	
				$("#close_formx input[type='text']").val('');
			});
		
			$("#close_formx input[type='text']").live('focus',function(){
				$("#close_formx label").each(function(i,val){
					$(val).find("input").attr('checked',false);
					$(val).ui_radiobox({refresh:true});
				});
			});
				
			},onok:function(){
			var is_done=true;
			if($.trim($("#close_formx input[type='text']").val())==''){
				is_done=false;
				$("#close_formx label[name='close_reason']").each(function(i,val){
					if($(val).find("input").attr('checked')){
						is_done=true;

					}
				});
			}
			if(is_done){
					var url=$("form[name='close_tip']").attr('action');
					var query = $("form[name='close_tip']").serialize();
					$.ajax({
						url:url,
						data:query,
						type:"post",
						dataType:"json",
						success:function(data){
							if(data.status==1){
								
								$.weeboxs.close('close_tip');
								$.showSuccess(data.info,function(){location.reload();});
							}else{
								$.showErr(data.info);
								
							}
						}
					});
				}
				else
				{
					$.showErr('请选择关闭交易的原因');
				}
				
			}});

	
	}

/**
 * 替用户完成订单
 */
function dc_over(obj){
		var id = parseInt($(obj).attr("data-id"));

			var query = new Object();
			query.act = "over_order";
			query.id = id;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					if(data.status==1){
						$.showSuccess(data.info,function(){location.reload();});
					}else{
						$.showErr(data.info);
						
					}
				}
			});
	
	}

	

/**
 * 打印小票
 * @param obj
 */
function print_order(obj){

	var id = parseInt($(obj).attr("data-id"));
	var query = new Object();
	query.act = "print_order";
	query.id = id;

	$.ajax({
		url:ajax_url,
		data:query,
		type:"post",
		dataType:"json",
		success:function(data){
		
			if(data.status){
				$.weeboxs.open(data.info, {boxid:'print_tip',contentType:'text',showButton:false, showCancel:false, showOk:false,title:'打印小票',width:230,type:'wee',onopen:function(){
					init_ui_button();
					init_ui_radiobox();
					init_ui_textbox();
					
						
					}});
			}else{
				$.showErr(data.info);
			}
			
		}
	});
	
}	

function change_delivery(obj){
	var id = parseInt($(obj).attr("data-id"));
	var query = new Object();
	query.act = "change_delivery";
	query.id = id;

	$.weeboxs.open('确认更改为商家配送？', {boxid:'change_delivery',contentType:'text',showButton:true, showCancel:true, showOk:true,title:'更改配送',width:230,type:'wee',onopen:function(){
		init_ui_button();
		
	},onok:function(){
		$.weeboxs.close('change_delivery');
		$.ajax({
			url:ajax_url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				
				if(data.status==1){					
					$.showSuccess(data.info,function(){location.reload();});
				}else{
					$.showErr(data.info);	
				}
			}
		});
		
	}});
	
	

}


