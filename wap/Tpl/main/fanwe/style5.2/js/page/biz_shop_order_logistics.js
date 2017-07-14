
$(document).on("pageInit", "#biz_shop_order_logistics", function(e, pageId, $page) {

    if($(".buttons-tab .tab-link").length>0){
        var _width=$(".buttons-tab .tab-link.active").find("span").width();
        var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;

        var btm_line=$(".buttons-tab .bottom_line");
        btm_line.css({"width":_width+"px","left":_left+"px"});

        var _tabs=$(".tabBox .tab_box");
    }
    $(".buttons-tab .tab-link").click(function () {
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;

        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();

        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");
//        init_confirm_button();

    });

//    if($(".no_delivery").hasClass("active") &&
//        $("input[type='checkbox']").length==$("input[disabled='disabled']").length
//        ){
//        $("#uc_logistic nav.bar-tab").hide();
//    }else{
//        init_confirm_button();
//    }

    $(".no_delivery_deal").click(function(){
        if($("input[type='checkbox']").length==$("input[disabled='disabled']").length){
            $("#uc_logistic nav.bar-tab").hide();
        }else{
            $("#uc_logistic nav.bar-tab").show();
        }
    });

//    $(document).on("click",".confirm_order",function(){
//        var data_id = $(".tabBox .tab_box.active").attr("data_id");
//        var query = new Object();
//        if(data_id){
//            query.item_id = data_id;
//            query.act = 'verify_delivery';
//        }else{
//            var order_ids=new Array();
//            $(".tabBox .tab_box.active").find("input[name='my-radio']:checked").each(function(){
//                order_ids.push($(this).attr("data_id"));
//            });
//            query.order_ids=JSON.stringify(order_ids);
//            query.act = 'verify_no_delivery';
//        }
//        $.ajax({
//            url: order_url,
//            data: query,
//            dataType: "json",
//            type: "POST",
//            success: function(obj){
//                if(obj.status==0){
//
//                    $.toast(obj.info);
//                }
//                if(obj.status == 1){
//                    $.toast(obj.info)
//                    window.setTimeout(function(){
//                        $("#uc_logistic .tabBox .tab_box.active").attr("is_arrival",1);
//                        init_confirm_button();
//                        window.location.href=obj.jump;
//                    },1500);
//                }
//            },
//            error:function(ajaxobj)
//            {
//
////						if(ajaxobj.responseText!='')
////						alert(ajaxobj.responseText);
//            }
//
//        });
//    });
});

//function init_confirm_button(){
//    var status = $("#uc_logistic .tabBox .tab_box.active").attr("status");
//    if(status==1){
//        $("#uc_logistic nav.bar-tab").hide();
//    }else{
//        $("#uc_logistic nav.bar-tab").show();
//    }
//}