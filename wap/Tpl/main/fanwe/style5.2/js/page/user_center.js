/**
 * Created by Administrator on 2016/11/4.
 */

$(document).on("pageInit", "#user_center", function(e, pageId, $page) {

    refreshdata([".j-order-lamp .swiper-wrapper",".cenList"]);
    $(".u-prompt .pro_close_btn").click(function () {
        $(".u-prompt").addClass("u-trans");
    });
    
    
    var order_child=$(".j-order-lamp .orderShow").length;
    var _width=$(".cenList .list_href").width();
    
    if(order_child<6){
        $("#user_center .orderBox").addClass("orderthan");
    }

    var swiper = new Swiper('.j-order-lamp', {
        scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        grabCursor: true
    });

   /* $(".is_read").click(function(){
    	$(this).find(".s_number").remove();
    });*/
    $(".content").off("click", ".fun-check-login");
    $(".content").on("click", ".fun-check-login",function () {
        var data_url = $(this).attr("data-url");
        if(is_login==0){
            if(app_index=="app"){
                App.login_sdk();
            }else{
                $.router.load(login_url, true);
            }
        }else{

            $.router.load(data_url, true);
            //window.location = data_url;
            //window.location=data_url;
        }
    });
   //  $(".fun-check-login").off("click");
   // $(".fun-check-login").on("click",".content .commonBox",function () {
   //     alert(22);
   //     var data_url = $(this).attr("data-url");
   //     if(is_login==0){
   //         if(app_index=="app"){
   //             $.toast("清先登录");
   //             App.login_sdk();
   //         }else{
   //             $.router.load(login_url, true);
   //         }
   //     }else{
   //         $.loadPage(data_url);
   //         //window.location=data_url;
   //     }
   // });
});