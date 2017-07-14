/**
 * Created by linzhibin on 2017/5/30.
 */
$(function () {
    $(".del-account-btn").bind("click",function() {
        var ajax_url = $(this).attr("rel");
        $.showConfirm("确认要删除吗?删除收将无法恢复。",function () {
            var query = new Object();
            $.ajax({
                type : "POST",
                url : ajax_url,
                dataType : "json",
                success : function(data) {
                   if(data.status){
                       $.showSuccess(data.info,function () {
                           location.reload()
                       });
                   }else{
                       $.showErr(data.info);
                   }
                }
            });
        })
    });

});