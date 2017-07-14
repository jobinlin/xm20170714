function close_pop()
{
	$.weeboxs.close("relate_goods_win");
}

$(document).ready(function(){
    $("#add_relate_goods").bind("click",function(){

    	if($("select[name='supplier_id']").length >0){
    		var supplier_idx = $("select[name='supplier_id']").val();
    	}else{
    		var supplier_idx = $("input[name='supplier_id']").val();
    	}	
    	var GOODS_LIST_URL=LOAD_GOODS_LIST_URL+'&supplier_id='+supplier_idx;

    	$.weeboxs.open(GOODS_LIST_URL, {boxid:'relate_goods_win',contentType:'ajax',showButton:true, showCancel:true, showOk:true,title:'选择要关联的商品',width:550,height:310,onopen:onOpenRelate,onok:onConfirmRelate,onclose:onCancelRelate});
    });

    $(".relate_row .relate_close_btn").live("click",function(){
        $(this).parents(".relate_row").remove();
        var relate_num = parseInt($("#relate_goods_box .relate_row").length);
        
        if(relate_goods_num > relate_num){
        	$("#relate_goods_box .add_icon").show();
        }
        
    });

    

    
});
var delete_icon = "__TMPL__Common/images/delete_icon.png";
function onConfirmRelate()
{
    var rowsCbo = $("input[rel='relate_goods_id']:checked");
    if(rowsCbo.length>0)
    {
        var relate_table = $("#relate_goods_box");
        
        //关联个数限制
        if($("input[name='relate_goods_id[]']").length>=relate_goods_num){
        	alert("最多 "+relate_goods_num+" 个关联商品!");
        	return false;
        }
    
        $.each(rowsCbo,function(i,o){
            //alert($(o).val());
            if($("#relate_goods_id_"+$(o).val()).length==0){
                if($("input[name='relate_goods_id[]']").length>=relate_goods_num){
                    close_pop();
                    return;
                }
                var row = $("<div class='relate_row'><div class='relate_left'><input type='hidden' id='relate_goods_id_"+$(o).val()+"' name='relate_goods_id[]' value='"+$(o).val()+"' />"+$(o).parent().parent().find(".goods_image").html()+"</div><div class='relate_right'>"+$(o).parent().parent().find(".goods_name").html()+"<a class='relate_close_btn' href='javascript:void(0);'><img src='"+delete_icon+"' /></a></div></div>");
                $(relate_table).prepend(row);

            }
        });
    }
   
    var relate_num = parseInt($("#relate_goods_box .relate_row").length);
    if(relate_goods_num <= relate_num){
    	$("#relate_goods_box .add_icon").hide();
    }
    close_pop();
}
function onCancelRelate()
{

}

function onOpenRelate(){    

	$("#ajax_news_form").bind("submit",function(){

        //改用ajax提交表单
        var ajaxurl = $(this).attr("action");
        var query = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            data:query,
            type: "POST",
            success: function(html){
                $("#relate_goods_win").find(".dialog-content").html(html);
				onOpenRelate();
            },
            error:function(ajaxobj){}
        });
        //end

        return false;
    });
	
	$("#ajax_news_page").find("a").bind("click",function(){

        //改用ajax提交表单
        var url = $(this).attr('href');
        url+="&page="+$(this).attr("page");
        var query =  $("#ajax_news_form").serialize();
        $.ajax({
            url: url,
            data:query,
            type: "POST",
            success: function(html){
                $("#relate_goods_win").find(".dialog-content").html(html);
				onOpenRelate();

            },
            error:function(ajaxobj)
            {

            }
        });
        //end

        return false;
    });

    $("#relate_goods_win").find(".check_all").bind("click",function(){
    	var checked = $(this).attr("checked");

    	if(checked){
    		$("#relate_goods_win").find("input[type='checkbox']").attr("checked",checked);
    	}else{
    		$("#relate_goods_win").find("input[type='checkbox']").attr("checked",false);
    	}	
    });
}
