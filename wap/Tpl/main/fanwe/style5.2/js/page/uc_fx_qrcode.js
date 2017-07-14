/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#uc_fx_qrcode", function(e, pageId, $page) {	


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
    $("#uc_fx_qrcode .j-reward").unbind('click').bind('click',function () {
		if($(".totop").hasClass("vible")){
			popupTransition();
			setTimeout(function () {
				$(".totop").removeClass("vible");
			},300);
		}else{
			$(".totop").addClass("vible");
			$(".popup-box .j-trans-way").css({"bottom":-_hei});
			$(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
			$(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
			$(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
		}
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
	var is_luck=false;
    $(document).on('click',".j-reward-list li",function () {
		if(is_luck)return ;
		if($("input[name='qrcode_type']:checked").val()==$(this).find("input[name='qrcode_type']").val())return ;
		
		is_luck=true;
        var lue_name=$(this).find(".pay-way-name .j-company-name").text();
        var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
        var lue_reward=$(this).find(".pay-way-name").text();
		var qrcode=$(this).find(".pay-way-name").attr("qrcode");
		var qrcode_urls=$(this).find(".pay-way-name").attr("qrcode_urls");

        $(this).parents("ul").find("input").prop("checked",false);
		
		$(this).find("input[name='qrcode_type']").prop("checked",true);
		var query = new Object();
		query.qrcode_type=$("input[name='qrcode_type']:checked").val();
		$.ajax({
            url: ajax_url,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					var query2 = new Object();
					query2.is_ajax=1;
					$.ajax({
						url:location.href,
						data:query2,
						type: "POST",
						dataType: "json",
						success:function(obj)
						{
							$(".qrcode img").attr('src',obj.share_mall_qrcode);
							$(".qrcode-info .j-app-share-btn,.qrcode-info .j-openshare").attr('data-share-url',obj.user_data.share_mall_url);
						},
						error:function()
						{
							$.toast('错误');
							//location.href=location.href;
						}
					 });
				}else{
					$.toast(obj.info);
				}
				setTimeout(function () {
					$(".totop").removeClass("vible");
				},500);
				popupTransition();
				is_luck=false;
				//location.href=location.href
			 }
		});
        //setTimeout(function () {
        //    $(".totop").removeClass("vible");
        //},500);
        //popupTransition();
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
});