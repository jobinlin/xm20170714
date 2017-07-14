$(document).on("pageInit", "#dealgroup", function(e, pageId, $page) {
	$(".goods-check").click(function() {
		if ($(this).find(".iconfont").hasClass('active')) {
		$(this).find(".iconfont").removeClass('active');
		init_price(main_id);
		} else {
		$(this).find(".iconfont").addClass('active');
		init_price(main_id);
		}
	});
	$(".j-open-choose").unbind( "click" );
	$(".j-open-choose").click(function(){
		//alert($(this).attr("data-id"));
		$(".j-flippedout-close").attr("rel","spec");
		$(".j-spec-choose-close").attr("rel","spec");
		$(".flippedout").addClass("showflipped");
		$(".spec-choose[data-id='"+$(this).attr("data-id")+"']").children(".j-flippedout-close").addClass("showflipped");
		$(".flippedout").addClass("z-open");
		$(".spec-choose[data-id='"+$(this).attr("data-id")+"']").addClass("z-open");
	});

	$(".j-spec-choose-close,.j-flippedout-close").unbind( "click" );
	
	
	$(".choose-list .j-choose").click(function(){
		var dataid=$(this).closest(".spec-choose").attr("data-id");
		if($(this).hasClass("active")){ //点击已选择属性，则取消选择
			$(this).removeClass("active");
			$(this).parent().siblings(".spec-tit").addClass("unchoose");
			$(this).closest(".choose-part").removeAttr("data-value");
			setSpecgood(dataid);
			init_price(main_id);
		}else if(!$(this).hasClass("isOver")){
			//判断是否是无库存属性，
			//如果不是无库存则正常选择，无库存属性不做任何操作
			$(this).siblings(".j-choose").removeClass("active");
			$(this).addClass("active");
			$(this).parent().siblings(".spec-tit").removeClass("unchoose");
			$(this).closest(".choose-part").attr("data-value",$(this).attr("data-value"));
			setSpecgood(dataid);
			init_price(main_id);
		}
		/*var data_value= $(".j-choose.active").attr("data-value");
		var data_id= $(this).attr("data-id");
		$(this).parent().siblings("input.spec-data").val(data_id);
		var data_value = []; // 定义一个空数组
		var txt = $('.j-choose.active'); // 获取所有文本框
		for (var i = 0; i < txt.length; i++) {
			data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
		}

		if (txt.length == 0) {//非初始化状态时，未选择属性页面操作区内容同步规格选择窗口内容
			$(".good-specifications span").empty();
			$(".good-specifications span").removeClass("isChoose");
			$(".good-specifications span").html($(".spec-goodspec").html());
		}else{//将已选择属性显示在页面操作区
			$(".good-specifications span").empty();
			$(".good-specifications span").addClass("isChoose");
			$(".good-specifications span").append("<i class='gray'>已选规格：</i>");
			$.each(data_value,function(i){
				$(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
				//传值可以考虑更改这里
				//$(".spec-data").attr("data-id-str"+[i],data_value[i]);
			});
		}*/
	});
	
	$(".j-spec-choose-close,.j-flippedout-close,.goods-confirm").click(function(){
		var id=$(".spec-choose.z-open").attr("data-id");
		//$("a.j-open-choose[data-id='"+id+"'] span").empty();
		/*
		if($(".spec-choose[data-id='"+id+"']").find(".unchoose").length != 0){
			$("a.j-open-choose[data-id='"+id+"'] span").addClass("defult");
			$("a.j-open-choose[data-id='"+id+"'] span").text("选择商品属性");
		}else{
			$(".spec-choose[data-id='"+id+"']").find(".j-choose.active").each(function(){
				$("a.j-open-choose[data-id='"+id+"'] span").append( $(this).html() + "&nbsp;");
				$("a.j-open-choose[data-id='"+id+"'] span").removeClass("defult");
			});
		}*/
		$("a.j-open-choose[data-id='"+id+"']").parent().siblings("span").empty();
		var stock=parseFloat($(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").attr("data-stock"));
		if(stock==0){
			$.toast("库存不足");
			$("a.j-open-choose[data-id='"+id+"']").parent().siblings("span").append("<em>&nbsp;&nbsp;库存不足</em>");
		}
			
		$(".flippedout").removeClass("z-open");
		$(".spec-choose").removeClass("z-open");
		$(".j-flippedout-close").removeClass("showflipped");
		$(".spec-btn-list").removeClass("isAddCart");
		$(".nav-dropdown-con").removeClass("dropdown-open");
		$('.flippedout').removeClass('showflipped');
		$(".j-flippedout-close").children(".iconfont").removeClass("jump");
		
	});
	function setSpecgood(id) {
		if($(".spec-choose[data-id='"+id+"']").find(".unchoose").length != 0){
			$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").empty();
			$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").append("请选择");
			//$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text(defaultStock);
			$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").text($(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").attr("data-text"));
			var stock=parseFloat($(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").attr("stock"));
			if(stock>=0)
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text("库存:"+stock+"件");
			else
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text("库存:不限");
			$(".spec-choose[data-id='"+id+"']").find(".unchoose").each(function(){
				// 选择<em></em>
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").append("<em>&nbsp;&nbsp;" + $(this).html() + "</em>");
			});
			$("a.j-open-choose[data-id='"+id+"'] span").addClass("defult");
			$("a.j-open-choose[data-id='"+id+"'] span").text("选择商品属性");
			$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").text("¥"+$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").attr("data-price"));
			$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").attr("data-value",$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").attr("data-price"));
		}else{
			$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").empty();
			$("a.j-open-choose[data-id='"+id+"'] span").empty();
			$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").append("已选择");
			$(".spec-choose[data-id='"+id+"']").find(".j-choose.active").each(function(){
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodspec").append("<em>&nbsp;&nbsp;" + $(this).html() + "</em>");
				$("a.j-open-choose[data-id='"+id+"'] span").append( $(this).html() + "&nbsp;");
				$("a.j-open-choose[data-id='"+id+"'] span").removeClass("defult");
			});
			
			var pirce=parseFloat($(".spec-choose[data-id='"+id+"']").find(".or_pirce").val());
			
			//$(".spec-choose[data-id='"+id+"']").find(".choose-list .active").each(function(){
			//	pirce+=parseFloat($(this).attr("pirce"));
			//	$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").text("￥"+pirce.toFixed(2));
			//	$(".price[price-id='"+id+"']").attr("data-value",pirce.toFixed(2));
			//	$(".price[price-id='"+id+"']").html("￥"+pirce.toFixed(2));
			//});
			//开始计算属性库存

			init_buy_ui(id);//检测库存
			
			//init_submit_btn_status();
		}
	}
	init_price(main_id);
	function init_price(main_id){
		var main_data=$("p[price-id='"+main_id+"'].price");
		var price=parseFloat(main_data.attr("data-value"))*parseFloat(main_data.attr("data-num"));
		
		$(".deal").each(function(){
			// 选择<em></em>
			if($(this).hasClass("active")){
				var id=$(this).attr("data-id");
				var part_data=$(this).parent().parent().find("p.price");
				price=price+parseFloat(part_data.attr("data-value"))*parseFloat(part_data.attr("data-num"));
			}
		});
		
		$(".dealgroup-bar p.total-price").eq(1).html("<em>&yen;"+price.toFixed(2)+"</em>");
	}
	//库存检测-更新面板-改变按钮状态
	function init_buy_ui(id){
			//var is_stock = true;      //库存是否满足
			//var stock = deal_stock;   //无规格时的库存数
			//var deal_show_price = deal_price;
			//var deal_show_buy_count = deal_buy_count;
			//var deal_remain_stock = -1;  //剩余库存 -1:无限

			var attr_checked_ids = []; // 定义一个空数组
			var txt = $(".spec-choose[data-id='"+id+"']").find('.j-choose.active'); // 获取所有选中对象
			for (var i = 0; i < txt.length; i++) {
				attr_checked_ids.push($(".spec-choose[data-id='"+id+"']").find('.j-choose.active').eq(i).attr("data-value")); // 将文本框的值添加到数组中
			}
			var attr_checked_ids = attr_checked_ids.sort(); //排序
			var attr_checked_ids_str = attr_checked_ids.join("_");//转字符串 _ 分割
			var attr_spec_stock_cfg = deal_attr_stock_json[id][attr_checked_ids_str];
			
			if(attr_spec_stock_cfg)
			{
				stock = attr_spec_stock_cfg['stock_cfg'];
				var price=(parseFloat($(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").attr("data-price"))+parseFloat(attr_spec_stock_cfg['price'])).toFixed(2);
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").text("￥"+price);
				$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").text("¥"+price);
				$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").attr("data-value",price);
			}
			else
			{//单个属性库存
				var has_stock_attr = false;
				for(var k=0;k<attr_checked_ids.length;k++)
				{
					var key = attr_checked_ids[k];
					attr_spec_stock_cfg = deal_attr_stock_json[id][key];
					if(attr_spec_stock_cfg)
					{
						stock = attr_spec_stock_cfg['stock_cfg'];
						var price=(parseFloat($(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").attr("data-price"))+attr_spec_stock_cfg['price']).toFixed(2);
						$(".spec-choose[data-id='"+id+"']").find(".spec-goodprice").text("￥"+price);
						$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").text("¥"+price);
						$("a.j-open-choose[data-id='"+id+"'] span").parent().siblings("p.price").attr("data-value",price);
						has_stock_attr = true;
						break;
					}
				}
				if(!has_stock_attr)
				stock = -1;
			}
			console.log(stock);
			//判断库存是否大于0
			//更新库存显示
			//判断库存，并更新数量显示
			//判断库存是否小于最小购买量，表示库存不足
			if(stock>0){
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text("库存:"+stock+"件");
				$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").attr("data-stock",stock);
				$("a.j-open-choose[data-id='"+id+"']").attr("is-stock",1);
			}else{
				if(stock==-1){
					$("a.j-open-choose[data-id='"+id+"']").attr("is-stock",1);
					$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text("库存:不限");
					$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").attr("data-stock","-1");
				}else{
					$("a.j-open-choose[data-id='"+id+"']").attr("is-stock",0);
					$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").text("库存:0件");//$.alert("库存不足");
					$(".spec-choose[data-id='"+id+"']").find(".spec-goodstock").attr("data-stock","0");
				}
					
			}
			

	}
	
});
/**
 * 合并购买
*/
function relateBy(){
	var idArray = [];
	var idnumArray = [];
	var dealAttrArray = {};
	var is_attr = true;
	idArray.push(main_id);
	idnumArray[main_id]=main_num;
	$(".deal").each(function(){
		// 选择<em></em>
		if($(this).hasClass("active")){
			
			idArray.push(parseFloat($(this).attr("data-id")));
			idnumArray[$(this).attr("data-id")]=$(this).attr("data-num");
		}
	});
	$(".spec-choose").each(function(){
		// 选择<em></em>
		var obj=this;
		var id=parseFloat($(obj).attr("data-id"));
		dealAttrArray[id]={};
		$(this).find(".choose-part").each(function(){
			if(isNaN(parseFloat($(this).attr("data-value")))&&$.inArray(id, idArray)!=-1){
				$.toast("规格未选择");
				is_attr = false;
			}
				
			dealAttrArray[parseFloat($(obj).attr("data-id"))][parseFloat($(this).attr("data-id"))]=parseFloat($(this).attr("data-value"));
			
		});
	});
	var is_stock=$(".main-goods").find(".j-open-choose").attr("is-stock");
	$(".deal").each(function(){
		// 选择<em></em>
		if($(this).hasClass("active")){
			is_stock=$(this).parent().parent().find(".j-open-choose").attr("is-stock");
			if(is_stock=="0")return false;
		}
	});
	if(is_stock=="0"){
		$.toast("库存不足");
		return false;
	}
	
	if(!is_attr){
		return false;
	}
	$.ajax({
		url:ajax_url,
		data:{'id':idArray,'dealAttrArray':dealAttrArray,'idnumArray':idnumArray},
		dataType:"json",
		type:"post",
		global:false,
		success:function(obj){
			if(obj.status==-1)
			{
				location.href = obj.jump;
			}
			else if(obj.status)
			{
				if($("input[name='type']").val()!=1){
					if(obj.jump!="")
					location.href = obj.jump;
				}else{
					$.toast("加入购物车成功");
					$(".j-spec-choose-close,.j-flippedout-close").click();
				}
				
			}
			else
			{
				//$.toast(obj.info);
				//console.log(obj.info);
				//$(obj.info).each(function(index){
				//    alert(this);
				//});
				 $.each(obj.info,function(n,value){
			            //alert(n);
					 	//$.toast(value);
			            //alert(value);
					 	//setTimeout(function () { 
					    //}, 2000);
					 $("span.tis[data-id='"+n+"']").html("<em>&nbsp;&nbsp;"+value+"</em>");
			        });
			}
		}
	});
	
	
}