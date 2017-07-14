$(document).on("pageInit", "#deal", function(e, pageId, $page) {
	qrcode_box();
	loadScript(jia_url);
	$(".j-activeopen").attr("style","");
	$('.content').scrollTop(0);
	//获得默认库存
	var defaultStock=$(".spec-goodstock").text();
	//收藏


	/*轮播初始化*/
	var mySwiper = new Swiper ('.j-deal-content-banner', {

		autoplay: 3000,/*设置3秒自动播放*/
		spaceBetween: 10,/*图间间隔10px*/
		onSlideChangeStart: function(swiper){/*回调函数：开始变化*/
			slideIndex();
		}
	});


	/*
	 *初始化轮播分页器
	*/
	slideIndex();


	/*
	 *初始化商家标签区是否显示更多图标
	*/
	setFuliIcon();


	/*
	 *显示更多商家标签与商家优惠
	 *用户点击显示区域，下拉显示更多信息，再次点击收起更多信息
	 *区域标识，用于区分商家标签与商家优惠  1：商家优惠   2：商家标签
	*/
	$(".j-activeopen").click(function(){
		var rel = $(this).attr("rel");//区域标识

		if(rel == 1){
			var childlengh = $(this).children("li").length;
		}else if (rel == 2) {
			var allliwidth = 0;
			$(this).children("li").each(function(){
				allliwidth += (parseInt($(this).width()) + parseInt($(this).css("margin-right").replace("px","")));
			});

			var ulwidth = $(this).width();
			var childlengh = Math.ceil(allliwidth / ulwidth);
		}

		var thisheight = $(this).height();
		var childheight = $(this).children("li").height();
		var childmargin = parseInt($(this).children("li").css("margin-top").replace("px",""));
		if(childlengh > 1){
			if($(this).hasClass("isClick")){
				$(this).removeClass("isClick");
				$(this).height(childheight + childmargin * 2);
			}else{
				$(this).addClass("isClick");
				$(this).height((childheight * childlengh)  + (childmargin * (childlengh + 1)));
			}
		}
	});


	/*
	 *显示当前商家更多团购信息
	 *用户点击显示区域，下拉显示更多信息，再次点击收起更多信息
	*/

	$(".j-tuan-showMore").click(function(){
		var childheight = $(this).parent().children(".tuan-list").children("li").height();  //子项高度，用于计算更多高度
		var childlengh = $(this).parent().children(".tuan-list").children("li").length;     //子项个数，用于计算更多高度

		if (childlengh > 1) {
			if($(this).hasClass("isClick")){
				$("#other").html($("#other").attr("content"));
				$(this).removeClass("isClick");
				$(this).parent().children(".tuan-list").height(childheight);
			}else{
				$("#other").html("收起");
				$(this).addClass("isClick");
				$(this).parent().children(".tuan-list").height(childheight * childlengh);
			}
		}
	});

	/*
	 *tab切换时下划线跟随
	*/
	var t_height=$(".m-head-nav").height();
	var s_height=$(".deal-detail").offset().top;
	$(".j-tab-link").click(function(){
        var $me=$(this);
		var type = $(this).parent(".tab-list").attr("data-type");
		var rel = parseInt($(this).attr("rel"));
		if(rel == 0){
			$(".content").scrollTo({toT:0});
            tab_lick_callback($me,type,rel);
        }
        else if (rel == 1) {
			$(".content").scrollTo({toT:s_height-t_height});
            tab_lick_callback($me,type,rel);
        }
        else{
            tab_lick_callback($me,type,rel);
        }
	});
    /**
     * 异步加载点评列表
     */
    function ajax_load_tab3(){
        $.post(get_dp_detail_url,"",function(data){
           var $html=$(data);
           if($html.length){
               $("#tab3").html($html.find("#tab3").html());
               $("#dp_list_click").html($html.find("#dp_list_click").html());
           }
        });
    }
    ajax_load_tab3();

    function tab_lick_callback($me,type,rel){
        $(".j-tab-link").removeClass("active");
        $me.addClass("active");
        setTablineLeft($me.parent(),type,rel);
    }

	$(".j-detail").live("click",function(){
		var index = $(this).attr("data");
		var type = $(this).attr("data-type");
		$(".native-scroll").scrollTop(0);
		setTablineLeft($(".tab-list"),type,index);
		$(".tab-link").eq(index).addClass("active");
	});
    /**
     * 加载推荐列表
     */
    function load_recomend_data(){
        $.get(get_recommend_data_url,"",function(data){
            var html=$(data).html();
            if(html){
                $("#recommend_data").html(html);
            }
        });
    }
    load_recomend_data();
	/*倒计时*/
	leftTimeAct();
	
	var leftTimeObj = setInterval(leftTimeAct,1000);
	function leftTimeAct(){
		var leftTime = parseInt($(".AdvLeftTime").attr("data"));
		
		if(leftTime > 0)
		{
			var day  = parseInt(leftTime / 24 /3600);
			var hour = parseInt((leftTime % (24 *3600)) / 3600);
			var min  = parseInt((leftTime % 3600) / 60);
			var sec  = parseInt((leftTime % 3600) % 60);
			if(day<10){
				day="0"+day;
			}
			if(hour<10){
				hour="0"+hour;
			}
			if(min<10){
				min="0"+min;
			}
			if(sec<10){
				sec="0"+sec;
			}
			$(".AdvLeftTime").find(".day").html(day);
			$(".AdvLeftTime").find(".hour").html(hour);
			$(".AdvLeftTime").find(".min").html(min);
			$(".AdvLeftTime").find(".sec").html(sec);
			leftTime--;
			$(".AdvLeftTime").attr("data",leftTime);
		}
		else{
			$(".AdvLeftTime").html('团购已结束');
			
			clearInterval(leftTimeObj);
		}
	}


	/*
	 *底部加入购物车按钮
	*/
	$(".j-addcart").click(function(){
		$(".j-flippedout-close").attr("rel","spec");
		$(".j-spec-choose-close").attr("rel","spec");
		$(".flippedout-spec").addClass("showflipped").addClass("z-open");
		$(".spec-choose").addClass("z-open");
		$(".spec-btn-list").addClass("isAddCart");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
	});

	init_addcart();
	/*
	 *底部立即购买按钮
	 *如未在规格选择按钮选择完所有属性，将规格选择窗口关闭，再次点击购买按钮则会再次弹出规格选择窗口
	 *如果在规格选择窗口选择完所有属性，则进行购买操作，不再弹出规格选择窗口
	 */
	$(".j-nowbuy").click(function(){
		if(is_login==0){
			if(app_index=="app"){
				App.login_sdk();
			}else{
				$.router.load(login_url, true);
			}
			return false;
		}
		var data_num = $(".choose-list").length;//获取属性个数
			//  未选择完商品属性，执行弹出规格选择窗口
			$(".j-flippedout-close").attr("rel","spec");
			$(".j-spec-choose-close").attr("rel","spec");
			$(".flippedout-spec").addClass("showflipped").addClass("z-open");
			$(".spec-choose").addClass("z-open");
			$(".totop").addClass("vhide");//隐藏回到头部按钮
	});
	$(".nowbuy").click(function(){
		var data_num = $(".choose-list").length;//获取属性个数
		//var choose_num = $(".good-specifications span em").length; //获取已选属性个数
		var choose_num = $(".flippedout-spec .spec-goodspec em.choose_item").length; //获取已选属性个数
		if (choose_num < data_num) {
			//  未选择完商品属性，执行弹出规格选择窗口
			$.toast("请选择商品规格");
		}else{
			// 已经选择完商品属性，执行购买操作
			now_buy=1;
			$("#goods-form").submit();
		}
	});
	$(".isOk,a.joincart").click(function(){
		var data_num = $(".choose-list").length;//获取属性个数
		//var choose_num = $(".good-specifications span em").length; //获取已选属性个数
		var choose_num = $(".flippedout-spec .spec-goodspec em.choose_item").length; //获取已选属性个数
		if (choose_num < data_num) {
			//  未选择完商品属性，执行弹出规格选择窗口
			$.toast("请选择商品规格");
		}else{
			// 已经选择完商品属性，执行购买操作
			$("input[name='type']").val("1");
			now_buy=0;
			$("#goods-form").submit();
		}
	});

	/*
	 *规格选择窗口 加减按钮事件
	 */
	$(".flippedout-spec").on('click',".j-add-miuns",function(){
		fun_add_miuns($(this));

		var max=parseInt($(this).attr("max-num"));
		//alert($(".numplusminus").val());
		if(max>=0 && parseInt($(".numplusminus").val())>=max){
			$(this).attr("class","numadd add-miuns j-add-miuns j-add isUse");
			$(".numplusminus").val(max);
		}else{
			setSpecgood();
		}
	});
	$(".choose-list .j-choose").click(function(){
		if($(this).hasClass("active")){ //点击已选择属性，则取消选择
			$(this).removeClass("active");
			$(this).parent().siblings(".spec-tit").addClass("unchoose");
			setSpecgood();
		}else if(!$(this).hasClass("isOver")){
			//判断是否是无库存属性，
			//如果不是无库存则正常选择，无库存属性不做任何操作
			$(this).siblings(".j-choose").removeClass("active");
			$(this).addClass("active");
			$(this).parent().siblings(".spec-tit").removeClass("unchoose");
			setSpecgood();
		}
		var data_value= $(".j-choose.active").attr("data-value");
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
		}
	});





	setSpecgood();
	function setSpecgood() {
		if($(".unchoose").length != 0){
			$(".spec-goodspec").empty();
			$(".spec-goodspec").append("请选择");
			$(".spec-goodstock").text(defaultStock);
			$(".spec-goodprice").text("￥"+deal_price.toFixed(2));
			$("input[name='max_bought']").val("0");
			$(".spec-btn-list").removeClass("isNo");
			$(".spec-btn-list div.noStock").text("确定");
			$(".unchoose").each(function(){
				// 选择<em></em>
				$(".spec-goodspec").append("<em>&nbsp;&nbsp;" + $(this).html() + "</em>");
			});
		}else{
			$(".spec-goodspec").empty();
			$(".spec-goodspec").append("已选择");
			$(".j-choose.active").each(function(){
				$(".spec-goodspec").append("<em class='choose_item'>&nbsp;&nbsp;" + $(this).attr("data-value") + "</em>");
			});
			//开始计算属性库存
			//var pirce=parseFloat(deal_price);
			//$(".choose-list .active").each(function(){
			//	pirce+=parseFloat($(this).attr("pirce"));
			//	$(".spec-goodprice").text("￥"+pirce.toFixed(2));
			//});

			if($(".choose-list").length!=0)
			init_buy_ui();//检测库存
			init_submit_btn_status();
		}
	}

	//库存检测-更新面板-改变按钮状态
	function init_buy_ui(){
			var is_stock = true;      //库存是否满足
			var stock = deal_stock;   //无规格时的库存数
			var deal_show_price = deal_price;
			var deal_show_buy_count = deal_buy_count;
			var deal_remain_stock = -1;  //剩余库存 -1:无限

			var attr_checked_ids = []; // 定义一个空数组
			var txt = $('.j-choose.active'); // 获取所有选中对象
			for (var i = 0; i < txt.length; i++) {
				attr_checked_ids.push($('.j-choose.active').eq(i).attr("data-id")); // 将文本框的值添加到数组中
			}
			var attr_checked_ids = attr_checked_ids.sort(); //排序
			var attr_checked_ids_str = attr_checked_ids.join("_");//转字符串 _ 分割
			var attr_spec_stock_cfg = deal_attr_stock_json[attr_checked_ids_str];
			if(attr_spec_stock_cfg)
			{
				deal_show_buy_count = attr_spec_stock_cfg['buy_count'];
				stock = attr_spec_stock_cfg['stock_cfg'];
				$(".spec-goodprice").text("￥"+(parseFloat(deal_price)+parseFloat(attr_spec_stock_cfg['price'])).toFixed(2));
			}
			else
			{//单个属性库存
				var has_stock_attr = false;
				for(var k=0;k<attr_checked_ids.length;k++)
				{
					var key = attr_checked_ids[k];
					attr_spec_stock_cfg = deal_attr_stock_json[key];
					if(attr_spec_stock_cfg)
					{
						stock = attr_spec_stock_cfg['stock_cfg'];
						has_stock_attr = true;
						break;
					}
				}
				if(!has_stock_attr)
				stock = -1;
			}
			//判断库存是否大于0
			//更新库存显示
			//判断库存，并更新数量显示
			//判断库存是否小于最小购买量，表示库存不足
			if(stock>0){
				$(".spec-goodstock").text("库存:"+stock+"件");
				$(".j-add-miuns").attr("max-num",stock);
				var num=parseInt($(".numplusminus").val());
				//alert(num);
				if(num>stock){
					$(".numplusminus").val(stock);
				}else if(num==0){
					$(".numplusminus").val(1);
				}
			}else{
				if(stock==-1){
					$(".spec-goodstock").text("库存:不限");
					$(".j-add-miuns").attr("max-num",100);
				}
				else{
					$(".spec-goodstock").text("库存:0 件");
					$(".j-add-miuns").attr("max-num",0);
					$(".numplusminus").val(0);
				}
			}
			$("input[name='max_bought']").val(stock);


	}
	//初始化购物车等相关提交按钮状态
	function init_submit_btn_status(){

			var is_stock=true;
			var deal_remain_stock=parseInt($("input[name='max_bought']").val());
			var buy_num=parseInt($("input[name='num']").val());
			var str='';
			if(deal_remain_stock>=0)
			{
                   if(buy_num>deal_remain_stock)
				{
					is_stock = false;
					str="库存不足";
				}
				else if(deal_user_max_bought>0&&buy_num>deal_user_max_bought)
				{
					is_stock = false;
					str="每单最多购买"+deal_user_max_bought+"份";
				}
			}
			else
			{
                   if(deal_user_max_bought>0&&buy_num>deal_user_max_bought)
				{
					is_stock = false;
					str="每单最多购买"+deal_user_max_bought+"份";
				}
			}
			//alert(11);
			if(is_stock){
				$(".spec-btn-list").removeClass("isNo");
			}else{
				$(".spec-btn-list").addClass("isNo");
				$(".spec-btn-list div.noStock").text(str);
			}

	}


	/*
	 *底部收藏按钮
	 *如果已经收藏则执行以下操作，否则本阶段不执行操作
	 */
	 $(".j-collection").click(function(){
		var is_del = $(this).attr("data-isdel");
		if(is_del == 1){
			//打开取消框
			$(".flippedout").addClass("z-open");
			$(".flippedout").addClass("showflipped");
			$(".cancel-shoucan").addClass("z-open");
		}else{
			if(is_login==0){
				if(app_index=="app"){
					App.login_sdk();
				}else{
					$.router.load(login_url, true);
				}
			}else{
				deal_add_collect(deal_id);
			}
		}
	});

	$(".j-head-collect").on("click",function(){
		var is_del = $(this).attr("data-isdel");
		$(".cancel-shoucan").attr("data-ishead",1);
		if(is_del == 1){
		 	//打开取消框
			$(".cancel-shoucan").addClass("z-open");
		}else{
			if(is_login==0){
				if(app_index=="app"){
					App.login_sdk();
				}else{
					$.router.load(login_url, true);
				}
			}else{
				deal_add_collect(deal_id);
			}
		}
	});

	/*
	 *取消收藏按钮弹出后的取消
	*/

	$(".cancel-shoucan .j-cancel").click(function(){
		var is_head = $(".cancel-shoucan").attr("data-ishead");
		if(is_head != 1){
			$(".flippedout").removeClass("z-open");
			$(".flippedout").removeClass("showflipped");
			$(".cancel-shoucan").removeClass("z-open");
		}else{
			$(".cancel-shoucan").removeClass("z-open");
			$(".cancel-shoucan").attr("data-ishead",0);
		}
	});

	/*
	 *取消收藏按钮弹出后的确认
	*/

	$(".cancel-shoucan .j-yes").click(function(){
		var is_head = $(".cancel-shoucan").attr("data-ishead");
		deal_del_collect(deal_id);
		if(is_head != 1){
			$(".flippedout").removeClass("z-open");
			$(".flippedout").removeClass("showflipped");
			$(".cancel-shoucan").removeClass("z-open");
		}else{
			$(".cancel-shoucan").removeClass("z-open");
			$(".cancel-shoucan").attr("data-ishead",0);
			$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
			$(".m-nav-dropdown").removeClass("showdropdown");
			$(".nav-dropdown-con").removeClass("dropdown-open");
		}
	});


	// 评价页滚动加载
	var stop=true;
	//var ajax_url=ajax_url;
	function ajax_dp_list(){
		var page=2;
		var page_total = 0;
		var pageload=$(".page-load");
		if (pageload.length==0) {
			var loadhtml="<div class='page-load hide'><span class='loading'>"+"</span></div>"
			$(".j-ajaxlist").append(loadhtml);
		};
		$(document).on('infinite',function() {

			if ($("#tab3").hasClass("active")) {
			$(".page-load").removeClass("hide");
			    if(stop==true){ 
			        stop=false; 
			        var query = new Object();
			        query.data_id = deal_id;
			        query.page = page;
			        query.act="ajax_dp_list";
			        query.dpajax = 1;
			        $.ajax({
		                url: ajax_url,
		                data: query,
		                type: "POST",
		                dataType: "json",
		                success: function (obj) {
		                	if (obj.html != '') {
		                		$(".page-load span").removeClass("loaded").addClass("loading").html("");
			                    $(".j-ajaxadd").append(obj.html);    
			                    stop=true;
			                    page++;
		                	} else {
		                		$(".page-load span").removeClass("loading").addClass("loaded").html("拉到底部啦~");
		                	}
		                },
		                error: function() {
		                    $(".page-load span").html("网络被风吹走啦~");
		                }
			        });
			    } else{
			    	$(".page-load span").removeClass("loading").addClass("loaded").html("拉到底部啦~");
			    }

			};
		});
	}

	if ($('.comment-tit').length == 2) {
		ajax_dp_list();
	}

	// 小能
	$('.xnOpenSdk').bind('click', function() {
		if (app_index != 'app') {
			return;
		}
		if(is_login==0){
			App.login_sdk();
			return false;
		}
		var xnOptionsObj = {
			goods_id:deal_id,
			goods_showURL:$(this).attr('goods_showURL'),
			goodsTitle: $(this).attr('goodsTitle'),
			goodsPrice: $(this).attr('goodsPrice'),
			goods_URL: $(this).attr('goods_URL'),
			settingid: $(this).attr('settingid'),
			appGoods_type: '3',
		};
		xnOptions = JSON.stringify(xnOptionsObj);
		try {
			App.xnOpenSdk(xnOptions);
		} catch (e) {
			$.toast(e);
		}
	})
});




function deal_del_collect(id){
		var query = new Object();
		query.id = id;
		query.act = "del_collect";
		$.ajax({
			url: ajax_url,
			data: query,
			dataType: "json",
			type: "post",
			success: function(obj){
				if(obj.status==0 && obj.user_login_status==0){
					$.alert(obj.info,function(){
						window.location.href=obj.jump;
					});
				}
				if(obj.status == 1){
					$.toast(obj.info);	
					$(".j-collection").attr("data-isdel",0);
					$(".j-head-collect").attr("data-isdel",0);
					$("i.icon-collection").removeClass("isCollection");
					if(obj.collect_count>0){
						$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+obj.collect_count+"</em></div>");
					}else{
						$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
					}
				}
			},
			error:function(ajaxobj)
			{
//						if(ajaxobj.responseText!='')
//						alert(ajaxobj.responseText);
			}
		});
	}
	function deal_add_collect(id){
		var query = new Object();
		query.id = id;
		query.act = "add_collect";
		$.ajax({
			url: ajax_url,
			data: query,
			dataType: "json",
			type: "post",
			success: function(obj){
				if(obj.status==0 && obj.user_login_status==0){
					$.toast("请先登录");
					setTimeout(function(){
						window.location.href=obj.jump;
					},1000);
				}
				if(obj.status == 1){
					$(".j-collection").attr("data-isdel",1);
					$(".j-head-collect").attr("data-isdel",1);
					$("i.icon-collection").addClass("isCollection");
					$.toast(obj.info);	
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+obj.collect_count+"</em></div>");
					$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
					$(".m-nav-dropdown").removeClass("showdropdown");
					$(".nav-dropdown-con").removeClass("dropdown-open");
				}
			},
			error:function(ajaxobj)
			{
//						if(ajaxobj.responseTexst!='')
//						alert(ajaxobj.responseText);
			}
		});
	}

/*
 *初始化商家标签区是否显示更多图标
 *循环遍历累加每个子项的宽度，如果大于内容区域大小则显示更多图标
*/
function setFuliIcon(){
	var ulwidth = $(".shop-fuli").children(".j-activeopen").width();//内容区域宽度
	var allliwidth = 0;//内容宽度，循环遍历累加每个子项的宽度
	$(".shop-fuli").children(".j-activeopen").children("li").each(function(){
		allliwidth += (parseInt($(this).width()) + parseInt($(this).css("margin-right").replace("px","")));
	});

	if(allliwidth < ulwidth){ //如果大于内容区域大小则显示更多图标
		$(".shop-fuli").children(".j-activeopen").children(".iconfont").hide();
	}
}


/*
 *自定义轮播分页器
*/
function slideIndex(){
	var index = $(".swiper-slide-active").attr("rel");
	$(".slideindex em").html(index);
}


/*
 *用于计算tab下划线移动距离
*/
function setTablineLeft(e,type,index){
	if (type == 0) {
		if(index == 1){
			var parentwidth = (e.width() / 3 * index) - 1;
		}else{
			var parentwidth = e.width() / 3 * index;
		}
	}else if (type == 1) {
		if(index > 0){
			var parentwidth = e.width() / index;
		}else{
			var parentwidth = 0;
		}
		
	}
	$('.m-head-nav .buttons-tab .tab-line').css("left",parentwidth);
}

function init_addcart()
{
	var is_lock=false;
	$("#goods-form").bind("submit",function(){
		if(is_lock) return false;
		is_lock=true;
		var is_stock=true;
		var deal_remain_stock=parseInt($("input[name='max_bought']").val());
		var buy_num=parseInt($("input[name='num']").val());
		if(deal_remain_stock>=0)
		{
			if(buy_num>deal_remain_stock)
			{
				is_stock = false;
				$.toast("库存不足");
			}
			else if(deal_user_max_bought>0&&buy_num>deal_user_max_bought)
			{
				is_stock = false;
				$.toast("每单最多购买"+deal_user_max_bought+"份");
			}
		}
		else
		{
            if(deal_user_max_bought>0&&buy_num>deal_user_max_bought)
			{
				is_stock = false;
				$.toast("每单最多购买"+deal_user_max_bought+"份");
			}
		}
		if(is_stock){
			var query = $(this).serialize();
			var action = $(this).attr("action");
			$.ajax({
				url:action,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj){
					if(obj.status==-1)
					{
						$.router.load(obj.jump, true);
					}
					else if(obj.status)
					{
						$(".cart-num").html(obj.cart_num);
						if(obj.in_cart==0){
							if(obj.jump!=""){
								
								$(".flippedout-spec").removeClass("z-open");
								$(".spec-choose").removeClass("z-open");
								$(".flippedout-spec").removeClass("showflipped");
								$(".spec-btn-list").removeClass("isAddCart");
								
								$.showIndicator();
							    setTimeout(function () {
							    	$.hideIndicator();
							    }, 2000);
								$.router.load(obj.jump, true);
							}else{
								is_lock=false;
							}
							
						}else{
							$.toast("加入购物车成功");
							$(".flippedout-spec").removeClass("z-open");
							$(".spec-choose").removeClass("z-open");
							$(".flippedout-spec").removeClass("showflipped");
							$(".spec-btn-list").removeClass("isAddCart");
							setTimeout("$('.flippedout').removeClass('showflipped')",300);
							$('.cart-num').removeClass('hide');
							if(now_buy==1){
								$.router.load(cart_url, true);
							}else{
								is_lock=false;
							}
						}
						
					}
					else
					{
						$.alert(obj.info);
						is_lock=false;
					}
				},
				error:function(o){
					$.alert(o.responseText);
					is_lock=false;
				}
			});
		}else{
			is_lock=false;
		}
		
		return false;
	});
}

