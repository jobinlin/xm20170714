/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#biz_qrcode", function(e, pageId, $page) {	


    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(document).on('click',".j-trans",function () {
    	var index = $(".j-trans").index($(this));
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").eq(index).css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });
    $(document).on('click',".j-reward",function () {
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
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"100"});
		setTimeout(function () {
                $(".j-cancel").parents(".popup-box").css({"z-index":"-1"});
            },300);
    }
    /*弹出层动画效果*/

    $(document).on('click',".j-trans-list li,.j-reward-list li",function () {
        var lue_name=$(this).find(".pay-way-name .j-company-name").text();
        var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
        var lue_reward=$(this).find(".pay-way-name").text();
		var qrcode=$(this).find(".pay-way-name").attr("qrcode");
		var qrcode_urls=$(this).find(".pay-way-name").attr("qrcode_urls");

        $(this).parents("ul").find("input").prop("checked",false);
		
		$(this).find("input[name='location_id']").prop("checked",true);
        $(".j-reward .j-reward-money").text(lue_reward);
		$(".qrcode img").attr("src",qrcode);
		$(".biz_qrcode_save").attr("href",qrcode_urls);
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },500);
        popupTransition();
        //count_buy_total();
    });




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


    $(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });

    function cssAnition() {
        $(".flippedout").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-flippedout-close").removeClass("showflipped");
        $(".j-open-choose").bind("click",open_choose);
        setTimeout("$('.flippedout').removeClass('showflipped')",300);
    }
	$(".biz_qrcode_save").unbind('click').bind('click',function () {
		if(app_index=='app'){
			try{
				App.savePhotoToLocal (this.href);
			}
			catch(ex)
			{
				$.toast("此app版本不支持下载图片");
			}
			
			return false;
		}
    });
});