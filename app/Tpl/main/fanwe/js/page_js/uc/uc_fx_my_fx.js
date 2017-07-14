$(function(){
	$(".alt").bind("mouseover",function(){

		$(".alt").removeClass("td_hover_box");
		$(this).addClass("td_hover_box");
	});
});

function do_is_effect(deal_id,obj){
    $.showConfirm("确定要【"+$(obj).html()+"】操作吗?",function(){
        var query = new Object();
        query.deal_id = deal_id;
        query.act='do_is_effect',
        $.ajax({
                url:ajax_url,
                data:query,
                type:"POST",
                dataType:"json",
                success:function(obj){
                        if(obj.status==1000)
                        {
                                ajax_login();
                        }
                        else if(obj.status==1)
                        {
                                $.showSuccess(obj.info,function(){
                                        location.reload();
                                });
                                return false;
                        }
                        else
                        {
                                
                                if(obj.jump){
                                	$.showErr(obj.info,function(){
                                		window.location.href=obj.jump;
                                });
                                }else{
                                	$.showErr(obj.info);
                                }
                                return false;
                        }
                }
        });
    })
    
}

function del_user_deal(deal_id,obj){
    $.showConfirm("确定要【"+$(obj).html()+"】操作码?",function(){
        var query = new Object();
        query.deal_id = deal_id;
        query.act='del_user_deal',
        $.ajax({
                url:ajax_url,
                data:query,
                type:"POST",
                dataType:"json",
                success:function(obj){
                        if(obj.status==1000)
                        {
                                ajax_login();
                        }
                        else if(obj.status==1)
                        {
                                $.showSuccess(obj.info,function(){
                                        location.reload();
                                });
                                return false;
                        }
                        else
                        {
                        	if(obj.jump){
                            	$.showErr(obj.info,function(){
                            		window.location.href=obj.jump;
                            	});
                            }else{
                            	$.showErr(obj.info);
                            }
                            return false;
                        }
                }
        });
    });
}