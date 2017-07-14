$(document).ready(function(){

    $("#syn_supplier_user").bind("click",function(){
        if(confirm("确定要同步吗?同步后将没有绑定会员的商户默认生成一个会员,并将删除所有商户子账户,需要在商户端重新配置!")){
            var ajaxurl = $(this).attr("data-url");
            $.weeboxs.open("<div style='height:30px; color:#f30; line-height:30px; text-align:center;' id='syn_data_info'>正在准备同步数据</div><div class='dialog-loading' style='height:50px;'></div>", {contentType:'text',showButton:false,title:"请勿刷新本页，请稍候...",width:300,height:80});
            syn_supplier_user(ajaxurl);
        }
    });
});


function syn_supplier_user(ajaxurl)
{

        $.ajax({
            url: ajaxurl,
            data: "ajax=1",
            dataType: "json",
            success: function(obj){
                if(obj.status)
                {
                    //同步成功
                    $(".dialog-content").html(obj.info);
                }
                else
                {
                    $("#syn_data_info").html(obj.info);
                    syn_supplier_user(obj.url);
                }
            }
        });


}