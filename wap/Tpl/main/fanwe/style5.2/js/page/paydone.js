/**
 * Created by Administrator on 2016/10/11.
 */
$(document).on("pageInit", "#payment_done", function(e, pageId, $page) {
	loadScript(jia_url);
	
    var lent=$(".order-replay").length;
    if (lent>3){
        $(".loadMore").show();
    }else if(lent<=3){
        $(".loadMore").hide();
    }



    var _click=0;
    $(".loadMore").click(function () {
        _click++;
        if(_click==1){
            $(".down_btn").show();
            $(".up_btn").hide();
            $(".j-moreThan").show();
        }
        if(_click==2){
            $(".down_btn").hide();
            $(".up_btn").show();
            $(".j-moreThan").hide();
        }
        if(_click>=2){
            _click=0;
        }

    });



    $(".j-showCode").click(function () {
        $(".codeShowBox").addClass("codeShow");
        $(".codeImgBox").removeClass("transi").addClass("trans");
        var $this=$(this);
        var codeNum=$this.parents(".order-replay").find(".j-codeNum").text();
        var codeSrc=$this.parents(".order-replay").find(".hiddenBox").attr("data-src");
        $(".codeShowBox .codeName").text(codeNum);
        $(".codeShowBox .codeImg").attr("src",codeSrc);
    });

    $(".blackBox").click(function () {
        $(".codeImgBox").removeClass("trans").addClass("transi");
        setTimeout(function () {
            $(".codeShowBox").removeClass("codeShow");
        },150);
    });
});
