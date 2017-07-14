$(document).on("pageInit", "#search_index", function(e, pageId, $page) {
	$("input[name='search_type']").val(2);
	//搜索类型切换

	function stopPropagation(e) {
			if (e.stopPropagation)
				e.stopPropagation();
			else
				e.cancelBubble = true;
		}

		$(document).bind('click', function() {
			$(".type-select").removeClass('active');
		});
		function clear_search() {
			if ($('#keyword').val().length==0) {
				$("#close").hide();
			} else {
				$('#close').show();
			}
		}
		$("#keyword").bind('input propertychange', function() {
			clear_search();
		});
		$('#close').click(function(){
			$('#keyword').val('');
			$("#close").hide();
		});
		$('.search-type').bind('click', function(e) {
			stopPropagation(e);
			$(".type-select").addClass('active');
		});
		$(".type-select li a").click(function() {
			$(".search-list li").hide();
			$("input[name='search_type']").val($(this).attr("data"));
			if($(this).html()=="商城"){
				$("input[name='keyword']").attr("placeholder","搜索商品");
			}else{
				$("input[name='keyword']").attr("placeholder","搜索"+$(this).html());
			}
		});
		$(".type-select li").bind("click",function(){
			$(".search-list li").hide();
			$(".search-list li").eq($(this).index()).show();
		});

	//初始化历史搜索记录
	var cookarr = new Array();
	cookobj = $.fn.cookie('cookobj');
	if(cookobj){
		var cookarr = cookobj.split(',');
	}
	var key_html='';
	$.each(cookarr,function(i,obj){
		if(obj){
			$("#history").css({display:""});	
		}
		key_html+='<li>'+ obj +'</li>';	
	});
    $(".history-search .key-list").html(key_html);
    
	function search_submit(){
		var keyword = $.trim($("#keyword").val());
		if(keyword==''){
			$.alert("请输入搜索内容");
			return false;
		}
		if($.inArray(keyword ,cookarr)== -1){
			cookarr.push(keyword);
		}
		$.fn.cookie('cookobj',cookarr);
		
		$("form[name='search_form']").submit();
		
	}
	$(".key-list li").click(function() {
		$("#keyword").val($(this).text());
		search_submit();
		
	});
	
	$(".key-list li").click(function() {
		$("#keyword").val($(this).text());
		search_submit();
		
	});
	
	$(".search").click(function(){
		search_submit();
	});
	
	
	//按回车键判断函数
	$(document).keypress(function(e){
        var eCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
        if (eCode == 13){
        	search_submit();
        	return false;
        }
	});
	/*
	$("form[name='search_form']").bind('submit',function(){
		search_submit();
		return false;
		
	});
	*/
	$(document).on('click','.confirm-ok', function () {
	      $.confirm('确定要清空历史数据？', function () {
	          $(".history-search .key-list").remove();
	          $.fn.cookie('cookobj',cookarr,{ expires: -1 });
	          $("#history").css({display:"none"});
	          window.location.reload();
	      });
	});
});