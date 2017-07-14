/**
 * Created by Administrator on 2016/11/17.
 */
$("#uc_order_refund").on("click",".load_page",function(){
		load_page($(this));
	});


    $(".j_label input[type=checkbox]").change(function () {
        if($(this).attr('checked')==false){
            //如果全选按钮没有选中，则列表的中的按钮也全部是未选中状态
            $(".j-select-body").find("label input[type=checkbox]").prop("checked",false);
        }else {
            //如果全选按钮选中，则列表的中的按钮也全部是选中状态
            $(".j-select-body").find("label input[type=checkbox]").prop("checked",true);
        }
    });

    $(".j-select-body input[type=checkbox]").change(function () {



        if($(this).attr('checked')==false){
            //如果该按钮没有选中，则列表的中的按钮也全部是未选中状态
            console.log("false")
        }else {
            //如果全选按钮选中，则列表的中的按钮也全部是选中状态
           /* $(this).prop("checked",true);*/
            console.log("true");
        }
        isSelect();
    });




    /*判断是不是全选*/

    function isSelect() {
        var _checkbox=$(".m-cart-list label input[type=checkbox]");
        var _radio=$(".m-cart-list label input[type=checkbox]:checked");

        var _lenght=_checkbox.length;

        _checkbox.each(function () {
            var a=$(".m-cart-list label input[type=checkbox]:checked").length;
            if(a<_lenght){
                $(".j_label input[type=checkbox]").prop("checked",false);
            }else {
                $(".j_label input[type=checkbox]").prop("checked",true);
            }
        });
    }

    $(".j_sure").click(function () {
        var check_box=$(".m-cart-list label input[type=checkbox]:checked");
        var array_deal_id=new Array();
        var array_coupon_id=new Array();
        check_box.each(function (i) {
            var deal_id=check_box.eq(i).attr("deal-id");
            var coupon_id=check_box.eq(i).attr("coupon-id");

            if(deal_id){
            	array_deal_id.push(deal_id);
            }
            if(coupon_id){
                array_coupon_id.push(coupon_id);
            }
        });
//        console.log(array_deal_id);
//        console.log(array_coupon_id);
        var d_id=array_deal_id.join(","); 
        var cou_id=array_coupon_id.join(","); 

        var str="";
        
        if(!d_id && !cou_id){
        	$.toast("请选择要退款的物品！");
        	return false;
        }
        if(d_id)
        	str+="&deal_id="+d_id;
        if(cou_id)
        	str+="&coupon_id="+cou_id;	
        /*ajax_url=ajax_url+str;
        console.log(ajax_url);*/
        $(this).attr("str",str);
        $(this).attr("url",ajax_url);
        //location.href=ajax_url;
    });
    




