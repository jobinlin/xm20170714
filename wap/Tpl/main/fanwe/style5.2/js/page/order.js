/**
 * Created by Administrator on 2016/11/14.
 */

$(document).on("pageInit", "#uc_order", function(e, pageId, $page) {

    var _width=$(".buttons-tab .tab-link.active").find("span").width();
    var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;

    var btm_line=$(".buttons-tab .bottom_line");
    btm_line.css({"width":_width+"px","left":_left+"px"});

    var _tabs=$(".tabBox .tab_box");
	var tab_link=new Array();
	tab_link[0] = true;
	tab_link[1] = true;
	tab_link[2] = true;
	tab_link[3] = true;
	tab_link[4] = true;
    $(".buttons-tab .tab-link").click(function () {
        $(document).off('infinite', '.infinite-scroll-bottom');
    	$(".content").scrollTop(1);
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;
        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();
        //加载内容
        if($.trim($(".j_ajaxlist_"+_index).html())==""&&tab_link[_index]){
			tab_link[_index]=false;
            var ajax_url =url[_index];
            $.ajax({
                url:ajax_url,
                type:"POST",
                success:function(html)
                {
                    //alert($(html).find(".j_ajaxlist_"+_index).html());
                    $(".j_ajaxlist_"+_index).append($(html).find(".j_ajaxlist_"+_index).html());
                    manageOrder();

                    //$(ajaxlist).find(".pages").html($(html).find(ajaxlist).find(".pages").html());
                    //init_listscroll(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
                	if($(".content").scrollTop()>0){
                		init_listscroll(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
                	}
                },
                error:function()
                {
                    $(".j_ajaxlist_"+_index).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
                }
            });
        }else{
        	if($(".content").scrollTop()>0){
                infinite(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
        	}
        }
        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");

        var swiperBox=_tabs.eq(_index).find(".j-order-lamp");


        var swiper = new Swiper(swiperBox, {
            scrollbarHide: true,
            slidesPerView: 'auto',
            centeredSlides: false,
            observer:true,
            grabCursor: true
        });
    });
    function manageOrder(){
        $(".manage-order").unbind("click").bind("click",function(){
              var message=$(this).attr("message");
              var url=$(this).attr("ajaxUrl");
             $.confirm(message, function () {
                 $.showIndicator();
                 $.ajax({
                     url:url,
                     dataType:"json",
                     success:function(data){
                         if(data.status==0){
                             $.toast(data.info);
                         }else{
//                             $.alert(data.info,function(){
//                                 window.location.href=data.jump;
//                             })
                        	 $.toast(data.info);
                        	 window.setTimeout(function(){
                        		 window.location.href=data.jump;
         					},1500);
                         }
                     }
                 });
             });
        });
    }
    var swiperm = new Swiper(".j-order-lamp1", {
        scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        observer:true,
        grabCursor: true
    });
    init_listscroll(".j_ajaxlist_"+pay_status,".j_ajaxadd_"+pay_status,"",manageOrder);
    manageOrder();

    
});