/**
 * 
 */
$(document).on("pageInit", "#biz_dc_abnormal_order", function(e, pageId, $page) {
	
	init_list_scroll_bottom();//下拉刷新加载
	
	var _rehei=$(".j-red-reward").height();
	
	$(document).on('click',".j-handle",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
        
        var data_account=$(this).attr('dada-account');
        
        $("input[name='order_id']").val($(this).attr('data-id'));
        $("input[name='dada_account']").val(data_account);
        
        if(!is_open_dada_delivery){
        	$("#dada-data").find(".item-title").html("委托达达配送(未开启，请在pc后台开启)"); 	
        }
        else if(data_account == ''){
        	$("#dada-data").find(".item-title").html("委托达达配送(帐号未注册，请在pc后台开启)");
        }
        else if(!delivery_money_enough){
        	$("#dada-data").find(".item-title").html("委托达达配送(配送余额不足，请在pc后台充值)"); 
        }
        
        if(!is_open_dada_delivery || data_account == '' || !delivery_money_enough){
        	$("#dada-data").find("input[name='delivery_part']").attr('checked',true);
        	$("#dada-data").find("input[name='delivery_part']").attr('disabled','disabled');
        	$("#dada-data").find(".icon-form-checkbox").css('border','gray!important');
        	$("#dada-data").find(".icon-form-checkbox").css('background-color','gray');
        }
        
	});
	
	$(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	$(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	/*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        //$(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
	
    $("input[type='radio']").change(function(){
    	popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    	
    	var type=$("input[type='radio']:checked").val();
    	
    	var dada_account=$("input[name='dada_account']").val();
    	
    	if(type==2){
    		if(!is_open_dada_delivery){
    			$.toast('达达未开启，请在pc后台开启'); 
    			return false;
    		}
    		if(dada_account == ''){
    			$.toast('达达帐号未注册，请在pc后台开启'); 
    			return false;
    		}
    		if(delivery_money_enough == 0){
    			$.toast('达达配送余额不足，请在pc后台充值'); 
    			return false;
    		}
    	}
    	var query=new Object();
    	query.act="accept_order";
    	query.type=type;
    	query.id=$("input[name='order_id']").val();
    	
    	$.ajax({
        	  url:ajax_url,
        	  data:query,
        	  type:'post',
        	  dataType:'json',
        	  success:function(data){
        		  
        		  $.toast(data.info); 
    			  setTimeout(function () {
   				  	  location.reload(); 
 			      }, 1000);
        		  
        	  }
          });
    	
    	return false;
    });
    
});