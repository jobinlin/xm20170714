$(function(){
	//初始化地图
/*	ini_map();
	//加载城市
	init_select_city();*/
	//加载地区
	load_area_list_box();
	//子分类载入
	init_sub_cate();	

	
	
	/*页面事件绑定调用*/
	$("select[name='cate_id']").bind("change",function(){
		init_sub_cate();	//子分类载入
	});
	/*切换城市*/
	$("button.select_city_btn").bind("click",function(){
		if($(".city_list_box").is(":hidden")){
			$(document).bind("click",function(e){ 
				var obj = e.srcElement ? e.srcElement : e.target;
				var target = $(obj); 
				if(target.closest(".city_list_box").length == 0 && target.closest(".city_select_box").length == 0){ 
					$(".city_list_box").hide();
					$(document).unbind("click");
				}else{
					$(".city_list_box").show();
				}
			});
		}else{
			$(".city_list_box").hide();
			$(document).unbind("click");
		}
		
	});
	$(".first-cate").bind('click', function() {
		$(".first-cate").removeClass('active');
		$(this).addClass('active');
		$(".sub-cate-wrap").hide();
		$(".sub-cate-wrap").eq($(this).parent().index()).show();
		console.log($(this).parent().index());
		$(".sub-cate").removeClass('active');
	});
	$(".sub-cate").bind('click', function() {
		$(this).toggleClass('active');
	});

	
	



	$("form[name='location_publish_form']").submit(function(){
		
		var form = $("form[name='location_publish_form']");
		var name = $("input[name='name']").val();
		if($.trim(name)==''){
			$.showErr("请输入门店名称");
			return false;
		}
		$(".sub_form_btn").html('<button class="ui-button" rel="disabled" type="button">提交</button>');
		init_ui_button();
		var query = $(form).serialize();
		var url = $(form).attr("action");
		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$(".sub_form_btn").html('<button class="ui-button " rel="orange" type="submit">确认提交</button>');
					init_ui_button();
					$.showErr(data.info);
				}else if(data.status==1){
					$.showSuccess(data.info,function(){window.location = data.jump;});
				}
				return false;
			}
		});
		
		return false;
	});
	
	
	/*选项卡切换*/
	$(".tab_item").bind("click",function(){
		var index = $(".tab_item").index(this);
		$(".tab_item").removeClass("curr");
		$(this).addClass("curr");
		$(".con_item").removeClass("curr");
		$(".con_item").eq(index).addClass("curr");
		
		if($(".tab_item").length ==(index+1) ){
			$("div.next_form_btn").hide();
		}else{
			$("div.next_form_btn").show();
		}
	});
	/*下一页表单切换*/
	$("button.next_form_btn").bind("click",function(){
		var curr_tab_index = $(".tab_item").index($(".tab_item.curr"));
		if(($(".tab_item").length-1)>curr_tab_index){
			$(".tab_item").removeClass("curr");
			$(".tab_item").eq((curr_tab_index+1)).addClass("curr");
			$(".con_item").removeClass("curr");
			$(".con_item").eq((curr_tab_index+1)).addClass("curr");
		}
		if(($(".tab_item").length-1) == (curr_tab_index+1)){
			$("div.next_form_btn").hide();
		}else{
			$("div.next_form_btn").show();
		}
		$("html,body").animate({scrollTop:0},200);
	});
	
	
	

	
});//JQUERY END


/*地图初始化*/
function ini_map()
{
	var xpoint ='119.3';
	var ypoint ='26.1';
	if($("input[name='xpoint']").val()){
		 xpoint = $("input[name='xpoint']").val();
	}
	
	if($("input[name='ypoint']").val()){
		 ypoint = $("input[name='ypoint']").val();
	}
	draw_map(xpoint,ypoint);	
	$("#search_api").bind("click", function() {
		var api_address = $("input[name='api_address']").val();		
		var city = $(".selected_city span").html();
	    if($.trim(api_address) == '') {
			$.showErr("请先输入地址");
		} else {
			search_api(api_address,city);
		}
	});
	
	$("#container_front").hide();
	$("#cancel_btn").bind("click", function() {
		$("#container_front").hide();
	});
	$("#chang_api").bind("click", function() {
		if($("input[name='xpoint']").val()){
			 xpoint = $("input[name='xpoint']").val();
		}
		
		if($("input[name='ypoint']").val()){
			 ypoint = $("input[name='ypoint']").val();
		}
		editMap(xpoint, ypoint);
	});		
}
/**
 * 载入地区
 */
function load_area_list_box(){
	var id = $("input[name='id']").val();
	var city_id = $("input[name='city_id']").val();
	var edit_type = $("input[name='edit_type']").val();
	if(city_id>0){
		var query = new Object();
		query.act = "load_area_list_box";
		query.city_id = city_id;
		query.edit_type = edit_type;
		query.id = id;
		$.ajax({
			url:ajax_url,
			data:query,
			type:"post",
			success:function(data){
				$("#area_list").html(data);
				$(".area_box").show();
				
				return false;
			}
		});
	}
}
/**
 * 选择城市
 * @param obj
 */
function select_city(obj){
	var city_id = $(obj).attr("data");
	$(".city_item").removeClass("curr");
	$(obj).addClass("curr");
	$(".selected_city").html("<span>"+$(obj).html()+"</span>");
	$("input[name='city_id']").val(city_id);
	$(".city_list_box").hide();
	$(document).unbind("click");
	if(city_id>0){
		$(".area_box").show();
		load_area_list_box();
	}else{
		$(".area_box").hide();
	}
	
}

function init_select_city(){
	var city_id = parseInt($("input[name='city_id']").val());
	if(city_id>0){
		$(".city_item[data='"+city_id+"']").addClass("curr");
	}
	var obj = $(".city_item.curr");
	if(obj.length>0){
		var city_id = $(obj).attr("data");
		$(".selected_city").html("<span>"+$(obj).html()+"</span>");
	}
	
}





