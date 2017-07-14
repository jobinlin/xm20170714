$(function(){	

$(".current").find("a").text("新订单("+total+")");


$("select[name='l_id']").bind("change",function(){
		$("input[name='sn']").attr("value",'');
		$("form[name='search_form']").submit();
	});
	

$(".hand_ref").live("click",function(){
	
	window.location.reload();
	
});



$(".print_order").live('click',function(){
		
	//	var printData=$("#print_content").html();
		var printData = document.getElementById("print_content").innerHTML;
		window.document.body.innerHTML = printData;
		window.print();
	});



$(".is_voice").live("click",function(){
	
	voice_switch();
	
});	

if(is_voice==1){
	
	audio_play();	
}
	
timeOut(refresh_time);	
});	
/**
 * 接受订单
 */
function dc_accept2(obj){
		var id = parseInt($(obj).attr("data-id"));
			var query = new Object();
			query.act = "accept_order";
			query.id = id;
			$.ajax({
				url:ajax_url2,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					if(data.status==1){
						$.showSuccess(data.info,function(){window.location=ajax_urlb;});
					}else{
						$.showErr(data.info,function(){
						if(data.jump){
						window.location=ajax_urlb;
						}
						});
						
					}
				}
			});
	
	}
	
	
	

function dc_accept(obj){
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
						$.showSuccess(data.info,function(){window.location=ajax_urlb;});
					}else{
						$.showErr(data.info,function(){
						if(data.jump){
						window.location=ajax_urlb;
						}
						});
						
					}
				}
			});
	
	}



/**
 * 关闭订单
 */
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
								$.showSuccess(data.info,function(){window.location=ajax_urlb;});
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
	



function index_right(){
			var l_id=$('select[name=l_id]').val();
			$.ajax({
				url : APP_ROOT + "/biz.php?ctl=dcallorder&act=index&tpl_id=2&l_id="+l_id,
				dataType: "json",
				success:function(obj){
					if(obj!=""){
						$(".right_box").html(obj.html);
							init_ui_button();
							init_ui_textbox();
							init_ui_select();
							init_ui_radiobox();
							init_ui_checkbox();
							init_ui_starbar();
							init_ui_lazy();
							init_gotop();
							$(".current").find("a").text("新订单("+obj.count+")");
						if(obj.is_voice==1){
							
							audio_play();
						}	
							
							timeOut(obj.refresh_time);
					$("select[name='l_id']").bind("change",function(){
					$("input[name='sn']").attr("value",'');
					$("form[name='search_form']").submit();
					});		
								
					}
					
				}
			});
	}		


function audio_play(){
	
	$.ajax({
				url : APP_ROOT + "/biz.php?ctl=dcallorder&act=audio_play",
				dataType:"html",
				success:function(result){
					if(result!=""){
						$(".audio_play").html(result);
								
					}
					
				}
			});
	
	
	}

  
function timeOut(wait){  
			var index_wait=wait;
			var waite=wait;
    			if(waite==0){  
					 index_right()      
   				 }else{                    
      			  setTimeout(function(){	  
           				 waite--;  
        		   		$(".esc_time").text(waite);
          			  	timeOut(waite);  
       					 },1000)  
   					 }  
	}
	
function voice_switch(){
	
	$.ajax({
		url : APP_ROOT + "/biz.php?ctl=dcallorder&act=voice_switch",
		dataType: "json",
		success:function(obj){
			var m_obj_left=$(".moves").offset().left;
			var b_width=$('.is_voice').width();
			var s_width=$('.moves').width();
			var move_width=b_width-s_width;


			if(obj==1){
				
						$('.is_voice').removeClass("voice_off").addClass('voice_on');
						$(".on_off").text("语音提示开");
			
					}else{

						$('.is_voice').removeClass("voice_on").addClass('voice_off');
						$(".on_off").text("语音提示关");
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
				$.weeboxs.open(data.info, {boxid:'print_tip',contentType:'text',showButton:false, showCancel:false, showOk:false,title:'打印小票',width:250,type:'wee',onopen:function(){
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
