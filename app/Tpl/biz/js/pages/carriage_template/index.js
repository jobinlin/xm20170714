$(document).ready(function(){
    init_delete_click();
    $("select[name=valuation_type]").bind("change",function(){
        console.log(this);
    });
});
function init_delete_click(){
    //为删除按钮绑定点击事件
   $("a.deleteButton").bind("click",function(){
       var $me=$(this);
       var url=$me.attr("data-url");
       var query={};
       query['data_id']=$me.attr("data-id");
       $.showConfirm("是否删除该数据？",function(){do_ajax(url,query)});
   });
}
function do_ajax(url,query){
    $.ajax({
        url:url,
        data:query,
        type:"post",
        dataType:"json",
        success:function(data){
            if(data.status == 0){
                $.showErr(data.info);
            }else if(data.status==1){
                if(data.jump&&data.jump!="")
                {
                    location.href = data.jump;
                }else{
                    location.reload();
                }
            }
        }
    });
}
