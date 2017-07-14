// +----------------------------------------------------------------------
// | Copyright (c) 2010-2013 http://www.YiiSpace.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Micheal Chen <shilong.chen2012@gmail.com>
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-03-06 09:49
// +----------------------------------------------------------------------
$(document).on("pageInit", "#dist_undeliver", function (e, pageId, $page) {
    $(".biz-link-bar").on('click', '.j-qrcode', function() {
        if(app_index == 'wap'){
            $.toast("手机浏览器暂不支持，请下载APP");
        }
    });
    $(".biz-manager-bar").on('click', '.j-unopen', function() {
        $.toast("暂未开放");
    });
    $(".biz-manager-bar").on('click', '.store_pay_unopen', function() {
        $.toast("没有操作权限");
    });
    $(".to-qrcode").on('click', function() {
        $.toast("暂未开放");
    });
    var pre_coupon_pwd = "";
    $("input[name='qr_code']").keyup(function () {
        var coupon_pwd = $(this).val();
        var code_len = coupon_pwd.length;
        var code_rule = /^[0-9]{12}$/;

        if (pre_coupon_pwd != coupon_pwd){
            pre_coupon_pwd = coupon_pwd;
            if (code_len == 12) {
                if (!code_rule.test(coupon_pwd)) {
                    $.toast('您输入的券码无效');
                }
                else {
                    $.post(index_check_url, { "coupon_pwd": coupon_pwd }, function (data) {
                        if (data.status) {
                            $(".code-input").val("");
                            location.href = data.jump;
                        } else {
                            $.toast(data.info);
                            if(data.jump){
                                setTimeout(function(){
                                        location.href=data.jump;
                                },2000);
                            }
                        }
                    }, "json");
                }
            } else if (code_len > 12) {
                $.toast('您输入的券码无效');
            }
        }
    });
});
