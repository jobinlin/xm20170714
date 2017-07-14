$(document).ready(function(){
	$("#account_money").val(fee);
	$("#account_money").attr("disabled","disabled");
	$(".ui-radiobox").click(function(){
		$(".ui-checkbox").removeClass("common_cbo_checked").addClass("common_cbo");
		$(".ui-checkbox").attr("checked","false");
	});
	$(".ui-checkbox").click(function(){
		$(".ui-radiobox").removeClass("payment_rdo_checked").addClass("payment_rdo");
	});
});
function pay_do(){
	var query = new Object();
	var payment=$(".payment_rdo_checked").find("input[name='payment']").val();
	var bank_id=$(".payment_rdo_checked").find("input[name='payment']").attr("rel");

	var all_account_money=$(".common_cbo_checked").attr("checked");
	if(all_account_money){
		query.all_account_money = 1;
	}else{
		query.all_account_money = 0;
	}
	
    query.order_id = order_id;
    query.bank_id = bank_id;
    query.payment = payment;
    //console.log(query);
    $.ajax({
        url:ajax_url,
        data:query,
        type:"POST",
        dataType:"json",
        success:function(data){
        	//console.log(data);
        	if(data.status==1){
                if(data.free){
                	$.showSuccess(data.info,function(){
                		window.location.href=data.jump;
            		});
                }else{
                	window.location.href=data.jump;
                }
            }else{
            	if(data.jump){
	            	$.showErr(data.info,function(){
	            		window.location.href=data.jump;
	        		});
            	}else{
            		$.showErr(data.info);
            	}
            }

        }			
    });
}