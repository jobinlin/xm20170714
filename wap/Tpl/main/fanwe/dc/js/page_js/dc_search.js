$(document).ready(function () { 
	
	$("#search_form").bind('submit',function(){
		return false;
	});
    
    $(".search-btn").bind("click",function(){
    	search_submit();
    	return false;
    });
    
    $('#lid_search_result table tr').live('click',function(){
    	var url=$(this).attr('data-i');
    	location.href=url;
    });
    
	//按回车键判断函数
	$(document).keypress(function(e){
        var eCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
        if (eCode == 13){
        	search_submit();
        	return false;
        }
	});
	
 });  

//关键字标红
function toRed(content){
    var bodyHtml = $("#search_content").html();
    var x = bodyHtml.replace(new RegExp(content,"gm"),"<font color='red' >"+content+"</font>")
    $("#search_content").html(x);
}

//搜索提交
function search_submit(){
	var content=$.trim($("#keyword").val());
	var dc_search_url=$("form[name='search_form']").attr('action');
	var query=new Object();
    query.keyword=content;
	$.ajax({
		url:dc_search_url,
		data:query,
		type:'post',
		dataType:'json',
		success:function(data){
			$('#search_content').html(data.html);
		    toRed(content);
		}
	});
}
