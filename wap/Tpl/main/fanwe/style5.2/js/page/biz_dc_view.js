$(document).on("pageInit", "#dc_view", function(e, pageId, $page) {
	//通用方法（接单，取消，确认）	
	$(document).on('click', '.j-submit', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});

	var _rehei=$(".j-red-reward").height();
	
	$(document).on('click',".j-accept",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
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
    
    /*$(document).off('click',".label-checkbox");
    $(document).on('click',".list-block",function(){
    	var val=$(this).find("input[name='delivery_type']").val();
    	
    	alert(111);
    });*/
    
    $("input[type='radio']").change(function(){
    	popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    	
    	var type=$("input[type='radio']:checked").val();
    	
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
    	query.id=order_id;
    	
    	$.ajax({
        	  url:ajax_url,
        	  data:query,
        	  type:'post',
        	  dataType:'json',
        	  success:function(data){
        		  if(data.status == 1){
        			  $.toast(data.info); 
        			  setTimeout(function () {
       				  	  location.reload(); 
     			      }, 2000);
           		  }else{
           			  $.toast(data.info);
           			  setTimeout(function () {
          				  location.reload(); 
    			      }, 2000);
           		  }
        	  }
          });
    	
    	return false;
    });
	
});