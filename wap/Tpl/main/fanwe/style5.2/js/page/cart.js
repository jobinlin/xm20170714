/**
 * Created by Administrator on 2016/9/7.
 */


$(document).on("pageInit", "#cart", function(e, pageId, $page) {
    $(".j-youhui").on('click', function() {
        $(".youhui-mask").addClass('active');
        $(".cart-youhui-box").addClass('active');
        
        var query = new Object();
        query.id = $(this).attr("data-id");
    	query.act = "get_youhui";
        
        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                
                $(".cart-youhui-box").find(".shop-name").html(data.supplier_name);
                $(".cart-youhui-box").find(".youhui-wrap").empty();
                
                var lenght=data.list.length;
                var li="";
                for(var i=0;i<lenght;i++){
                	
                	if(data['list'][i]['status']==1){
	                	li+="<div class='youhui-item b-line flex-box'>"+
									"<div class='youhui-info flex-1'>"+
										"<p class='youhui-price'>"+data['list'][i]['youhui_value']+"元</p>"+
										"<p class='youhui-tip'>"+data['list'][i]['use_info']+"</p>"+
										"<p class='youhui-time'>使用期限："+data['list'][i]['time_info']+"</p>"+
									"</div>"+
									"<a href='javascript:void(0);' class='youhui-btn j-get-youhui' data-id='"+data['list'][i]['id']+"' status='"+data['list'][i]['status']+"'>"+data['list'][i]['status_info']+"</a>"+
								"</div>";
                	}else{
                		li+="<div class='youhui-item b-line flex-box'>"+
								"<div class='youhui-info flex-1'>"+
									"<p class='youhui-price'>"+data['list'][i]['youhui_value']+"元</p>"+
									"<p class='youhui-tip'>"+data['list'][i]['use_info']+"</p>"+
									"<p class='youhui-time'>使用期限："+data['list'][i]['time_info']+"</p>"+
								"</div>"+
								"<a href='javascript:void(0);' class='youhui-btn' style='border: 1px solid gray;color: gray;' data-id='"+data['list'][i]['id']+"' status='"+data['list'][i]['status']+"'>"+data['list'][i]['status_info']+"</a>"+
							"</div>";
                	}
                }
                $(li).appendTo($(".cart-youhui-box .youhui-wrap"));
               
            }
            ,error:function(){
            }
        });
        
    });
    
    $(".cart-youhui-box").on('click', ".j-get-youhui" ,function() {
    	var status=$(this).attr("status");
    	
    	$(".youhui-mask").removeClass('active');
        $(".cart-youhui-box").removeClass('active');
        
    	if(status==1){
	    	var query = new Object();
	        query.data_id = $(this).attr("data-id");
	    	query.act = "download_youhui";
	    	
	    	$.ajax({
	            url:CART_URL,
	            data:query,
	            type:"post",
	            dataType:"json",
	            success:function(data){
	            	$.toast(data.info);
	            	if(data.status){
		            	setTimeout(function(){
		            		window.location.reload();
		            	},1000);
	            	}
	            }
	            ,error:function(){
	            }
	        });
    	}else{
    		$.toast("您已经领取了优惠券，留一点给别人吧~");
    	}
    });
    
    $(".j-close-mask").on('click', function() {
        $(".youhui-mask").removeClass('active');
        $(".cart-youhui-box").removeClass('active');
    });
    count_buy_total();
    //count_buy_total(1);
    isSelect();
    /*编辑按钮点击事件开始*/
    $(".j-edit-cur").unbind("click");
    $(".j-edit-cur").click(function () {
        var deal_json_key='dealkey_161010493611354';
        var $this=$(this);
        var curBtn=$this.text();
        var $parents=$this.parent().parent().parent();

        if(curBtn=="编辑"){
            $parents.find(".m-cart-list li .z-opera-sure").hide();
            $parents.find(".m-cart-list li .z-opera-edit").addClass("active");
            $this.text("完成");
            isSelect();
        }else if(curBtn=="完成"){
            $parents.find(".m-cart-list li .z-opera-sure").show();
            $parents.find(".m-cart-list li .z-opera-edit").removeClass('active');
            $this.text("编辑");
            isSelect();
        }
    });

    $(".j-edit-all").unbind("click");
    $(".j-edit-all").click(function () {
        var allBtn= $(this).text();
        if(allBtn=="编辑全部"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").hide();
            $(".m-cart-list li .z-opera-edit").addClass("active");
            $(".j-del-order").show().text("删除("+accnum+")");
            $(".allCount").hide();
            $(".j-accounts").hide();
            $(".j-edit-cur").hide();
            isSelect();
            $(this).text("完成");
        }else if(allBtn=="完成"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").show();
            $(".m-cart-list li .z-opera-edit").removeClass('active');
            $(this).text("编辑全部");
            $(".j-del-order").hide();
            $(".allCount").show();
            $(".j-edit-cur").show().text("编辑");
            $(".j-accounts").show().text("结算("+accnum+")");
            isSelect();
        }
    });
    /*编辑按钮点击事件结束*/


    /*点击删除按钮*/
    $(".z-opera-edit").off('click','.confirm-ok');
    $(".z-opera-edit").on('click','.confirm-ok', function () {
        var _this=$(this);
        var _parent=$(_this).parents(".j-select-body");
        var parents=$(_this).parents(".j-conBox");
        $.confirm('确定要删除这个宝贝吗？', function () {
            var query = new Object();
            var id = parseInt($(_this).parents("li").attr("data-id"));
            var ids = new Array();
            ids.push(id);
            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents("li").remove();
                        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
                        $(".j-del-order").text("删除("+accn+")");

                        var childLen=_parent.find("li").length;
                        if(childLen==0){
                            parents.remove();
                        }

                        var count=isSelect();
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });


        });
    });

    /*点击全删除按钮*/


    /*点击删除全部按钮*/
    // $(document).on('click','.j-del-order', function () {
    $('.j-del-order').off('click');
    $('.j-del-order').on('click', function() {
        var _this=$(this);
        $.confirm('确定要删除所选宝贝吗？', function () {
            var checkBox=$(".m-conBox").find("input[type=checkbox]:checked");
            if(checkBox.length==0){
                $.confirm('没有选择宝贝');
            }

            var query = new Object();
            var ids = new Array();
            var checked_box = $(".m-cart-list").find("input[type=checkbox]:checked");
            checked_box.each(function(){
                var id = parseInt($(this).parents("li").attr("data-id"));
                ids.push(id);
            });

            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {

                        checkBox.parent().parent().remove();
                        $(".j-del-order").text("删除(0)");
                        var count=isSelect();
                        $(".j-select-all label input[type=checkbox]").prop("checked",false);
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });





        });
    });
    /*点击删除全部按钮*/



    /*返回按钮*/
    /*
     $(document).on('click','.j-sure-cancel', function () {
     var _this=$(this);
     $.confirm('您确定要取消订单吗？', function () {
     window.history.back(-1);
     });
     });
     */



    /*输入框加减按钮*/
    $(".u-add").click(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        //var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        val++;
        var num=$(".u-txt[deal-id='"+id+"']").length;
        if(val>max && max!=-1){
            val=max;
        }

        if(num==1){
            if((max>user_max && max!=-1) || (max==-1)){
                if(user_max>0 && val>user_max){
                    val=user_max;
                    $.alert("该商品最多还能购买"+user_max+"件");
                }
            }
        }else{
            var allval=0;
            $(".u-txt[deal-id='"+id+"']").each(function(){
                allval+=parseInt($(this).val());
            });
            if(user_max>0 && allval>=user_max){
                $.alert("该商品最多还能购买"+user_max+"件");
                if(val>1){
                    val=val-1;
                }
            }
            if(val>max && max!=-1){
                $.alert("库存不足");
                val=max;
            }
        }

        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });
    $(".u-reduce").click(function () {
        var val=$(this).parent().find(".u-txt").val();
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;
        val--;
        /*if(num==1){
         if(user_min>0 && val<user_min){
         val=user_min;
         alert("该商品最小购买量为"+user_min);
         }
         }else{
         var allval=0;
         $(".u-txt[deal-id='"+id+"']").each(function(){
         allval+=parseInt($(this).val());
         });
         if(user_min>0 && allval<=user_min){
         alert("该商品最小购买量为"+user_min);
         val=val+1;
         }
         }*/
        if(val<1){
            val=1;
        }
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        $(this).parent().find(".u-txt").val(val);
        isSelect();
    });
    /*改变编辑框数量*/
    $(".u-txt").blur(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;

        if(val>0){
            if(num==1){
                if(user_max>0 && val>user_max){
                    if( (user_max<max && max!=-1) || (max==-1)){
                        val=user_max;
                        $.alert("该商品最多还能购买"+user_max+"件");
                    }

                }/*else if(user_min>0 && val<user_min){
                 val=user_min;
                 alert("该商品最小购买量为"+user_min);
                 }*/else{
                    if(val>max && max!=-1){
                        val=max;
                        $.alert("该商品库存不足");
                    }else{
                        val=val;
                    }
                }
            }else{
                var allval=0;

                $(".u-txt[deal-id='"+id+"']").each(function(){
                    allval+=parseInt($(this).val());
                });
                var elseval=allval-val;
                if(user_max>0 && allval>=user_max){
                    $.alert("该商品最多还能购买"+user_max+"件");
                    val=user_max-elseval;
                }/*else if(user_min>0 && allval<=user_min){
                 alert("该商品最小购买量为"+user_min);
                 val=user_min-elseval;
                 }*/
                if(val>max && max!=-1){
                    val=max;
                    $.alert("该商品库存不足");
                }else{
                    val=val;
                }

            }
        }else{
            /*if(user_min>0){
             val=user_min;
             }else{*/
            val=1;
            /*}*/
            $.alert("请输入有效数字");
        }
        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });



    /*点击清空按钮*/
    $('.j-clear-all').off('click');
    $('.j-clear-all').on('click', function () {
        var _this=$(this);
        $(_this).removeClass('j-clear-all');
        $.confirm('您确定要清空失效商品吗？', function () {
        	
            var disable_id = new Array();
            $(".m-invalid .m-cart-list .item-content").each(function(i,obj){
                disable_id.push($(obj).attr("data-id"));
            });
            var query = new Object();
            query.act='clear_deal_cart';
            query.id = disable_id;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents(".m-invalid").remove();
                        var count=isSelect();
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                    $(_this).addClass('j-clear-all');
                }
                ,error:function(){
                	$(_this).addClass('j-clear-all');
                }
            });

        });
    });


    /*全选按钮点击事件*/
    $(".j-select-all label input[type=checkbox]").change(function () {
        if($(this).attr('checked')==false){
            //如果全选按钮没有选中，则列表的中的按钮也全部是未选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",false);
        }else {
            //如果全选按钮选中，则列表的中的按钮也全部是选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
    });



    /*列表中头部checkbox按钮点击事件开始*/

    $(".j-select-title input[type=checkbox]").change(function () {
        if($(this).is(':checked')==false){
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",false);
        }else {
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");

    });
    /*列表中头部checkbox按钮点击事件结束*/

    /*宝贝列表单个checkbox点击事件开始*/
    $(".j-select-body input[type=checkbox]").change(function () {
        isSelect();

        var _samePar=$(this).parents(".m-cart-list").find("input[type=checkbox]");
        var _len=_samePar.length;
        _samePar.each(function () {
            var anum=$(this).parents(".m-cart-list").find("input[type=checkbox]:checked").length;

            if(anum<_len){
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",false);
            }else {
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",true);
            }
        });

    });
    /*宝贝列表单个checkbox点击事件接结束*/

    /*判断是否全部选中*/
    function isSelect() {
        var _checkbox=$(".m-cart-list label input[type=checkbox]");
        var _radio=$(".m-cart-list label input[type=checkbox]:checked");

        var _lenght=_checkbox.length;

        _checkbox.each(function () {
            var a=$(".m-cart-list label input[type=checkbox]:checked").length;
            if(a<_lenght){
                $(".j-select-all label input[type=checkbox]").prop("checked",false);
            }else {
                $(".j-select-all label input[type=checkbox]").prop("checked",true);
                $(".j-select-title input[type=checkbox]").prop("checked",true);
            }
        });

        var allprice = 0;
        var promote_price = 0;
        var promote_count = 0;
        var select_count = 0;
        _radio.each(function () {
            var data_price=parseFloat($(this).parents("li").find(".u-money").attr("data_value"));
            var data_num=parseInt($(this).parents("li").find(".j-count-num").text());
            var allow_promote = parseInt($(this).parents("li").attr("allow_promote"));
            select_count++;
            var account=data_num*data_price;
            allprice+=account;
            if(allow_promote==1){
                promote_price+=account;
                promote_count++;
            }
        });


        if(typeof(promote_cfg)!='undefined'){
            if(promote_cfg && promote_count==select_count){
                var all_promote_price=0;
                for(var i=0;i<promote_cfg.length;i++){
                    if(promote_price >= parseInt(promote_cfg[i]['discount_limit'])){
                        allprice -= parseInt(promote_cfg[i]['discount_amount']);
                        all_promote_price+=parseInt(promote_cfg[i]['discount_amount']);
                    }
                }
                $("#promote_price").html("¥"+all_promote_price);
            }else{
                $("#promote_price").html("¥0");
            }
        }
        allprice = allprice.toFixed(2);
        var priceStr=allprice.toString();
        if(priceStr.indexOf(".") > 0 ){
            var price_split=priceStr.split(".");
            $(".j-price-int").text(price_split[0]);
            $(".j-price-piont").text(price_split[1]);
        }else {
            $(".j-price-int").text(priceStr);
            $(".j-price-piont").text("00");
        }


        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        var allaccn=$(".m-conBox .j-select-body").find("input[type=checkbox]").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");
        if(accn==0){
            $(".j-accounts").addClass("invalid");
            /*if(index){
             location.reload();
             }*/
        }else{
            $(".j-accounts").removeClass("invalid");
        }
        return allaccn;
    }


    $(".j-accounts").unbind("click");
    $(".j-accounts").click(function(){
        if(is_login==0){
            if(app_index=="app"){
                App.login_sdk();
            }else{
                $.router.load(login_url, true);
            }
            return false;
        }
        var _this=$(this);
        var _radio=$(".m-cart-list label input[type=checkbox]");
        var checked_ids = new Array();
        var nochecked_ids = new Array();
        $(_radio).each(function(){
            var id = $(this).parents("li").attr("data-id");
            var attr = $(this).parents("li").find(".sizes").attr("attr_key");
            var attr_str = $(this).parents("li").find(".sizes").attr("attr_str");
            var number = parseInt($(this).parents("li").find(".j-count-num").text());
            var cart_item = new Object();
            cart_item.id = id;
            cart_item.attr = attr;
            cart_item.attr_str = attr_str;
            cart_item.number = number;
            if($(this).is(":checked")){
                checked_ids.push(cart_item);
            }else{
                nochecked_ids.push(cart_item);
            }

        });
        var disable_raido = $(".m-invalid .m-cart-list li");
        $(disable_raido).each(function(){ //失效商品
            var id = parseInt($(this).attr("data-id"));
            var cart_item = new Object();
            cart_item.id = id;
            nochecked_ids.push(cart_item);
        });

        //console.log(nochecked_ids);return false;
        if(checked_ids.length==0){
            return false;
        }
		$.showIndicator();
        var query = new Object();
        query.act='set_cart_status';
        query.checked_ids = checked_ids;
        query.nochecked_ids = nochecked_ids;

        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    $.hideIndicator();
					$.alert(data.info,function(){
						if(app_index=="app"){
							App.login_sdk();
						}else{
							window.location.href=data.jump;
						}
                    });
                    /*window.setTimeout(function(){
                    	//$.router.load(data.jump,true);
                    	window.location.href=data.jump;
                    },1000);*/

                }else if(data.status==1)
                {
        			
        			
        		    setTimeout(function () {
        		    	 $.hideIndicator();
        		    }, 2000);
					location.href = cart_check_url;
					
                  //  $.router.load(cart_check_url, true);
                }else{
					$.hideIndicator();
                	$.alert(data.info,function(){
                		if(data.jump){
                			window.location.href=data.jump;
                		}
                	});
                }
            }
            ,error:function(){
				$.hideIndicator();
            }
        });


    });



    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(".j-cancel").click(function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(".j-trans").click(function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });
    $(".j-reward").click(function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_hei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });


    /*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
    /*弹出层动画效果*/

    /*弹出框点击事件*/
    function listCli(obj) {
        obj.click(function () {
            var lue_name=$(this).find(".pay-way-name .j-company-name").text();
            var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
            var lue_reward=$(this).find(".pay-way-name").text();

            var parText=$(obj).parents(".m-trans-way").find(".u-ti").text();

            $(this).parents("ul").find("input").prop("checked",false);
            if(parText=="配送方式"){
                $(this).find("input[name='delivery']").prop("checked",true);
                var is_pick=$(this).find("input[name='delivery']").val();
                //alert(is_pick);
                $(".j-trans .j-trans-commpany").find(".j-company-name").text(lue_name);
                if(is_pick!=-1){
                    $(".j-trans .j-trans-commpany").find(".j-company-money").text(lue_momey);
                    $("#delivery-address").show();
                }else{
                    $(".j-trans .j-trans-commpany").find(".j-company-money").text("");
                    $("#delivery-address").hide();
                }
            }
            if(parText=="红包"){
                $(this).find("input[name='ecvsn']").prop("checked",true);
                $(".j-reward .j-reward-money").text(lue_reward);
            }
            setTimeout(function () {
                $(".totop").removeClass("vible");
            },500);
            popupTransition();
            count_buy_total();
        });
    }

    listCli($(".j-reward-list li"));
    listCli($(".j-trans-list li"));








    /*弹层开始*/
    $(".choose-list .j-choose").click(function(){
        $(this).siblings(".j-choose").removeClass("active");
        $(this).addClass("active");
        setSpecgood();
        var data_value= $(".j-choose.active").attr("data-value");
        var data_value = []; // 定义一个空数组
        var txt = $('.j-choose.active'); // 获取所有文本框
        for (var i = 0; i < txt.length; i++) {
            data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
        }
        $(".good-specifications span").empty();
        $(".good-specifications span").addClass("isChoose");
        $(".good-specifications span").append("已选规格：");
        $.each(data_value,function(i){
            $(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
            //传值可以考虑更改这里
            $(".spec-data").attr("data-value"+[i],data_value[i]);
        });
    });


    $(".j-box-bg").click(function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });



    
    $(".j-open-choose").bind("click",open_choose);
    function open_choose(){
        $(".j-flippedout-close").attr("rel","spec");
        $(".j-spec-choose-close").attr("rel","spec");
        $(".flippedout").addClass("showflipped").addClass("z-open");
        $(".spec-choose").addClass("z-open");
        $(".totop").addClass("vhide");//隐藏回到头部按钮
        var $this=$(this);
        $(this).unbind("click");
        $this.parents("li").addClass("choose");
        //调用属性HTML
        var id =  $this.parents("li").attr("data-id");
        var attr_key = $this.parents("li").find(".sizes").attr("attr_key");
        var query = new Object();
        query.act='get_cart_deal_attr';
        query.id = id;
        query.attr_key = attr_key;
        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    location.href=data.jump;
                }else if(data.status==1)
                {
                    $(".page-current .cart_box").html(data.html);
                    set_attr_name();
                    $(".flippedout .choose-list .j-choose").click(function(){
                        if(!$(this).hasClass("active")){
                            $(this).siblings(".j-choose").removeClass("active");
                            $(this).addClass("active");
                            set_attr_name();
                        }
                    });


                    $(".j-spec-choose-close,.j-flippedout-close,.j-cancel-flip").click(function(){
                        cssAnition();
                    });

                    $(".j-nowbuy").click(function () {
                        if($(this).attr('max') && $(this).attr('max')==0){
                            $.alert("库存不足");
                        }else{
                            if($this.parents("li").hasClass("choose")){
                                $this.parents("li").removeClass("choose");
                            }

                            var attr_check_ids = new Array();
                            var attr_name = '';
                            $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
                                attr_name+=$(obj).text();
                                attr_check_ids.push($(obj).attr("data-id"));
                            });

                            if(attr_check_ids.length==attr_num){

                                var attr_checked_ids = attr_check_ids.join(",");
                                //同步属性
                                $this.parents("li").find(".sizes").attr({'attr_key':attr_checked_ids,'attr_str':attr_name}).text("规格:"+attr_name);
                                var deal_name=$this.parents("li").find(".item-subtitle a").attr('deal-name'); 
                                if(deal_name){
                                	$this.parents("li").find(".item-subtitle a").html(deal_name+"["+attr_name+"]");
                                }
                                
                                //同步值
                                if($(this).attr('max') != '不限'){

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("仅剩"+$(this).attr('max')+"件");
                                    if($(this).attr('max')>=10){
                                    	$(".item-content[data-id='"+id+"']").find(".u-surplus").hide();
                                    }else{
                                    	$(".item-content[data-id='"+id+"']").find(".u-surplus").show();
                                    }
 
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                    var $val=parseInt($val);
                                    var $max=parseInt($(this).attr('max'));
                                    if($val>$max){
                                        $(".item-content[data-id='"+id+"']").find("input[type=text]").val($max);
                                        $(".item-content[data-id='"+id+"']").find(".j-count-num").text($max);
                                        isSelect();
                                    }
                                }else{

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("");
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                }

                                //同步价格
                                var num=parseFloat($(".showflipped .spec-goodprice").attr("data_value"));
                                num = Math.round(num*100)/100;  //保留两位小数
                                num =Number(num).toFixed(2);  //保留两位小数
                                var num_arr = num.split('.');
                                var price_str='¥ <i class="j-goods-money">'+num_arr[0]+'.</i>'+num_arr[1];
                                $this.parents("li").find(".u-money").attr("data_value",num).html(price_str);

                                cssAnition();
                            }else{
                                $.alert("请选择属性");
                            }
                        }
                    });

                }else{
                    $.alert(data.info);
                }
            }
            ,error:function(){
            }
        });
    }

    function set_attr_name(){
        var attr_name='';
        var attr_check_ids = new Array();
        var attr_check_key='';
        var deal_price = deal_current_price; // 商品基础价
        $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
            attr_name+='&nbsp;&nbsp;'+$(obj).text();
            attr_check_ids.push($(obj).attr("data-id"));
        });

        var attr_check_ids_new = attr_check_ids.sort();
        attr_check_key=attr_check_ids_new.join("_");
        if(deal_attr_stock_json[attr_check_key]){
            var stock = deal_attr_stock_json[attr_check_key]['stock_cfg'];
            if(parseInt(stock)<0){
                stock = '不限';
            }
            deal_price += parseFloat(deal_attr_stock_json[attr_check_key]['price']);
        }else{
            var stock = '不限';
        }
        $(".spec-goodspec").empty();
        $(".spec-goodspec").append("已选择");
        //$(".spec-goodspec em").html(attr_name);
        $(".spec-goodspec").append("<em class='choose_item'>" + attr_name + "</em>");
        $(".spec-goodstock").text("库存:"+stock);
        $(".j-nowbuy").attr("max",stock);

        
        /*$.each(deal_attr_json,function(i,obj){
            $.each(obj['attr_list'],function(xi,xobj){
                if($.inArray(xobj.id,attr_check_ids_new) >= 0){

                    deal_price += parseFloat(xobj.price);
                }
            });

        });*/

		deal_price=parseFloat(deal_price).toFixed(2);
        $(".spec-goodprice").attr("data_value",deal_price).html("¥"+deal_price);

    }


    function cssAnition() {
        $(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-open-choose").bind("click",open_choose);
    }


    function count_buy_total()
    {
        ajaxing = true;
        var query = new Object();

        //获取配送方式
        var delivery_id = $("input[name='delivery']:checked").val();

        if(!delivery_id)
        {
            delivery_id = 0;
        }
        query.delivery_id = delivery_id;

        var address_id = $("input[name='address_id']").val();

        //全额支付
        if($("input[name='all_account_money']").attr("checked"))
        {
            query.all_account_money = 1;
        }
        else
        {
            query.all_account_money = 0;
        }

        //代金券
        var ecvsn = $("input[name='ecvsn']:checked").val();

        if(!ecvsn)
        {
            ecvsn = '';
        }

        var ecvpassword = $("input[name='ecvpassword']").val();
        if(!ecvpassword)
        {
            ecvpassword = '';
        }

        var buy_type = $("input[name='buy_type']").val();
        query.ecvsn = ecvsn;
        query.ecvpassword = ecvpassword;
        query.address_id = address_id;
        query.buy_type = buy_type;
        //支付方式
        var payment = $("input[name='payment']:checked").val();
        if(!payment)
        {
            payment = 0;
        }
        query.payment = payment;
        query.bank_id = $("input[name='payment']:checked").attr("rel");
        query.id = order_id;
        //query.reward = reward;
        query.act = "count_buy_total";
        $.ajax({
            url: AJAX_URL,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){
                //alert(1111);
                /*if(data.free && delivery_id!=-1){
                 $(".j-company-money").html("运费：0");
                 }*/
                if(data.total_price==0 && $('div').is('.voucher_box')){
                    $(".voucher_box").remove();
                    count_buy_total();
                }
                /*if(reward==1){*/
                $("#cart_total").html(data.html);
                $(".total_price_box").html(data.pay_price_html);
                ajaxing = false;
                /*}else{
                 var ecv_money = parseFloat($("input[name='ecvsn']:checked").attr("money"));
                 var pay_moeny = parseFloat(data);
                 if(pay_moeny<ecv_money){
                 //$("div.j-reward-money").html("不使用红包");
                 var now_ecv=0;
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(pay_moeny<this_money){
                 $(this).remove();
                 }else{
                 if(this_money>now_ecv){
                 now_ecv=this_money;
                 }
                 }
                 });
                 now_ecv=parseFloat(now_ecv);
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(this_money==now_ecv){
                 $(".j-reward-list").find("input[name='ecvsn']").removeAttr("checked");   ;
                 $(this).find("input[name='ecvsn']").attr("checked","checked");
                 $("div.j-reward-money").html($(this).find(".pay-way-name").html());
                 }
                 });
                 }*/
                //count_buy_total(1);
                //}
            },
            error:function(ajaxobj)
            {
//    			if(ajaxobj.responseText!='')
//    			alert(LANG['REFRESH_TOO_FAST']);
            }
        });
    }
    
    
    $(".go_pay").unbind("click");
    $(".go_pay").click(function(){

        var query = $("#pay_box").serialize();
        //console.log(query);
        /*if($("input[name='payment']:checked").val()==-1){
         query['is_pick']=1;
         }else{
         query['is_pick']=0;
         }*/
        var url = $("#pay_box").attr("action");
        $.ajax({
            url: url,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){

                if(data.status==1)
                {
                    location.href=data.jump;
                }else{
                    $.alert(data.info);
                }

                ajaxing = false;
            },
            error:function(ajaxobj)
            {

            }
        });

    });


});
