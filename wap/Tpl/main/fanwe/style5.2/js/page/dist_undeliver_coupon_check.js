// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 吴庆祥
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-03-09 10:46
// +----------------------------------------------------------------------
$(document).on("pageInit", "#dist_undeliver_coupon_check", function (e, pageId, $page) {
    $("#dist_undeliver_coupon_check .check-cancel").bind("click",function(){
        window.location.href=index_url;
    });
    $("#dist_undeliver_coupon_check .check-confirm").bind("click",function(){
        var query = {};
        query.coupon_pwd = coupon_pwd;
        $.ajax({
            url:url,
            data:query,
            dataType: "json",
            type:"post",
            success:function(obj){
                if(obj.status==1){
                    $.toast(obj.info);
                    if(obj.jump){
                        setTimeout(function(){
                            location.href = obj.jump;
                        },1000);
                    }
                }else{
                    $.toast(obj.info);
                }
            },
            error: function() {
                $.toast("网络被风吹走啦~");
            }
        });
    });
});
