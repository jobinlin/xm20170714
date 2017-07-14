$(document).on("pageInit", "#dc_search_index", function(e, pageId, $page) {
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
	
	//初始化历史搜索记录
	var cookarr = new Array();
	dc_cookobj = $.fn.cookie('dc_cookobj');
	if(dc_cookobj){
		var cookarr = dc_cookobj.split(',');
	}
	var key_html='';
	$.each(cookarr,function(i,obj){
		if(obj){
			$("#history").css({display:""});	
		}
		key_html+='<li>'+ obj +'</li>';	
	});
    $(".history-search .key-list").html(key_html);
    
    

	  //搜索提交
	  function search_submit(){
	  	var content=$.trim($("#keyword").val());
	  	if(content==''){
	  		alert("请输入内容！");
	  		window.location.reload();
	  		return false
	  	}else{
	  		if($.inArray(content ,cookarr)== -1){
	  			cookarr.push(content);
	  		}
	  		$.fn.cookie('dc_cookobj',cookarr);
	  	}
	  	var dc_search_url=$("form[name='search_form']").attr('action');
	  	var query=new Object();
	      query.keyword=content;
	      query.type=$("#keyword").attr('search_type');
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
    
	//历史记录点击事件
	$(".key-list li").click(function() {
		$("#keyword").val($(this).text());
		search_submit();
	});  

	//清空历史记录
	$('.confirm-ok').on('click', function () {
	      $.confirm('确定要清空历史数据？', function () {
	          $(".history-search .key-list").remove();
	          $.fn.cookie('dc_cookobj',cookarr,{ expires: -1 });
	          $("#history").css({display:"none"});
	          window.location.reload();
	      });
	});
 });  

//关键字标红
function toRed(content){
	$("#search_content .shop-name").each(function () {
		var bodyHtml = $(this).html();
		var x = bodyHtml.replace(new RegExp(content,"gm"),"<font color='red' >"+content+"</font>");
		$(this).html(x);
	});
}

