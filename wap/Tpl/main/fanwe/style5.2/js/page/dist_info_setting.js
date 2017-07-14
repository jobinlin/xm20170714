$(document).on("pageInit", "#dist_info_setting", function(e, pageId, $page)  {

    //退出登录
    $(".btn-con").click(function(){
        if(app_index=='app'){
            App.logout();
            return false;
        }
        $.confirm("是否退出当前账号？","",function(){
            dist_login_out();
        });
    });
    function dist_login_out(){
        var exit_url=$(this).attr("data-url");
        var query = new Object();
        query.act='loginout';
        $.ajax({
            url:exit_url,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status)
                {
                    $.toast(obj.info);
                    setTimeout(function(){
                        window.location.href=obj.jump;
                    },1500);
                }
                else
                {
                    $.toast(obj.info);
                }
            }
        });
    }
});