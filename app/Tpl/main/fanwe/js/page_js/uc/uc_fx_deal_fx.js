$(function(){
	$(".fx_search_btn").bind("click",function(){
		$("form[name='fx_deal_search_form']").submit();
	});
	
	$(".alt").bind("mouseover",function(){
		$(".alt").removeClass("td_hover_box");
		$(this).addClass("td_hover_box");
		var dta_url = $(this).attr("data-url");
		var sub_name = $(this).attr("data-name");
		jiathis_config = {
			 	title:sub_name,
			    url:dta_url
			};
		$(".share_to").css("visibility","hidden");
		$(this).find(".share_to").css("visibility","visible");

	});
        
        
});

function add_user_deal_fx(deal_id){

    var query = new Object();
    query.deal_id = deal_id;
    query.act='add_fx_deal',
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
    return false;
}