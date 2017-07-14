$(document).on("pageInit", ".page", function(e, pageId, $page) {
totop();
colsebut();
init_ui_lazy();


if(typeof(appId) != 'undefined'){
	

	// 微信分享

	wx.config({
		  debug: false,
		  appId: appId,
		  timestamp: timestamp,
		  nonceStr: nonceStr,
		  signature: signature,
		  jsApiList: [
		    // 所有要调用的 API 都要加到这个列表中
		    'onMenuShareAppMessage',
		    'onMenuShareTimeline',
		    'onMenuShareQQ',
		    'onMenuShareWeibo',
		    'onMenuShareQZone',
		  ]
		});

		// 分享给朋友
		wx.ready(function () {

		  // 在这里调用 API
			wx.onMenuShareAppMessage({
			    title: share_title, // 分享标题
			    desc: share_content, // 分享描述
			    link: share_url, // 分享链接
			    imgUrl: imgUrl, // 分享图标
			    success: function () {
			        // 用户确认分享后执行的回调函数

			    },
			    cancel: function () {
			        // 用户取消分享后执行的回调函数

			    }
			});

			// 分享到朋友圈
			wx.onMenuShareTimeline({
			    title: share_title, // 分享标题
			    desc: share_content, // 分享描述
			    link: share_url, // 分享链接
			    imgUrl: imgUrl, // 分享图标
			    success: function () {
			        // 用户确认分享后执行的回调函数

			    },
			    cancel: function () {
			        // 用户取消分享后执行的回调函数

			    }
			});

			// 分享到qq
			wx.onMenuShareQQ({
			    title: share_title, // 分享标题
			    desc: share_content, // 分享描述
			    link: share_url, // 分享链接
			    imgUrl: imgUrl, // 分享图标
			    success: function () {
			       // 用户确认分享后执行的回调函数
			    },
			    cancel: function () {
			       // 用户取消分享后执行的回调函数
			    }
			});

			// 分享到腾讯微博
			wx.onMenuShareWeibo({
			    title: share_title, // 分享标题
			    desc: share_content, // 分享描述
			    link: share_url, // 分享链接
			    imgUrl: imgUrl, // 分享图标
			    success: function () {
			       // 用户确认分享后执行的回调函数
			    },
			    cancel: function () {
			        // 用户取消分享后执行的回调函数
			    }
			});

			// 分享到qq空间
			wx.onMenuShareQZone({
			    title: share_title, // 分享标题
			    desc: share_content, // 分享描述
			    link: share_url, // 分享链接
			    imgUrl: imgUrl, // 分享图标
			    success: function () {
			       // 用户确认分享后执行的回调函数
			    },
			    cancel: function () {
			        // 用户取消分享后执行的回调函数
			    }
			});

	        wx.error(function(res){
	            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
	           // alert("errorMSG:"+res);
	        });
		});
		

}


});
$(document).on("pageAnimationEnd", ".page", function(e, pageId, $page) {
	//alert(1);

});

//路由禁用
$.config = {
	swipePanelOnlyClose:true, // 初始化侧栏禁止滑动打开
	router: true  // 初始化禁用路由
};
// 初始化回到头部
function colsebut() {
	$(".Client").find(".close_but").bind("click",function(){
		$(".Client").hide();
		var query = new Object();
		query.act = "close_appdown";
		$.ajax({
			url:AJAX_URL,
			data:query,
			type:"POST",
			success:function(){

			},
			error:function(o){
				alert(o.responseText);
			}
		});
	});
}
/*回到顶部*/    
function totop(){
	var totophight=$(window).height() - $(".bar-tab").height() - $(".bar-nav").height() - $(".headerindex").height();
	$(document).on('click','.totop', function () {
		$(".content").scrollTo({toT:0});
	        return false;
	});	
	$(".content").scroll(function(){
	if ($(".content").scrollTop()>totophight){
	    $(".totop").show();
	}
	else
	{
	    $(".totop").hide();
	}
	});
}                                                     

/*首页导航条变化*/
function headerScroll(){  
	$(".content").scroll(function(){
		var top=$(".content").scrollTop();
		var opacity=top/100;
		if (opacity>=0.6) {
			$(".headerindex .mark").css({"opacity":0.6,"box-shadow":"0 0 0 #d82020"});
			$(".headerindex .middle a").css("background-color","rgba(255,255,255,1)");
		}else{
			$(".headerindex .mark").css({"opacity":opacity,"box-shadow":"0 2px 3px #d82020"});
			$(".headerindex .middle a").css("background-color","rgba(255,255,255,0.8)");
		};
	});
};

//以下是处理UI的公共函数
function init_ui_lazy()
{
	$.refresh_image();
	$(".content").bind("scroll", function(e){
		$.refresh_image();
	});	
	
}

//重新刷新页面数据
function refreshdata(contentArr){
	var url=window.location.href;
	var content = new Array();
	$.ajax({
		url:url,
		type:"POST",
		success:function(html)
		{	
			for (var i = 0; i < contentArr.length; i++) {
			$(contentArr[i]).html($(html).find(contentArr[i]).html());
			//console.log(contentArr[i]);
			}
		},
		error:function()
		{
			$.toast("加载失败咯~");
		}
	});
}

//无图片时
/*function noimg(){
	$("img").each(function(i,obj){
		var img = $(this);
		var ifnull=$.trim($(this).attr("src"));
			console.log(ifnull);
		if(img.attr("src")==""){
			console.log($(this).attr("src")+11);
			$(this).attr("src","no_image");
		}else{
		console.log(2);
		};
	});
}*/

/**
 * 延时加载图片
 * 
 */	
 $.refresh_image = function(){
	$("img[date-load='1']").ui_lazy({placeholder:'',no_image:no_image});
};	
$.fn.ui_lazy = function(options){
	var op = {placeholder:"",src:"",refresh:false,no_image:""};
	options = $.extend({},op, options);
	var imgs = this;	
	imgs.each(function(){
 	
		var img = $(this);			
		var windheight = $(".content").height();
		var imgoffset = img.offset().top;
		var screenheight = $(".content").height();
		if(img.attr("date-load")||options.refresh)
		{
		    if(windheight>=imgoffset)
		    {			 
		    	if(options.src!=""){
		    		//img.attr("src",options.src);
			    	img.attr("src",img.attr("data-src"));
			    	img.attr("isload",true);
			    	img.attr("date-load",false);  
		    	}
		    	else{
			    	img.attr("src",img.attr("data-src"));
			    	img.attr("isload",true);
			    	img.attr("date-load",false);  
		    	}  
		    	if (img.attr("data-src") == "") {
		    		img.attr("src",no_image);
		    	};	
		    }
		}		
	});			
}	

//首页下拉加载刷新事件
var stop=true;
function init_auto_load_data(){

var page=2;
var page_total = 0;
var pageload=$(".page-load");
if (pageload.length==0) {
var loadhtml="<div class='page-load hide'><span class='loading'>"+"</span></div>"
$(".j-ajaxlist").append(loadhtml);
};
$(document).on('infinite', '.infinite-index-bottom',function() {
 // 如果正在加载，则退出

	$(".page-load").removeClass("hide");
    if(page_total>0 && page>page_total){
            stop=false;
            $(".page-load span").removeClass("loading").addClass("loaded").html("拉到底部啦~");
    }else{
    	$(".page-load span").removeClass("loading").addClass("loaded").html("正在加载更多~");
    }

    //if (!stop) return;
    if(stop==true){ 
        stop=false; 
        var query = new Object();
        query.page = page;
        query.act="load_index_list_data";

        $.ajax({
                url: INDEX_URL,
                data: query,
                type: "POST",
                dataType: "json",
                success: function (obj) {
                	if (page_total != page) {
                    $(".j-ajaxadd").append(obj.html);    
                    stop=true;
                    page++;
                    page_total = obj.page_total;
                    console.log(page_total);
                	};
                },
                error: function() {
                    $(".page-load span").html("网络被风吹走啦~");
                }
        });
    } else{
    	$(".page-load span").removeClass("loading").addClass("loaded").html("拉到底部啦~");
    }
});
}


//列表页下拉刷新
var infinite_loading = false;
function init_list_scroll_bottom(){
var pageload=$(".page-load");
if (pageload.length==0) {
var loadhtml="<div class='page-load hide'><span class=''>"+"</span></div>"
$(".j-ajaxlist").append(loadhtml);
};

	$(document).on('infinite', '.infinite-scroll-bottom',function() {
		
		if(infinite_loading)return;
		var next_dom = $(".j-ajaxlist").find("span.current").next();
		$(".page-load").removeClass("hide");
		if(next_dom.length>0){
				var url = $(".j-ajaxlist .pages").find("span.current").next().attr("href");
				$(".page-load span").removeClass("loading").addClass("loaded").html("正在加载更多~");
				infinite_loading = true;
				$.ajax({
					url:url,
					type:"POST",
					success:function(html)
					{
						$(".j-ajaxadd").append($(html).find(".j-ajaxadd").html());
						$(".j-ajaxlist .pages").html($(html).find(".j-ajaxlist .pages").html());
						infinite_loading = false;
					},
					error:function()
					{
						$(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
					}
				});
			}
			else
			{
				$(".page-load span").removeClass("loading").addClass("loaded").html("没有更多信息~");
			}
	});
}


//相同页面出现不同下拉加载使用 
//ajaxlist:执行的大结构
//ajaxadd:append进去的结构
var infinite_loading2 = false;
var infinite_callback = "";
function init_listscroll(ajaxlist,ajaxadd,infinite_loading2,callback){
    infinite_callback="";
var pageload=$(ajaxlist).find(".page-load");
if (pageload.length==0) {
var loadhtml="<div class='page-load hide'><span class='loading'>"+"</span></div>"
$(ajaxlist).append(loadhtml);
};

	infinite(ajaxlist,ajaxadd,infinite_loading2,callback);
}

function infinite(ajaxlist,ajaxadd,infinite_loading2,callback){
    infinite_callback=callback;
    $(document).off('infinite', '.infinite-scroll-bottom');
	$(document).on('infinite', '.infinite-scroll-bottom',function() {
		if(infinite_loading2)return;
		var next_dom = $(ajaxlist).find("span.current").next();
		$(ajaxlist).find(".page-load").removeClass("hide");
		if(next_dom.length>0)
			{
				var url =$(ajaxlist).find(".pages").find("span.current").next().attr("href");
				$(ajaxlist).find(".page-load span").removeClass("loading").addClass("loaded").html("正在加载更多~");
				infinite_loading2 = true;
				$.ajax({
					url:url,
					type:"POST",
					success:function(html)
					{
						$(ajaxadd).append($(html).find(ajaxadd).html());
						$(ajaxlist).find(".pages").html($(html).find(ajaxlist).find(".pages").html());
						infinite_loading2 = false;
                        if(typeof infinite_callback =="function"){
                            infinite_callback();
                        }
					},
					error:function()
					{
						$(ajaxlist).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
					}
				});
			}
			else
			{
				$(ajaxlist).find(".page-load span").removeClass("loading").addClass("loaded").html("没有更多信息~");
			}
	});
}


function share_complete(share_key){
    $.alert("分享成功");
}





$(document).on("pageInit", ".page", function(e, pageId, $page) {
	/*
	 *打开规格选择窗口
	 *触发源.j-open-choose
	*/

	$(document).on('click',".j-open-choose",function(){
		console.log(01);
		var page_id= $(".page").attr("id");
		if(page_id !="dealgroup"){
			$(".j-flippedout-close").attr("rel","spec");
			$(".j-spec-choose-close").attr("rel","spec");
			$(".flippedout-spec").addClass("showflipped").addClass("z-open");
			$(".spec-choose").addClass("z-open");
			$(".totop").addClass("vhide");//隐藏回到头部按钮
		}
	});

	/*
	 *打开下拉导航窗口
	 *触发源.j-opendropdowm
	*/
	$(document).on('click',".j-opendropdowm",function(){
		$(".j-flippedout-close").attr("rel","dropdowm");
		$(".flippedout").addClass("showflipped").addClass("dropdowm-open");
		$(".m-nav-dropdown").addClass("showdropdown");
		$(".m-nav-dropdown .nav-dropdown-con").addClass("dropdown-open");
		$(".j-flippedout-close").children(".iconfont").addClass("jump");
	});

	$(document).on('click',".j-opendropdowm-default",function(){
		console.log(0);
		$(".j-flippedout-close").attr("rel","dropdowm");
		$(".flippedout-default").addClass("showflipped").addClass("dropdowm-open");
		$(".m-nav-dropdown").addClass("showdropdown");
		$(".m-nav-dropdown .nav-dropdown-con").addClass("dropdown-open");
		$(".flippedout-default .j-flippedout-close").children(".iconfont").addClass("jump");
	});

	/*
	 *打开分享弹出窗口
	 *触发源为.j-openshare
	*/
	$(document).on('click',".j-openshare",function(){
		$(".j-flippedout-close").attr("rel","share");
		$("#boxclose_share").attr("rel","share");
		$(".flippedout").addClass("z-open").addClass("showflipped");
		$(".box_share").addClass("z-open");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
	});



	/*
	 *下拉导航点击分享操作
	 *触发源为.j-dropdown-share
	*/
	$(document).on('click',".j-dropdown-share",function(){
		$(".j-flippedout-close").attr("rel","share");
		$("#boxclose_share").attr("rel","share");
		$(".m-nav-dropdown .nav-dropdown-con").removeClass("dropdown-open");//下拉导航还原
		$(".j-flippedout-close .iconfont").removeClass("jump");
		$(".box_share").addClass("z-open");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
	});

	var imglight = new Swiper ('.img-light', {
		onSlideChangeStart: function(swiper){
			var index = $(".img-light-box .swiper-slide-active").attr("rel");
			$(".light-index .now-index").html(index);
		}
	});

	/*
	 *评论图点击显示当前评论所有图片集
	*/
	$('.page').on('click',".j-review-item,.j-comment-item",function(){
		var flag = $(this).parent(".comment-imglist").attr("rel");
		if(flag == "review"){
			var obj = "j-review-item";
		}else{
			var obj = "j-comment-item";
		}
		$(".pop-light-img").addClass("z-open-black");
		$(".light-txt").addClass("z-open");
		$(".img-light-box").addClass("z-open");
		$(".j-flippedout-close").attr("rel","light");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
		var index = 0;

		$(this).parent(".comment-imglist").children("." + obj).each(function(index){//动态为查看器添加内容
			var url = $(this).children("img").attr("data-lingtsrc");;
			index = parseInt(index) + 1;
			imglight.appendSlide('<div class="swiper-slide j-slide-img" rel="'+ index +'"><img class="j-slide-img" src="'+ url +'" width="100%"></div>');
		});
		var index = parseInt($(this).attr("data"))-1;//获取点击的是第几张图片
		imglight.slideTo(index,0);//设置查看器图片为点击的图片
		if(flag == "review"){
			var txt = $(this).parent().siblings().children(".comment-txt").html();//获取评论内容
		}else{
			var txt = $(this).parent().siblings(".comment-txt").html();//获取评论内容
		}
		var name = $(this).parent().siblings(".commenter").children().children(".username").html();//获取用户名
		console.log(txt);
		console.log(name);
		//$(".light-txt .light-con").html(txt);//设置评论内容
		//$(".light-txt .light-name .name").html(name);//设置用户名
		$(".light-index .light-count").html($(this).parent().children("." + obj).length); //设置图片索引总数
		$(".light-index .now-index").html($(this).attr("data"));//设置当前图片索引

	});

	/*
	 *为新添加的查看器图片添加点击关闭事件
	*/
	$(".swiper-wrapper").on("click",".j-slide-img",function(){
		$(".pop-light-img").removeClass("z-open-black").removeClass("showflipped");
		$(".light-txt").removeClass("z-open");
		$(".img-light-box").removeClass("z-open");
		imglight.removeAllSlides();
		$(".totop").removeClass("vhide");
	});


	/*
	 *关闭弹出层
	*/
	$(document).on("click","#boxclose_share,.j-spec-choose-close,.j-flippedout-close",function(){
		var rel = $(this).attr("rel");
		$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
		$(".cancel-shoucan").removeClass("z-open");
		if(rel == "light"){
			//关闭图片查看器
			$(".pop-light-img").removeClass("z-open-black");
			$(".light-txt").removeClass("z-open");
			$(".img-light-box .j-flippedout-close").removeClass("showflipped");
			imglight.removeAllSlides();

		}else if (rel == "spec") {
			//关闭图片规格选择器
			$(".flippedout-spec").removeClass("showflipped").removeClass("z-open");
			$(".spec-choose").removeClass("z-open");
			$(".spec-btn-list").removeClass("isAddCart");
			$(".img-light-box").removeClass("z-open");

		}else if (rel == "dropdowm") {
			//关闭下拉导航
			$(".flippedout-default").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
			$(".m-nav-dropdown").removeClass("showdropdown");
			$(".nav-dropdown-con").removeClass("dropdown-open");
			$(".j-flippedout-close .iconfont").removeClass("jump");

		}else if (rel == "share") {
			//关闭分享
			$(".box_share").removeClass("z-open");
			$("#jiathis_weixin_share").remove();
		}

		$(".totop").removeClass("vhide");
	});


});

function fun_add_miuns(e) {
	var operate = e.attr("data-operate");//获取按钮的操作 判断是执行加还是减
	var txt = e.siblings(".numplusminus");//获取当前文本框
	var txt_val = parseInt(txt.val());//获取文本框中的值，并转化为Int类型
	var max = parseInt(txt.attr("data-max"));//获取可购买的最大值
	var min = parseInt(txt.attr("data-min"));//获取可购买的最小值
	var new_val;
	if(operate == "+"){
		if (txt_val < max) {
			new_val = txt_val + 1;//当前文本框中的值小于最大可购买数，则进行+1操作
			txt.val(new_val);
			$("input[name='num']").val(new_val);

			//以下是判断加减按钮是否可用
			if (new_val == max) {
				$(".j-add").addClass("isUse");
			}else if(new_val == min){
				$(".j-miuns").addClass("isUse");
			}else{
				$(".j-add-miuns").removeClass("isUse");
			}
		}

	}else if (operate == "-") {
		if (txt_val > min) {//当前文本框中的值大于最小可购买数，则进行-1操作
			new_val = txt_val - 1;
			txt.val(new_val);
			$("input[name='num']").val(new_val);
			//以下是判断加减按钮是否可用
			if (new_val == max) {
				$(".j-add").addClass("isUse");
			}else if(new_val == min){
				$(".j-miuns").addClass("isUse");
			}else{
				$(".j-add-miuns").removeClass("isUse");
			}
		}
	}

}
$(document).on("pageInit", ".page", function(e, pageId, $page) {
	//列表类型切换
	$(".j-type-btn").click(function() {
		$(this).hide();
	});
	$("#type-cube").click(function() {
		$("#type-list").show();
		$(".m-goods-list ul").removeClass('type-cube').addClass('type-list');
	});
	$("#type-list").click(function() {
		$("#type-cube").show();
		$(".m-goods-list ul").removeClass('type-list').addClass('type-cube');
	});
});

$(document).on("pageInit", ".page", function(e, pageId, $page) {
	reset_flippedout();

	//js_ajax_load();
//专题链接跳转
	go_back();
	$(".page").on("click",".load_page",function(){
		load_page($(this));
	});

    $(".page").on("click",".load_page2",function(){
        load_page2($(this));
    });




	$(document).on("click",".load_content",function(){
		load_content($(this));
	});

	$(".header-reload").on("click",function () {

		$.showIndicator();
		window.location.reload();
	});

});
//导航切换
function nav_tab() {
	$(".m-nav-tab").on('click', '.j-nav-item', function() {
		$(".j-nav-item").removeClass('active');
		$(this).addClass('active');
		tab_line_init();
	});
}
//导航线
function tab_line_init() {
	var m_left=$(".m-nav-tab .active").find('span').offset().left;
	var s_left=$(".m-nav-tab").scrollLeft();
	var m_width=$(".m-nav-tab .active").find('span').width();
	$(".m-nav-tab").find('.tab-line').css({
		left: m_left+s_left,
		width: m_width
	});
}

function open_url(url)
{
	try{
		App.open_type('{"url":"'+url+'"}');
	}
	catch(ex)
	{
		window.open(url);
	}

	
}

//扫码回调
function js_qr_code_scan(qr_code)
{
	$.ajax({
        url:url,
        data:{"coupon_pwd":qr_code},
        type: "POST",
        dataType:"json",
        success: function (obj) {
        	if(obj.status == 0){
				$.toast(obj.info);
			}else if(obj.status == 1){
				$.router.load(obj.url, true);
				//location.href = obj.url;
			}
        },
        error: function() {
            $.toast("网络被风吹走啦~");
        }
	});
	
}


function init_sms_btn()
{
	$(".login-panel").find("button.ph_verify_btn[init_sms!='init_sms']").each(function(i,o){
		$(o).attr("init_sms","init_sms");
		var lesstime = $(o).attr("lesstime");
		var divbtn = $(o).next();
		divbtn.attr("form_prefix",$(o).attr("form_prefix"));
		divbtn.attr("lesstime",lesstime);
		if(parseInt(lesstime)>0)
		init_sms_code_btn($(divbtn),lesstime);	
	});
}

//关于短信验证码倒计时
function init_sms_code_btn(btn,lesstime)
{

	$(btn).stopTime();
	$(btn).removeClass($(btn).attr("rel"));
	$(btn).removeClass($(btn).attr("rel")+"_hover");
	$(btn).removeClass($(btn).attr("rel")+"_active");
	$(btn).attr("rel","disabled");
	$(btn).addClass("disabled");	
	$(btn).find("span").html("重新获取("+lesstime+")");
	$(btn).attr("lesstime",lesstime);
	$(btn).everyTime(1000,function(){
		var lt = parseInt($(btn).attr("lesstime"));
		lt--;
		$(btn).find("span").html("重新获取("+lt+")");
		$(btn).attr("lesstime",lt);
		if(lt==0)
		{
			$(btn).stopTime();
			$(btn).removeClass($(btn).attr("rel"));
			$(btn).removeClass($(btn).attr("rel")+"_hover");
			$(btn).removeClass($(btn).attr("rel")+"_active");
			$(btn).attr("rel","light");
			$(btn).addClass("light");
			$(btn).find("span").html("发送验证码");
		}
	});
}


//专题链接跳转
function go_back()
{
	var back_url = $(".go_back_url").attr('url');

	if($(".go_back").length > 0 && back_url!=''){
		//back_url = back_url.replace(/"/g, '\"');
		$(".go_back").attr('href',back_url);
	}
	/*
	var history_url = document.referrer;
	var reg = new RegExp("^"+ sitename ,"gim");
	if(reg.test(history_url)){
		//location.href = history_url;
		if(back_url !=''){  		
			location.href = back_url;
		}else{
			window.history.go(-1);
		}
	}else{
		location.href = sitename;
	}
	*/
}
function weixin_login()
{
	var url = wx_url;
	if(url.indexOf("?")==-1)
	{
		url+="?weixin_login=1";
	}
	else
	{
		url+="&weixin_login=1";
	}
	location.href = url;
}

function load_page(obj){
	$.showIndicator();
	$(obj).removeClass('load_page');

	var url = $(obj).attr('url');
	if($(obj).attr('str')){
		url = url+$(obj).attr('str');
	}
	var js_url = $(obj).attr('js_url');
	var callback = $(obj).attr('callback');

    $.ajax({
            url: url,
            type: "POST",
            success: function (html) {
            	//console.log(html);
            	$(".page-current").after($(html).find(".page").addClass('page-current')).removeClass('page-current');
            	$(".page-current").find(".back").addClass("close_page").removeClass("back");
            	$(".page-current").find(".go_back").addClass("close_page").removeClass("go_back");
            	
            	loadScript(js_url,callback);
            	$.hideIndicator();
            	$(obj).addClass('load_page');
            	$(".close_page").on("click",function(){
            		close_page();
            	});
            	//$.init();
            },
            error: function() {
                $.toast("网络被风吹走啦~");
                $(obj).addClass('load_page');
            	$.hideIndicator();
            }
    });	
}


function load_page2(obj){
	$.showIndicator();
	$(obj).removeClass('load_page2');
    var url = $(obj).attr('url');
    var js_url = $(obj).attr('js_url');
    var callback = $(obj).attr('callback');

    $.ajax({
        url: url,
        type: "POST",
        success: function (html) {
            //console.log(html);
            $(".page-current").after($(html).find(".page").addClass('loadpage')).removeClass('page-current');
            $(".loadpage").find(".back").addClass("close_page2").removeClass("back");

            loadScript(js_url,callback);
            $.hideIndicator();
           	$(obj).addClass('load_page2');
            $(".close_page2").on("click",function(){
                close_page2();
            });
            //$.init();
        },
        error: function() {
            $.toast("网络被风吹走啦~");
            $(obj).addClass('load_page2');
        	$.hideIndicator();    
        }
    });
}

function close_page(){
	
	if($(".page").length >1){
		$(".page-current").remove();
		$(".page").last().addClass('page-current');
	}
	
}

function close_page2() {
	var page_len=$(".page").length;

    if ($(".page").length > 1) {
    	$(".loadpage").addClass("colsepage").removeClass("loadpage");


        $(".page").eq(page_len-2).addClass('page-current');
        setTimeout(function () {
            $(".colsepage").remove();
        },100);
    }
}


function reset_flippedout(){
	// 去除弹出层
	$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
	$(".flippedout-default").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
	// 去除分享层
	$(".box_share").removeClass("z-open");
	// 去除下拉导航
	$(".m-nav-dropdown").removeClass("showdropdown");
	$(".nav-dropdown-con").removeClass("dropdown-open");
	// 去除规格选择
	$(".spec-choose").removeClass("z-open");
	$(".spec-btn-list").removeClass("isAddCart");
	// 去除关闭层弹跳
	$(".j-flippedout-close .iconfont").removeClass("jump");
}



//异步加载js  
function loadScript(url,callback){  
	var head=document.getElementsByTagName("head");
	var id = document.getElementById(url);
	if (id) {
		head[0].removeChild(id);
	}
    var script = document.createElement("script");  
    script.type="text/javascript";  
    script.id=url;
    
    if(script.readyState){  
        script.onreadystatechange = function(){  
            if(script.readyState=="loaded"||script.readyState=="complete"){  
                script.onreadystatechange=null;  
                if (typeof test == 'function') {
                callback();
                };  
            }  
        }  
    }else{  
        script.onload = function(){  
	        if (typeof test == 'function') {
	        callback();
	        };  
        }  
    }  
    script.src = url;  
    document.getElementsByTagName("head")[0].appendChild(script);  
 
} 

function loadScriptcallback(){

}

function load_content(obj){
	
	var url = $(obj).attr('url');

    $.ajax({
            url: url,
            type: "POST",
            success: function (html) {
            	//console.log(html);
            	$(".page-current .content").replaceWith($(html).find(".page .content"));
            },
            error: function() {
                $.toast("网络被风吹走啦~");
            }
    });
}

function weixin_bind_authorize()
{
	$.ajax({
		url:AJAX_URL,
		data:{"act":"del_is_weixin_bind"},
		type:"post",
		dataType:"json",
		success:function(obj)
		{
			var url = location.href;
			if(url.indexOf("?")==-1)
			{
				url+="?weixin_bind=1";
			}
			else
			{
				url+="&weixin_bind=1";
			}
			//$.loadPage(url);
			location.href = url;
		}
	});
}
function weixin_login_app()
{
	App.third_party_login_sdk("wxlogin");
}
function js_third_party_login_sdk(jsonstr)
{
	js_weixin_login(jsonstr,0);
	
}
function js_weixin_login(jsonstr,type)
{
	$.ajax({
		url:AJAX_URL,
		data:{"act":"is_user","param":jsonstr,"type":type},
		type:"post",
		dataType:"json",
		success:function(obj)
		{
			if(obj.err==1){
				$.toast(obj.err_code);
				return false;
			}
			if(obj.is_user == 1){
				if(obj.is_mobile==1){
					$.toast(obj.err_code);
				}else{
					$.confirm("该微信已有会员，是否合并？",function(){
						$.ajax({
							url:AJAX_URL,
							data:{"act":"get_wx_app_userinfo","param":jsonstr,"type":type},
							type:"post",
							dataType:"json",
							success:function(obj)
							{
								if(obj.err_code == 0){
									$.toast("绑定成功");
									$.loadPage(obj.jump);
									//location.href = obj.jump;
								}	
								else
								{
									$.toast(obj.err);
								}
							}
						});
					},function(){
						location.href=obj.jump;
					});
				}
			}else{
				$.ajax({
					url:AJAX_URL,
					data:{"act":"get_wx_app_userinfo","param":jsonstr,"type":type},
					type:"post",
					dataType:"json",
					success:function(obj)
					{
						if(obj.err_code == 0){
							$.toast("绑定成功");
							$.loadPage(obj.jump);
							//location.href = obj.jump;
						}
						else
						{
							$.toast(obj.err);
						}
					}
				});
			}
		}
	});
}
/**
 * 关于分享
 * @param share_key
 */
function share_complete(share_key){
	$.toast("分享成功");
}
function init_share(){
	
	if($(".j-app-share-btn").length>0){
		
		$(".j-app-share-btn").unbind("click");
		$(".j-app-share-btn").click(function(){
			
			var share_data={};
			share_data["share_content"]=$(this).attr("data-content");
			share_data["share_url"]=$(this).attr("data-url");
			share_data["key"]='';
			share_data['sina_app_api']=1;
			share_data['qq_app_api']=1;
			share_data["share_imageUrl"]=$(this).attr("data-img");
			share_data['share_title'] = $(this).attr("data-title");
			share_data=JSON.stringify(share_data);
			try{
				App.sdk_share(share_data);
			}catch(e){

			}
		});
	}

}





var current_param = {}; //当前页面刷新参数
function set_current_url(url,param)
{
	current_url = url;
	current_param = param;
	save_url = false;
}
/**
 * ajax加载整个页面的方法
 */
$.loadPage = function(url){
	$.showIndicator();
	$.ajax({
		url:url,
		data:current_param,
		type:"POST",
		success:function(html){
			//$(".content").html($(html).find(".content").html());
			$(".page").replaceWith($(html).find(".page"));
			$(".panel").html($(html).find(".panel").html());
			$.hideIndicator(); 			
			set_current_url(url,current_param);
			$.init();				
		},
		error:function()
		{
			$.toast("请求超时");
			$.hideIndicator(); 
		}
	});		
};
//定位
function position(){
	var options = {timeout: 8000};
	if(html_id=="index"||html_id=="main"){
		
	}else if(html_id=="tuan"||html_id=="stores"){
		$(".refresh").addClass('rotate');
	}
	if(app_index=="app"){
		try{
			App.position();
		}
		catch(ex)
		{
			//$.alert("POS"+ex);
			var geolocation = new qq.maps.Geolocation(TENCENT_MAP_APPKEY, "myapp");
			geolocation.getLocation(showPosition, showErr, options);
		}
	}else{
		var geolocation = new qq.maps.Geolocation(TENCENT_MAP_APPKEY, "myapp");
		geolocation.getLocation(showPosition, showErr, options);
	}
}
//app定位成功回调
function js_position(lat,lng){
	has_location = 1;//定位成功;
	userxypoint(lat, lng,"BD09");
}
/*关于定位函数*/

function showPosition(p){ 
	has_location = 1;//定位成功;
    m_latitude = p.lat; //纬度
    m_longitude = p.lng;
	userxypoint(m_latitude, m_longitude,'GCJ02');
}
function showErr(p){
	if(html_id=="index"||html_id=="main"){
		//alert("定位失败");
		console.log("定位失败");
	}else if(html_id=="tuan"||html_id=="stores"){
		console.log("定位失败");
		$(".refresh").removeClass('rotate');
	}
}
//将坐标返回到服务端;
function userxypoint(latitude,longitude,type){
	var query = new Object();
	query.m_latitude = latitude;
	query.m_longitude = longitude;
	query.m_type=type;
	$.ajax({
		url:geo_url,
		data:query,
		type:"post",
		dataType:"json",
		success:function(data){
			if(html_id=="index"){
				if(data.status==0)
				{
					$.confirm("当前城市是["+data.city.name+"],是否切换到"+data.city.name+"站？",function(){
						url=INDEX_URL;
						if(url.indexOf("?")==-1)
						{
							url+="?city_id="+data.city.id;
						}
						else
						{
							url+="&city_id="+data.city.id;
						}
						window.location.href = url;
					},function(){
						$.fn.cookie("cancel_geo",1,1);
					});
				}
			}else if(html_id=="main"){
				if(data.status==0)
				{
					$.confirm("当前城市是["+data.city.name+"],是否切换到"+data.city.name+"站？",function(){
						location.href = INDEX_URL+"&city_id="+data.city.id;
					},function(){
						$.fn.cookie("cancel_geo",1,1);
					});
				}
			}else if(html_id=="tuan"||html_id=="stores"){
				setTimeout(function () {
					$(".refresh").removeClass('rotate');
					$(".address").html("<i class='iconfont'>&#xe62f;</i>"+data.add);
				}, 2000);
			}else if(html_id=="position"){
				location.href = location.href;
			}
		}
		,error:function(){
		}
	});
}
/*end 关于定位函数*/
//登录页面清除input
function clear_input(inputbox,clearbtn) {
	inputbox.bind('input propertychange', function() {
		if (inputbox.val().length==0) {
			clearbtn.hide();
		} else {
			clearbtn.show();
		}
	});
	clearbtn.click(function(){
		inputbox.val('');
		clearbtn.hide();
	});
}

/**
 * 页面刷新，可以配置需要刷新的页面
 */
function js_ajax_load(sess_id){

	var pageId = $(".page-current").attr('id');
	var refresh_page = new Array();  //需要刷新的页面
	refresh_page.push('scores_index','user_center');
	if($.inArray(pageId,refresh_page) >= 0){
		var url = window.location.href;
		url = changeURLArg(url,'sess_id',sess_id);	
		$.ajax({
			url:url,
			type:"post",
			success:function(data){
				$(".content").html($(data).find(".content").html());
			}
			,error:function(){
			}
		});
	}

}


function changeURLArg(url,arg,arg_val){ 
    var pattern=arg+'=([^&]*)'; 
    var replaceText=arg+'='+arg_val; 
    if(url.match(pattern)){ 
        var tmp='/('+ arg+'=)([^&]*)/gi'; 
        tmp=url.replace(eval(tmp),replaceText); 
        return tmp; 
    }else{ 
        if(url.match('[\?]')){ 
            return url+'&'+replaceText; 
        }else{ 
            return url+'?'+replaceText; 
        } 
    } 
    return url+'\n'+arg+'\n'+arg_val; 
} 


function pay_sdk_json(data)
{
	var json = '{"pay_sdk_type":"'+data['pay_sdk_type']+'","config":{';
	for(var k in data['config'])
	{
		json+='"'+k+'":"'+data['config'][k]+'",';
	}
	json = json.substring(0,json.length-1);
	json+='}}';
	return json;

}

function app_detail_json(type,data)
{
	var json = '{'+type+',{'+data+'}}';
	App.app_detail(type,json);

}

/**
 * state： 1：订单支付成功  2	：正在处理中  3：订单支付失败 4：用户中途取消  5：网络连接出错  6：调用第三方失败（主要指：在调用支付sdk之前先遍历所需参数是否为空，为空则返回该值，此时app会抛出对应参数为空，便于调试）
 */
function js_pay_sdk(state){

	if(state==1){
		//$.router.load(pay_success_url, true);
		$.loadPage(location.href);
	}
}


function checkMobilePhone (value){
	value = $.trim(value);
	if(value != '') {
		var reg = /^(1[34578]\d{9})$/;
		return reg.test(value);
	} else {
		return false;
	}
};


function qrcode_box() {
	$(".j-open-qrcode").on('click', function() {
		$(".m-mask").addClass('active');
		$(".m-qrcode-box").addClass('active');
	});
	$(".j-close-qrcode").on('click', function() {
		$(".m-mask").removeClass('active');
		$(".m-qrcode-box").removeClass('active');
	});
}
function screen_bar() {
	$(document).on("click",".dropdown-navlist",function() {
		screen_bar_close();
	});
	if ($(document).find('.screen-all')) {
		$(".screen-all").attr({
			'data-cid': $("#all-goods .type-detail .active").attr('data-cid'),
			'data-tid': $("#all-goods .type-detail .active").attr('data-tid')
		});
	} else {
		return;
	}
	if ($(document).find('.screen-area')) {
		$(".screen-area").attr({
			'data-qid': $("#area-screen .type-detail .active").attr('data-qid')
		});
	} else {
		return;
	}
	$(".m-screen-bar").on("click",".screen-link",function() {
		screen_bar_close();
		$(".screen-link").removeClass('active');
		$(this).addClass('active');
		$(".m-screen-bar").attr('data-type', $(this).attr('data-type'));
	});
	//筛选
	//标签
	$(".m-screen-bar").on("click",".screen-item a",function(){
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".m-screen-list").removeClass('active');
		$(".goods-type li").removeClass('active');
	});
	//全部
	function screen_open() {
		$(".content").css('overflow', 'hidden');
		$(".m-screen-list").addClass('active');
	}
	function screen_close() {
		$(".content").css('overflow', 'auto');
		$(".m-screen-list").removeClass('active');
	}
	$(".m-screen-bar").on("click",".screen-all",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#all-goods").removeClass('active');
		} else {
			$(this).addClass('active');
			$(".screen-area").removeClass('active');
			$("#area-screen").removeClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			screen_open();
			$("#all-goods").addClass('active');
			$("#all-goods .goods-type li").eq($(this).attr("data-id")).addClass('active');
			$("#all-goods .type-detail ul").eq($(this).attr("data-id")).show();
		}
	});
	$(".m-screen-list").on("click",".goods-type li",function() {
		$(".goods-type li").removeClass('active');
		$(this).addClass('active');
		$(".type-detail ul").hide();
		if ($(".goods-type li").hasClass('active')) {
			var type_id = $(this).attr('data-id');
			$(this).parent().parent().find(".type-detail ul").eq(type_id).show();
		}
	});
	$("#all-goods").on('click', '.type-detail li', function() {
		$("#all-goods .type-detail li").removeClass('active');
		$(this).addClass('active');
		$(".screen-all p").html($(this).find('.flex-1').html());
		$(".screen-all").attr('data-id', $(this).parent().attr("data-id"));
		$(".screen-all").attr('data-cid', $(this).attr("data-cid"));
		$(".screen-all").attr('data-tid', $(this).attr("data-tid"));
		$(".screen-link").removeClass('active');
	});
	$("#all-goods").on('click', '.type-detail li:first-child', function() {
		var type_id = $(this).parent().attr('data-id');
		$(".screen-all p").html($("#all-goods .goods-type li").eq(type_id).html());
	});
	//全城
	$(".m-screen-bar").on("click",".screen-area",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#area-screen").removeClass('active');
		} else {
			$(this).addClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			$(".screen-all").removeClass('active');
			$("#all-goods").removeClass('active');
			screen_open();
			$("#area-screen").addClass('active');
			$(".goods-type li").removeClass('acitve');
			$("#area-screen .goods-type li").eq($(this).attr("data-id")).addClass('active');
			$("#area-screen .type-detail ul").eq($(this).attr("data-id")).show();
		}
	});
	$("#area-screen").on('click', '.type-detail li', function() {
		$("#area-screen .type-detail li").removeClass('active');
		$(this).addClass('active');
		$(".screen-area p").html($(this).find('p').html());
		$(".screen-area").attr('data-id', $(this).parent().attr("data-id"));
		$(".screen-area").attr('data-qid', $(this).attr("data-qid"));
		$(".screen-link").removeClass('active');
	});
	$("#area-screen").on('click', '.type-detail li:first-child', function() {
		var type_id = $(this).parent().attr('data-id');
		$(".screen-area p").html($("#area-screen .goods-type li").eq(type_id).html());
	});
	$(document).on("click",".j-listchoose",function() {
		var c_id = $(".screen-all").attr('data-cid');
		var t_id = $(".screen-all").attr('data-tid');
		var q_id = $(".screen-area").attr('data-qid');
		var order_type = $(".m-screen-bar").attr('data-type');
		var url = sitename+'/wap/index.php?ctl='+ctl_name+'&cate_id='+c_id+'&tid='+t_id+'&qid='+q_id+'&order_type='+order_type;
		if(key!=''){
			url+='&keyword='+key;
		}
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-pj").html($(html).find(".j-pj").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-zj").html($(html).find(".j-zj").html());
				$(".j-zx").html($(html).find(".j-zx").html());
				$("#all-goods").html($(html).find("#all-goods").html());
				$("#area-screen").html($(html).find("#area-screen").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					init_list_scroll_bottom();
				};
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		screen_bar_close();
	});
	$(document).on("click",".screen-link",function() {
		var url = $(this).attr('date-href');
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-pj").html($(html).find(".j-pj").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-zj").html($(html).find(".j-zj").html());
				$(".j-zx").html($(html).find(".j-zx").html());
				$(".m-screen-list").html($(html).find(".m-screen-list").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					init_list_scroll_bottom();
				};
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		screen_bar_close();
	});
}

function screen_bar_close() {
	$(".m-screen-list").removeClass('active');
	$(".content").css('overflow', 'auto');
	$(".arrow-up").hide();
	$(".arrow-down").show();
	$(".screen-area").removeClass('active');
	$(".screen-all").removeClass('active');
}
function select_box(open,box) {
	open.on('click', function() {
		box.addClass('active');
		$(".m-mask").addClass('active');
	});
	$(".j-close-select").on('click', function() {
		$(".m-select-box").removeClass('active');
		$(".m-mask").removeClass('active');
	});
}
/*
 * Swipe 1.0
 *
 * Brad Birdsall, Prime
 * Copyright 2011, Licensed GPL & MIT
 *
*/

window.Swipe = function(element, options) {

  // return immediately if element doesn't exist
  if (!element) return null;

  var _this = this;

  // retreive options
  this.options = options || {};
  this.index = this.options.startSlide || 0;
  this.speed = this.options.speed || 300;
  this.callback = this.options.callback || function() {};
  this.delay = this.options.auto || 0;
  this.unresize = this.options.unresize; //anjey

  // reference dom elements
  this.container = element;
  this.element = this.container.children[0]; // the slide pane

  // static css
  //this.container.style.overflow = 'hidden'; //by anjey
  this.element.style.listStyle = 'none';

  // trigger slider initialization
  this.setup();

  // begin auto slideshow
  this.begin();

  // add event listeners
  if (this.element.addEventListener) {
  	//by anjey
  	this.element.addEventListener('mousedown', this, false);
  	 
    this.element.addEventListener('touchstart', this, false);
    this.element.addEventListener('touchmove', this, false);
    this.element.addEventListener('touchend', this, false);
    this.element.addEventListener('webkitTransitionEnd', this, false);
    this.element.addEventListener('msTransitionEnd', this, false);
    this.element.addEventListener('oTransitionEnd', this, false);
    this.element.addEventListener('transitionend', this, false);
    if(!this.unresize){ // anjey
    	window.addEventListener('resize', this, false);
    }
  }

};

Swipe.prototype = {

  setup: function() {

    // get and measure amt of slides
    this.slides = this.element.children;
    this.length = this.slides.length;

    // return immediately if their are less than two slides
    if (this.length < 2) return null;

    // determine width of each slide
    this.width = this.container.getBoundingClientRect().width || this.width; //anjey
    // return immediately if measurement fails
    if (!this.width) return null;

    // hide slider element but keep positioning during setup
    this.container.style.visibility = 'hidden';

    // dynamic css
    this.element.style.width = (this.slides.length * this.width) + 'px';
    var index = this.slides.length;
    while (index--) {
      var el = this.slides[index];
      el.style.width = this.width + 'px';
      el.style.display = 'table-cell';
      el.style.verticalAlign = 'top';
    }
    // set start position and force translate to remove initial flickering
    this.slide(this.index, 0); 

    // show slider element
    this.container.style.visibility = 'visible';

  },

  slide: function(index, duration) {

    var style = this.element.style;

    // fallback to default speed
    if (duration == undefined) {
        duration = this.speed;
    }

    // set duration speed (0 represents 1-to-1 scrolling)
    style.webkitTransitionDuration = style.MozTransitionDuration = style.msTransitionDuration = style.OTransitionDuration = style.transitionDuration = duration + 'ms';

    // translate to given index position
    style.MozTransform = style.webkitTransform = 'translate3d(' + -(index * this.width) + 'px,0,0)';
    style.msTransform = style.OTransform = 'translateX(' + -(index * this.width) + 'px)';

    // set new index to allow for expression arguments
    this.index = index;

  },

  getPos: function() {
    
    // return current index position
    return this.index;

  },

  prev: function(delay) {

    // cancel next scheduled automatic transition, if any
    this.delay = delay || 0;
    clearTimeout(this.interval);

    // if not at first slide
    if (this.index) this.slide(this.index-1, this.speed);

  },

  next: function(delay) {

    // cancel next scheduled automatic transition, if any
    this.delay = delay || 0;
    clearTimeout(this.interval);

    if (this.index < this.length - 1) this.slide(this.index+1, this.speed); // if not last slide
    else this.slide(0, this.speed); //if last slide return to start

  },

  begin: function() {

    var _this = this;

    this.interval = (this.delay)
      ? setTimeout(function() { 
        _this.next(_this.delay);
      }, this.delay)
      : 0;
  
  },
  
  stop: function() {
    this.delay = 0;
    clearTimeout(this.interval);
  },
  
  resume: function() {
    this.delay = this.options.auto || 0;
    this.begin();
  },

  handleEvent: function(e) {
  	var that = this;
  	if(!e.touches){
  		e.touches = new Array(e);
  		e.scale = false;
  	}
    switch (e.type) {
      // by anjey
      case 'mousedown': (function(){
      		that.element.addEventListener('mousemove', that, false);
   			that.element.addEventListener('mouseup', that, false);
   			that.element.addEventListener('mouseout', that, false);
      		that.onTouchStart(e);
      })(); break;
      case 'mousemove': this.onTouchMove(e); break;
      case 'mouseup': (function(){
	      	that.element.removeEventListener('mousemove', that, false);
	   		that.element.removeEventListener('mouseup', that, false);
	   		that.element.removeEventListener('mouseout', that, false);
	      	that.onTouchEnd(e);
      })(); break;
     case 'mouseout': (function(){
      		that.element.removeEventListener('mousemove', that, false);
   			that.element.removeEventListener('mouseup', that, false);
   			that.element.removeEventListener('mouseout', that, false);
      		that.onTouchEnd(e);
      })(); break;
    	
      case 'touchstart': this.onTouchStart(e); break;
      case 'touchmove': this.onTouchMove(e); break;
      case 'touchend': this.onTouchEnd(e); break;
      case 'webkitTransitionEnd':
      case 'msTransitionEnd':
      case 'oTransitionEnd':
      case 'transitionend': this.transitionEnd(e); break;
      case 'resize': this.setup(); break;
    }
  },

  transitionEnd: function(e) {
    e.preventDefault();
    if (this.delay) this.begin();

    this.callback(e, this.index, this.slides[this.index]);

  },

  onTouchStart: function(e) {
    
    this.start = {

      // get touch coordinates for delta calculations in onTouchMove
      pageX: e.touches[0].pageX,
      pageY: e.touches[0].pageY,

      // set initial timestamp of touch sequence
      time: Number( new Date() )

    };

    // used for testing first onTouchMove event
    this.isScrolling = undefined;
    
    // reset deltaX
    this.deltaX = 0;

    // set transition time to 0 for 1-to-1 touch movement
    this.element.style.MozTransitionDuration = this.element.style.webkitTransitionDuration = 0;

  },

  onTouchMove: function(e) {

    // ensure swiping with one touch and not pinching
    if(e.touches.length > 1 || e.scale && e.scale !== 1) return;

    this.deltaX = e.touches[0].pageX - this.start.pageX;

    // determine if scrolling test has run - one time test
    if ( typeof this.isScrolling == 'undefined') {
      this.isScrolling = !!( this.isScrolling || Math.abs(this.deltaX) < Math.abs(e.touches[0].pageY - this.start.pageY) );
    }

    // if user is not trying to scroll vertically
    if (!this.isScrolling) {

      // prevent native scrolling 
      e.preventDefault();

      // cancel slideshow
      clearTimeout(this.interval);

      // increase resistance if first or last slide
      this.deltaX = 
        this.deltaX / 
          ( (!this.index && this.deltaX > 0               // if first slide and sliding left
            || this.index == this.length - 1              // or if last slide and sliding right
            && this.deltaX < 0                            // and if sliding at all
          ) ?                      
          ( Math.abs(this.deltaX) / this.width + 1 )      // determine resistance level
          : 1 );                                          // no resistance if false
      
      // translate immediately 1-to-1
      this.element.style.MozTransform = this.element.style.webkitTransform = 'translate3d(' + (this.deltaX - this.index * this.width) + 'px,0,0)';

    }

  },

  onTouchEnd: function(e) {

    // determine if slide attempt triggers next/prev slide
    var isValidSlide = 
          Number(new Date()) - this.start.time < 250      // if slide duration is less than 250ms
          && Math.abs(this.deltaX) > 20                   // and if slide amt is greater than 20px
          || Math.abs(this.deltaX) > this.width/2,        // or if slide amt is greater than half the width

    // determine if slide attempt is past start and end
        isPastBounds = 
          !this.index && this.deltaX > 0                          // if first slide and slide amt is greater than 0
          || this.index == this.length - 1 && this.deltaX < 0;    // or if last slide and slide amt is less than 0

    // if not scrolling vertically
    if (!this.isScrolling) {

      // call slide function with slide end value based on isValidSlide and isPastBounds tests
      this.slide( this.index + ( isValidSlide && !isPastBounds ? (this.deltaX < 0 ? 1 : -1) : 0 ), this.speed );

    }

  }

};

/**
 * Created by Administrator on 2016/9/8.
 */




$(document).on("pageInit", "#uc_address_index", function(e, pageId, $page){
	
	
	
	
    $("#uc_address_index").on('click','.confirm-address', function () {
        var _this=$(this);
        $.confirm('确定要删除该地址吗？', function () {
        	$.ajax({
				url: _this.attr('del_url'),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status == 1){
						_this.parents("li").remove();
					}else{
						$.alert(obj.info);
					}
				},
        	});
        });
    });


    $("#uc_address_index").on("change",".j-address-set input[type=radio]",function () {
		

        if($(this).prop('checked')==true){

			var vobj=$(this);
        	$.ajax({
				url: $(this).attr('dfurl'),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status == 1){
						vobj.parents(".j-address-set").find(".u-set-default").addClass("j-address-color");
						vobj.parents("li").siblings("li").find(".u-set-default").removeClass("j-address-color");
					}else{
						$.toast("失败");
					}
				},
        	});
            
        }
    });
    

});
$(document).on("pageInit", "#biz_coupon_check", function(e, pageId, $page) {
	function openSelect(open_btn,open_item) {
		$(open_btn).on('click', function() {
			$(".delivery-mask").addClass('active');
			$(open_item).addClass('active');
		});
		$(".delivery-mask").on('click', function() {
			$(this).removeClass('active');
			$(open_item).removeClass('active');
		});
	}
	function closeSelect(close_item) {
		$(".delivery-mask").removeClass('active');
		$(close_item).removeClass('active');
	}
	//绑定团购数量
	$(".flex-box .coupon_use_count").on("blur",function(){
		var use_count = $(this).val();
		var can_number = $(".coupon-check-count .num").text();
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(parseInt(use_count) > parseInt(can_number)){
			use_count=can_number;
		}
		$(this).val(use_count);
	});
	$(".flex-box .coupon_use_count").on("focus",function(){
		$(this).select();
	});
	//团购券数量
	$(".flex-box .count-disable").on("click",function(){
		var use_count = $(".flex-box .coupon_use_count").val();
		var can_number = $(".coupon-check-count .num").text();
		use_count = parseInt(use_count) - 1;
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(use_count>can_number){
			use_count=can_number;
		}
		$(".flex-box .coupon_use_count").val(use_count);
	});
	$(".flex-box .count-plus").on("click",function(){
		var use_count = $(".flex-box .coupon_use_count").val();
		var can_number = $(".coupon-check-count .num").text();
		use_count = parseInt(use_count) + 1;
		if(isNaN(use_count)||parseInt(use_count)<=0){
			use_count=1;
		}
		if(use_count>can_number){
			use_count=can_number;
		}
		$(".flex-box .coupon_use_count").val(use_count);
	});
	//选择验证门店
	openSelect('.j-shop-select','.shop-select');
	$(".shop-list").on('click', 'li', function() {
		$(".shop-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".shop-cancle").on('click', function() {
		closeSelect('.shop-select');
	});
	$(".shop-confirm").on('click', function() {
		closeSelect('.shop-select');
		$(".j-shop-select .shop-name").html($(".shop-select .active .shop-name").html());
		$(".j-shop-select .shop-id").val($(".shop-select .active .shop-id").val());
	});
	
	//核销提交
	$(".check-confirm").on('click', function() {
		var query = new Object();
		query.location_id = $(".j-shop-select .shop-id").val();
		query.coupon_use_count = $(".flex-box .coupon_use_count").val();
		query.coupon_pwd = coupon_pwd;
		$.ajax({
			url:url,
			data:query,
			dataType: "json",
			type:"post",
			success:function(obj){
				if(obj.status==1){
					$.toast(obj.info);
					setTimeout(function() {
                    	location.href = obj.jump;
                    }, 1500);
				}else{
					$.toast(obj.info);
				}
			},
            error: function() {
                $.toast("网络被风吹走啦~");
           	}
		});
	});
});
$(document).on("pageInit", "#biz_coupon_use_log", function(e, pageId, $page) {
	$(document).on('click', '.j-use-search', function() {
		$(".use-search-bar").addClass('open');
	});
	$(".use-search-bar").on('click', '.j-close-use-search', function() {
		$(".use-search-bar").removeClass('open');
	});

	init_list_scroll_bottom();

	$('.search').bind('click', function() {
		var pwd = $.trim($('input[name="coupon_pwd"]').val());
		if (pwd == '') {
			$.toast('请输入要搜索的券码');
			return;
		}
		pwd = pwd.replace(/\s/g,'');
		if (pwd.length!=12) {
			$.toast('请输入有效券码');
			return;
		}
		var param = {
			act: 'search_log',
			coupon_pwd: pwd
		};
		$.ajax({
			url: use_log,
			type:"GET",
			data: param,
			dataType:"JSON",
			success: function(html) {

				$('.j-ajaxlist').html($(html).find('.j-ajaxlist').html());
				init_list_scroll_bottom();
			},
			error: function(err) {
				console.log(err);
			}
		});
		return false;
	});
});
/**
 * 
 */
$(document).on("pageInit", "#biz_dc_abnormal_order", function(e, pageId, $page) {
	
	init_list_scroll_bottom();//下拉刷新加载
	
	var _rehei=$(".j-red-reward").height();
	
	$(document).on('click',".j-handle",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
        
        var data_account=$(this).attr('dada-account');
        
        $("input[name='order_id']").val($(this).attr('data-id'));
        $("input[name='dada_account']").val(data_account);
        
        if(!is_open_dada_delivery){
        	$("#dada-data").find(".item-title").html("委托达达配送(未开启，请在pc后台开启)"); 	
        }
        else if(data_account == ''){
        	$("#dada-data").find(".item-title").html("委托达达配送(帐号未注册，请在pc后台开启)");
        }
        else if(!delivery_money_enough){
        	$("#dada-data").find(".item-title").html("委托达达配送(配送余额不足，请在pc后台充值)"); 
        }
        
        if(!is_open_dada_delivery || data_account == '' || !delivery_money_enough){
        	$("#dada-data").find("input[name='delivery_part']").attr('checked',true);
        	$("#dada-data").find("input[name='delivery_part']").attr('disabled','disabled');
        	$("#dada-data").find(".icon-form-checkbox").css('border','gray!important');
        	$("#dada-data").find(".icon-form-checkbox").css('background-color','gray');
        }
        
	});
	
	$(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	$(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	/*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        //$(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
	
    $("input[type='radio']").change(function(){
    	popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    	
    	var type=$("input[type='radio']:checked").val();
    	
    	var dada_account=$("input[name='dada_account']").val();
    	
    	if(type==2){
    		if(!is_open_dada_delivery){
    			$.toast('达达未开启，请在pc后台开启'); 
    			return false;
    		}
    		if(dada_account == ''){
    			$.toast('达达帐号未注册，请在pc后台开启'); 
    			return false;
    		}
    		if(delivery_money_enough == 0){
    			$.toast('达达配送余额不足，请在pc后台充值'); 
    			return false;
    		}
    	}
    	var query=new Object();
    	query.act="accept_order";
    	query.type=type;
    	query.id=$("input[name='order_id']").val();
    	
    	$.ajax({
        	  url:ajax_url,
        	  data:query,
        	  type:'post',
        	  dataType:'json',
        	  success:function(data){
        		  
        		  $.toast(data.info); 
    			  setTimeout(function () {
   				  	  location.reload(); 
 			      }, 1000);
        		  
        	  }
          });
    	
    	return false;
    });
    
});
$(document).on("pageInit", "#biz_dc_order", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-"+sort_1,".j-ajaxadd-"+sort_1);
	
	function tab_line() {
		var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	
	//分类加载内容
	$(".j-list-choose").on('click', function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		var sort=$(this).attr("sort");
		//alert(sort);
		$(".j-list-choose").removeClass('active');
		$(this).addClass('active');
		$(".biz-order-list").hide();
		tab_line();
		var url=$(this).attr("data-href");
		$(".j-ajaxlist-"+sort).show();
		$(".content").scrollTop(1); 
		if($(".j-ajaxlist-"+sort).html()==null){
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			    },
			    error:function()
			    {
			    	
			    	$(".j-ajaxlist-"+sort).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			}
        }

	});
	
	
	/*$(document).on('click', '.j-accept', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});*/
	
	var _rehei=$(".j-red-reward").height();
	
	$(document).on('click',".j-accept",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
        $("input[name='order_id']").val($(this).attr('data-id'));
	});
	
	$(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	$(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	/*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        //$(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
	
    $("input[type='radio']").change(function(){
    	popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    	
    	var type=$("input[type='radio']:checked").val();
    	
    	if(type==2){
    		if(!is_open_dada_delivery){
    			$.toast('达达未开启，请在pc后台开启'); 
    			return false;
    		}
    		if(dada_account == ''){
    			$.toast('达达帐号未注册，请在pc后台开启'); 
    			return false;
    		}
    		if(delivery_money_enough == 0){
    			$.toast('达达配送余额不足，请在pc后台充值'); 
    			return false;
    		}
    	}
    	var query=new Object();
    	query.act="accept_order";
    	query.type=type;
    	query.id=$("input[name='order_id']").val();
    	
    	$.ajax({
        	  url:ajax_url,
        	  data:query,
        	  type:'post',
        	  dataType:'json',
        	  success:function(data){
        		  if(data.status == 1){
        			  $.toast(data.info); 
        			  setTimeout(function () {
       				  	  location.reload(); 
     			      }, 2000);
           		  }else{
           			  $.toast(data.info);
           			  setTimeout(function () {
          				  location.reload(); 
    			      }, 2000);
           		  }
        	  }
          });
    	
    	return false;
    });
	
});
$(document).on("pageInit", "#dc_resview", function(e, pageId, $page) {
	//通用方法（接单，取消，确认）	
	$(document).on('click', '.j-submit', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});

});
$(document).on("pageInit", "#biz_dc_rsorder", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-"+sort_1,".j-ajaxadd-"+sort_1);
	
	$(document).on('click', '.j-submit', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});
	
	
	function tab_line() {
		var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	
	//分类加载内容
	$(".j-list-choose").on('click', function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		var sort=$(this).attr("sort");
		//alert(sort);
		$(".j-list-choose").removeClass('active');
		$(this).addClass('active');
		$(".biz-order-list").hide();
		tab_line();
		var url=$(this).attr("data-href");
		$(".j-ajaxlist-"+sort).show();
		$(".content").scrollTop(1); 
		if($(".j-ajaxlist-"+sort).html()==null){
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			    },
			    error:function()
			    {
			    	
			    	$(".j-ajaxlist-"+sort).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			}
        }

	});


	
});
$(document).on("pageInit", "#dc_view", function(e, pageId, $page) {
	//通用方法（接单，取消，确认）	
	$(document).on('click', '.j-submit', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});

	var _rehei=$(".j-red-reward").height();
	
	$(document).on('click',".j-accept",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
	});
	
	$(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	$(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });
	
	/*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        //$(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
    
    /*$(document).off('click',".label-checkbox");
    $(document).on('click',".list-block",function(){
    	var val=$(this).find("input[name='delivery_type']").val();
    	
    	alert(111);
    });*/
    
    $("input[type='radio']").change(function(){
    	popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    	
    	var type=$("input[type='radio']:checked").val();
    	
    	if(type==2){
    		if(!is_open_dada_delivery){
    			$.toast('达达未开启，请在pc后台开启'); 
    			return false;
    		}
    		if(dada_account == ''){
    			$.toast('达达帐号未注册，请在pc后台开启'); 
    			return false;
    		}
    		if(delivery_money_enough == 0){
    			$.toast('达达配送余额不足，请在pc后台充值'); 
    			return false;
    		}
    	}
    	var query=new Object();
    	query.act="accept_order";
    	query.type=type;
    	query.id=order_id;
    	
    	$.ajax({
        	  url:ajax_url,
        	  data:query,
        	  type:'post',
        	  dataType:'json',
        	  success:function(data){
        		  if(data.status == 1){
        			  $.toast(data.info); 
        			  setTimeout(function () {
       				  	  location.reload(); 
     			      }, 2000);
           		  }else{
           			  $.toast(data.info);
           			  setTimeout(function () {
          				  location.reload(); 
    			      }, 2000);
           		  }
        	  }
          });
    	
    	return false;
    });
	
});
$(document).on("pageInit", "#biz_getpassword", function(e, pageId, $page) {

});

$(document).on("pageInit", "#biz_info_setting", function(e, pageId, $page)  {

    //退出登录
	$(".btn-con").click(function(){
		var exit_url=$(this).attr("data-url");
		var query = new Object();
		query.act='loginout';
		$.ajax({
			url:exit_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.toast(obj.info);
					setTimeout(function(){
						$.router.load(obj.jump,true);
					},1500);
				}
				else
				{
					$.toast(obj.info);
					return false;
				}
			},
			error:function(){
			$.toast("服务器提交错误");
			return false;
			}
		});
		return false;
	});

});
$(document).on("pageInit", "#biz_manage", function(e, pageId, $page) {
	$(".biz-manage-list").on('click', '.j-unauth', function() {
		$.toast("没有操作权限");
	});
	dc_popup($(".j-open-dc"),$(".j-dc-popup"));
	dc_popup($(".j-open-rs"),$(".j-rs-popup"));
	$(".j-close-popup").on('click', function() {
		$(".dc-mask").removeClass('active');
		$(".dc-popup").removeClass('active');
	});
	//打开弹层
	function dc_popup(dc_open,popup) {
		dc_open.on('click', function() {
			$(".dc-mask").addClass('active');
			popup.addClass('active');
			/* Act on the event */
		});
	}
});
$(document).on("pageInit", "#biz_money_log", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-"+type_1,".j-ajaxadd-"+type_1);
	
	function tab_line() {
		var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	
	//分类加载内容
	$(".j-list-choose").on('click', function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		var type=$(this).attr("type");
//		alert(type);
		$(".j-list-choose").removeClass('active');
		$(this).addClass('active');
		$(".biz-money-log").hide();
		tab_line();
		var url=$(this).attr("data-href");
		$(".j-ajaxlist-"+type).show();
		$(".content").scrollTop(1); 
		if($(".j-ajaxlist-"+type).html()==null){
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
			    },
			    error:function()
			    {
			    	
			    	$(".j-ajaxlist-"+type).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
			}
        }

	});
	
});
$(document).on("pageInit", "#biz_msg_cate", function(e, pageId, $page) {
	refreshdata([".biz_msg_change"]);
});
$(document).on("pageInit", "#biz_msg_index", function(e, pageId, $page) {
	
});
/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#biz_qrcode", function(e, pageId, $page) {	


    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(document).on('click',".j-trans",function () {
    	var index = $(".j-trans").index($(this));
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").eq(index).css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });
    $(document).on('click',".j-reward",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_hei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });


    /*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"100"});
		setTimeout(function () {
                $(".j-cancel").parents(".popup-box").css({"z-index":"-1"});
            },300);
    }
    /*弹出层动画效果*/

    $(document).on('click',".j-trans-list li,.j-reward-list li",function () {
        var lue_name=$(this).find(".pay-way-name .j-company-name").text();
        var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
        var lue_reward=$(this).find(".pay-way-name").text();
		var qrcode=$(this).find(".pay-way-name").attr("qrcode");
		var qrcode_urls=$(this).find(".pay-way-name").attr("qrcode_urls");

        $(this).parents("ul").find("input").prop("checked",false);
		
		$(this).find("input[name='location_id']").prop("checked",true);
        $(".j-reward .j-reward-money").text(lue_reward);
		$(".qrcode img").attr("src",qrcode);
		$(".biz_qrcode_save").attr("href",qrcode_urls);
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },500);
        popupTransition();
        //count_buy_total();
    });




    /*弹层开始*/
    $(".choose-list .j-choose").click(function(){
        $(this).siblings(".j-choose").removeClass("active");
        $(this).addClass("active");
        setSpecgood();
        var data_value= $(".j-choose.active").attr("data-value");
        var data_value = []; // 定义一个空数组
        var txt = $('.j-choose.active'); // 获取所有文本框
        for (var i = 0; i < txt.length; i++) {
            data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
        }
        $(".good-specifications span").empty();
        $(".good-specifications span").addClass("isChoose");
        $(".good-specifications span").append("已选规格：");
        $.each(data_value,function(i){
            $(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
            //传值可以考虑更改这里
            $(".spec-data").attr("data-value"+[i],data_value[i]);
        });
    });


    $(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });

    function cssAnition() {
        $(".flippedout").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-flippedout-close").removeClass("showflipped");
        $(".j-open-choose").bind("click",open_choose);
        setTimeout("$('.flippedout').removeClass('showflipped')",300);
    }
	$(".biz_qrcode_save").unbind('click').bind('click',function () {
		if(app_index=='app'){
			try{
				App.savePhotoToLocal (this.href);
			}
			catch(ex)
			{
				$.toast("此app版本不支持下载图片");
			}
			
			return false;
		}
    });
});
$(document).on("pageInit", "#biz_refund_order_view", function(e, pageId, $page) {
	$(".refund-btn").on('click', '.j-refund-agree', function() {
		$.confirm('是否立即退款', function () {
			$.ajax({
				url: $('.j-refund-agree').attr("data-href"),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					console.log(obj);
					if(obj.biz_login_status==0){
						$.router.load(obj.jump,true);
					}else{
						if(obj.status){
							$.toast(obj.info);
							$.loadPage(window.location.href );
						}else{
							$.toast(obj.info);
						}
					}
				},
        	});
		});
	});
	$(".refund-btn").on('click', '.j-refund-refuse', function() {
		$.confirm('是否拒绝退款', function () {
			$.ajax({
				url: $('.j-refund-refuse').attr("data-href"),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.biz_login_status==0){
						$.router.load(obj.jump,true);
					}else{
						if(obj.status){
							$.toast(obj.info);
							$.loadPage(window.location.href );
						}else{
							$.toast(obj.info);
						}
					}
				},
        	});
		});
	});
});
$(document).on("pageInit", "#biz_shop_order", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-0",".j-ajaxadd-0");
	init_listscroll(".j-ajaxlist-1",".j-ajaxadd-1");
	init_listscroll(".j-ajaxlist-2",".j-ajaxadd-2");
	function tab_line() {
		var init_width=$(".biz-shop-order-tab .active span").width();
		var init_left=$(".j-tab-item.active span").offset().left;
		$(".tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	$(".biz-shop-order-tab").on('click', '.j-tab-item', function(event) {
		var type=$(this).attr("type");
		
		if($(".content").find(".j-ajaxadd-"+type).length > 0){

			$(".biz-shop-order-tab .j-tab-item").removeClass('active');
			$(this).addClass('active').siblings().removeClass('active');
			
			$(".content .m-biz-shop-order-list").removeClass('active');
			$(".content").find(".j-ajaxlist-"+type).addClass('active').siblings().removeClass('active');
			tab_line();
			init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
		}else{
		

			$(document).off('infinite', '.infinite-scroll-bottom');
			$(".j-tab-item").removeClass('active');
			$(this).addClass('active');
			var item_width=$(this).find('span').width();
			var item_left=$(this).find('span').offset().left;
			$(".tab-line").css({
				width: item_width,
				left: item_left
			});
			var url=$(this).attr("data-href");
			
			$.ajax({
				url:url,
				type:"POST",
				success:function(html)
				{
					
					$(".j-ajaxlist-"+type).addClass('active').html($(html).find(".j-ajaxlist-"+type).html()).siblings().removeClass('active');
			
					if ($(html).find(".j-ajaxadd-"+type).length==0) {
						return;
					}else{
						init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
					};
				},
				error:function()
				{
					$.toast("加载失败咯~");
				}
			});
			$.showIndicator();
			setTimeout(function () {
				$.hideIndicator();
			}, 800);
		}
	});
	var swiperm = new Swiper(".j-order-shop-img", {
	    scrollbarHide: true,
	    slidesPerView: 'auto',
	    freeMode: false,
	});
});
$(document).on("pageInit", "#biz_shop_order_delivery", function(e, pageId, $page) {
	function openSelect(open_btn,open_item) {
		$('.delivery-hd').on('click', open_btn, function() {
			$(".delivery-mask").addClass('active');
			$(open_item).addClass('active');
		});
		$(".delivery-mask").on('click', function() {
			$(this).removeClass('active');
			$(open_item).removeClass('active');
		});
	}
	function closeSelect(close_item) {
		$(".delivery-mask").removeClass('active');
		$(close_item).removeClass('active');
	}
	//选择发货门店
	openSelect('.j-shop-select','.shop-select');
	$(".shop-list").on('click', 'li', function() {
		$(".shop-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".shop-cancle").on('click', function() {
		closeSelect('.shop-select');
	});
	$(".shop-confirm").on('click', function() {
		closeSelect('.shop-select');
		$(".delivery-hd .shop-name").html($(".shop-select .active .shop-name").html());
		$(".delivery-hd input[name='location_id']").val($(".shop-select .active .shop-name").attr("data-id"));
	});
	//选择配送方式
	openSelect('.j-logistics-select','.logistics-select');
	$(".logistics-list").on('click', 'li', function() {
		$(".logistics-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".logistics-cancle").on('click', function() {
		closeSelect('.logistics-select');
	});
	$(".logistics-confirm").on('click', function() {
		closeSelect('.logistics-select');
		var express_id=$(".logistics-select .active .logistics-name").attr("data-id");
		$(".delivery-hd .logistics-name").html($(".logistics-select .active .logistics-name").html());
		$(".delivery-hd input[name='express_id']").val(express_id);
		if(express_id == 0){
			$(".delivery-hd .j-logistics-code").css("display",'none');
			$(".delivery-hd .j-remark").css("display",'none');
			$(".user-delivery-info").hide();

			$(".delivery-hd input[name='is_delivery']").val(0);

			$(".write-logistics-code input[name='delivery_sn']").attr("disabled","disabled");
			$(".write-remark input[name='memo']").attr("disabled","disabled");
			$(".j-goods-item[is-delivery='1']").removeClass("active").addClass("disable");
			$(".j-goods-item[is-delivery='0']").removeClass("disable");
			$(".j-goods-item[is-delivery='0'] input[type='checkbox']").removeAttr("disabled");
			$(".j-goods-item[is-delivery='1'] input[type='checkbox']").prop('checked',false).attr("disabled","disabled");
			all_check();
		}else{
			$(".delivery-hd .j-logistics-code").css("display",'flex');
			$(".delivery-hd .j-remark").css("display",'flex');
			$(".user-delivery-info").show();

			$(".delivery-hd input[name='is_delivery']").val(1);

			$(".write-logistics-code input[name='delivery_sn']").removeAttr("disabled");
			$(".write-remark input[name='memo']").removeAttr("memo");
			$(".j-goods-item[is-delivery='0']").removeClass("active").addClass("disable");
			$(".j-goods-item[is-delivery='1']").removeClass("disable");
			$(".j-goods-item[is-delivery='1'] input[type='checkbox']").removeAttr("disabled");
			$(".j-goods-item[is-delivery='0'] input[type='checkbox']").prop('checked',false).attr("disabled","disabled");
			all_check();
		}
	});
	//输入单号
	openSelect('.j-logistics-code','.write-logistics-code');
	$(".shop-list").on('click', 'li', function() {
		$(".shop-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".j-logistics-code").on('click', function() {
		$(".write-logistics-code .logistics-code").attr('placeholder',$(this).find('.logistics-code').html());
		/* Act on the event */
	});
	$(".logistics-code-cancle").on('click', function() {
		closeSelect('.write-logistics-code');
	});
	$(".logistics-code-confirm").on('click', function() {
		closeSelect('.write-logistics-code');
		$(".delivery-hd .logistics-code").html($(".write-logistics-code .logistics-code").val())
	});
	//输入备注
	openSelect('.j-remark','.write-remark');
	$(".shop-list").on('click', 'li', function() {
		$(".shop-list li").removeClass('active');
		$(this).addClass('active');
	});
	$(".j-remark").on('click', function() {
		$(".write-remark .remark").attr('value',$(this).find('.remark').html());
		/* Act on the event */
	});
	$(".remark-cancle").on('click', function() {
		closeSelect('.write-remark');
	});
	$(".remark-confirm").on('click', function() {
		closeSelect('.write-remark');
		if (document.getElementById('remark').value=='') {
			$(".delivery-hd .remark").html('请输入发货备注')
		} else {
			$(".delivery-hd .remark").html($(".write-remark .remark").val())
		}
	});
	//选择商品
	$(".j-goods-item").click(function() {
		if(!$(this).hasClass("disable")){
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$(this).find('input').prop("checked",false);
			} else {
				$(this).addClass('active');
				$(this).find('input').prop("checked",true);
			}
			all_check();
		}
	});
	function all_check() {
		var goods_num = $(".j-goods-item").length;
		var not_num = $(".disable").length;
		goods_num=goods_num-not_num;
		var check_num = $(".delivery-goods-list .active").length;
		$(".delivery-count").html(check_num);
		if (goods_num==check_num) {
			$('.j-all-goods').addClass('active');
		} else {
			$('.j-all-goods').removeClass('active');
		}
	}
	$(".delivery-nav").on('click', '.j-all-goods', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(".j-goods-item").removeClass('active');
			$(".j-goods-item").find('input').prop("checked",false);
		} else {
			$(this).addClass('active');
			$(".j-goods-item").addClass('active');
			$(".disable").removeClass('active');
			$(".j-goods-item.active").find('input').prop("checked",true);
		}
		var check_num = $(".delivery-goods-list .active").length;
		$(".delivery-count").html(check_num);
	});

	$("form[name='do_delivery']").bind("submit",function(){

		var is_delivery=$("input[name='is_delivery']").val();
		if(is_delivery==1){
			var delivery_sn=$("input[name='delivery_sn']").val();
			var express_id=$("input[name='express_id']").val();
			if($.trim(delivery_sn)==""){
				$.toast("请填写快递单号");
				return false;
			}
			if(express_id==0){
				$.toast("请选择快递");
				return false;
			}
		}

		var deal_num=$("input[type='checkbox']:checked").length;
		if(deal_num==0){
			$.toast("请选择发货商品");
			return false;
		}

		var ajax_url = $("form[name='do_delivery']").attr("action");
		var query = $("form[name='do_delivery']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				//console.log(obj);
				if(obj.status==1){
					$.toast("发货成功");
					$(".logistics-code").val('');
					$("#remark").val('');
					$(".j-goods-item").find('input').attr("checked",false);
					if(obj.jump){
						setTimeout(function(){
							location.href = obj.jump;
						},1500);
					}
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)location.href = obj.jump;
					}

				}
			}
		});
		return false;
	});
	var autoTextarea = function (elem, extra, maxHeight) {
	        extra = extra || 0;
	        var isFirefox = !!document.getBoxObjectFor || 'mozInnerScreenX' in window,
	        isOpera = !!window.opera && !!window.opera.toString().indexOf('Opera'),
	                addEvent = function (type, callback) {
	                        elem.addEventListener ?
	                                elem.addEventListener(type, callback, false) :
	                                elem.attachEvent('on' + type, callback);
	                },
	                getStyle = elem.currentStyle ? function (name) {
	                        var val = elem.currentStyle[name];

	                        if (name === 'height' && val.search(/px/i) !== 1) {
	                                var rect = elem.getBoundingClientRect();
	                                return rect.bottom - rect.top -
	                                        parseFloat(getStyle('paddingTop')) -
	                                        parseFloat(getStyle('paddingBottom')) + 'px';
	                        };

	                        return val;
	                } : function (name) {
	                                return getComputedStyle(elem, null)[name];
	                },
	                minHeight = parseFloat(getStyle('height'));

	        elem.style.resize = 'none';

	        var change = function () {
	                var scrollTop, height,
	                        padding = 0,
	                        style = elem.style;

	                if (elem._length === elem.value.length) return;
	                elem._length = elem.value.length;

	                if (!isFirefox && !isOpera) {
	                        padding = parseInt(getStyle('paddingTop')) + parseInt(getStyle('paddingBottom'));
	                };
	                scrollTop = document.body.scrollTop || document.documentElement.scrollTop;

	                elem.style.height = minHeight + 'px';
	                if (elem.scrollHeight > minHeight) {
	                        if (maxHeight && elem.scrollHeight > maxHeight) {
	                                height = maxHeight - padding;
	                                style.overflowY = 'auto';
	                        } else {
	                                height = elem.scrollHeight - padding;
	                                style.overflowY = 'hidden';
	                        };
	                        style.height = height + extra + 'px';
	                        scrollTop += parseInt(style.height) - elem.currHeight;
	                        document.body.scrollTop = scrollTop;
	                        document.documentElement.scrollTop = scrollTop;
	                        elem.currHeight = parseInt(style.height);
	                };
	        };

	        addEvent('propertychange', change);
	        addEvent('input', change);
	        addEvent('focus', change);
	        change();
	};
	var text = document.getElementById("remark");
	autoTextarea(text);
});

$(document).on("pageInit", "#biz_shop_order_logistics", function(e, pageId, $page) {

    if($(".buttons-tab .tab-link").length>0){
        var _width=$(".buttons-tab .tab-link.active").find("span").width();
        var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;

        var btm_line=$(".buttons-tab .bottom_line");
        btm_line.css({"width":_width+"px","left":_left+"px"});

        var _tabs=$(".tabBox .tab_box");
    }
    $(".buttons-tab .tab-link").click(function () {
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;

        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();

        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");
//        init_confirm_button();

    });

//    if($(".no_delivery").hasClass("active") &&
//        $("input[type='checkbox']").length==$("input[disabled='disabled']").length
//        ){
//        $("#uc_logistic nav.bar-tab").hide();
//    }else{
//        init_confirm_button();
//    }

    $(".no_delivery_deal").click(function(){
        if($("input[type='checkbox']").length==$("input[disabled='disabled']").length){
            $("#uc_logistic nav.bar-tab").hide();
        }else{
            $("#uc_logistic nav.bar-tab").show();
        }
    });

//    $(document).on("click",".confirm_order",function(){
//        var data_id = $(".tabBox .tab_box.active").attr("data_id");
//        var query = new Object();
//        if(data_id){
//            query.item_id = data_id;
//            query.act = 'verify_delivery';
//        }else{
//            var order_ids=new Array();
//            $(".tabBox .tab_box.active").find("input[name='my-radio']:checked").each(function(){
//                order_ids.push($(this).attr("data_id"));
//            });
//            query.order_ids=JSON.stringify(order_ids);
//            query.act = 'verify_no_delivery';
//        }
//        $.ajax({
//            url: order_url,
//            data: query,
//            dataType: "json",
//            type: "POST",
//            success: function(obj){
//                if(obj.status==0){
//
//                    $.toast(obj.info);
//                }
//                if(obj.status == 1){
//                    $.toast(obj.info)
//                    window.setTimeout(function(){
//                        $("#uc_logistic .tabBox .tab_box.active").attr("is_arrival",1);
//                        init_confirm_button();
//                        window.location.href=obj.jump;
//                    },1500);
//                }
//            },
//            error:function(ajaxobj)
//            {
//
////						if(ajaxobj.responseText!='')
////						alert(ajaxobj.responseText);
//            }
//
//        });
//    });
});

//function init_confirm_button(){
//    var status = $("#uc_logistic .tabBox .tab_box.active").attr("status");
//    if(status==1){
//        $("#uc_logistic nav.bar-tab").hide();
//    }else{
//        $("#uc_logistic nav.bar-tab").show();
//    }
//}
$(document).on("pageInit", "#biz_shop_verify", function(e, pageId, $page) {
	$(".biz-link-bar").on('click', '.j-qrcode', function() {
		if(app_index == 'wap'){
			$.toast("手机浏览器暂不支持，请下载APP");
		}
	});
	$(".biz-manager-bar").on('click', '.j-unopen', function() {
		$.toast("暂未开放");
	});
	$(".biz-manager-bar").on('click', '.store_pay_unopen', function() {
		$.toast("没有操作权限");
	});
	$(".to-qrcode").on('click', function() {
		if(is_store_payment==1){
			if(open_store_payment_count>0){
				
			}else{
				$.toast("不存在支持到店买单的门店");
				return false;
			}
		}else{
			$.toast("该商户不支持到店买单");
			return false;
		}
	});


/* 消费券验证 */
	var pre_coupon_pwd="";
	$("input[name='qr_code']").keyup(function(){
		var coupon_pwd = $(this).val();
		var code_len = coupon_pwd.length;
		var code_rule = /^[0-9]{12}$/;

		if(pre_coupon_pwd == coupon_pwd){

		}else{
			pre_coupon_pwd = coupon_pwd;
			if(code_len == 12){
				if(!code_rule.test(coupon_pwd)){
					$.toast('您输入的券码无效');
				}
				else{
					$.post(index_check_url, { "coupon_pwd": coupon_pwd },function(data){
						if (data.status){
							$(".code-input").val("");
							location.href = data.jump+'&coupon_pwd='+coupon_pwd;
						}else{
							$.toast(data.info);
						}
					}, "json");
				}
			}else if (code_len > 12){
				$.toast('您输入的券码无效');
			}
		}
	});


});



$(document).on("pageInit", "#biz_store_pay_order", function(e, pageId, $page) {
	function stopPropagation(e) {
			if (e.stopPropagation)
				e.stopPropagation();
			else
				e.cancelBubble = true;
		}
	$(document).bind('click', function() {
		$(".m-month-list").removeClass('active');
	});
	$(document).on('click','.j-month-select',function(e) {
		stopPropagation(e);
		$(".m-month-list").addClass('active');
	});


	$(".j-month").unbind();
	$(".j-month").bind('click', function() {
	//$(document).on('click', '.j-month', function() {
		$.showIndicator();
		$.loadPage($(this).attr("data-href"));
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		/*$(".m-month-list").removeClass('active');
		var url=$(this).attr("data-href");
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					return;
				}else{
					init_list_scroll_bottom();
				};
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);*/
	});
});
$(document).on("pageInit", "#biz_tuan_order", function(e, pageId, $page) {
	init_list_scroll_bottom();
	var swiperm = new Swiper(".j-order-shop-img", {
	    scrollbarHide: true,
	    slidesPerView: 'auto',
	    freeMode: false,
	});
});
$(document).on("pageInit", "#biz_user_login", function(e, pageId, $page) {
	clear_input($('#account_name'),$('.j-name-clear'));
	clear_input($('#account_password'),$(".j-password-clear"));
	$("#login-btn").bind("click",function(){
		var account_name = $.trim($("input[name='account_name']").val());
		var account_password = $.trim($("input[name='account_password']").val());
		var form = $("form[name='user_login_form']");
		if(!account_name){
			$.toast("请填写账户名称");
			return false;
		}
		if(!account_password){
			$.toast("请输入密码");
			return false;
		}

		var query = $(form).serialize();
		var ajaxurl = $(form).attr("action");
		$.ajax({
			url:ajaxurl,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data["status"]==1){
					$.toast(data.info);
					window.setTimeout(function(){
						location.href = data.jump;
					},1500);
				}else{
					$.toast(data.info);
					return false;
				}
			}
			,error:function(){
				$.toast("服务器提交错误");
				return false;
			}
		});
		return false;
	});
});
$(document).on("pageInit", "#biz_withdrawal_bindbank", function(e, pageId, $page) {

	$("#btn").bind("click",function(){
		var phone=$("#phonenumer").val();
		if(phone==""){
			$.toast("请到PC端绑定手机");
		}
	});
	
	$("form[name='add_card']").bind("submit",function(){		
		var bank_name = $("form[name='add_card']").find("input[name='bank_name']").val();
		var bank_account = $("form[name='add_card']").find("input[name='bank_account']").val();
		var bank_user = $("form[name='add_card']").find("input[name='bank_user']").val();
		var sms_verify = $("form[name='add_card']").find("input[name='sms_verify']").val();		
		if($.trim(bank_name)=="")
		{
			$.toast("请输入开户行名称");
			return false;
		}
		if($.trim(bank_account)=="")
		{
			$.toast("请输入开户行账号");
			return false;
		}
		if($.trim(bank_user)=="")
		{
			$.toast("请输入开户人真实姓名");
			return false;
		}
		if($.trim(sms_verify)=="")
		{
			$.toast("请输入短信验证码");
			return false;
		}
		
		var ajax_url = $("form[name='add_card']").attr("action");
		var query = $("form[name='add_card']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$.toast(obj.info);	
					setTimeout(function(){
						location.href = obj.jump;
					},1500);
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)location.href = obj.jump;
					}
					
				}
				else{
					
				}
			}
		});		
		return false;
	});
});

$(document).on("pageInit", "#biz_withdrawal_form", function(e, pageId, $page) {
	$(".ui-textbox").val('');
	$("form[name='withdrawal_form']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>all_money){
			$.toast("提现超额");
			$(this).val(all_money);
		}
	});

	submit();
	function submit(){	
		$(".withdrawal_submit").bind("click",function(){	
			$(".withdrawal_submit").attr('disabled',"true");
			setTimeout(function(){
				$(".withdrawal_submit").removeAttr("disabled");
			},3000);
			
			var money = $("form[name='withdrawal_form']").find("input[name='money']").val();
			var pwd = $("form[name='withdrawal_form']").find("input[name='pwd_verify']").val();
			if(is_bank=="")
			{	
				$.toast("请先绑定银行卡");
				setTimeout(function(){
					load_page($(".load_page"));
				},1000);
				return false;
			}
			
			if($.trim(pwd)=="")
			{
				$.toast("请输入登录密码");
				return false;
			}
			
			if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
			{
				$.toast("请输入正确的提现金额");
				return false;
			}
			
			var ajax_url = $("form[name='withdrawal_form']").attr("action");
			var query = $("form[name='withdrawal_form']").serialize();
			//console.log(query);
			$.ajax({
				url:ajax_url,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1){
						$(".ui-textbox").val('');
						$.toast("提现申请成功，请等待管理员审核");
						if(obj.jump){
							setTimeout(function(){
								$.router.load(obj.jump, true);
								//location.href = obj.jump;
							},1500);
						}
					}else if(obj.status==0){
						if(obj.info)
						{
							$.toast(obj.info);
							if(obj.jump){
								setTimeout(function(){
									$.router.load(obj.jump, true);
									//location.href = obj.jump;
								},1500);
							}
						}
						else
						{
							if(obj.jump)$.router.load(obj.jump, true);
						}
						
					}
				}
			});		
			return false;
		});
	}
});

/**
 * Created by Administrator on 2016/9/7.
 */


$(document).on("pageInit", "#cart", function(e, pageId, $page) {
    $(".j-youhui").on('click', function() {
        $(".youhui-mask").addClass('active');
        $(".cart-youhui-box").addClass('active');
        
        var query = new Object();
        query.id = $(this).attr("data-id");
    	query.act = "get_youhui";
        
        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                
                $(".cart-youhui-box").find(".shop-name").html(data.supplier_name);
                $(".cart-youhui-box").find(".youhui-wrap").empty();
                
                var lenght=data.list.length;
                var li="";
                for(var i=0;i<lenght;i++){
                	
                	if(data['list'][i]['status']==1){
	                	li+="<div class='youhui-item b-line flex-box'>"+
									"<div class='youhui-info flex-1'>"+
										"<p class='youhui-price'>"+data['list'][i]['youhui_value']+"元</p>"+
										"<p class='youhui-tip'>"+data['list'][i]['use_info']+"</p>"+
										"<p class='youhui-time'>使用期限："+data['list'][i]['time_info']+"</p>"+
									"</div>"+
									"<a href='javascript:void(0);' class='youhui-btn j-get-youhui' data-id='"+data['list'][i]['id']+"' status='"+data['list'][i]['status']+"'>"+data['list'][i]['status_info']+"</a>"+
								"</div>";
                	}else{
                		li+="<div class='youhui-item b-line flex-box'>"+
								"<div class='youhui-info flex-1'>"+
									"<p class='youhui-price'>"+data['list'][i]['youhui_value']+"元</p>"+
									"<p class='youhui-tip'>"+data['list'][i]['use_info']+"</p>"+
									"<p class='youhui-time'>使用期限："+data['list'][i]['time_info']+"</p>"+
								"</div>"+
								"<a href='javascript:void(0);' class='youhui-btn' style='border: 1px solid gray;color: gray;' data-id='"+data['list'][i]['id']+"' status='"+data['list'][i]['status']+"'>"+data['list'][i]['status_info']+"</a>"+
							"</div>";
                	}
                }
                $(li).appendTo($(".cart-youhui-box .youhui-wrap"));
               
            }
            ,error:function(){
            }
        });
        
    });
    
    $(".cart-youhui-box").on('click', ".j-get-youhui" ,function() {
    	var status=$(this).attr("status");
    	
    	$(".youhui-mask").removeClass('active');
        $(".cart-youhui-box").removeClass('active');
        
    	if(status==1){
	    	var query = new Object();
	        query.data_id = $(this).attr("data-id");
	    	query.act = "download_youhui";
	    	
	    	$.ajax({
	            url:CART_URL,
	            data:query,
	            type:"post",
	            dataType:"json",
	            success:function(data){
	            	$.toast(data.info);
	            	if(data.status){
		            	setTimeout(function(){
		            		window.location.reload();
		            	},1000);
	            	}
	            }
	            ,error:function(){
	            }
	        });
    	}else{
    		$.toast("您已经领取了优惠券，留一点给别人吧~");
    	}
    });
    
    $(".j-close-mask").on('click', function() {
        $(".youhui-mask").removeClass('active');
        $(".cart-youhui-box").removeClass('active');
    });
    count_buy_total();
    //count_buy_total(1);
    isSelect();
    /*编辑按钮点击事件开始*/
    $(".j-edit-cur").unbind("click");
    $(".j-edit-cur").click(function () {
        var deal_json_key='dealkey_161010493611354';
        var $this=$(this);
        var curBtn=$this.text();
        var $parents=$this.parent().parent().parent();

        if(curBtn=="编辑"){
            $parents.find(".m-cart-list li .z-opera-sure").hide();
            $parents.find(".m-cart-list li .z-opera-edit").addClass("active");
            $this.text("完成");
            isSelect();
        }else if(curBtn=="完成"){
            $parents.find(".m-cart-list li .z-opera-sure").show();
            $parents.find(".m-cart-list li .z-opera-edit").removeClass('active');
            $this.text("编辑");
            isSelect();
        }
    });

    $(".j-edit-all").unbind("click");
    $(".j-edit-all").click(function () {
        var allBtn= $(this).text();
        if(allBtn=="编辑全部"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").hide();
            $(".m-cart-list li .z-opera-edit").addClass("active");
            $(".j-del-order").show().text("删除("+accnum+")");
            $(".allCount").hide();
            $(".j-accounts").hide();
            $(".j-edit-cur").hide();
            isSelect();
            $(this).text("完成");
        }else if(allBtn=="完成"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").show();
            $(".m-cart-list li .z-opera-edit").removeClass('active');
            $(this).text("编辑全部");
            $(".j-del-order").hide();
            $(".allCount").show();
            $(".j-edit-cur").show().text("编辑");
            $(".j-accounts").show().text("结算("+accnum+")");
            isSelect();
        }
    });
    /*编辑按钮点击事件结束*/


    /*点击删除按钮*/
    $(".z-opera-edit").off('click','.confirm-ok');
    $(".z-opera-edit").on('click','.confirm-ok', function () {
        var _this=$(this);
        var _parent=$(_this).parents(".j-select-body");
        var parents=$(_this).parents(".j-conBox");
        $.confirm('确定要删除这个宝贝吗？', function () {
            var query = new Object();
            var id = parseInt($(_this).parents("li").attr("data-id"));
            var ids = new Array();
            ids.push(id);
            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents("li").remove();
                        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
                        $(".j-del-order").text("删除("+accn+")");

                        var childLen=_parent.find("li").length;
                        if(childLen==0){
                            parents.remove();
                        }

                        var count=isSelect();
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });


        });
    });

    /*点击全删除按钮*/


    /*点击删除全部按钮*/
    // $(document).on('click','.j-del-order', function () {
    $('.j-del-order').off('click');
    $('.j-del-order').on('click', function() {
        var _this=$(this);
        $.confirm('确定要删除所选宝贝吗？', function () {
            var checkBox=$(".m-conBox").find("input[type=checkbox]:checked");
            if(checkBox.length==0){
                $.confirm('没有选择宝贝');
            }

            var query = new Object();
            var ids = new Array();
            var checked_box = $(".m-cart-list").find("input[type=checkbox]:checked");
            checked_box.each(function(){
                var id = parseInt($(this).parents("li").attr("data-id"));
                ids.push(id);
            });

            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {

                        checkBox.parent().parent().remove();
                        $(".j-del-order").text("删除(0)");
                        var count=isSelect();
                        $(".j-select-all label input[type=checkbox]").prop("checked",false);
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });





        });
    });
    /*点击删除全部按钮*/



    /*返回按钮*/
    /*
     $(document).on('click','.j-sure-cancel', function () {
     var _this=$(this);
     $.confirm('您确定要取消订单吗？', function () {
     window.history.back(-1);
     });
     });
     */



    /*输入框加减按钮*/
    $(".u-add").click(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        //var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        val++;
        var num=$(".u-txt[deal-id='"+id+"']").length;
        if(val>max && max!=-1){
            val=max;
        }

        if(num==1){
            if((max>user_max && max!=-1) || (max==-1)){
                if(user_max>0 && val>user_max){
                    val=user_max;
                    $.alert("该商品最多还能购买"+user_max+"件");
                }
            }
        }else{
            var allval=0;
            $(".u-txt[deal-id='"+id+"']").each(function(){
                allval+=parseInt($(this).val());
            });
            if(user_max>0 && allval>=user_max){
                $.alert("该商品最多还能购买"+user_max+"件");
                if(val>1){
                    val=val-1;
                }
            }
            if(val>max && max!=-1){
                $.alert("库存不足");
                val=max;
            }
        }

        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });
    $(".u-reduce").click(function () {
        var val=$(this).parent().find(".u-txt").val();
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;
        val--;
        /*if(num==1){
         if(user_min>0 && val<user_min){
         val=user_min;
         alert("该商品最小购买量为"+user_min);
         }
         }else{
         var allval=0;
         $(".u-txt[deal-id='"+id+"']").each(function(){
         allval+=parseInt($(this).val());
         });
         if(user_min>0 && allval<=user_min){
         alert("该商品最小购买量为"+user_min);
         val=val+1;
         }
         }*/
        if(val<1){
            val=1;
        }
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        $(this).parent().find(".u-txt").val(val);
        isSelect();
    });
    /*改变编辑框数量*/
    $(".u-txt").blur(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;

        if(val>0){
            if(num==1){
                if(user_max>0 && val>user_max){
                    if( (user_max<max && max!=-1) || (max==-1)){
                        val=user_max;
                        $.alert("该商品最多还能购买"+user_max+"件");
                    }

                }/*else if(user_min>0 && val<user_min){
                 val=user_min;
                 alert("该商品最小购买量为"+user_min);
                 }*/else{
                    if(val>max && max!=-1){
                        val=max;
                        $.alert("该商品库存不足");
                    }else{
                        val=val;
                    }
                }
            }else{
                var allval=0;

                $(".u-txt[deal-id='"+id+"']").each(function(){
                    allval+=parseInt($(this).val());
                });
                var elseval=allval-val;
                if(user_max>0 && allval>=user_max){
                    $.alert("该商品最多还能购买"+user_max+"件");
                    val=user_max-elseval;
                }/*else if(user_min>0 && allval<=user_min){
                 alert("该商品最小购买量为"+user_min);
                 val=user_min-elseval;
                 }*/
                if(val>max && max!=-1){
                    val=max;
                    $.alert("该商品库存不足");
                }else{
                    val=val;
                }

            }
        }else{
            /*if(user_min>0){
             val=user_min;
             }else{*/
            val=1;
            /*}*/
            $.alert("请输入有效数字");
        }
        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });



    /*点击清空按钮*/
    $('.j-clear-all').off('click');
    $('.j-clear-all').on('click', function () {
        var _this=$(this);
        $(_this).removeClass('j-clear-all');
        $.confirm('您确定要清空失效商品吗？', function () {
        	
            var disable_id = new Array();
            $(".m-invalid .m-cart-list .item-content").each(function(i,obj){
                disable_id.push($(obj).attr("data-id"));
            });
            var query = new Object();
            query.act='clear_deal_cart';
            query.id = disable_id;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents(".m-invalid").remove();
                        var count=isSelect();
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                    $(_this).addClass('j-clear-all');
                }
                ,error:function(){
                	$(_this).addClass('j-clear-all');
                }
            });

        });
    });


    /*全选按钮点击事件*/
    $(".j-select-all label input[type=checkbox]").change(function () {
        if($(this).attr('checked')==false){
            //如果全选按钮没有选中，则列表的中的按钮也全部是未选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",false);
        }else {
            //如果全选按钮选中，则列表的中的按钮也全部是选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
    });



    /*列表中头部checkbox按钮点击事件开始*/

    $(".j-select-title input[type=checkbox]").change(function () {
        if($(this).is(':checked')==false){
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",false);
        }else {
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");

    });
    /*列表中头部checkbox按钮点击事件结束*/

    /*宝贝列表单个checkbox点击事件开始*/
    $(".j-select-body input[type=checkbox]").change(function () {
        isSelect();

        var _samePar=$(this).parents(".m-cart-list").find("input[type=checkbox]");
        var _len=_samePar.length;
        _samePar.each(function () {
            var anum=$(this).parents(".m-cart-list").find("input[type=checkbox]:checked").length;

            if(anum<_len){
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",false);
            }else {
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",true);
            }
        });

    });
    /*宝贝列表单个checkbox点击事件接结束*/

    /*判断是否全部选中*/
    function isSelect() {
        var _checkbox=$(".m-cart-list label input[type=checkbox]");
        var _radio=$(".m-cart-list label input[type=checkbox]:checked");

        var _lenght=_checkbox.length;

        _checkbox.each(function () {
            var a=$(".m-cart-list label input[type=checkbox]:checked").length;
            if(a<_lenght){
                $(".j-select-all label input[type=checkbox]").prop("checked",false);
            }else {
                $(".j-select-all label input[type=checkbox]").prop("checked",true);
                $(".j-select-title input[type=checkbox]").prop("checked",true);
            }
        });

        var allprice = 0;
        var promote_price = 0;
        var promote_count = 0;
        var select_count = 0;
        _radio.each(function () {
            var data_price=parseFloat($(this).parents("li").find(".u-money").attr("data_value"));
            var data_num=parseInt($(this).parents("li").find(".j-count-num").text());
            var allow_promote = parseInt($(this).parents("li").attr("allow_promote"));
            select_count++;
            var account=data_num*data_price;
            allprice+=account;
            if(allow_promote==1){
                promote_price+=account;
                promote_count++;
            }
        });


        if(typeof(promote_cfg)!='undefined'){
            if(promote_cfg && promote_count==select_count){
                var all_promote_price=0;
                for(var i=0;i<promote_cfg.length;i++){
                    if(promote_price >= parseInt(promote_cfg[i]['discount_limit'])){
                        allprice -= parseInt(promote_cfg[i]['discount_amount']);
                        all_promote_price+=parseInt(promote_cfg[i]['discount_amount']);
                    }
                }
                $("#promote_price").html("¥"+all_promote_price);
            }else{
                $("#promote_price").html("¥0");
            }
        }
        allprice = allprice.toFixed(2);
        var priceStr=allprice.toString();
        if(priceStr.indexOf(".") > 0 ){
            var price_split=priceStr.split(".");
            $(".j-price-int").text(price_split[0]);
            $(".j-price-piont").text(price_split[1]);
        }else {
            $(".j-price-int").text(priceStr);
            $(".j-price-piont").text("00");
        }


        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        var allaccn=$(".m-conBox .j-select-body").find("input[type=checkbox]").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");
        if(accn==0){
            $(".j-accounts").addClass("invalid");
            /*if(index){
             location.reload();
             }*/
        }else{
            $(".j-accounts").removeClass("invalid");
        }
        return allaccn;
    }


    $(".j-accounts").unbind("click");
    $(".j-accounts").click(function(){
        if(is_login==0){
            if(app_index=="app"){
                App.login_sdk();
            }else{
                $.router.load(login_url, true);
            }
            return false;
        }
        var _this=$(this);
        var _radio=$(".m-cart-list label input[type=checkbox]");
        var checked_ids = new Array();
        var nochecked_ids = new Array();
        $(_radio).each(function(){
            var id = $(this).parents("li").attr("data-id");
            var attr = $(this).parents("li").find(".sizes").attr("attr_key");
            var attr_str = $(this).parents("li").find(".sizes").attr("attr_str");
            var number = parseInt($(this).parents("li").find(".j-count-num").text());
            var cart_item = new Object();
            cart_item.id = id;
            cart_item.attr = attr;
            cart_item.attr_str = attr_str;
            cart_item.number = number;
            if($(this).is(":checked")){
                checked_ids.push(cart_item);
            }else{
                nochecked_ids.push(cart_item);
            }

        });
        var disable_raido = $(".m-invalid .m-cart-list li");
        $(disable_raido).each(function(){ //失效商品
            var id = parseInt($(this).attr("data-id"));
            var cart_item = new Object();
            cart_item.id = id;
            nochecked_ids.push(cart_item);
        });

        //console.log(nochecked_ids);return false;
        if(checked_ids.length==0){
            return false;
        }
		$.showIndicator();
        var query = new Object();
        query.act='set_cart_status';
        query.checked_ids = checked_ids;
        query.nochecked_ids = nochecked_ids;

        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    $.hideIndicator();
					$.alert(data.info,function(){
						if(app_index=="app"){
							App.login_sdk();
						}else{
							window.location.href=data.jump;
						}
                    });
                    /*window.setTimeout(function(){
                    	//$.router.load(data.jump,true);
                    	window.location.href=data.jump;
                    },1000);*/

                }else if(data.status==1)
                {
        			
        			
        		    setTimeout(function () {
        		    	 $.hideIndicator();
        		    }, 2000);
					location.href = cart_check_url;
					
                  //  $.router.load(cart_check_url, true);
                }else{
					$.hideIndicator();
                	$.alert(data.info,function(){
                		if(data.jump){
                			window.location.href=data.jump;
                		}
                	});
                }
            }
            ,error:function(){
				$.hideIndicator();
            }
        });


    });



    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(".j-cancel").click(function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(".j-trans").click(function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });
    $(".j-reward").click(function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_hei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });


    /*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
    /*弹出层动画效果*/

    /*弹出框点击事件*/
    function listCli(obj) {
        obj.click(function () {
            var lue_name=$(this).find(".pay-way-name .j-company-name").text();
            var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
            var lue_reward=$(this).find(".pay-way-name").text();

            var parText=$(obj).parents(".m-trans-way").find(".u-ti").text();

            $(this).parents("ul").find("input").prop("checked",false);
            if(parText=="配送方式"){
                $(this).find("input[name='delivery']").prop("checked",true);
                var is_pick=$(this).find("input[name='delivery']").val();
                //alert(is_pick);
                $(".j-trans .j-trans-commpany").find(".j-company-name").text(lue_name);
                if(is_pick!=-1){
                    $(".j-trans .j-trans-commpany").find(".j-company-money").text(lue_momey);
                    $("#delivery-address").show();
                }else{
                    $(".j-trans .j-trans-commpany").find(".j-company-money").text("");
                    $("#delivery-address").hide();
                }
            }
            if(parText=="红包"){
                $(this).find("input[name='ecvsn']").prop("checked",true);
                $(".j-reward .j-reward-money").text(lue_reward);
            }
            setTimeout(function () {
                $(".totop").removeClass("vible");
            },500);
            popupTransition();
            count_buy_total();
        });
    }

    listCli($(".j-reward-list li"));
    listCli($(".j-trans-list li"));








    /*弹层开始*/
    $(".choose-list .j-choose").click(function(){
        $(this).siblings(".j-choose").removeClass("active");
        $(this).addClass("active");
        setSpecgood();
        var data_value= $(".j-choose.active").attr("data-value");
        var data_value = []; // 定义一个空数组
        var txt = $('.j-choose.active'); // 获取所有文本框
        for (var i = 0; i < txt.length; i++) {
            data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
        }
        $(".good-specifications span").empty();
        $(".good-specifications span").addClass("isChoose");
        $(".good-specifications span").append("已选规格：");
        $.each(data_value,function(i){
            $(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
            //传值可以考虑更改这里
            $(".spec-data").attr("data-value"+[i],data_value[i]);
        });
    });


    $(".j-box-bg").click(function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });



    
    $(".j-open-choose").bind("click",open_choose);
    function open_choose(){
        $(".j-flippedout-close").attr("rel","spec");
        $(".j-spec-choose-close").attr("rel","spec");
        $(".flippedout").addClass("showflipped").addClass("z-open");
        $(".spec-choose").addClass("z-open");
        $(".totop").addClass("vhide");//隐藏回到头部按钮
        var $this=$(this);
        $(this).unbind("click");
        $this.parents("li").addClass("choose");
        //调用属性HTML
        var id =  $this.parents("li").attr("data-id");
        var attr_key = $this.parents("li").find(".sizes").attr("attr_key");
        var query = new Object();
        query.act='get_cart_deal_attr';
        query.id = id;
        query.attr_key = attr_key;
        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    location.href=data.jump;
                }else if(data.status==1)
                {
                    $(".page-current .cart_box").html(data.html);
                    set_attr_name();
                    $(".flippedout .choose-list .j-choose").click(function(){
                        if(!$(this).hasClass("active")){
                            $(this).siblings(".j-choose").removeClass("active");
                            $(this).addClass("active");
                            set_attr_name();
                        }
                    });


                    $(".j-spec-choose-close,.j-flippedout-close,.j-cancel-flip").click(function(){
                        cssAnition();
                    });

                    $(".j-nowbuy").click(function () {
                        if($(this).attr('max') && $(this).attr('max')==0){
                            $.alert("库存不足");
                        }else{
                            if($this.parents("li").hasClass("choose")){
                                $this.parents("li").removeClass("choose");
                            }

                            var attr_check_ids = new Array();
                            var attr_name = '';
                            $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
                                attr_name+=$(obj).text();
                                attr_check_ids.push($(obj).attr("data-id"));
                            });

                            if(attr_check_ids.length==attr_num){

                                var attr_checked_ids = attr_check_ids.join(",");
                                //同步属性
                                $this.parents("li").find(".sizes").attr({'attr_key':attr_checked_ids,'attr_str':attr_name}).text("规格:"+attr_name);
                                var deal_name=$this.parents("li").find(".item-subtitle a").attr('deal-name'); 
                                if(deal_name){
                                	$this.parents("li").find(".item-subtitle a").html(deal_name+"["+attr_name+"]");
                                }
                                
                                //同步值
                                if($(this).attr('max') != '不限'){

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("仅剩"+$(this).attr('max')+"件");
                                    if($(this).attr('max')>=10){
                                    	$(".item-content[data-id='"+id+"']").find(".u-surplus").hide();
                                    }else{
                                    	$(".item-content[data-id='"+id+"']").find(".u-surplus").show();
                                    }
 
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                    var $val=parseInt($val);
                                    var $max=parseInt($(this).attr('max'));
                                    if($val>$max){
                                        $(".item-content[data-id='"+id+"']").find("input[type=text]").val($max);
                                        $(".item-content[data-id='"+id+"']").find(".j-count-num").text($max);
                                        isSelect();
                                    }
                                }else{

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("");
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                }

                                //同步价格
                                var num=parseFloat($(".showflipped .spec-goodprice").attr("data_value"));
                                num = Math.round(num*100)/100;  //保留两位小数
                                num =Number(num).toFixed(2);  //保留两位小数
                                var num_arr = num.split('.');
                                var price_str='¥ <i class="j-goods-money">'+num_arr[0]+'.</i>'+num_arr[1];
                                $this.parents("li").find(".u-money").attr("data_value",num).html(price_str);

                                cssAnition();
                            }else{
                                $.alert("请选择属性");
                            }
                        }
                    });

                }else{
                    $.alert(data.info);
                }
            }
            ,error:function(){
            }
        });
    }

    function set_attr_name(){
        var attr_name='';
        var attr_check_ids = new Array();
        var attr_check_key='';
        var deal_price = deal_current_price; // 商品基础价
        $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
            attr_name+='&nbsp;&nbsp;'+$(obj).text();
            attr_check_ids.push($(obj).attr("data-id"));
        });

        var attr_check_ids_new = attr_check_ids.sort();
        attr_check_key=attr_check_ids_new.join("_");
        if(deal_attr_stock_json[attr_check_key]){
            var stock = deal_attr_stock_json[attr_check_key]['stock_cfg'];
            if(parseInt(stock)<0){
                stock = '不限';
            }
            deal_price += parseFloat(deal_attr_stock_json[attr_check_key]['price']);
        }else{
            var stock = '不限';
        }
        $(".spec-goodspec").empty();
        $(".spec-goodspec").append("已选择");
        //$(".spec-goodspec em").html(attr_name);
        $(".spec-goodspec").append("<em class='choose_item'>" + attr_name + "</em>");
        $(".spec-goodstock").text("库存:"+stock);
        $(".j-nowbuy").attr("max",stock);

        
        /*$.each(deal_attr_json,function(i,obj){
            $.each(obj['attr_list'],function(xi,xobj){
                if($.inArray(xobj.id,attr_check_ids_new) >= 0){

                    deal_price += parseFloat(xobj.price);
                }
            });

        });*/

		deal_price=parseFloat(deal_price).toFixed(2);
        $(".spec-goodprice").attr("data_value",deal_price).html("¥"+deal_price);

    }


    function cssAnition() {
        $(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-open-choose").bind("click",open_choose);
    }


    function count_buy_total()
    {
        ajaxing = true;
        var query = new Object();

        //获取配送方式
        var delivery_id = $("input[name='delivery']:checked").val();

        if(!delivery_id)
        {
            delivery_id = 0;
        }
        query.delivery_id = delivery_id;

        var address_id = $("input[name='address_id']").val();

        //全额支付
        if($("input[name='all_account_money']").attr("checked"))
        {
            query.all_account_money = 1;
        }
        else
        {
            query.all_account_money = 0;
        }

        //代金券
        var ecvsn = $("input[name='ecvsn']:checked").val();

        if(!ecvsn)
        {
            ecvsn = '';
        }

        var ecvpassword = $("input[name='ecvpassword']").val();
        if(!ecvpassword)
        {
            ecvpassword = '';
        }

        var buy_type = $("input[name='buy_type']").val();
        query.ecvsn = ecvsn;
        query.ecvpassword = ecvpassword;
        query.address_id = address_id;
        query.buy_type = buy_type;
        //支付方式
        var payment = $("input[name='payment']:checked").val();
        if(!payment)
        {
            payment = 0;
        }
        query.payment = payment;
        query.bank_id = $("input[name='payment']:checked").attr("rel");
        query.id = order_id;
        //query.reward = reward;
        query.act = "count_buy_total";
        $.ajax({
            url: AJAX_URL,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){
                //alert(1111);
                /*if(data.free && delivery_id!=-1){
                 $(".j-company-money").html("运费：0");
                 }*/
                if(data.total_price==0 && $('div').is('.voucher_box')){
                    $(".voucher_box").remove();
                    count_buy_total();
                }
                /*if(reward==1){*/
                $("#cart_total").html(data.html);
                $(".total_price_box").html(data.pay_price_html);
                ajaxing = false;
                /*}else{
                 var ecv_money = parseFloat($("input[name='ecvsn']:checked").attr("money"));
                 var pay_moeny = parseFloat(data);
                 if(pay_moeny<ecv_money){
                 //$("div.j-reward-money").html("不使用红包");
                 var now_ecv=0;
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(pay_moeny<this_money){
                 $(this).remove();
                 }else{
                 if(this_money>now_ecv){
                 now_ecv=this_money;
                 }
                 }
                 });
                 now_ecv=parseFloat(now_ecv);
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(this_money==now_ecv){
                 $(".j-reward-list").find("input[name='ecvsn']").removeAttr("checked");   ;
                 $(this).find("input[name='ecvsn']").attr("checked","checked");
                 $("div.j-reward-money").html($(this).find(".pay-way-name").html());
                 }
                 });
                 }*/
                //count_buy_total(1);
                //}
            },
            error:function(ajaxobj)
            {
//    			if(ajaxobj.responseText!='')
//    			alert(LANG['REFRESH_TOO_FAST']);
            }
        });
    }
    
    
    $(".go_pay").unbind("click");
    $(".go_pay").click(function(){

        var query = $("#pay_box").serialize();
        //console.log(query);
        /*if($("input[name='payment']:checked").val()==-1){
         query['is_pick']=1;
         }else{
         query['is_pick']=0;
         }*/
        var url = $("#pay_box").attr("action");
        $.ajax({
            url: url,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){

                if(data.status==1)
                {
                    location.href=data.jump;
                }else{
                    $.alert(data.info);
                }

                ajaxing = false;
            },
            error:function(ajaxobj)
            {

            }
        });

    });


});

/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#cart_check", function(e, pageId, $page) {
    //打开发票须知
    $(document).on('click','.j-open-invoice-popup', function () {
      $.popup('.invoice-popup');
    });
    //发票类型
    $(document).off('click', '.j-open-type');
    $(document).on('click', '.j-open-type', function() {
        var shop_id=$(this).parents(".m-invoice-box").attr('shop-id');
        $(".invoice-type-box").attr('shop-id', shop_id);
        $('.invoice-type-box').addClass('active');
        $(".m-mask").addClass('active');
    });
    $(document).off('click', '.j-select-type');
    $(document).on('click', '.j-select-type', function() {
        var val_id=$(this).attr('value');
        var shop_id=$(this).parents(".invoice-type-box").attr('shop-id');
        var obj=$(".m-invoice-box[shop-id='"+shop_id+"']");
        obj.find('.invoice-type .invoice-tip').html($(this).find('.invoice-type').html());
        obj.find('.invoice-type input').val($(this).attr('value'));
        if (val_id==0) {
            obj.find('.invoice-detail').addClass('hide');
        } else if (val_id == 1) {
            obj.find('.invoice-detail').removeClass('hide');
            obj.find('.inv-tax-box').addClass('hide');
        } else {
            obj.find('.invoice-detail').removeClass('hide');
            obj.find('.inv-tax-box').removeClass('hide');
        }
    });
    //发票内容
    $(document).off('click', '.j-open-info');
    $(document).on('click', '.j-open-info', function() {
        var shop_id = $(this).parents('.m-invoice-box').attr('shop-id');
        var link_shop_id = shop_id;
        if(! parseInt(shop_id)) {
            link_shop_id = shop_id
            shop_id = 0;
        }
        $('div[shop-id="'+shop_id+'"]').attr('link-shop-id', link_shop_id);
        $('div[shop-id="'+shop_id+'"]').addClass('active');
        $('.invoice-type-box').removeClass('active');
        $(".m-mask").addClass('active');
    })
    $(document).off('click', '.j-select-info');
    $(document).on('click', '.j-select-info', function() {
        var shop_id=$(this).parents(".invoice-info-box").attr('link-shop-id');
        var obj=$(".m-invoice-box[shop-id='"+shop_id+"']");
        obj.find('.invoice-info .invoice-tip').html($(this).find('.invoice-info').html());
        obj.find('.invoice-info input').val($(this).attr('value'));
    });

    // 关闭弹层
    $(document).off('click', '.j-close-select');
    $(document).on('click', '.j-close-select', function() {
        $(".m-select-box").removeClass('active');
        $(".m-mask").removeClass('active');
    });

    var _close=false;
    $(document).on('click',"#cart_check .remarkBox p.remarkTitle",function () {
    	var remarkArea = $(this).siblings('.remarkArea');
        if(_close==false){
            $(remarkArea).show();
            return _close=true;
        }
        if(_close==true){
            $(remarkArea).hide();
            return _close=false;
        }
    });

    /*$("#cart_check .remarkBox .remarkArea textarea")[0].oninput=function () {
        var _value=$(this).val();

        $(".remarkBox .textInfo").attr("data_val",_value);
        // console.log($(".remarkBox .textInfo").attr("data_val"));
    };*/
    $('#cart_check .remarkBox .remarkArea textarea').on('input propertychange', function() {
        var that = $(this),
            _val = that.val();
        if (_val.length > 100) {
            that.val(_val.substring(0, 100));
        }
    });

    count_buy_total();
    //count_buy_total(1);
    isSelect();
    /*编辑按钮点击事件开始*/
    $(".j-edit-cur").click(function () {
        var deal_json_key='dealkey_161010493611354';
        var $this=$(this);
        var curBtn=$this.text();
        var $parents=$this.parent().parent().parent();

        if(curBtn=="编辑"){
            $parents.find(".m-cart-list li .z-opera-sure").hide();
            $parents.find(".m-cart-list li .z-opera-edit").addClass("active");
            $this.text("完成");
            isSelect();
        }else if(curBtn=="完成"){
            $parents.find(".m-cart-list li .z-opera-sure").show();
            $parents.find(".m-cart-list li .z-opera-edit").removeClass('active');
            $this.text("编辑");
            isSelect();
        }
    });

    $(".j-edit-all").click(function () {
        var allBtn= $(this).text();
        if(allBtn=="编辑全部"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").hide();
            $(".m-cart-list li .z-opera-edit").addClass("active");
            $(".j-del-order").show().text("删除("+accnum+")");
            $(".allCount").hide();
            $(".j-accounts").hide();
            $(".j-edit-cur").hide();
            isSelect();
            $(this).text("完成");
        }else if(allBtn=="完成"){
            var accnum=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
            $(".m-cart-list li .z-opera-sure").show();
            $(".m-cart-list li .z-opera-edit").removeClass('active');
            $(this).text("编辑全部");
            $(".j-del-order").hide();
            $(".allCount").show();
            $(".j-edit-cur").show().text("编辑");
            $(".j-accounts").show().text("结算("+accnum+")");
            isSelect();
        }
    });
    /*编辑按钮点击事件结束*/


    /*点击删除按钮*/

    $(document).on('click','.confirm-ok', function () {
        var _this=$(this);
        var _parent=$(_this).parents(".j-select-body");
        var parents=$(_this).parents(".j-conBox");
        $.confirm('确定要删除这个宝贝吗？', function () {

            var query = new Object();
            var id = parseInt($(_this).parents("li").attr("data-id"));
            var ids = new Array();
            ids.push(id);
            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents("li").remove();
                        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
                        $(".j-del-order").text("删除("+accn+")");

                        var childLen=_parent.find("li").length;
                        if(childLen==0){
                            parents.remove();
                        }
                        var count=isSelect();
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });


        });
    });

    /*点击全删除按钮*/


    /*点击删除全部按钮*/
    $(document).on('click','.j-del-order', function () {
        var _this=$(this);
        $.confirm('确定要删除所选宝贝吗？', function () {
            var checkBox=$(".m-conBox").find("input[type=checkbox]:checked");
            if(checkBox.length==0){
                $.confirm('没有选择宝贝');
            }

            var query = new Object();
            var ids = new Array();
            var checked_box = $(".m-cart-list").find("input[type=checkbox]:checked");
            checked_box.each(function(){
                var id = parseInt($(this).parents("li").attr("data-id"));
                ids.push(id);
            });

            query.act='clear_deal_cart';
            query.id = ids;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {

                        checkBox.parent().parent().remove();
                        $(".j-del-order").text("删除(0)");
                        var count=isSelect();
                        $(".j-select-all label input[type=checkbox]").prop("checked",false);
                        if(count==0){
                            location.reload();
                        }
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });





        });
    });
    /*点击删除全部按钮*/



    /*返回按钮*/
    /*
     $(document).on('click','.j-sure-cancel', function () {
     var _this=$(this);
     $.confirm('您确定要取消订单吗？', function () {
     window.history.back(-1);
     });
     });
     */
    $(document).off('click', '.j-sure-cancel');
    $(document).on("click",".j-sure-cancel",function(){
        var _this=$(this);
        $(this).removeClass('j-sure-cancel');
        $.confirm('您确定要取消订单吗？', function () {
        	$(_this).addClass('j-sure-cancel');
        	if(app_index=='app'){
        		App.page_finsh();
        	}else{
        		$.router.back();
        	}
        	
        	//$.router.load('#cart');
        },function(){
        	 $(_this).addClass('j-sure-cancel');
        });
    });


    /*输入框加减按钮*/
    $(".u-add").click(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        //var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        val++;
        var num=$(".u-txt[deal-id='"+id+"']").length;
        if(val>max && max!=-1){
            val=max;
        }

        if(num==1){
            if((max>user_max && max!=-1) || (max==-1)){
                if(user_max>0 && val>user_max){
                    val=user_max;
                    $.alert("该商品最多还能购买"+user_max+"件");
                }
            }
        }else{
            var allval=0;
            $(".u-txt[deal-id='"+id+"']").each(function(){
                allval+=parseInt($(this).val());
            });
            if(user_max>0 && allval>=user_max){
                $.alert("该商品最多还能购买"+user_max+"件");
                if(val>1){
                    val=val-1;
                }
            }
            if(val>max && max!=-1){
                $.alert("库存不足");
                val=max;
            }
        }

        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });
    $(".u-reduce").click(function () {
        var val=$(this).parent().find(".u-txt").val();
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;
        val--;
        /*if(num==1){
         if(user_min>0 && val<user_min){
         val=user_min;
         alert("该商品最小购买量为"+user_min);
         }
         }else{
         var allval=0;
         $(".u-txt[deal-id='"+id+"']").each(function(){
         allval+=parseInt($(this).val());
         });
         if(user_min>0 && allval<=user_min){
         alert("该商品最小购买量为"+user_min);
         val=val+1;
         }
         }*/
        if(val<1){
            val=1;
        }
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        $(this).parent().find(".u-txt").val(val);
        isSelect();
    });
    /*改变编辑框数量*/
    $(".u-txt").blur(function () {
        var val=parseInt($(this).parent().find(".u-txt").val());
        var max=parseInt($(this).parent().find(".u-txt").attr("max"));
        var user_min=parseInt($(this).parent().find(".u-txt").attr("user_min_bought"));
        var user_max=parseInt($(this).parent().find(".u-txt").attr("user_max_bought"));
        var id=parseInt($(this).parent().find(".u-txt").attr("deal-id"));
        var num=$(".u-txt[deal-id='"+id+"']").length;

        if(val>0){
            if(num==1){
                if(user_max>0 && val>user_max){
                    if( (user_max<max && max!=-1) || (max==-1)){
                        val=user_max;
                        $.alert("该商品最多还能购买"+user_max+"件");
                    }

                }/*else if(user_min>0 && val<user_min){
                 val=user_min;
                 alert("该商品最小购买量为"+user_min);
                 }*/else{
                    if(val>max){
                        val=max;
                        $.alert("该商品库存不足");
                    }else{
                        val=val;
                    }
                }
            }else{
                var allval=0;

                $(".u-txt[deal-id='"+id+"']").each(function(){
                    allval+=parseInt($(this).val());
                });
                var elseval=allval-val;
                if(user_max>0 && allval>=user_max){
                    $.alert("该商品最多还能购买"+user_max+"件");
                    val=user_max-elseval;
                }/*else if(user_min>0 && allval<=user_min){
                 alert("该商品最小购买量为"+user_min);
                 val=user_min-elseval;
                 }*/
                if(val>max){
                    val=max;
                    $.alert("该商品库存不足");
                }else{
                    val=val;
                }

            }
        }else{
            /*if(user_min>0){
             val=user_min;
             }else{*/
            val=1;
            /*}*/
            $.alert("请输入有效数字");
        }
        $(this).parent().find(".u-txt").val(val);
        $(this).parents(".item-inner").find(".j-count-num").text(val);
        isSelect();
    });



    /*点击清空按钮*/
    $(document).on('click','.j-clear-all', function () {
        var _this=$(this);
        $.confirm('您确定要清空失效商品吗？', function () {
            var disable_id = new Array();
            $(".m-invalid .m-cart-list .item-content").each(function(i,obj){
                disable_id.push($(obj).attr("data-id"));
            });
            var query = new Object();
            query.act='clear_deal_cart';
            query.id = disable_id;
            $.ajax({
                url:CART_URL,
                data:query,
                type:"post",
                dataType:"json",
                success:function(data){
                    if(data.status==-1)
                    {
                        location.href=data.jump;
                    }else if(data.status==1)
                    {
                        _this.parents(".m-invalid").remove();
                    }else{
                        $.alert(data.info);
                    }
                }
                ,error:function(){
                }
            });

        });
    });


    /*全选按钮点击事件*/
    $(".j-select-all label input[type=checkbox]").change(function () {
        if($(this).attr('checked')==false){
            //如果全选按钮没有选中，则列表的中的按钮也全部是未选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",false);
        }else {
            //如果全选按钮选中，则列表的中的按钮也全部是选中状态
            $(".m-cart").find("label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
    });



    /*列表中头部checkbox按钮点击事件开始*/

    $(".j-select-title input[type=checkbox]").change(function () {
        if($(this).is(':checked')==false){
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",false);
        }else {
            $(this).parents(".m-conBox").find(".m-cart-list label input[type=checkbox]").prop("checked",true);
        }
        isSelect();
        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");

    });
    /*列表中头部checkbox按钮点击事件结束*/

    /*宝贝列表单个checkbox点击事件开始*/
    $(".j-select-body input[type=checkbox]").change(function () {
        isSelect();

        var _samePar=$(this).parents(".m-cart-list").find("input[type=checkbox]");
        var _len=_samePar.length;
        _samePar.each(function () {
            var anum=$(this).parents(".m-cart-list").find("input[type=checkbox]:checked").length;

            if(anum<_len){
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",false);
            }else {
                $(this).parents(".m-conBox").find(".j-select-title input[type=checkbox]").prop("checked",true);
            }
        });

    });
    /*宝贝列表单个checkbox点击事件接结束*/

    /*判断是否全部选中*/
    function isSelect() {
        var _checkbox=$(".m-cart-list label input[type=checkbox]");
        var _radio=$(".m-cart-list label input[type=checkbox]:checked");

        var _lenght=_checkbox.length;

        _checkbox.each(function () {
            var a=$(".m-cart-list label input[type=checkbox]:checked").length;
            if(a<_lenght){
                $(".j-select-all label input[type=checkbox]").prop("checked",false);
            }else {
                $(".j-select-all label input[type=checkbox]").prop("checked",true);
            }
        });

        var allprice = 0;
        var promote_price = 0;
        var promote_count = 0;
        var select_count = 0;
        _radio.each(function () {
            var data_price=parseFloat($(this).parents("li").find(".u-money").attr("data_value"));
            var data_num=parseInt($(this).parents("li").find(".j-count-num").text());
            var allow_promote = parseInt($(this).parents("li").attr("allow_promote"));
            select_count++;
            var account=data_num*data_price;
            allprice+=account;
            if(allow_promote==1){
                promote_price+=account;
                promote_count++;
            }
        });


        if(typeof(promote_cfg)!='undefined'){
            if(promote_cfg && promote_count==select_count){
                var all_promote_price=0;
                for(var i=0;i<promote_cfg.length;i++){
                    if(promote_price >= parseInt(promote_cfg[i]['discount_limit'])){
                        allprice -= parseInt(promote_cfg[i]['discount_amount']);
                        all_promote_price+=parseInt(promote_cfg[i]['discount_amount']);
                    }
                }
                $("#promote_price").html("¥"+all_promote_price);
            }else{
                $("#promote_price").html("¥0");
            }
        }
        allprice = allprice.toFixed(2);
        var priceStr=allprice.toString();
        if(priceStr.indexOf(".") > 0 ){
            var price_split=priceStr.split(".");
            $(".j-price-int").text(price_split[0]);
            $(".j-price-piont").text(price_split[1]);
        }else {
            $(".j-price-int").text(priceStr);
            $(".j-price-piont").text("00");
        }


        var accn=$(".m-conBox .j-select-body").find("input[type=checkbox]:checked").length;
        var allaccn=$(".m-conBox .j-select-body").find("input[type=checkbox]").length;
        $(".j-del-order").text("删除("+accn+")");
        $(".j-accounts").text("结算("+accn+")");
        if(accn==0){
            $(".j-accounts").addClass("invalid");
            /*if(index){
             location.reload();
             }*/
        }else{
            $(".j-accounts").removeClass("invalid");
        }
        return allaccn;
    }



    $(document).on('click',".j-accounts",function(){
        var _this=$(this);
        var _radio=$(".m-cart-list label input[type=checkbox]");
        var checked_ids = new Array();
        var nochecked_ids = new Array();
        $(_radio).each(function(){
            var id = $(this).parents("li").attr("data-id");
            var attr = $(this).parents("li").find(".sizes").attr("attr_key");
            var attr_str = $(this).parents("li").find(".sizes").attr("attr_str");
            var number = parseInt($(this).parents("li").find(".j-count-num").text());
            var cart_item = new Object();
            cart_item.id = id;
            cart_item.attr = attr;
            cart_item.attr_str = attr_str;
            cart_item.number = number;
            if($(this).is(":checked")){
                checked_ids.push(cart_item);
            }else{
                nochecked_ids.push(cart_item);
            }

        });
        var disable_raido = $(".m-invalid .m-cart-list li");
        $(disable_raido).each(function(){ //失效商品
            var id = parseInt($(this).attr("data-id"));
            var cart_item = new Object();
            cart_item.id = id;
            nochecked_ids.push(cart_item);
        });

        //console.log(nochecked_ids);return false;
        if(checked_ids.length==0){
            return false;
        }

        var query = new Object();
        query.act='set_cart_status';
        query.checked_ids = checked_ids;
        query.nochecked_ids = nochecked_ids;

        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    $.toast(data.info);
                    window.setTimeout(function(){
                        location.href=data.jump;
                    },1000);

                }else if(data.status==1)
                {
                    location.href = cart_check_url;

                }else{
                    $.alert(data.info);
                }
            }
            ,error:function(){
            }
        });


    });



    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(document).on('click',".j-trans",function () {
    	//var index = $(".j-trans").index($(this));
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});

        $(".j-trans-way").find(".j-trans-list").hide();
        $(".j-trans-way").find(".j-trans-list[data-id='"+$(this).attr("data-id")+"']").show();

    });
    $(document).on('click',".j-reward",function () {
        $(".totop").addClass("vible");
        $(".popup-box .j-trans-way").css({"bottom":-_hei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });


    /*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"-1"});
    }
    /*弹出层动画效果*/

    /*弹出框点击事件*/
    function listCli(obj) {
        obj.click(function () {
            var lue_name=$(this).find(".pay-way-name .j-company-name").text();
            var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
            var lue_reward=$(this).find(".pay-way-name").text();

            var parText=$(obj).parents(".m-trans-way").find(".u-ti").text();

            $(this).parents("ul").find("input").prop("checked",false);
            if(parText=="优惠券"){
            	var data_id=$(this).parents("ul").attr("data-id");
            	alert(data_id);
                $(this).find("input[name='youhui_log_id["+data_id+"]']").prop("checked",true);
                var money=$(this).find("input[name='youhui_log_id["+data_id+"]']").attr("money");
                //alert(is_pick);
                $(".j-trans .j-trans-commpany").find(".j-company-name").text("-"+money);

            }
            if(parText=="红包"){
                $(this).find("input[name='ecvsn']").prop("checked",true);
                $(".j-reward .j-reward-money").text(lue_reward);
            }
            setTimeout(function () {
                $(".totop").removeClass("vible");
            },500);
            popupTransition();
            count_buy_total();
        });
    }

    /*listCli($(".j-reward-list li"));
    listCli($(".j-trans-list li"));*/

    $(document).on('click',".j-trans-list li,.j-reward-list li",function () {

        var lue_name=$(this).find(".pay-way-name .j-company-name").text();
        var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
        var lue_reward=$(this).find(".pay-way-name").text();

        var parText=$(this).parents(".m-trans-way").find(".u-ti").text();

        $(this).parents("ul").find("input[disabled=false]").prop("checked",false);
        if(parText=="优惠券"){
        	if($(this).find('input').attr("disabled")=="disabled"){
        		$.toast("该优惠券已选择，无法选择");
        		return false;
        	}
        	
        	var data_id=$(this).parents("ul").attr("data-id");

            var youhui_id=$(this).find("input[name='youhui_log_id["+data_id+"]']").val();

        	if (data_id=='p_wl'){
        		var p_yz_youhui=$("input[name='youhui_log_id[p_yz]']:checked").val();
        		if(p_yz_youhui==youhui_id && youhui_id!=0){

        			return false;
        		}
        		else{
        			$(".j-trans-way ul[data-id='p_yz']").find("input[disabled='disabled']").prop("checked",false);
        			$(".j-trans-way ul[data-id='p_yz']").find("input[disabled='disabled']").parent().find(".icon-form-checkbox").removeClass("disabled-checked");
        			$(".j-trans-way ul[data-id='p_yz']").find("input:not([value='"+youhui_id+"'])").removeAttr("disabled");
        			if(youhui_id!=0){
	        			$(".j-trans-way ul[data-id='p_yz']").find("input[value='"+youhui_id+"']").attr("disabled","disabled");
	        			//$(".j-trans-way ul[data-id='p_yz']").find("input[value='"+youhui_id+"']").prop("checked",true);
	        			$(".j-trans-way ul[data-id='p_yz']").find("input[value='"+youhui_id+"']").parent().find(".icon-form-checkbox").addClass("disabled-checked");
        			}
        		}
        	}
            else if(data_id=='p_yz'){
            	var p_wl_youhui=$("input[name='youhui_log_id[p_wl]']:checked").val();
            	if(p_wl_youhui==youhui_id && youhui_id!=0){

        			return false;
        		}
        		else{
        			$(".j-trans-way ul[data-id='p_wl']").find("input[disabled='disabled']").prop("checked",false);
        			$(".j-trans-way ul[data-id='p_wl']").find("input[disabled='disabled']").parent().find(".icon-form-checkbox").removeClass("disabled-checked");
        			$(".j-trans-way ul[data-id='p_wl']").find("input:not([value='"+youhui_id+"'])").removeAttr("disabled");
        			if(youhui_id!=0){
	        			$(".j-trans-way ul[data-id='p_wl']").find("input[value='"+youhui_id+"']").attr("disabled","disabled");
	        			//$(".j-trans-way ul[data-id='p_wl']").find("input[value='"+youhui_id+"']").prop("checked",true);
	        			$(".j-trans-way ul[data-id='p_wl']").find("input[value='"+youhui_id+"']").parent().find(".icon-form-checkbox").addClass("disabled-checked");
        			}
        		}

            }

            $(this).find("input[name='youhui_log_id["+data_id+"]']").prop("checked",true);


            var money=$(this).find("input[name='youhui_log_id["+data_id+"]']").attr("money");
            //alert(is_pick);
            if(money){
            	$(".j-trans[data-id='"+data_id+"'] .j-trans-commpany").find(".j-company-money").text("-￥"+money);
            	$(".j-trans[data-id='"+data_id+"'] .j-trans-commpany").find(".j-company-money").css("color","red");
            }else{
            	$(".j-trans[data-id='"+data_id+"'] .j-trans-commpany").find(".j-company-money").text("不使用优惠券");
            	$(".j-trans[data-id='"+data_id+"'] .j-trans-commpany").find(".j-company-money").css("color","#5f646e");
            }

        }
        if(parText=="红包"){
        	if($(this).find('input').attr("disabled")=="disabled"){
        		$.toast("应付金额已为零");
        		return false;
        	}
        	
            $(this).find("input[name='ecvsn']").prop("checked",true);
            $(".j-reward .j-reward-money").text(lue_reward);
        }
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },500);
        popupTransition();
        count_buy_total();
    });




    /*弹层开始*/
    $(".choose-list .j-choose").click(function(){
        $(this).siblings(".j-choose").removeClass("active");
        $(this).addClass("active");
        setSpecgood();
        var data_value= $(".j-choose.active").attr("data-value");
        var data_value = []; // 定义一个空数组
        var txt = $('.j-choose.active'); // 获取所有文本框
        for (var i = 0; i < txt.length; i++) {
            data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
        }
        $(".good-specifications span").empty();
        $(".good-specifications span").addClass("isChoose");
        $(".good-specifications span").append("已选规格：");
        $.each(data_value,function(i){
            $(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
            //传值可以考虑更改这里
            $(".spec-data").attr("data-value"+[i],data_value[i]);
        });
    });


    $(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });




    $(".j-open-choose").bind("click",open_choose);
    function open_choose(){
        var $this=$(this);
        $(this).unbind("click");
        $this.parents("li").addClass("choose");
        //调用属性HTML
        var id =  $this.parents("li").attr("data-id");
        var attr_key = $this.parents("li").find(".sizes").attr("attr_key");
        var query = new Object();
        query.act='get_cart_deal_attr';
        query.id = id;
        query.attr_key = attr_key;
        $.ajax({
            url:CART_URL,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data.status==-1)
                {
                    location.href=data.jump;
                }else if(data.status==1)
                {
                    $(".page-current").append(data.html);
                    set_attr_name();
                    $(".flippedout .choose-list .j-choose").click(function(){
                        if(!$(this).hasClass("active")){
                            $(this).siblings(".j-choose").removeClass("active");
                            $(this).addClass("active");
                            set_attr_name();
                        }
                    });


                    $(".j-spec-choose-close,.j-flippedout-close,.j-cancel-flip").click(function(){
                        cssAnition();
                    });

                    $(".j-nowbuy").click(function () {
                        if($(this).attr('max') && $(this).attr('max')==0){
                            $.alert("库存不足");
                        }else{
                            if($this.parents("li").hasClass("choose")){
                                $this.parents("li").removeClass("choose");
                            }

                            var attr_check_ids = new Array();
                            var attr_name = '';
                            $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
                                attr_name+=$(obj).text();
                                attr_check_ids.push($(obj).attr("data-id"));
                            });

                            if(attr_check_ids.length==attr_num){

                                var attr_checked_ids = attr_check_ids.join(",");
                                //同步属性
                                $this.parents("li").find(".sizes").attr({'attr_key':attr_checked_ids,'attr_str':attr_name}).text("规格:"+attr_name);

                                //同步值
                                if(parseInt($(this).attr('max')) != 99999){

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("仅剩"+$(this).attr('max')+"件");
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                    var $val=parseInt($val);
                                    var $max=parseInt($(this).attr('max'));
                                    if($val>$max){
                                        $(".item-content[data-id='"+id+"']").find("input[type=text]").val($max);
                                        $(".item-content[data-id='"+id+"']").find(".j-count-num").text($max);
                                        isSelect();
                                    }
                                }else{

                                    $(".item-content[data-id='"+id+"']").find("input[type=text]").attr("max",$(this).attr('max'));
                                    $(".item-content[data-id='"+id+"']").find(".u-surplus").html("");
                                    $val=$(".item-content[data-id='"+id+"']").find("input[type=text]").val();
                                }

                                //同步价格
                                var num=parseFloat($(".showflipped .spec-goodprice").attr("data_value"));
                                num = Math.round(num*100)/100;  //保留两位小数
                                num =Number(num).toFixed(2);  //保留两位小数
                                var num_arr = num.split('.');
                                var price_str='¥ <i class="j-goods-money">'+num_arr[0]+'.</i>'+num_arr[1];
                                $this.parents("li").find(".u-money").attr("data_value",num).html(price_str);

                                cssAnition();
                            }else{
                                $.alert("请选择属性");
                            }
                        }
                    });

                }else{
                    $.alert(data.info);
                }
            }
            ,error:function(){
            }
        });
    }

    function set_attr_name(){
        var attr_name='';
        var attr_check_ids = new Array();
        var attr_check_key='';
        $(".showflipped .spec-info .j-choose.active").each(function(i,obj){
            attr_name+='&nbsp;&nbsp;'+$(obj).text();
            attr_check_ids.push($(obj).attr("data-id"));
        });

        var attr_check_ids_new = attr_check_ids.sort();
        attr_check_key=attr_check_ids_new.join("_");
        if(deal_attr_stock_json[attr_check_key]){
            var stock = deal_attr_stock_json[attr_check_key]['stock_cfg'];
            if(parseInt(stock)<0){
                stock = 99999;
            }
        }else{
            var stock = 99999;
        }
        $(".spec-goodspec").empty();
        $(".spec-goodspec").append("已选择");
        //$(".spec-goodspec em").html(attr_name);
        $(".spec-goodspec").append("<em class='choose_item'>" + attr_name + "</em>");
        $(".spec-goodstock").text("库存:"+stock+"件");
        $(".j-nowbuy").attr("max",stock);
        //deal_current_price
        var deal_price = deal_current_price;
        $.each(deal_attr_json,function(i,obj){
            $.each(obj['attr_list'],function(xi,xobj){
                if($.inArray(xobj.id,attr_check_ids_new) >= 0){

                    deal_price += parseFloat(xobj.price);
                }
            });

        });

        $(".spec-goodprice").attr("data_value",parseFloat(deal_price)).html("¥"+parseFloat(deal_price));

    }


    function cssAnition() {
        $(".flippedout").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-flippedout-close").removeClass("showflipped");
        $(".j-open-choose").bind("click",open_choose);
        setTimeout("$('.flippedout').removeClass('showflipped')",300);
    }


    function count_buy_total()
    {
        ajaxing = true;
        var query = new Object();

        //获取配送方式
        var delivery_id = $("input[name='delivery']:checked").val();

        if(!delivery_id)
        {
            delivery_id = 0;
        }
        query.delivery_id = delivery_id;

        var address_id = $("input[name='address_id']").val();

        //全额支付
        if($("input[name='all_account_money']").attr("checked"))
        {
            query.all_account_money = 1;
        }
        else
        {
            query.all_account_money = 0;
        }
		//积分抵现
		if($('input[name="all_score"]:checked').length>0)
		{
			query.all_score = 1;
		}
		else
		{
			query.all_score = 0;
		}

		//优惠券
		var youhui =new Object();
		$(".j-trans-way ul").each(function(){
			var data_id=$(this).attr("data-id");
			youhui[data_id]=$("input[name='youhui_log_id["+data_id+"]']:checked").val();

		});
		query.youhui_ids = youhui;

        //代金券
        var ecvsn = $("input[name='ecvsn']:checked").val();

        if(!ecvsn)
        {
            ecvsn = '';
        }

        var ecvpassword = $("input[name='ecvpassword']").val();
        if(!ecvpassword)
        {
            ecvpassword = '';
        }

        var id = $("input[name='id']").val();
        var buy_type = $("input[name='buy_type']").val();
        query.ecvsn = ecvsn;
        query.ecvpassword = ecvpassword;
        query.address_id = address_id;
        query.id = id;
        query.buy_type = buy_type;
        //支付方式
        var payment = $("input[name='payment']:checked").val();
        if(!payment)
        {
            payment = 0;
        }
        query.payment = payment;
        query.bank_id = $("input[name='payment']:checked").attr("rel");
        query.order_id = order_id;
        //query.reward = reward;
        query.act = "count_buy_total";
        $.ajax({
            url: AJAX_URL,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){
                //alert(1111);
                /*if(data.free && delivery_id!=-1){
                 $(".j-company-money").html("运费：0");
                 }*/
            	console.log(data);
                if(data.total_price==0 && $('div').is('.voucher_box')){
                    $(".voucher_box").remove();
                    count_buy_total();
                }
                /*if(reward==1){*/
                $("#cart_total").html(data.html);
                $(".total_price_box").html(data.pay_price_html);
                ajaxing = false;
				if($('input[name="all_score"]').length){
					$("input[name='all_score']").unbind('change');
					$("input[name='all_score']").bind("change",function () {
						count_buy_total();
					});
				}

				if(data.ecv_no_use_status==1 && $('.voucher_box')){
                    $(".j-red-reward").find("input[name='ecvsn']").prop("checked",false);
                    $(".j-red-reward").find("input:not([value='0'])").attr("disabled",'disabled');
                    $(".j-red-reward").find("input:not([value='0'])").parent().find(".icon-form-checkbox").addClass("disabled-checked");
                    $(".j-red-reward").find("input[value='0']").prop("checked",true);
                    $(".j-reward .j-reward-money").text("不使用红包");
                }else{
                	$(".j-red-reward").find("input[name='ecvsn']").removeAttr("disabled");
                	$(".j-red-reward").find("input[name='ecvsn']").parent().find(".icon-form-checkbox").removeClass("disabled-checked");
                }
				
                /*}else{
                 var ecv_money = parseFloat($("input[name='ecvsn']:checked").attr("money"));
                 var pay_moeny = parseFloat(data);
                 if(pay_moeny<ecv_money){
                 //$("div.j-reward-money").html("不使用红包");
                 var now_ecv=0;
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(pay_moeny<this_money){
                 $(this).remove();
                 }else{
                 if(this_money>now_ecv){
                 now_ecv=this_money;
                 }
                 }
                 });
                 now_ecv=parseFloat(now_ecv);
                 $(".j-reward-list li").each(function(){
                 var this_money=parseFloat($(this).find("input[name='ecvsn']").attr("money"));
                 if(this_money==now_ecv){
                 $(".j-reward-list").find("input[name='ecvsn']").removeAttr("checked");   ;
                 $(this).find("input[name='ecvsn']").attr("checked","checked");
                 $("div.j-reward-money").html($(this).find(".pay-way-name").html());
                 }
                 });
                 }*/
                //count_buy_total(1);
                //}
            },
            error:function(ajaxobj)
            {
//    			if(ajaxobj.responseText!='')
//    			alert(LANG['REFRESH_TOO_FAST']);
            }
        });
    }

    var pay_lock = false;
    $(".go_pay").click(function() {
        if (pay_lock) {
            return;
        }

        // 发票内容完整性确认
        var ivo_check = invoice_check();
        if (!ivo_check) {
            $.toast('请完善发票内容');
            return false;
        }

		$.showIndicator();
        pay_lock = true;
        var query = $("#pay_box").serialize();
        var url = $("#pay_box").attr("action");

        $.ajax({
            url: url,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){
				$.hideIndicator();
                if(data.status==1) {
                    pay_lock = false;
                    //先留着，后期有用
//                    if(app_index=='app'){
//                    	App.app_detail(data.type,'{"id":'+data.id+'}');
//                    }else{
//                    	$.router.load(data.jump, true);
//                    }
                    $.router.load(data.jump, true);
                    
                } else if (data.status == -2) {
                    $.toast(data.info);
                    setTimeout(function() {
                        pay_lock = false;
                        $.router.load(data.jump, true);
                    }, 2000);
                } else {
                    pay_lock = false;
                    $.alert(data.info);
                }

                ajaxing = false;
            },
            error:function(ajaxobj) {
				$.hideIndicator();

            }
        });

    });


    function invoice_check() {
        // 如果开票判断是否选择发票须知
        
        // 判断每个发票填充的内容是否合法
        vioCheck = true;
        $('div.invoice-type').each(function(index, elm) {
            var type = $(elm).find('input').val();
            type = parseInt(type);
            if (type !== 0) {
                var title = $.trim($(elm).parent().find('.invoice-title').val());
                if (title === '') {
                    vioCheck = false;
                    return false;
                }
                if (type === 2) {
                    var taxnu = $.trim($(elm).parent().find('.invoice-taxnu').val());
                    if (taxnu === '') {
                        vioCheck = false;
                        return false;
                    }
                }
            }
        });
        return vioCheck;
    }
});
$(document).on("pageInit", "#cate", function(e, pageId, $page) {
	var active_length=$(".cate-list li.active").length;
	if(active_length==0){
		$(".cate-list li").eq(0).addClass('active');
		$(".cate-info ul").eq(0).addClass('active');
	}
	$(".cate-list li").click(function() {
		$(".cate-list li").removeClass('active');
		$(".cate-info ul").removeClass('active');
		$(this).addClass('active');
		$(".cate-info ul").eq($(this).index()).addClass('active');
	});;
});

/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#changepassword", function(e, pageId, $page) {
    $(".userBtn-yellow").click(function () {
        $("#ph_getpassword").submit();
    });


    $("#ph_getpassword").bind("submit",function(){
        var mobile = $.trim($(this).find("input[name='user_mobile']").val());
        var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
        var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());

        if(mobile=="")
        {
            $.toast("请输入手机号");
            return false;
        }
        if(user_pwd=="")
        {
            $.toast("请输入密码");
            return false;
        }
        if (user_pwd.length < 4) {
            $.toast('密码过短');
            return false;
        }
        if(sms_verify=="")
        {
            $.toast("请输入收到的验证码");
            return false;
        }

        var query = $(this).serialize();
        var ajax_url = $(this).attr("action");
        $.ajax({
            url:ajax_url,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status) {
                    // 先清理当前页的信息
                    //$('input[name=sms_verify]').val('');
                    //$('#btn').attr('lesstime', 0);

                    // 执行跳转
                    // $.alert(obj.info,function(){
                    // 	location.href = obj.jump;
                    // });
                    // 转弱提示跳转
                    $.toast(obj.info);
                    setTimeout(function() {
                        $.router.load(obj.jump);
                    }, 1500);
                } else {
                    $.toast(obj.info);
                }
            }
        });

        return false;
    });
});
/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#changeuname", function(e, pageId, $page) {
    $(".userBtn-yellow").click(function () {
        $("#ph_getuname").submit();
    });


    $("#ph_getuname").bind("submit",function(){
        var user_name = $.trim($(this).find("input[name='user_name']").val());
        if(user_name=="")
        {
            $.toast("请输入昵称");
            return false;
        }
        
        //获取字符长度（包括中文）
        var name_len = getByteLen(user_name);
        if (name_len < 4) {
            $.toast('昵称过短');
            return false;
        }
        if(/\_/.test(user_name) == true){
        	$.toast('用户名不能使用下划线');
            return false;
        }
        var query = $(this).serialize();
        var ajax_url = $(this).attr("action");
        $.ajax({
            url:ajax_url,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status) {
                    // 转弱提示跳转
                    $.toast(obj.info);
                    setTimeout(function() {
                    	location.href = obj.jump;
                    }, 1500);
                } else {
                    $.toast(obj.info);
                }
            }
        });

        return false;
    });
    
    
    function getByteLen(val) { 
    	var len = 0; 
    	for (var i = 0; i < val.length; i++) { 
	    	if (val[i].match(/[^\x00-\xff]/ig) != null){
		    	len += 2; 
	    	} //全角 
	    	else{
		    	len += 1; 
	    	} 
    	} 
    	return len; 
    } 
    
});
$(document).on("pageInit", ".page", function(e, pageId, $page) {

	
	$('#search').keyup(function(){
		search_city();
	}).focus(function(){
		$('#search_result').show();
	});
	$("#search").blur(function(){
		setTimeout(function(){
	 		$('#search_result').hide();
		},"500");
	});
	$(".searchbar .searchbar-cancel").click(function(){
		$('.searchbar #search').val('');
	});
	$("#city .city_change").unbind('click').bind('click',function(){
		$.ajax({
			url: $(this).attr('url'),
			data: {},
			dataType: "json",
			type: "post",
			success: function(obj){
				$.router.load(obj.jump, true);
			}
		});
	});
	
});
function search_city(){
	var query = new Object();
	query.act = "searchcity";
	var kw=$.trim($('#search').val());
	query.kw=kw;
	//if(kw){
		$.ajax({
					url: CITY_URL,
					data: query,
					dataType: "json",
					type: "post",
					success: function(data){
							
						$('#search_result').remove();
						$('.searchbar').append(data.city.html);
						$('#search_result').show();
						
					}
		});
	//}


}
$(document).on("pageInit", "#dcorder_index", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	//打开评论
	$(document).on('click', '.j-open-comment', function() {
		$(".img-comment-1").attr("src",$(this).parents('li').find(".img-comment").attr('src'));
		$(".name-comment-1").html($(this).parents('li').find(".name-comment").html());
		$("input[name='order_id_1']").val($(this).parents('li').find("input[name='order_id']").val());
		$("input[name='location_id_1']").val($(this).parents('li').find("input[name='location_id']").val());
		$.popup('.popup-comment');
	});
	//关闭当前弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	});
	$(".comment-stars").on('click', '.j-point', function() {
		$(".j-point").removeClass('active');
		$(this).addClass('active');
		$(this).prevAll().addClass('active');
		$("#star-value").attr('value', $(this).attr('value'));
	});
	
	$(document).on('click','.to-pay',function(){
		
		var url = $(this).attr('data_url');
		var jump_url = $(this).attr('jump_url');
		$.ajax({
			url:url,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					location.href = jump_url;
				}else{
					$.toast(data.info);
				}
			}
		});
		
	});
	
	
	//发表评论
	$('.j-comment-sub').bind('click',function(){
		
    	var is_pass=1;
    	var dp_points=$("#star-value").val();
			if(dp_points==0){
				$.toast('请给出您宝贵的评分！');
				is_pass=0;
				return false;
			}
    	if(is_pass==1){

	    	if($("textarea[name='content']").val()==''){
	    		$.toast('请填写您的宝贵意见！');
	    		is_pass=0;
	    		return false;
	    	}
    	}
    		
		if(is_pass==0){
			return false;
		}


    	var url=$(this).attr('action');
    	
		var query = new Object();
		query.location_id = $("input[name='location_id_1']").val();
		query.order_id = $("input[name='order_id_1']").val();
		 
		query.dp_points=dp_points;

    	query.content = $("textarea[name='content']").val(); 
    	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json', 
			success:function(data){
			
				if(data.status==1){
				$.showIndicator();
			      setTimeout(function () {
			    	  close_comment();
			      }, 2000);
					
				}else{
					$.toast(data.info);
				}
				
				function close_comment(){
					$.toast(data.info);
					$(".popup-comment").removeClass('modal-in').addClass('modal-out');
					$.hideIndicator();
					$(".j-point").removeClass('active');
					$("#star-value").attr('value', '');
					$("textarea[name='content']").val('');
					var AJAX_URL = data.jump;
					var is_ajax  = 1;
					$.ajax({
						url:AJAX_URL,
						data:{"is_ajax":is_ajax},
						type:'post',
						dataType:'json', 
						success:function(obj){
							$(".infinite-scroll-bottom").html(obj.html);
							init_list_scroll_bottom();//下拉刷新加载
						}
					});
				}
			}
    	});
    	   
          
    });
	
});
$(document).on("pageInit", "#dcorder_view", function(e, pageId, $page) {
	//打开评论
	$(document).on('click', '.j-open-comment', function() {
		$(".img-comment-1").attr("src",$(".img-comment").attr('src'));
		$(".name-comment-1").html($(".name-comment").html());
		$("input[name='order_id_1']").val($("input[name='order_id']").val());
		$("input[name='location_id_1']").val($("input[name='location_id']").val());
		$.popup('.popup-comment');
	});
	//关闭当前弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	});
	$(".comment-stars").on('click', '.j-point', function() {
		$(".j-point").removeClass('active');
		$(this).addClass('active');
		$(this).prevAll().addClass('active');
		$("#star-value").attr('value', $(this).attr('value'));
	});
	
	
	//发表评论
	$('.j-comment-sub').bind('click',function(){
		
    	var is_pass=1;
    	var dp_points=$("#star-value").val();
			if(dp_points==0){
				$.toast('请给出您宝贵的评分！');
				is_pass=0;
				return false;
			}
    	if(is_pass==1){

	    	if($("textarea[name='content']").val()==''){
	    		$.toast('请填写您的宝贵意见！');
	    		is_pass=0;
	    		return false;
	    	}
    	}
    		
		if(is_pass==0){
			return false;
		}


    	var url=$(this).attr('action');
    	
		var query = new Object();
		query.location_id = $("input[name='location_id_1']").val();
		query.order_id = $("input[name='order_id_1']").val();
		 
		query.dp_points=dp_points;

    	query.content = $("textarea[name='content']").val(); 
    	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json', 
			success:function(data){
			
				if(data.status==1){
//				$.showIndicator();
				$.toast(data.info);
			      setTimeout(function () {
			    	  close_comment();
			      }, 2000);
					
				}else{
					$.toast(data.info);
				}
				
				function close_comment(){
					location.reload(); 
					$(".popup-comment").removeClass('modal-in').addClass('modal-out');
					$.hideIndicator();
					$(".j-point").removeClass('active');
					$("#star-value").attr('value', '');
					$("textarea[name='content']").val('');
					
//					var AJAX_URL = data.jump;
//					var is_ajax  = 1;
//					$.ajax({
//						url:AJAX_URL,
//						data:{"is_ajax":is_ajax},
//						type:'post',
//						dataType:'json', 
//						success:function(obj){
//							$(".infinite-scroll-bottom").html(obj.html);
//							init_list_scroll_bottom();//下拉刷新加载
//						}
//					});
				}
			}
    	});
    	   
          
    });
	var lock = true;
	
	$(".dc-view-bar").on('click', '.j-cancle', function() {
		
		if(!lock){
			return;
		}else{
			lock = false;
			var url = $(this).attr('data_url');
			var query = new Object();
			//取消订单
			
			
			$.confirm('确定要取消订单吗？', function () {
		          $.ajax({
		        	  url:url,
		        	  type:'post',
		        	  dataType:'json',
		        	  success:function(data){
		        		  if(data.status == 1){
		        			  $.toast(data.info);
		        			  setTimeout(function () {
		        				  location.reload(); 
		    			      }, 2000);
		        			  
		        		  }else{
		        			  $.confirm(data.info,function(){
		        				  lock = true;
		        				  window.location.href = "tel:"+data.location_tel;
		        			  },function(){
		        				  location.reload(); 
		        			  });
		        		  }
		        	  }
		          });
		      },function () {
	        	  lock = true;
	          });
		}
		
	});
	
	$(".dc-view-bar").on('click', '.j-quick', function() {
		//催单
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
      			 $.toast(data.info);
      			  
      		  }else{
      			$.toast(data.info);
      		  }
      	  }
        });
	});
	$(".dc-view-bar").on('click','.harvest',function(){
		//确认收货
		var url = $(this).attr('data_url');
		$.confirm('确定收到商品了吗？', function () {
			$.ajax({
				url:url,
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.status == 1){
						$.toast(data.info);
						setTimeout(function () {
	      				  location.reload(); 
	  			      }, 2000);
					}else{
						$.toast(data.info);
					}
				}
			});
		});
		
		
	});
	
	$(".dc-view-bar").on('click','.to-pay',function(){
		var url = $(this).attr('data_url');
		var jump_url = $(this).attr('jump_url');
		$.ajax({
			url:url,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					location.href = jump_url;
				}else{
					$.toast(data.info);
				}
			}
		});
		
	});
});
$(document).on("pageInit", "#dc_cart", function(e, pageId, $page) {

	var lock = false; // 全局锁定变量

	//打开送货时间选择
	$(".j-open-time").on('click', function() {
		$(".dc-mask").addClass('active');
		$(".time-select").addClass('active');
		var send_time=$(this).find('input').attr('value');
		$(".j-time-choose").each(function() {
			if ($(this).attr('value')==send_time) {
				$(this).addClass('active');
			}
		});
	});
	//关闭送货时间选择
	$(".j-close-time").on('click', function() {
		$(".dc-mask").removeClass('active');
		$(".time-select").removeClass('active');
	});
	//选择时间
	$(".j-time-choose").on('click', function() {
		$(".j-time-choose").removeClass('active');
		$(this).addClass('active');
		$(".j-send-time").html($(this).find('p').html());
		$("#time-value").attr('value', $(this).attr('value'));
	});
	//打开备注
	$(".j-open-memo").on('click', function() {
		$("#memo").focus();
		$(".dc-mask").addClass('active');
		$(".memo-box").addClass('active');
	});
	//关闭备注
	$(".j-close-memo").on('click', function() {
		var memo = $.trim($('textarea[name="dc_comment"]').val()).substr(0,100);
		$('#memo').val(memo);
		close_memo();
	});
	//确认备注
	$(".j-memo").on('click', function() {
		var memo_txt = $.trim($('textarea[name="dc_comment"]').val());
		if (memo_txt == "") {
			$(".memo-txt").html('<span class="default-txt">备注您的口味、偏好等</span>');
		}else {
			if (memo_txt.length > 100) {
				$.toast('备注不超过100字,当前'+memo_txt.length+'字');
				return;
			}
			$(".memo-txt").html(memo_txt);
		}
		close_memo();
	});
	function close_memo() {
		$(".dc-mask").removeClass('active');
		$(".memo-box").removeClass('active');
	}
	//打开选择地址
	$(document).on('click', '.open-address', function() {
		if (lock) {
			return;
		}
		lock = true;
		load_consignee_list();
		$.popup('.popup-address');
		setTimeout(function() {
			lock = false;
		}, 2000);
	});
	$(".popup-address").on('click', '.j-select-address', function() {
		// 判断地址的起送价和计算配送费
		var dp = Number($(this).find('.delivery_price').val());
		if (dp <= 0) {
			dp = 0;
			$('.de-price-box').addClass('hide');
		} else {
			$('.de-price-box').removeClass('hide');
		}
		dp = Math.round(dp * 100) / 100;
		$('em.delivery_price').html(dp);
		cal_price();

		$(".dc-address-list li").removeClass('active');
		$(this).parent().addClass('active');
		$(".dc-address-box .dc-address-info").html($(this).html());
		var con_id = $(".dc-address-box .dc-address-info").find('input').val();
		if (con_id) {
			window.history.replaceState({}, document.title, base_url+'&consignee_id='+con_id);
		}
	});
	//打开新增地址
	$(document).on('click', '.j-open-new-address', function() {
		$.popup('.popup-address-new');
	});
	//打开编辑地址
	$(document).on('click', '.j-open-edit', function() {
		if (lock) {
			return;
		}
		lock = true;
		var id = $(this).attr('data-id');
		var param = {'act':'add', 'id':id};
		$.ajax({
			url: DC_CONSIGNEE_URL,
			data: param,
			type: "post",
			dataType:"json",
			success: function(data){
				lock = false;
				$('.popup-address-edit').html(data.html);
				$.popup('.popup-address-edit');
			},
			error: function() {
				lock = false;
				$.toast('网络异常地址加载错误');
			}
		});
	});
	// 新增地址
	$('.popup-address-new').on('click', '.j-save-address', function() {
		var consignee = $('.add-item input[name="consignee"]').val();
		if (!consignee) {
			$.toast('请填写姓名');
			return;
		}
		var mobile = $('.add-item input[name="mobile"]').val();
		if (!mobile) {
			$.toast('请填写手机号');
			return;
		}
		if (!checkMobilePhone(mobile)) {
			$.toast('请正确填写手机号');
			return;
		}
		var address = $('.add-item input[name="address"]').val();
		if (!address) {
			$.toast('请填写门牌信息');
			return;
		}
		var api_address = $('.add-item input[name="api_address"]').val();
		if (!api_address) {
			$.toast('请定位一个地址');
			return;
		}
		var xpoint = $('.add-item input[name="xpoint"]').val();
		var ypoint = $('.add-item input[name="ypoint"]').val();
		if (!xpoint || !ypoint) {
			$.toast('地址定位发送错误,请重试');
			return;
		}

		var param = {
			'act':'save_dc_consignee',
			'consignee':consignee,
			'mobile': mobile,
			'api_address': api_address,
			'address': address,
			'xpoint': xpoint,
			'ypoint': ypoint
		};
		if (lock) {
			return;
		}
		lock = true;
		var _this = this;
		$.ajax({
			url: DC_CONSIGNEE_URL,
			data: param,
			type: "post",
			dataType:"json",
			success: function(data){
				if (data.status) {
					$.toast('保存成功');
					//关闭当前弹层
					load_consignee_list();
					setTimeout(function() {
						$(_this).parents('.popup').removeClass('modal-in').addClass('modal-out');
						if ($('.dc-address-box').hasClass('j-open-new-address')) {
							$('.dc-address-box').removeClass('j-open-new-address').addClass('open-address');
						}
						// 如果只有一个地址，并且这个地址是有效的，直接获取并返回提交订单页
						if ($('.popup-bd').find('ul').length == 1 && $('.active-address-list').find('li').length == 1) {
							syn_address()
							return false;
						}
						$.popup('.popup-address');
					}, 2000);
				} else {
					$.toast(data.info);
				}
				lock = false;
			},
			error: function() {
				lock = false;
				$.toast('网络异常地址加载错误');
			}
		});
	});
	// 修改地址再写一份
	$(document).on('click', '.j-edit-address', function() {
		var consignee = $('.edit-item input[name="consignee"]').val();
		if (!consignee) {
			$.toast('请填写姓名');
			return;
		}
		var mobile = $('.edit-item input[name="mobile"]').val();
		if (!mobile) {
			$.toast('请填写手机号');
			return;
		}
		if (!checkMobilePhone(mobile)) {
			$.toast('请正确填写手机号');
			return;
		}
		var address = $('.edit-item input[name="address"]').val();
		if (!address) {
			$.toast('请填写门牌信息');
			return;
		}
		var api_address = $('.edit-item input[name="api_address"]').val();
		if (!api_address) {
			$.toast('请定位一个地址');
			return;
		}
		var xpoint = $('.edit-item input[name="xpoint"]').val();
		var ypoint = $('.edit-item input[name="ypoint"]').val();
		if (!xpoint || !ypoint) {
			$.toast('地址定位发送错误,请重试');
			return;
		}
		var id = $('.edit-item input[name="consignee_id"]').val();
		var param = {
			'act':'save_dc_consignee',
			'id': id,
			'consignee':consignee,
			'mobile': mobile,
			'api_address': api_address,
			'address': address,
			'xpoint': xpoint,
			'ypoint': ypoint
		};
		if (lock) {
			return;
		}
		lock = true;
		var _this = this;
		$.ajax({
			url: DC_CONSIGNEE_URL,
			data: param,
			type: "post",
			dataType:"json",
			success: function(data){
				$.toast(data.info);
				if (data.status) {
					//关闭当前弹层
					load_consignee_list();
					setTimeout(function() {
						$(_this).parents('.popup').removeClass('modal-in').addClass('modal-out');
					}, 2000);
				}
				lock = false;
			},
			error: function() {
				lock = false;
				$.toast('网络异常地址加载错误');
			}
		});
	});
	// 清空新增/修改地址信息
	function cls_add_info() {
		$('input[name="consignee"]').val('');
		$('input[name="mobile"]').val('');
		$('input[name="xpoint"]').val('');
		$('input[name="ypoint"]').val('');
		$('input[name="api_address"]').val('');
		$('input[name="address"]').val('');
	}
	// 计算支付金额
	function cal_price() {
		var cart_price = Number(total_price);
		var delivery_price = Number($('em.delivery_price').html());
		var package_price = Number($('em.package_price').html());
		var promote_amount = Number($('em.promote_amount').html());
		var total_count = cart_price + package_price + delivery_price;
		var pay_price = total_count - promote_amount;
		if (pay_price <= 0) {
			pay_price = 0;
		}
		total_count = Math.round(total_count * 100) / 100;
		pay_price = Math.round(pay_price * 100) / 100;
		$('em.total_count').html(total_count);
		$('em.pay_price').html(pay_price);
	}

	function load_consignee_list() {
		cls_add_info();
		var param = {'act':'index', 'lid': location_id};
		$.ajax({
			url: DC_CONSIGNEE_URL,
			data: param,
			type: "post",
			dataType:"json",
			success: function(data){
				$('.popup-bd').html(data.html);
				addrListActiveCheck();
			},
		});
	}
	function addrListActiveCheck() {
		var default_id = $('.j-ajaxaddress').find('input[name="consignee_id"]').val();
		if (default_id) {
			$(".dc-address-list li").removeClass('active');
			$('li[data-id="'+default_id+'"]').addClass('active');
		}
	}
	$(document).on('click', '.j-del-address', function() {
		$.confirm('确定要删除这个地址吗？', function () {
			var id = $('.edit-item input[name="consignee_id"]').val();
			if (!id) {
				$.toast('页面异常，请刷新重试');
				return;
			}
			var param = {'act': 'del', 'id': id};
			$.ajax({
				url: DC_CONSIGNEE_URL,
				data: param,
				type: "post",
				dataType: "json",
				success: function(data) {
					if (data.status) {
						$.toast('删除成功');
						load_consignee_list();
						setTimeout(function() {
							$('.popup-address-edit').removeClass('modal-in').addClass('modal-out');
						}, 2000);
					} else {
						$.toast(data.info);
					}
				}
			})
		});
	});
	//关闭当前地址弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	    // $('#uc_address_map_pick').hide();
	});
	// 地址列表页返回的数据同步
	$(document).on('click', '.address-back', syn_address);
	function syn_address() {
		var addrhtml = $($('.active-address-list li.active').children().get(1)).html();
		if (!addrhtml) {
			addrhtml = $($('.active-address-list li').children().get(1)).html();
			if (!addrhtml) {
				addrhtml = '请选择送餐地址';
				$(".dc-address-box .dc-address-info").addClass('no-address');
			}
		}
		$(".dc-address-box .dc-address-info").html(addrhtml);
	}

	var priceChangeLock = false;
	$('.dc-data-btn').on('click', function() {
		if (lock) {
			return;
		}
		if (priceChangeLock) {
			$.confirm('商品价格出错，请重新下单', function() {
				dc_cart_clear();
				$.router.back();
			})
			return;
		}
		lock = true;
		if (!$('input[name="consignee_id"]').val()) {
			$.toast('请选择一个配送地址');
			return;
		}
		var param = $('form[name="cart_form"]').serialize();
		var action = $(this).attr('data-url');
		$.ajax({
			url: action,
			data: param,
			type: "post",
			dataType: "json",
			success: function(data) {
				lock = false;
				if (data.user_login_status == 0) {
					setTimeout(function() {
						$.router.load(data.jump, true);
					}, 2000);
				}
				if (data.status == 1) {
					setTimeout(function() {
						$.router.load(data.jump, true);
					}, 2000);
				} else {
					if (data.isPriceChange) {
						priceChangeLock = true;
						$.confirm(data.info, function() {
							dc_cart_clear();
							$.router.back();
						});
						return;
					}
				}
				$.toast(data.info);
			}
		});
	})
});

function dc_pickmap(street, addr, x, y) {
    $('.modal-in input[name="api_address"]').val(street);
    // var patt = /^([^(]*?省|)([^(]*?市|)([^(]*?(区|县)|)(.*)/;
    // var mat = addr.match(patt);
    // var addr1 = mat.pop();
    // $('.add-item textarea[name="address"]').val(addr1);
    $('.modal-in input[name=xpoint]').val(x);
    $('.modal-in input[name=ypoint]').val(y);
    $('.popup-address-map').removeClass('modal-in').addClass('modal-out');
}
$(document).on('click', '.dc_mappick', function() {

    var region = '';
    /*$('#uc_address_map_pick').show();*/
    
    $.popup('.popup-address-map');
    $('#uc_address_map_pick').addClass('baidu_mapBox');
    // 百度地图API功能
    var map = new BMap.Map("baidu_allmap");
    var orx = $('.modal-in input[name="xpoint"]').val();
    var ory = $('.modal-in input[name="ypoint"]').val();
    var point = new BMap.Point(0,0);
    map.centerAndZoom(point,16);
    map.enableScrollWheelZoom(true);
    var myValue = '';
    // region += $('input[name="street"]').val();

    var geoc = new BMap.Geocoder();

    if (orx && ory) {
        map.panTo(new BMap.Point(orx, ory));
        getLoc();
    } else {
        var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r){
			if(this.getStatus() == BMAP_STATUS_SUCCESS){
				// var mk = new BMap.Marker(r.point);
				// map.addOverlay(mk);
				map.panTo(r.point);
			} else {
				$.toast('定位异常，请手动尝试');
			}        
		},{enableHighAccuracy: true})
    }

    map.addEventListener('moveend', getLoc); // 移动结束检索地区
    function getLoc() {
        var p = map.getCenter();
        geoc.getLocation(p, function(rs) {
            var addComp = rs.addressComponents;
            var lstr = /*addComp.province + addComp.city + addComp.district +*/ addComp.street + addComp.streetNumber;
            var sstr = addComp.street ? addComp.street : addComp.district;
            var surrPois = rs.surroundingPois;
            var cx = rs.point.lng;
            var cy = rs.point.lat;
            var res = '<div class="r-loca">';
            res += '<div class="b-line" onclick="dc_pickmap(\''+sstr+'\',\''+lstr+'\','+cx+','+cy+')"><li class="loca-curr"><h3><i class="search-icon iconfont">&#xe62f;</i><em>[当前]</em>'+sstr+'</h3><p class="loca-curr">'+lstr+'</p></li></div>';
            if (surrPois) {
                // console.log(surrPois);
                for (i in surrPois) {
                    var x = surrPois[i].point.lng;
                    var y = surrPois[i].point.lat;
                    res += '<div class="b-line" onclick="dc_pickmap(\''+surrPois[i]['title']+'\',\''+surrPois[i]['address']+'\','+x+','+y+');"><li><h3><i class="search-icon iconfont">&#xe62f;</i>'+surrPois[i]['title']+'</h3><p>'+surrPois[i]['address']+'</p></li></div>';
                }
            }
            res += '</div>'
            $('#baidu-m-result').html(res);
        });
    }

    // 搜索方法
    var ac = new BMap.Autocomplete({'input':'suggestId', 'location': map});
    ac.addEventListener('onhighlight', function(e) {
        var str = '';
        var _value = e.fromitem.value;
        var value = '';
        if (e.fromitem.index > -1) {
            value = _value.province + _value.city + _value.district + _value.street;
        }
        str = "FromItem<br />index = " + e.fromitem.index + "<br />value= " + value;

        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        }    
        str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
        $("#baidu_searchResultPanel").html(str);
    });

    var geocoder = new BMap.Geocoder();
    ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
    var _value = e.item.value;
        myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        $("#baidu_searchResultPanel").html("onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue);
        geocoder.getPoint(myValue, function(point) {
        	if (point) {
        		var street = _value.business;
        		dc_pickmap(street, '', point.lng, point.lat);
        	} else {
        		setPlace();
        	}
        })
    });

    function setPlace(){
        function myFun(){
            var pp = local.getResults().getPoi(0);    //获取第一个智能搜索的结果
            if (!pp) {
                $.toast('地址查询错误');
                setTimeout(function() {
                    // var pro = myValue.substr(0, myValue.indexOf('省'));
                    // console.log(pro);
                    map.centerAndZoom('北京', 12);
                }, 2000);
                
                return;
            }
            map.centerAndZoom(pp.point, 18);
        }
        var local = new BMap.LocalSearch(map, { //智能搜索
            onSearchComplete: myFun
        });
        // local.clearResults();
        local.search(myValue);
    }

    // 添加定位控件
    var geolocationControl = new BMap.GeolocationControl({
        // 靠左上角位置
        anchor: BMAP_ANCHOR_BOTTOM_LEFT,
        // 是否显示定位信息面板
        showAddressBar: false,
        // 启用显示定位
        enableGeolocation: true
    });
    geolocationControl.addEventListener("locationSuccess", function(e){

    });
    geolocationControl.addEventListener("locationError",function(e){
        // 定位失败事件
        alert(e.message);
    });
    map.addControl(geolocationControl);
})
$(document).on("pageInit", "#dc_location", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	
	var mySwiper = new Swiper('.j-index-banner', {
		speed: 400,
		spaceBetween: 0,
		pagination: '.swiper-pagination',
		autoplay: 2500
	});
	var mySwiper = new Swiper('.j-sort_nav', {
	    speed: 400,
	    spaceBetween: 0
	});
	$(document).on('click', '.j-open-youhui', function() {
		var i_height=$(this).parent().find('.youhui-item').height();
		var t_height=i_height*$(this).parent().find('.youhui-item').length;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().find('.youhui-list').css('max-height', i_height*2);
		} else {
			$(this).addClass('active');
			$(this).parent().find('.youhui-list').css('max-height', t_height);
		}
	});
});
$(document).on("pageInit", "#dc_locations_list", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	
	$("#dc_locations_list").on('click', '.j-open-youhui', function() {
		var i_height=$(this).parent().find('.youhui-item').height();
		var t_height=i_height*$(this).parent().find('.youhui-item').length;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().find('.youhui-list').css('max-height', i_height*2);
		} else {
			$(this).addClass('active');
			$(this).parent().find('.youhui-list').css('max-height', t_height);
		}
	});
	$("#dc_locations_list").on('click', '.j-open-select', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-mask").removeClass('active');
		} else {
			$(".j-open-select").removeClass('active');
			$(this).addClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-select").eq($(this).index()).addClass('active');
			$(".dc-mask").addClass('active');
		}
	});
	
	$("#dc_locations_list").on('click', '.j-fliter-item', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).find('input').prop("checked",false);
		} else {
			$(this).find('input').prop("checked",true);
			$(this).addClass('active');
		}
	});
	
	$(".dc-dp-dis").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		
		param_location();
	});
	
	$(".dc-cate-list").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		
		param_location();
	});
	
	$("#dc_locations_list").on('click', '.j-close-select', function() {
		$(".j-open-select").removeClass('active');
		$(".dc-select").removeClass('active');
		$(".dc-mask").removeClass('active');
		
		param_location();
	});
	
	function param_location(){
		
		var query = $("form[name='dc_location_param']").serialize();
		var ajax_url = $("form[name='dc_location_param']").attr("action");
		$(".content").scrollTop(0);
		$(document).off('infinite', '.infinite-scroll-bottom');
		
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"html",
			type:"POST",
			success:function(html){
				
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				
				init_list_scroll_bottom();
			}
		});
		return false;
	}

});

$(document).on("pageInit", "#dc_location_index", function(e, pageId, $page) {
	
	init_list_scroll_bottom();//下拉刷新加载
	
	/*if(menu_id){
		dc_change_num(menu_id,menu_num,1);
		$(".goods-info[data_id='"+menu_id+"']").find(".goods-num-box").removeClass('no-num').addClass("num");
        var g_height=$(".goods-info[data_id='"+menu_id+"']").parent().offset().top;
        var m_height=$(".m-cate-list").offset().top;
        $(".m-cate-list").scrollTo({toT:g_height-m_height});
        $(".goods-info[data_id='"+menu_id+"']").parent().addClass('search-menu');
	}*/
	
	if(is_in_open_time==0 || is_free_delivery==2){
		dc_cart_clear();
	}else{
		// cart_total_price();
        load_dc_cart_list();
	}
    // 异步获取购物车信息
    function load_dc_cart_list() {
        var param = {
            'act': 'get_dc_cart_list',
            'location_id': location_id
        };
        $.ajax({
            url:ajaxurl,
            data: param,
            dataType: 'json',
            success: function(data) {
                if (data.hasCart) {
                    var mAndN = data.menuidAndNum;
                    for (mid in mAndN) {
                        $(".goods-info[data_id='"+mid+"']").find(".goods-num-box").removeClass('no-num').addClass("num");
                        $(".goods-num[data_id='"+mid+"']").html(mAndN[mid]);
                        $(".min[data_id='"+mid+"']").attr("onclick","dc_change_num("+mid+","+mAndN[mid]+",-1);");
                        $(".plus[data_id='"+mid+"']").attr("onclick","dc_change_num("+mid+","+mAndN[mid]+",1);");

                        $item=$("<li class='flex-box b-line'  data_id='"+mid+"'>"
                            +"<p class='goods-name flex-1'>"+$(".goods-info[data_id='"+mid+"']").find(".goods-name").html()+"</p>"
                            +"<p class='edit-price' price='"+$(".goods-info[data_id='"+mid+"']").find(".price").attr("price")+"'>"+$(".goods-info[data_id='"+mid+"']").find(".price").html()+"</p>"
                            +"<div class='goods-num-box flex-box'>"
                            +"<a href='javascript:void(0);' class='min iconfont' data_id='"+mid+"' onclick='dc_change_num("+mid+","+mAndN[mid]+",-1);'>&#xe915;</a>"
                            +"<p class='goods-num' data_id='"+mid+"'>"+mAndN[mid]+"</p>"
                            +"<a href='javascript:void(0);' class='iconfont plus' data_id='"+mid+"' onclick='dc_change_num("+mid+","+mAndN[mid]+",1);'>&#xe685;</a>"
                            +"</div></li>");
                        $(".edit-list").prepend($item);
                    }
                }
                if (menu_id) { // 从搜索入口进来的，购物车信息加1
                    var cartNum = parseInt($(".goods-num[data_id='"+menu_id+"']").html());
                    dc_change_num(menu_id, cartNum, 1);
                    $(".goods-info[data_id='"+menu_id+"']").find(".goods-num-box").removeClass('no-num').addClass("num");
                    var g_height=$(".goods-info[data_id='"+menu_id+"']").parent().offset().top;
                    var m_height=$(".m-cate-list").offset().top;
                    $(".m-cate-list").scrollTo({toT:g_height-m_height});
                    $(".goods-info[data_id='"+menu_id+"']").parent().addClass('search-menu');
                    window.history.replaceState({}, document.title, base_url);
                }
                cart_total_price();
            }
        })
    }
	
	
	var swiper = new Swiper('.m-youhui-info', {
		speed:500,
        pagination: '',
        direction: 'vertical',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        mousewheelControl: true,
        autoplay: 3000,
        loop: true
    });
    tab_line();
    function tab_line() {
    	var init_width=$(".shop-tab li:first-child span").width();
    	var init_left=$(".shop-tab li:first-child span").offset().left;
    	$(".m-shop-tab .tab-line").css({
    		width: init_width,
    		left: init_left
    	});
    }
    $(".j-tab-item").bind('click', function() {
    	var l_width=$(this).find('span').width();
    	var l_left=$(this).find('span').offset().left;
    	$(".j-tab-item").removeClass('active');
    	$(this).addClass('active');
    	$(".m-shop-tab .tab-line").css({
    		width: l_width,
    		left: l_left
    	});
    	$(".j-shop-item").removeClass('active');
    	$(".j-shop-item").eq($(this).index()).addClass('active');
    });
    
    $(".plus").bind('click', function() {
    	if(is_in_open_time && is_free_delivery!=2){
    		$(this).parent().removeClass('no-num');
    	}
    });
    $(".j-cate-select").bind('click', function() {
    	$(".m-cate-list").unbind('scroll');
    	$(".j-cate-select").removeClass('active');
    	$(this).addClass('active');
    	var menu_top=$(".menu").offset().top;
    	var list_top=$(".m-cate-list").scrollTop();
    	var cate_top=$(".dc-cate-list").eq($(this).index()).offset().top;
    	s_height=cate_top-menu_top+list_top;
    	$(".m-cate-list").scrollTo({toT:s_height});
    });
    $(".m-cate-list").bind('touchstart', function() {
	    $(".m-cate-list").bind('scroll', function() {
			cate_scroll();
	    });
    });

    function cate_scroll() {
    	var menu_top=$(".menu").offset().top;
    	$(".cate-title").each(function(){
    		var cate_top=$(this).offset().top;
    		if (cate_top<=menu_top) {
    			$(".j-cate-select").removeClass('active');
    			$(".j-cate-select").eq($(this).parent().index()).addClass('active');
    		}
    	});
    }
    
    $(document).off('click',".j-show-edit");
    $(document).on('click',".j-show-edit", function() {
        if($('.cart-count').hasClass("hide")==false){
            $(".cart-count").toggleClass('active');
            $(".cart-mask").toggleClass('active');
        }
    });
    $(document).on('click', ".j-empty-edit",function() {
        $.toast("购物车是空的");
    });
    $(".no-goods-btn").bind('click', function() {
        $.toast("还未达到起送价格");
    });
    $(".j-close-edit").on('click', function() {
    	$(".cart-count").removeClass('active');
    	$(".cart-mask").removeClass('active');
    });
    $(".j-open-detail").bind('click', function() {
    	$(".dc-shop-detail").addClass('active');
    });
    $(".j-close-detail").bind('click', function() {
    	$(".dc-shop-detail").removeClass('active');
    });
    $(".m-cate-list .plus").on('click', function() {
    	if(is_in_open_time){
	    	$(".m-fly").addClass('active').css({
	    		left: $(this).offset().left,
	    		top: $(this).offset().top
	    	});
	        $(".cart-bar .cart-ico .iconfont").addClass('active');
	    	bool.init();
	    	bool.setOptions({
	    		targetEl: $("#target")
	    	});
	    	bool.start();
    	}
    });
    var bool = new Parabola({
    	el: ".m-fly",
		curvature: 0.004,
		duration: 300,
    	callback:function(){
            setTimeout('$(".cart-bar .cart-ico .iconfont").removeClass("active")', 300);
    		$(".m-fly").removeClass('active');
    	}
    });
    
    
    //增加收藏
	$('.add_location_collect').bind('click',function(){
		add_location_collect_function($(this));
        if ($(this).hasClass('collected')) {
            $(this).removeClass('collected');
        } else {
            $(this).addClass('collected');
        }
	});

    
    
});

function dc_change_num(id,count,num){
	
	if(is_in_open_time==0){
		$.toast("商家休息中，无法下单");
		return;
	}
	
	if(is_free_delivery==2){
		$.toast("超出配送范围，无法配送");
		return;
	}
	
	var menu_id=parseInt(id);
	var number=parseInt(num);
	var number_total=parseInt(count)+num;
	if(number_total<0){
		$.toast("该商品数量无法再减少");
		return;
	}
	if(num==1){
		if(count==0){
			$item=$("<li class='flex-box b-line'  data_id='"+id+"'>"
				+"<p class='goods-name flex-1'>"+$(".goods-info[data_id='"+id+"']").find(".goods-name").html()+"</p>"
				+"<p class='edit-price' price='"+$(".goods-info[data_id='"+id+"']").find(".price").attr("price")+"'>"+$(".goods-info[data_id='"+id+"']").find(".price").html()+"</p>"
				+"<div class='goods-num-box flex-box'>"
				+"<a href='javascript:void(0);' class='min iconfont' data_id='"+id+"' onclick='dc_change_num("+id+","+number_total+",-1);'>&#xe915;</a>"
				+"<p class='goods-num' data_id='"+id+"'>"+number_total+"</p>"
				+"<a href='javascript:void(0);' class='iconfont plus' data_id='"+id+"' onclick='dc_change_num("+id+","+number_total+",1);'>&#xe685;</a>"
				+"</div></li>");
			$(".edit-list").prepend($item);
			
		}
		
	}
	else{
		if(count==1){
			$(".edit-list").find("li[data_id='"+id+"']").remove();
			$(".goods-info[data_id='"+id+"']").find(".goods-num-box").removeClass("num").addClass("no-num");
		}
	}
	$(".goods-num[data_id='"+id+"']").html(number_total);
	$(".min[data_id='"+id+"']").attr("onclick","dc_change_num("+id+","+number_total+",-1);");
	$(".plus[data_id='"+id+"']").attr("onclick","dc_change_num("+id+","+number_total+",1);");
	cart_total_price();
	
	var query=new Object();
	query.menu_id=menu_id;
	query.number=number;
	query.number_total=number_total;
	query.tid=tid;
	query.location_id=location_id;
	query.supplier_id=supplier_id;
	query.distance=distance;
	query.act='dc_add_cart';
	$.ajax({
		url:ajaxurl,
		data:query,
		type:'post',
		dataType:'json',
		success:function(data){
			if(data.status==1){
				
			}else{
				$.toast(data.info);
				setTimeout(function(){
					window.location.reload();
				},500);
			}
		}
	});
		
}

//计算购物车商品价格
function cart_total_price(){
	var cart_num=0;
	var total_price=0;
	$(".edit-list").find("li[data_id]").each(function(){
		var num=parseInt($(this).find(".goods-num").html());
		var price=parseFloat($(this).find(".edit-price").attr("price"));
		cart_num+=num;
		total_price+=price*num;
	});
	
	var mune_price=total_price;
	
	if(payonline_conf){
		if(mune_price>0){
			var max=payonline_conf.length-1;
			if(mune_price>payonline_conf[max]['discount_limit']){
				var discount_limit=payonline_conf[max]['discount_limit'];
				var discount_amount=payonline_conf[max]['discount_amount'];
			}else{
				for(var i=0;i<=max;i++ ){
					if(payonline_conf[i]['discount_limit']>mune_price){
						if(i>0){
							var discount_limit=payonline_conf[i-1]['discount_limit'];
							var discount_amount=payonline_conf[i-1]['discount_amount'];
						}
						break;
					}
				}
			}	
		}
		
		if(discount_limit){
			if($(".cart-tip").length)
			{
				$(".cart-tip").html("已满"+discount_limit+",结算减"+discount_amount+"元");
			}
			else{
				var cart_tip=$("<div class='cart-tip t-line'>已满"+discount_limit+",结算减"+discount_amount+"元</div>");
				$(".cart-count").prepend(cart_tip);
			}
		}else{
			$(".cart-tip").remove();
		}
	}
	
	if(mune_price>=package_start_price && package_start_price>=0){
		total_package_price=0;
	}else{
		total_package_price=cart_num*package_price;
	}
	
	//total_price+=total_package_price;
	$(".no-goods-btn").remove();
	$(".cart-btn").remove();
	if(total_price>0){
		total_package_price=total_package_price.toFixed(2);
		total_price=total_price.toFixed(2);
		
		if(total_package_price>0){
			$(".cart-bar").find('.send-price').html("另需打包费￥"+total_package_price);
		}else{
			$(".cart-bar").find('.send-price').html('');
		}
		
		$(".package_price").attr("price",total_package_price);
        $(".no-goods-txt").hide();
        $(".cart-price").show();
        $(".send-price").show();
		$(".cart-price").html("￥"+total_price);
        $(".cart-ico").removeClass('j-empty-edit');
        $(".cart-ico").addClass('j-show-edit');
	}else{
        $(".no-goods-txt").show();
        $(".cart-price").hide();
        $(".send-price").hide();
        $(".cart-ico").removeClass('j-show-edit');
        $(".cart-ico").addClass('j-empty-edit');
        $(".cart-count").removeClass('active');
        $(".cart-mask").removeClass('active');
        setTimeout('$(".edit-list").empty()', 500);
		$(".goods-info").each(function(){
			$(this).find(".goods-num-box").addClass("no-num");
			$(this).find(".min").attr("onclick","dc_change_num("+$(this).attr("data_id")+",0,-1);");
			$(this).find(".plus").attr("onclick","dc_change_num("+$(this).attr("data_id")+",0,1);");
		});
	}
	
	if(cart_num>9){
		$(".num-count").html("9+");
	}else{
		$(".num-count").html(cart_num);
	}
	
	if(cart_num==0){
		$(".num-count").addClass("hide");
	}else{
		$(".num-count").removeClass("hide");
	}
	
	if(is_in_open_time==0){
		var btn=$("<div class='no-goods-btn cart-btn'>休息中</div>");
		btn.appendTo($(".cart-bar"));
	}
	else if(is_free_delivery==2){
		var btn=$("<div class='no-goods-btn cart-btn'>超出配送范围</div>");
		btn.appendTo($(".cart-bar"));
	}
	else if(mune_price>=start_price && total_price>0){	
		var btn=$("<a href='"+buy_url+"' data-no-cache='true' class='external cart-btn'>结算</a>");
		btn.appendTo($(".cart-bar"));
	}
	else{
		var btn=$("<div class='no-goods-btn cart-btn'>￥"+start_price.toFixed(2)+"起送</div>");
		btn.appendTo($(".cart-bar"));
	}
}

//清空购物车
function dc_cart_clear(){
	var query=new Object();
	query.location_id=location_id;
	query.act='dc_cart_clear';
	$.ajax({
			url:ajaxurl,
			data:query,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status==1){
					$(".edit-list").empty();
					cart_total_price();
				}
			}
	});
	
}

function add_location_collect_function(o){
	var query=new Object();

	var url=$(o).attr('action-url');
	
	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status==1){
					$.toast(data.info);
				}else if(data.status==0){
					$.toast(data.info);
				}
				if(data.status==-1){
					$.toast(data.info);
				}
			}
	});
	
}

//滑动脚本
$.fn.scrollTo =function(options){
        var defaults = {
            toT : 0,    //滚动目标位置
            durTime :100,  //过渡动画时间
            delay : 10,     //定时器时间
            callback:null   //回调函数
        };
        var opts = $.extend(defaults,options),
            timer = null,
            _this = this,
            curTop = _this.scrollTop(),//滚动条当前的位置
            subTop = opts.toT - curTop,    //滚动条目标位置和当前位置的差值
            index = 0,
            dur = Math.round(opts.durTime / opts.delay),
            smoothScroll = function(t){
                index++;
                var per = Math.round(subTop/dur);
                if(index >= dur){
                    _this.scrollTop(t);
                    window.clearInterval(timer);
                    if(opts.callback && typeof opts.callback == 'function'){
                        opts.callback();
                    }
                    return;
                }else{
                    _this.scrollTop(curTop + index*per);
                }
                sb=index;
            };
        timer = window.setInterval(function(){
            smoothScroll(opts.toT);
        }, opts.delay);
        return _this;
    };
    var Parabola = function(opts){
        this.init(opts);
    };
    Parabola.prototype = {
        constructor: Parabola,
        /*
         * @fileoverview 页面初始化
         * @param opts {Object} 配置参数
         */
        init: function(opts){
            this.opts =  $.extend(defaultConfig, opts || {});
            // 如果没有运动的元素 直接return
            if(!this.opts.el) {
                return;
            }
            // 取元素 及 left top
            this.$el = $(this.opts.el);
            this.$elLeft = this._toInteger(this.$el.offset().left);
            this.$elTop = this._toInteger(this.$el.offset().top);
            // 计算x轴，y轴的偏移量
            if(this.opts.targetEl) {
                this.diffX = this._toInteger($(this.opts.targetEl).offset().left+10) - this.$elLeft;
                this.diffY = this._toInteger($(this.opts.targetEl).offset().top) - this.$elTop;
            }else {
                this.diffX = this.opts.offset[0];
                this.diffY = this.opts.offset[1];
            }
            // 运动时间
            this.duration = this.opts.duration;
            // 抛物线曲率
            this.curvature = this.opts.curvature;
            
            // 计时器
            this.timerId = null;
            /*
             * 根据两点坐标以及曲率确定运动曲线函数（也就是确定a, b的值）
             * 公式： y = a*x*x + b*x + c;
             * 因为经过(0, 0), 因此c = 0
             * 于是：
             * y = a * x*x + b*x;
             * y1 = a * x1*x1 + b*x1;
             * y2 = a * x2*x2 + b*x2;
             * 利用第二个坐标：
             * b = (y2 - a*x2*x2) / x2
             */
             this.b = (this.diffY - this.curvature * this.diffX * this.diffX) / this.diffX;

             // 是否自动运动
             if(this.opts.autostart) {
                 this.start();
             }
        },
        /*
         * @fileoverview 开始
         */
        start: function(){
            // 开始运动
            var self = this;
            // 设置起始时间 和 结束时间
            this.begin = (new Date()).getTime();
            this.end = this.begin + this.duration;
            
            // 如果目标的距离为0的话 就什么不做
            if(this.diffX === 0 && this.diffY === 0) {
                return;
            }
            if(!!this.timerId) {
                clearInterval(this.timerId);
                this.stop();
            }
            // 每帧（对于大部分显示屏）大约16~17毫秒。默认大小是166.67。也就是默认10px/ms
            this.timerId = setInterval(function(){
                var t = (new Date()).getTime();
                self.step(t);
            },16);
            return this;
        },
        /*
         * @fileoverview 执行每一步
         * @param {string} t 时间
         */
        step: function(t){
            var opts = this.opts;
            var x,
                y;
            // 如果当前运行的时间大于结束的时间
            if(t > this.end) {
                // 运行结束
                x = this.diffX;
                y = this.diffY;
                this.move(x,y);
                this.stop();
                // 结束后 回调
                if(typeof opts.callback === 'function') {
                    opts.callback.call(this);
                }
            }else {
                // 每一步x轴的位置
                x = this.diffX * ((t - this.begin) / this.duration);
                // 每一步y轴的位置 y = a * x *x + b*x + c; c = 0
                y = this.curvature * x * x + this.b * x;
                // 移动
                this.move(x,y);
                if(typeof opts.stepCallback === 'function') {
                    opts.stepCallback.call(this,x,y);
                }
            }
            return this;
        },
        /*
         * @fileoverview 给元素定位
         * @param {x,y} x,y坐标
         * @return this
         */
        move: function(x,y) {
            this.$el.css({
                "position":'absolute',
                "left": this.$elLeft + x + 'px',
                "top": this.$elTop + y + 'px'
            });
            return this;
        },
        /*
         * 获取配置项
         * @param {object} options配置参数
         * @return {object} 返回配置参数项
         */
        getOptions: function(options){
            if(typeof options !== "object") {
                options = {};
            }
            options = $.extend(defaultConfig, options || {});
            return options;
        },
        /*
         * 设置options
         * @param options
         */
        setOptions: function(options) {
            this.reset();
            if(typeof options !== 'object') {
                options = {};
            }
            options = $.extend(this.opts,options);
            this.init(options);
            return this;
        },
        /*
         * 重置
         */
        reset: function(x,y) {
            this.stop();
            x = x ? x : 0;
            y = y ? y : 0;
            this.move(x,y);
            return this;
        },
        /*
         * 停止
         */
        stop: function(){
            if(!!this.timerId){
                clearInterval(this.timerId);
            }
            return this;
        },
        /*
         * 变成整数
         * isFinite() 函数用于检查其参数是否是无穷大。
         */
        _toInteger: function(text){
            text = parseInt(text);
            return isFinite(text) ? text : 0;
        }
    };
    var defaultConfig = {
        //需要运动的元素 {object | string}
        el: null,

        // 运动的元素在 X轴，Y轴的偏移位置
        offset: [0,0],

        // 终点元素 
        targetEl: null,

        // 运动时间，默认为500毫秒
        duration: 500,

        // 抛物线曲率，就是弯曲的程度，越接近于0越像直线，默认0.001
        curvature: 0.01,
        
        // 运动后执行的回调函数
        callback: null,

        // 是否自动开始运动，默认为false
        autostart: false,
        
        // 运动过程中执行的回调函数，this指向该对象，接受x，y参数，分别表示X，Y轴的偏移位置。
        stepCallback: null
    };
/**
 * Created by Administrator on 2016/9/7.
 */

$(document).on("pageInit", "#dc_order_pay", function(e, pageId, $page) {

	$('.fee_count').hide();
	init_payment_input();
	init_pay_btn();
	function init_payment_input(){
		$("input[name='all_account_money']").live("change",function () {

			if($("#all_account_money").hasClass("active")){
				$("#all_account_money").removeClass("active");
			}else{
				$("#all_account_money").addClass("active");
				$("input[name='payment']").prop("checked",false);
			}
			//count_buy_total();
			$('.fee_count').hide();
			$('.fee_count .payment_fee').text(0);
			local_count()
		});
		
		$(".payment").live("click",function(){
			$("input[name='payment']").prop("checked",false);
			$(".payment").removeClass('active');
			$(this).siblings("input[name='payment']").prop("checked",true);
			$(this).addClass("active");

			$("#all_account_money").removeClass("active");
			$("input[name='all_account_money']").prop("checked",false);
			var fee = Number($('.active .fee_amount').text());
			if (fee > 0) {
				$('.fee_count .payment_fee').text(fee.toFixed(2));
				$('.fee_count').show();
			} else {
				$('.fee_count .payment_fee').text(0);
				$('.fee_count').hide();
			}
			local_count()
		});
	}

	function local_count() {
		var total= $('.total_count').text().replace(",","");
		var payment_fee= $('.payment_fee').text().replace(",","");
		var discount= 0; // $('.discount').text().replace(",","");
		var ready_pay = Number(total) - Number(discount) + Number(payment_fee);
		$('.ready_pay').text(ready_pay.toFixed(2));
	}

	function init_pay_btn(){
	    $(".u-sure-pay").bind("click",function(){
	    	var all_account_money = 0; // 是否余额支付
			var payment = 0;
			//全额支付
			if($("#all_account_money").hasClass("active")) {
				all_account_money = 1;
			} else { // 其它支付方式
				payment = $("input[name='payment']:checked").val();
			}

			if (all_account_money == 0 && payment == 0) {
				$.toast('请选择一个支付方式');
				return;
			}
			var query = {
				'payment': payment, 
				'all_account_money': all_account_money,
				'id': order_id,
				'act': 'order_done'
			};
	        $.ajax({
				url: ORDER_AJAX,
				data:query,
				type: "POST",
				dataType: "json",
				success: function(data){
					if(data.status==1){
						if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
							if(data.pay_status==1){
								$.router.load(data.jump, true);
							}else{
								location.href=data.jump;
							}
						} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
							 $.router.load(data.jump, true);

						} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
							if(data.online_pay==3){
								try {

									var str = pay_sdk_json(data.sdk_code);
									App.pay_sdk(str);
								} catch (ex) {

									$.toast(ex);
									$.loadPage(location.href);
								}
							}else{
								var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

								try {
									App.open_type(pay_json);
									$.confirm('已支付完成？', function () {
										$.loadPage(location.href);

									},function(){
										$.loadPage(location.href);

									});
								} catch (ex) {
									$.toast(ex);
									$.loadPage(location.href);
								}
							}
						}
					}else{
						
						$.alert(data.info);
					}
				},
				error:function(ajaxobj) {

				}
			});
	    });
	};
});


$(document).on("pageInit", "#dc_point", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
});
$(document).on("pageInit", "#dc_position", function(e, pageId, $page) {
	//左侧结果点击对象
	var cur_item = null;
	var total;
	var cur_page = 0;
	var marker_array = new Array();
	$(function(){
	
		init_search_name();
		var map = new BMap.Map("map_show");
		map.centerAndZoom(CITY_NAME,12);                   // 初始化地图,设置城市和地图级别。
		//添加点击事件监听
		map.addEventListener("click", function(e){    
		 
		 var query = {ak:BAIDU_APPKEY,location:e.point.lat+","+e.point.lng,output:"json"};
			$.ajax({
				url:"http://api.map.baidu.com/geocoder/v2/",
				dataType:"jsonp",
				callback:"callback",
				data:query,
				success:function(obj){
					var address = obj.result.formatted_address;
					var title = obj.result.sematic_description;
					var infoWindow_obj = create_window({title:title,content:address,lng:e.point.lng,lat:e.point.lat});
					map.openInfoWindow(infoWindow_obj,new BMap.Point(e.point.lng,e.point.lat)); //开启信息窗口
				}
			});
	
		});
		var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
			{"input" : "q_text"
			,"location" : map
		});
	
	
		if($.trim(dc_title)!=''){
			$('#q_text').val(dc_title);
			ac.setInputValue(dc_title);
		}
		var myValue;
		ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
			var _value = e.item.value;
			myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
	
			searchlocation(myValue,_value.city);
		});
	
		
		$('.dc_clear_history').bind('click',function(){
			$.ajax({
				url:dc_clear_history_url,
				type:"GET",
				success:function(data){
					$('.search-history').hide();
					$('.history_address').hide();
					$('#now_address').html("<p class='flex-1 address'>定位失败</p><div class='flex-box re-position'><p class='iconfont position-ico'>&#xe691;</p><p>重新定位</p></div>");
				}
			});
			return false;
		});
	
			
	
		$('.result-item').bind("click",function(){
	
			var data=$(this).attr('data-params');
			var dataset=eval("("+data+")");  //json字体串转为json对象
			url=dc_position_url;
			$.ajax({
				url:url,
				type:"POST",
				data:{'xpoint':dataset.lng,'m_longitude':dataset.lng,'ypoint':dataset.lat,'m_latitude':dataset.lat,'dc_title':dataset.title,'dc_content':dataset.content,'dc_num':dataset.dc_num,'city':dataset.city},
				success:function(data){
				location.href=dc_url;	
	
				}
			});
		});
	
	
		$('.do_search').bind('click',function(){
			var kw=$.trim($("#q_text").val());
		
			searchlocation(kw,CITY_NAME);
		});
	
			
		$('.re-position').live('click', function() {
			relocation();
		})
	});
	
	$('.history_address_d').bind('click',function(){
		var h_val = $(this).html();
		searchlocation(h_val,CITY_NAME);
	});
	
	function init_search_name(){
	
		if(typeof(dc_title)!='undefined'){
		$('#q_text').val(dc_title);
		}
	}
	
	
	function searchlocation(kw,city){
	
		cur_item = null;
		marker_array = new Array();
		var op_ak = BAIDU_APPKEY;
		if($.trim(kw)){
		var op_q=encodeURIComponent(kw);
		}
		else
		{
		var op_q = encodeURIComponent($.trim($("#q_text").val()));
		}
	
		if(op_q==''){
			alert('请输入地址搜索周边商家');
			return false;
		}
		
		var op_page_size = 1;
		var op_page_num = cur_page;
		var op_region = encodeURIComponent(city);
		var url = "http://api.map.baidu.com/place/v2/search?ak="+op_ak+"&output=json&query="+op_q+"&page_size="+op_page_size+"&page_num="+op_page_num+"&scope=1&region="+op_region;
	
		if($.trim($("#q_text").val())){
			$.ajax({
				url:url,
				dataType:"jsonp",
		        jsonp: 'callback',
				type:"GET",
				success:function(obj){
					if(obj.status == 0){			
							var item=obj.results[0];
							var query = new Object();
							query.act = "get_dc_num";
							query.dc_xpoint = item.location.lng;
							query.dc_ypoint = item.location.lat;
							query.dc_title = item.name;
							query.city = city;
							query.dc_content = item.address;
							$.ajax({
								url:DC_AJAX_URL,
								data:query,
								dataType:"json",
								type:"POST",
								success:function(objdata)
								{
					
									url=dc_position_url;
									$.ajax({
										url:url,
										type:"POST",
										dataType:"json",
										data:{'xpoint':item.location.lng,'m_longitude':item.location.lng,'ypoint':item.location.lat,'m_latitude':item.location.lat,'dc_title':item.name,'dc_content':item.address,'dc_num':objdata.dc_num,'city':city},
										success:function(obj){
	
											if(obj.status){
												location.href=dc_url;
											}else{
												$.toast(obj.info);
											}
										
										
										}
									});
									
								
								}
							});
							
	
	
					}
				}
			});
		}	
		
	}

	function relocation() {

		var options = {timeout: 8000};
		var geolocation = new qq.maps.Geolocation(TENCENT_MAP_APPKEY, "myapp");
		geolocation.getLocation(showPosition, showErr, options);

	}
	function showPosition(p){
		has_location = 1;//定位成功;
		m_latitude = p.lat; //纬度
		m_longitude = p.lng;
		userxypoint(m_latitude, m_longitude,'GCJ02');
	}
	function showErr(p){
		//alert("定位失败");
		console.log("定位失败");
	}
//将坐标返回到服务端;
	function userxypoint(latitude,longitude,type){
		var query = new Object();
		query.m_latitude = latitude;
		query.m_longitude = longitude;
		query.m_type=type;
		//alert(latitude+":"+longitude);
		//return;
		$.ajax({
			url:geo_url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){

				if(data.status==1)
				{
						location.href = dc_url;

				}
				else
				{
					alert(data.info);
				}
			}
			,error:function(){
			}
		});
	}
});


			
$(document).on("pageInit", "#dc_res_cart", function(e, pageId, $page) {

	//打开送货时间选择
	$(".j-open-time").on('click', function() {
		$(".dc-mask").addClass('active');
		$(".time-select").addClass('active');
		var send_time=$(this).find('input').attr('value');
		$(".j-time-choose").each(function() {
			if ($(this).attr('value')==send_time) {
				$(this).addClass('active');
			}
		});
	});
	//关闭送货时间选择
	$(".j-close-time").on('click', function() {
		$(".dc-mask").removeClass('active');
		$(".time-select").removeClass('active');
	});
	//选择时间
	$(".j-time-choose").on('click', function() {
        if ($(this).hasClass('timeerror')) {
            $.toast('本时段无法预订');
            return;
        }
        if ($(this).hasClass('fullbuy')) {
            $.toast('本时段预订已满');
            return;
        }
		$(".j-time-choose").removeClass('active');
		$(this).addClass('active');
		$(".j-res-time").html($(this).find('p').html());
		$("#time-value").attr('value', $(this).attr('value'));
	});
	//打开备注
	$(".j-open-memo").on('click', function() {
		$("#memo").focus();
		$(".dc-mask").addClass('active');
		$(".memo-box").addClass('active');
	});
	//关闭备注
	$(".j-close-memo").on('click', function() {
        var memo = $.trim($('textarea[name="dc_comment"]').val()).substr(0,100);
        $('#memo').val(memo);
		close_memo();
	});
	//确认备注
	$(".j-memo").on('click', function() {
        var memo = $.trim($('textarea[name="dc_comment"]').val());
        if (memo == "") {
            $(".j-res-memo").html('<span class="default-txt">备注您的口味、偏好等</span>');
        }else {
            if (memo.length > 100) {
                $.toast('备注不超过100字,当前'+memo.length+'字');
                return;
            }
            $(".j-res-memo").html(memo);
        }
        close_memo();
	});
    function close_memo() {
        $(".dc-mask").removeClass('active');
        $(".memo-box").removeClass('active');
    }
	//选择只订座
	$(".j-only-res").on('click', function() {
        if ($(this).hasClass('active')) {
            return;
        }
		$(".res-way").removeClass('active');
		$(this).addClass('active');
		$("#res-way").attr('value', $(this).attr('value'));
        $('.res-goods-info').hide();
        pay_price_format();
		/* Act on the event */
	});
	//打开菜单
	$(document).on('click', '.j-open-menu', function() {
        /*if ($(this).hasClass('active')) {
            return;
        }*/
        if ($(this).hasClass('res-way')) {
            if ($('.res-goods-info').find('.goods-list li').length > 0) {
                $(".res-way").removeClass('active');
                $(this).addClass('active');
                $("#res-way").attr('value', $(this).attr('value'));
                $('.res-goods-info').show();
                pay_price_format();
                return;
            }
        }
        var param = {
            'lid': location_id, 
            'table_menu_id': table_menu_id,
            'act': 'res_cart_item'
        };
        $.ajax({
            url: CART_URL,
            data: param,
            dataType: 'json',
            success: function(data) {
                $('.j-shop-item').html(data.html);
                $.popup('.popup-menu');
            }
        })
	});
	//关闭菜单
	$(document).on('click', '.j-close-popup', function() {
        refresh_dc_cart();
        $(this).parents('.popup').show();
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
        pay_price_format();
	});
	//购物车脚本
    $(".plus").bind('click', function() {
    	$(this).parent().removeClass('no-num');
    });
    $(".menu").on('click', '.j-cate-select',function() {
    	$(".m-cate-list").unbind('scroll');
    	$(".j-cate-select").removeClass('active');
    	$(this).addClass('active');
    	var menu_top=$(".menu").offset().top;
    	var list_top=$(".m-cate-list").scrollTop();
    	var cate_top=$(".dc-cate-list").eq($(this).index()).offset().top;
    	s_height=cate_top-menu_top+list_top;
    	$(".m-cate-list").scrollTo({toT:s_height});
    });
    $(".m-cate-list").bind('touchstart', function() {
	    $(".m-cate-list").bind('scroll', function() {
			cate_scroll();
	    });
    });

    function cate_scroll() {
    	var menu_top=$(".menu").offset().top;
    	$(".cate-title").each(function(){
    		var cate_top=$(this).offset().top;
    		if (cate_top<=menu_top) {
    			$(".j-cate-select").removeClass('active');
    			$(".j-cate-select").eq($(this).parent().index()).addClass('active');
    		}
    	});
    }
    $(document).on('click',".j-show-edit", function() {
        if($('.cart-count').hasClass("hide")==false){
            $(".cart-count").toggleClass('active');
            $(".cart-mask").toggleClass('active');
        }
    });
    $(document).on('click', ".j-empty-edit",function() {
        $.toast("购物车是空的");
    });
    $(".no-goods-btn").bind('click', function() {
        $.toast("还未达到最低价格");
    });
    $(document).on('click', ".j-close-edit", function() {
        refresh_dc_cart();
        $(this).parents('.popup').hide();
        $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
    	// $(".cart-count").removeClass('active');
    	// $(".cart-mask").removeClass('active');
        
    });
    $(".j-open-detail").bind('click', function() {
    	$(".dc-shop-detail").addClass('active');
    });
    $(".j-close-detail").bind('click', function() {
    	$(".dc-shop-detail").removeClass('active');
    });
    $(".menu").on('click', '.m-cate-list .plus',function() {
    	$(".m-fly").addClass('active').css({
    		left: $(this).offset().left,
    		top: $(this).offset().top
    	});
        $(".cart-bar .cart-ico .iconfont").addClass('active');
    	bool.init();
    	bool.setOptions({
    		targetEl: $("#target")
    	});
    	bool.start();
    });
    var bool = new Parabola({
    	el: ".m-fly",
		curvature: 0.004,
		duration: 300,
    	callback:function(){
            setTimeout('$(".cart-bar .cart-ico .iconfont").removeClass("active")', 300);
    		$(".m-fly").removeClass('active');
    	}
    });

    // 确认支付
    $('.res-pay').on('click', function() {
        if ($(this).hasClass('disable')) {
            return;
        }
        // 进本信息判断
        var time_id = $('input[name="order_delivery_time"]').val();
        if (time_id == 0) {
            $.toast('请选择到店时间');
            return;
        }
        var consignee = $.trim($('input[name="consignee"]').val());
        if (!consignee) {
            $.toast('请输入预订人的姓名');
            return;
        }
        var mobile = $.trim($('input[name="mobile"]').val());
        if (mobile == '') {
            $.toast('请输入预订人的手机号');
            return;
        }
        if (/^1[34578]\d{9}$/.test(mobile) == false) {
            $.toast('手机号码格式有误');
            return;
        }
        var dc_comment = $.trim($('textarea[name="dc_comment"]').val());

        // 订座定金
        var res_price = Number($('.res-price').attr('data-value'));
        // 预订方式
        var rs_type = Number($('#res-way').val());
        var count_price = Number($('.count-price').attr('data-value'));
        if (res_price > 0 && rs_type == 2) { // 有订单有点菜
            if (count_price < res_price) {
                $.toast('点菜的金额需要超过定金金额');
                return;
            }
        }
        var param = {
            'lid': location_id,
            'item_time_id': time_id,
            'table_menu_id': table_menu_id,
            'consignee': consignee,
            'mobile': mobile,
            'dc_comment': dc_comment,
            'rs_type': rs_type,
            'rs_date': rs_date,
            'act': 'res_make_order',
            // 'act': 'old_make_order',
        };

        $.ajax({
            url: CART_URL,
            data: param,
            dataType: 'json',
            type: 'post',
            success: function(data) {
                if (data.user_login_status == 0) {
                    $.toast('未登录');
                } else {
                    $.toast(data.info);
                    if (data.status == 1) {
                        setTimeout(function() {
                            $.router.load(data.jump, true);
                        }, 2000);
                    }
                }
            }
        });
    });
    // refresh_dc_cart();
    // 获取实时的购物车信息
    function refresh_dc_cart() {
        var param = {
            'location_id': location_id,
            'table_menu_id': table_menu_id,
            'act': 'dc_res_cart_list',
        };

        $.ajax({
            url: DC_AJAX_URL,
            data: param,
            dataType: 'json',
            type: 'post',
            success: function(data) {
                if (data.list.length > 0) {
                    var list_html = '';
                    // var total_html = '';
                    var list = data.list;
                    for (i in list) {
                        list_html += '<li class="flex-box">' + 
                        '<p class="goods-name flex-1">'+list[i].name+'</p>' + 
                        '<p class="goods-num">x'+list[i].num+'</p>' + 
                        '<p class="goods-price" data-value="'+list[i].unit_price+'">'+list[i].format_unit_price+'</p>' + 
                        '</li>';
                    }
                    $('.res-goods-info .goods-list').html(list_html);
                    $('.count-price').html(data.format_total_price);
                    $('.count-price').attr('data-value', data.total_price);
                    $(".res-way").removeClass('active');
                    $('.j-open-menu').addClass('active');
                    $("#res-way").attr('value', 2);
                    $('.res-goods-info').show();
                } else { // 未点菜
                    $(".res-way").removeClass('active');
                    $('.j-only-res').addClass('active');
                    $("#res-way").attr('value', 1);
                    $('.res-goods-info').hide();
                }
                pay_price_format();
            }
        });
    }

    function pay_price_format() {
        var res_way = Number($('#res-way').val());
        if (res_way != 1 && res_way != 2) {
            res_way = 1;
        }
        item_price = Number(item_price);
        var rht = '预订定金';
        var rp = item_price;
        if (res_way == 1) {
            $('.res-pay').removeClass('disable');
        } else {
            var cp = Number($('.count-price').attr('data-value'));
            if (cp >= item_price) {
                rht = '预订菜金';
                rp = cp;
                $('.res-pay').removeClass('disable');
            } else {
                rht = '还差';
                rp = Math.round((item_price - cp) * 100)/100;
                $('.res-pay').addClass('disable');
            }
        }
        $('.res-content').html(rht);
        $('.res-price').html('&yen;' + rp);
    }
    pay_price_format();
});


function dc_change_res_num(id,count,num) {
    var menu_id=parseInt(id);
    var number=parseInt(num);
    var number_total=parseInt(count)+num;
    if(number_total<0){
        // $.toast("该商品数量无法再减少");
        return;
    }
    if(num==1){
        if(count==0){
            $(".goods-info[data_id='"+id+"']").find(".goods-num-box").removeClass("no-num").addClass("num");
            $item=$("<li class='flex-box b-line'  data_id='"+id+"'>"
                +"<p class='goods-name flex-1'>"+$(".goods-info[data_id='"+id+"']").find(".goods-name").html()+"</p>"
                +"<p class='edit-price' price='"+$(".goods-info[data_id='"+id+"']").find(".price").attr("price")+"'>"+$(".goods-info[data_id='"+id+"']").find(".price").html()+"</p>"
                +"<div class='goods-num-box flex-box'>"
                +"<a href='javascript:void(0);' class='min iconfont' data_id='{$item.menu_id}' onclick='dc_change_res_num("+id+","+number_total+",-1);'>&#xe915;</a>"
                +"<p class='goods-num' data_id='"+id+"'>"+number_total+"</p>"
                +"<a href='javascript:void(0);' class='iconfont plus' data_id='{$item.menu_id}' onclick='dc_change_res_num("+id+","+number_total+",1);'>&#xe685;</a>"
                +"</div></li>");
            $(".edit-list").prepend($item);
        }
    } else{
        if(count==1){
            $(".edit-list").find("li[data_id='"+id+"']").remove();
            $(".goods-info[data_id='"+id+"']").find(".goods-num-box").removeClass("num").addClass("no-num");
        }
    }
    $(".goods-num[data_id='"+id+"']").html(number_total);
    $(".min[data_id='"+id+"']").attr("onclick","dc_change_res_num("+id+","+number_total+",-1);");
    $(".plus[data_id='"+id+"']").attr("onclick","dc_change_res_num("+id+","+number_total+",1);");
    res_cart_total_price();
    
    var query=new Object();
    query.menu_id=menu_id;
    query.number=number;
    query.number_total=number_total;
    // query.tid=tid;
    query.table_menu_id = table_menu_id;
    query.location_id=location_id;
    // query.supplier_id=supplier_id;
    query.act='dc_add_cart';
    $.ajax({
        url:DC_AJAX_URL,
        data:query,
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status==1){
                
            }else{
                $.toast(data.info);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }
        }
    });
}

//计算购物车商品价格
function res_cart_total_price(){
    var cart_num=0;
    var total_price=0;
    $(".edit-list").find("li[data_id]").each(function(){
        var num=parseInt($(this).find(".goods-num").html());
        var price=parseFloat($(this).find(".edit-price").attr("price"));
        cart_num+=num;
        total_price+=price*num;
    });

    $(".no-goods-btn").remove();
    $(".cart-btn").remove();
    if(total_price>0){
        total_price=total_price.toFixed(2);


        $(".no-goods-txt").hide();
        $(".cart-price").show();
        $(".send-price").show();
        $(".cart-price").html("￥"+total_price);
        $(".cart-ico").removeClass('j-empty-edit');
        $(".cart-ico").addClass('j-show-edit');
    }else{
        $(".no-goods-txt").show();
        $(".cart-price").hide();
        $(".send-price").hide();
        $(".cart-ico").removeClass('j-show-edit');
        $(".cart-ico").addClass('j-empty-edit');
        $(".cart-count").removeClass('active');
        $(".cart-mask").removeClass('active');
        setTimeout('$(".edit-list").empty()', 500);
        $(".goods-info").each(function(){
            $(this).find(".goods-num-box").addClass("no-num");
            $(this).find(".min").attr("onclick","dc_change_res_num("+$(this).attr("data_id")+",0,-1);");
            $(this).find(".plus").attr("onclick","dc_change_res_num("+$(this).attr("data_id")+",0,1);");
        });
    }
    
    $(".num-count").html(cart_num);
    if(cart_num==0){
        $(".num-count").addClass("hide");
    }else{
        $(".num-count").removeClass("hide");
    }
    if(total_price>0){  
        var btn=$("<a class='cart-btn j-close-edit'>确认</a>");
        btn.appendTo($(".cart-bar"));
    }
    else{
        var btn=$("<div class='no-goods-btn cart-btn'>未点菜</div>");
        btn.appendTo($(".cart-bar"));
    }
}

//清空购物车
function dc_res_cart_clear(){
    var query=new Object();
    query.table_menu_id=table_menu_id;
    query.location_id=location_id;
    query.act='dc_cart_clear';
    $.ajax({
        url:DC_AJAX_URL,
        data:query,
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status==1){
                $('.goods-list').html('');
                $('.res-goods-info').hide();
                $(".edit-list").empty();
                res_cart_total_price();
            }
        }
    });
}
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


$(document).on("pageInit", "#dc_table", function(e, pageId, $page) {

	$('a.rs-btn').on('click', function() {
		if (location_close) {
			$.toast('店铺休息中,暂停预订');
			return;
		} else {
			$.router.load($(this).attr('data-url'), true);
		}
	})

	$(".j-rs-day").on('click', function() {
		$(".j-rs-day").removeClass('active');
		$(this).addClass('active');
		$(".shop-rs-list").removeClass('active');
		$(".shop-rs-list").eq($(this).index()).addClass('active');
	});


	//增加收藏
	$('.add_location_collect').bind('click',function(){
		add_location_collect_function($(this));
        if ($(this).hasClass('collected')) {
            $(this).removeClass('collected');
        } else {
            $(this).addClass('collected');
        }
	});
});
$(document).on("pageInit", "#dc_table_list", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	$("#dc_table_list").on('click', '.j-open-select', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-mask").removeClass('active');
		} else {
			$(".j-open-select").removeClass('active');
			$(this).addClass('active');
			$(".dc-select").removeClass('active');
			$(".dc-select").eq($(this).index()).addClass('active');
			$(".dc-mask").addClass('active');
		}
	});
	/*$(".dc-dp-dis").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		param_location();
	});*/
	$('.dc-dp-dis').on('click', '.j-ajaxchoose', function() {
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='sort']").val(sort);
		var sort=$(this).find(".select-tit").html();
		$(".now-sort").html(sort);
		param_location();
	})
	/*$(".dc-cate-list").find(".j-ajaxchoose").click(function(){
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		param_location();
	});*/
	$('.dc-cate-list').on('click', '.j-ajaxchoose', function() {
		$(".dc-cate-list").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var sort=$(this).attr('data-id');
		$("input[name='cid']").val(sort);
		var cate=$(this).find(".select-tit").html();
		$(".now-cate-name").html(cate);
		param_location();
	})
	/*$(".dc-area").find('.j-ajaxchoose').click(function() {
		$(".dc-area").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var qid=$(this).attr('data-id');
		$("input[name='qid']").val(qid);
		var aid=$(this).parents('ul').attr('data-id');
		$("input[name='aid']").val(aid);
		var dc_area=$(this).find(".select-tit").html();
		$(".now-area-name").html(dc_area);
		param_location();
	});*/
	$('.dc-area').on('click', '.j-ajaxchoose', function() {
		$(".dc-area").find(".j-ajaxchoose").removeClass("active");
		$(this).addClass("active");
		var qid=$(this).attr('data-id');
		$("input[name='qid']").val(qid);
		var aid=$(this).parents('ul').attr('data-id');
		$("input[name='aid']").val(aid);
		var dc_area=$(this).find(".select-tit").html();
		$(".now-area-name").html(dc_area);
		param_location();
	})
	$("#dc_table_list").on('click', '.j-area', function() {
		$(".j-area").removeClass('active');
		$(this).addClass('active');
		$(".area-list").removeClass('active');
		$(".area-list").eq($(this).index()).addClass('active');
		/* Act on the event */
	});
	$("#dc_table_list").on('click', '.j-close-select', function() {
		$(".j-open-select").removeClass('active');
		$(".dc-select").removeClass('active');
		$(".dc-mask").removeClass('active');
		// param_location();
	});
	function param_location(){
		var query = $("form[name='dc_location_param']").serialize();
		var ajax_url = $("form[name='dc_location_param']").attr("action");
		$(".content").scrollTop(0);
		$(document).off('infinite', '.infinite-scroll-bottom');
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"html",
			type:"POST",
			success:function(html){
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".dc-cate-list").html($(html).find('.dc-cate-list').html());
				$(".dc-dp-dis").html($(html).find('.dc-dp-dis').html());
				$(".dc-area").html($(html).find('.dc-area').html());
				init_list_scroll_bottom();
			}
		});
		return false;
	}

});

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
/**
 * Created by Administrator on 2016/11/18.
 */

$(document).on("pageInit", "#dist_center", function(e, pageId, $page)  {

    $('.dist_scope').bind('click', function() {
        var url = $(this).attr('url');
        window.location = url;
    })
});
$(document).on("pageInit", "#dist_info_setting", function(e, pageId, $page)  {

    //退出登录
    $(".btn-con").click(function(){
        if(app_index=='app'){
            App.logout();
            return false;
        }
        $.confirm("是否退出当前账号？","",function(){
            dist_login_out();
        });
    });
    function dist_login_out(){
        var exit_url=$(this).attr("data-url");
        var query = new Object();
        query.act='loginout';
        $.ajax({
            url:exit_url,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status)
                {
                    $.toast(obj.info);
                    setTimeout(function(){
                        window.location.href=obj.jump;
                    },1500);
                }
                else
                {
                    $.toast(obj.info);
                }
            }
        });
    }
});
$(document).on("pageInit", "#dist_msg_index", function(e, pageId, $page) {
	refreshdata([".dist_msg_change"]);
});
$(document).on("pageInit", "#dist_order_index", function(e, pageId, $page) {
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j-ajaxlist-"+status,".j-ajaxadd-"+status);
	}
	
	function tab_line() {
		var init_width=$(".biz-shop-order-tab .active span").width();
		var init_left=$(".j-tab-item.active span").offset().left;
		$(".tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	$(".biz-shop-order-tab").on('click','.j-tab-item', function(event) {
		var type=$(this).attr("type");
		
		if($(".content").find(".j-ajaxlist-"+type).length > 0){

			$(".biz-shop-order-tab .j-tab-item").removeClass('active');
			$(this).addClass('active').siblings().removeClass('active');
			
			$(".content .m-biz-shop-order-list").removeClass('active');
			$(".content").find(".j-ajaxlist-"+type).addClass('active').siblings().removeClass('active');
			tab_line();
			
			$(".content").scrollTop(1); 
		    if( $(".content").scrollTop()>0 ){
		    	infinite(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
		    }
			
		}else{
		
			$(".j-tab-item").removeClass('active');
			$(this).addClass('active');
			var item_width=$(this).find('span').width();
			var item_left=$(this).find('span').offset().left;
			$(".tab-line").css({
				width: item_width,
				left: item_left
			});
			var url=$(this).attr("data-href");
			
			$.ajax({
				url:url,
				type:"POST",
				success:function(html)
				{
					$(".content").append($(html).find(".content").html());
					$(".j-ajaxlist-"+type).addClass('active').html($(html).find(".j-ajaxlist-"+type).html()).siblings().removeClass('active');
			
					if ($(html).find(".j-ajaxadd-"+type).length==0) {
						return;
					}else{
						$(".content").scrollTop(1); 
					    if( $(".content").scrollTop()>0 ){
							$(document).off('infinite', '.infinite-scroll-bottom');
							init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
					    }
					};
				},
				error:function()
				{
					$.toast("加载失败咯~");
				}
			});
			$.showIndicator();
			setTimeout(function () {
				$.hideIndicator();
			}, 800);
		}
	});
	var swiperm = new Swiper(".j-order-shop-img", {
	    scrollbarHide: true,
	    slidesPerView: 'auto',
	    freeMode: false,
	});
});
$(document).on("pageInit", "#dist_order_view", function(e, pageId, $page) {
	$(".do_delivery").bind("click",function(){
		var action=$(this).attr("action");
		$.confirm('确认发货吗？', function () {
			$.ajax({
				url:action,
				dataType:"json",
				type:"POST",
				success:function(obj){
					console.log(obj);
					if(obj.status==1){
						$.toast("发货成功");
						$(".logistics-code").val('');
						$("#remark").val('');
						$(".j-goods-item").find('input').attr("checked",false);
						if(obj.jump){
							setTimeout(function(){
								location.reload();
							},1500);
						}
					}else if(obj.status==0){
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
				}
			});
		});
	});
});
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2013 http://www.YiiSpace.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Micheal Chen <shilong.chen2012@gmail.com>
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-03-06 09:49
// +----------------------------------------------------------------------
$(document).on("pageInit", "#dist_undeliver", function (e, pageId, $page) {
    $(".biz-link-bar").on('click', '.j-qrcode', function() {
        if(app_index == 'wap'){
            $.toast("手机浏览器暂不支持，请下载APP");
        }
    });
    $(".biz-manager-bar").on('click', '.j-unopen', function() {
        $.toast("暂未开放");
    });
    $(".biz-manager-bar").on('click', '.store_pay_unopen', function() {
        $.toast("没有操作权限");
    });
    $(".to-qrcode").on('click', function() {
        $.toast("暂未开放");
    });
    var pre_coupon_pwd = "";
    $("input[name='qr_code']").keyup(function () {
        var coupon_pwd = $(this).val();
        var code_len = coupon_pwd.length;
        var code_rule = /^[0-9]{12}$/;

        if (pre_coupon_pwd != coupon_pwd){
            pre_coupon_pwd = coupon_pwd;
            if (code_len == 12) {
                if (!code_rule.test(coupon_pwd)) {
                    $.toast('您输入的券码无效');
                }
                else {
                    $.post(index_check_url, { "coupon_pwd": coupon_pwd }, function (data) {
                        if (data.status) {
                            $(".code-input").val("");
                            location.href = data.jump;
                        } else {
                            $.toast(data.info);
                            if(data.jump){
                                setTimeout(function(){
                                        location.href=data.jump;
                                },2000);
                            }
                        }
                    }, "json");
                }
            } else if (code_len > 12) {
                $.toast('您输入的券码无效');
            }
        }
    });
});

// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 吴庆祥
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-03-09 10:46
// +----------------------------------------------------------------------
$(document).on("pageInit", "#dist_undeliver_coupon_check", function (e, pageId, $page) {
    $("#dist_undeliver_coupon_check .check-cancel").bind("click",function(){
        window.location.href=index_url;
    });
    $("#dist_undeliver_coupon_check .check-confirm").bind("click",function(){
        var query = {};
        query.coupon_pwd = coupon_pwd;
        $.ajax({
            url:url,
            data:query,
            dataType: "json",
            type:"post",
            success:function(obj){
                if(obj.status==1){
                    $.toast(obj.info);
                    if(obj.jump){
                        setTimeout(function(){
                            location.href = obj.jump;
                        },1000);
                    }
                }else{
                    $.toast(obj.info);
                }
            },
            error: function() {
                $.toast("网络被风吹走啦~");
            }
        });
    });
});

$(document).on("pageInit", "#dist_undeliver_scope", function (e, pageId, $page) {
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    var xpoint = $('input[name="xpoint"]').val();
    var ypoint = $('input[name="ypoint"]').val();
    var xpoints = $('input[name="xpoints"]').val().split(",");
    var ypoints = $('input[name="ypoints"]').val().split(",");
    map.centerAndZoom(new BMap.Point(xpoint, ypoint), 11);
    map.enableScrollWheelZoom(true);
    //鼠标绘制完成回调方法   获取各个点的经纬度
    var styleOptions = {
        strokeColor: "red",    //边线颜色。
        fillColor: "red",      //填充颜色。当参数为空时，圆形将没有填充效果。
        strokeWeight: 3,       //边线的宽度，以像素为单位。
        strokeOpacity: 0.8,       //边线透明度，取值范围0 - 1。
        fillOpacity: 0.6,      //填充的透明度，取值范围0 - 1。
        strokeStyle: 'solid' //边线的样式，solid或dashed。
    };
    var points = [];
    for (var i in xpoints) {
        points.push(new BMap.Point(xpoints[i], ypoints[i]));
    }
    var marker = new BMap.Marker(new BMap.Point(xpoint, ypoint));
    map.addOverlay(marker);
    var polygon = new BMap.Polygon(points, styleOptions);
    map.addOverlay(polygon);
});
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 吴庆祥
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-03-10 10:08
// +----------------------------------------------------------------------
$(document).on("pageInit", "#dist_undeliver_verify_log_list", function(e, pageId, $page) {
    $(document).on('click', '.j-use-search', function() {
        $(".use-search-bar").addClass('open');
    });
    $(".use-search-bar").on('click', '.j-close-use-search', function() {
        $(".use-search-bar").removeClass('open');
    });

    init_list_scroll_bottom();

    $('.search').bind('click', function() {
        var pwd = $.trim($('input[name="coupon_pwd"]').val());
        if (pwd == '') {
            $.toast('请输入要搜索的券码');
            return;
        }
        pwd = pwd.replace(/\s/g,'');
        if (pwd.length!=12) {
            $.toast('请输入有效券码');
            return;
        }
        var param = {
            act: 'search_log',
            coupon_pwd: pwd
        };
        $.ajax({
            url: use_log,
            type:"GET",
            data: param,
            dataType:"JSON",
            success: function(html) {
                $('.j-ajaxlist').html($(html).find('.j-ajaxlist').html());
                init_list_scroll_bottom();
            },
            error: function(err) {
                console.log(err);
            }
        });
        return false;
    });
});
$(document).on("pageInit", "#dist_user_getpassword", function(e, pageId, $page)  {
    clear_input($('#phonenumer'),$('.j-phone-clear'));
    clear_input($('#sms_verify'),$('.j-verify-clear'));
    clear_input($('#password'),$('.j-password-clear'));

    $("#getpassword").click(function () {
        $("#ph_getpassword").submit();
    });

    $("#ph_getpassword").bind("submit",function(){
        var mobile = $.trim($(this).find("input[name='user_mobile']").val());
        var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
        var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());

        if(mobile=="")
        {
            $.toast("请输入手机号");
            return false;
        }
        if(user_pwd=="")
        {
            $.toast("请输入密码");
            return false;
        }
        if (user_pwd.length < 4) {
            $.toast('密码过短');
            return false;
        }
        if(sms_verify=="")
        {
            $.toast("请输入收到的验证码");
            return false;
        }

        var query = $(this).serialize();
        var ajax_url = $(this).attr("action");
        $.ajax({
            url:ajax_url,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status) {
                    // 先清理当前页的信息
                    $("input[name='user_mobile']").val('');
                    $("input[name='user_pwd']").val('');
                    $('input[name=sms_verify]').val('');
                    $('#btn').attr('lesstime', 0);

                    // 转弱提示跳转
                    $.toast(obj.info);
                    setTimeout(function() {
                        location.href = obj.jump;
                    }, 1500);
                } else {
                    $.toast(obj.info);
                }
            }
        });

        return false;
    });
});
$(document).on("pageInit", "#dist_user_login", function(e, pageId, $page) {
    function clear_name() {
        if ($('#account_name').val().length==0) {
            $(".j-name-clear").hide();
        } else {
            $('.j-name-clear').show();
        }
    }
    $("#account_name").bind('input propertychange', function() {
        clear_name();
    });
    $('.j-name-clear').click(function(){
        $('#account_name').val('');
        $(".j-name-clear").hide();
    });
    $("#login-btn").bind("click",function(){
        var account_name = $.trim($("input[name='account_name']").val());
        var account_password = $.trim($("input[name='account_password']").val());
        var form = $("form[name='user_login_form']");
        if(!account_name){
            $.toast("请填写账户名称");
            return false;
        }
        if(!account_password){
            $.toast("请输入密码");
            return false;
        }
        var query = $(form).serialize();
        var ajaxurl = $(form).attr("action");
        $.ajax({
            url:ajaxurl,
            data:query,
            type:"post",
            dataType:"json",
            success:function(data){
                if(data["status"]==1){
                    $.toast(data.info);
                    window.setTimeout(function(){
                        location.href = data.jump;
                    },1500);
                }else{
                    $.toast(data.info);
                    return false;
                }
            }
            ,error:function(){
                $.toast("服务器提交错误");
                return false;
            }
        });
        return false;
    });
});
$(document).on("pageInit", "#dist_withdrawal_bindbank", function(e, pageId, $page) {

	$("#btn").bind("click",function(){
		var phone=$("#phonenumer").val();
		if(phone==""){
			$.toast("请到PC端绑定手机");
		}
	});
	
	$("form[name='add_card']").bind("submit",function(){		
		var bank_name = $("form[name='add_card']").find("input[name='bank_name']").val();
		var bank_account = $("form[name='add_card']").find("input[name='bank_account']").val();
		var bank_user = $("form[name='add_card']").find("input[name='bank_user']").val();
		var sms_verify = $("form[name='add_card']").find("input[name='sms_verify']").val();		
		if($.trim(bank_name)=="")
		{
			$.toast("请输入开户行名称");
			return false;
		}
		if($.trim(bank_account)=="")
		{
			$.toast("请输入开户行账号");
			return false;
		}
		if($.trim(bank_user)=="")
		{
			$.toast("请输入开户人真实姓名");
			return false;
		}
		if($.trim(sms_verify)=="")
		{
			$.toast("请输入短信验证码");
			return false;
		}
		
		var ajax_url = $("form[name='add_card']").attr("action");
		var query = $("form[name='add_card']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$.toast(obj.info);	
					setTimeout(function(){
						location.href = obj.jump;
					},1500);
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)location.href = obj.jump;
					}
					
				}
				else{
					
				}
			}
		});		
		return false;
	});
});

$(document).on("pageInit", "#dist_withdrawal_form", function(e, pageId, $page) {
	$(".ui-textbox").val('');
	$("form[name='withdrawal_form']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>all_money){
			$.toast("提现超额");
			$(this).val(all_money);
		}
	});

	var load_page_count=0;
	
	$("form[name='withdrawal_form']").bind("submit",function(){		
		var money = $("form[name='withdrawal_form']").find("input[name='money']").val();
		var pwd = $("form[name='withdrawal_form']").find("input[name='pwd_verify']").val();
		if(is_bank=="")
		{	
			if(load_page_count==0){
				$.toast("请先绑定银行卡");
				setTimeout(function(){
					load_page($(".load_page"));
					setTimeout(function(){
						load_page_count=0;
					},100);
				},1500);
			}
			
			load_page_count++;
			return false;
		}
		
		if($.trim(pwd)=="")
		{
			$.toast("请输入登录密码");
			return false;
		}
		
		if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
		{
			$.toast("请输入正确的提现金额");
			return false;
		}
		
		var ajax_url = $("form[name='withdrawal_form']").attr("action");
		var query = $("form[name='withdrawal_form']").serialize();
		//console.log(query);
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$(".ui-textbox").val('');
					$.toast("提现申请成功，请等待管理员审核");
					if(obj.jump){
						setTimeout(function(){
							$.router.load(obj.jump, true);
							//location.href = obj.jump;
						},1500);
					}
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.jump){
							setTimeout(function(){
								$.router.load(obj.jump, true);
								//location.href = obj.jump;
							},1500);
						}
					}
					else
					{
						if(obj.jump)$.router.load(obj.jump, true);
					}
					
				}
			}
		});		
		return false;
	});
});

$(document).on("pageInit", "#event", function(e, pageId, $page) {

	loadScript(jia_url);
	/*倒计时*/

	var nowtime = parseInt($(".j-LeftTime").attr("nowtime"));
	var endtime = parseInt($(".j-LeftTime").attr("endtime"));
	var leftTime = (endtime - nowtime) / 1000;
	leftTimeAct();
	setInterval(leftTimeAct,1000);
	
	function leftTimeAct(){
		if(leftTime > 0)
		{
			var day  = parseInt(leftTime / 24 /3600);
			var hour = parseInt((leftTime % (24 *3600)) / 3600);
			var min  = parseInt((leftTime % 3600) / 60);
			var sec  = parseInt((leftTime % 3600) % 60);
			$(".j-LeftTime").find(".day").html(day);
			$(".j-LeftTime").find(".hour").html(hour);
			$(".j-LeftTime").find(".min").html(min);
			$(".j-LeftTime").find(".sec").html(sec);
			leftTime--;
		}
	}

	
	/*
	 *下拉导航收藏按钮
	 *如果已经收藏则执行以下操作，否则本阶段不执行操作
	 */
	$(".j-head-collect").on("click",function(){
		var is_del = $(this).attr("data-isdel");
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
				event_add_collect(id);
			}
		}
	});
	/*
	 *取消收藏按钮弹出后的取消
	 */
	$(".cancel-shoucan .j-cancel").click(function(){
		$(".cancel-shoucan").removeClass("z-open");
	});

	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		event_del_collect(id);
		$(".cancel-shoucan").removeClass("z-open");
	});
	
	$("#event_submit").unbind("click");
	$("#event_submit").bind("click",function(){
		$.confirm("你确认要报名吗？",function(){
			var url=$(this).attr("url");
			var query = new Object();
			query.event_id = id;
			query.act = "do_submit";
			$.ajax({
				url: url,
				data: query,
				dataType: "json",
				type: "post",
				success:function(data){
					if(data.status==1){
						$.toast(data.info);
						setTimeout(function(){
							window.location.href=data.jump;
						},2000);
					}else{
						$.toast(data.info);
					}
				}
			});
		});
	});

	$(".login_submit").unbind("click");
	$(".login_submit").bind("click",function () {
		if(is_login==0){
			if(app_index=="app"){
				App.login_sdk();
			}else{
				$.router.load(login_url, true);
			}
		}
	});
});

function event_add_collect(id){
	var query = new Object();
	query.id = id;
	query.act = "add_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(data){
			if(data.status==0 && data.user_login_status==0){
				$.toast("请先登录");
				setTimeout(function(){
					window.location.href=data.jump;
				},1000);
			}
			if(data.status==1){
				$("i.icon-collection").addClass("isCollection");
				$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+data.collect_count+"</em></div>");
				$.toast(data.info);
				$(".j-head-collect").attr("data-isdel",1);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			}
		},
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}

function event_del_collect(id){
	var query = new Object();
	query.id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(data){
			if(data.status==0 && data.user_login_status==0){
				$.alert(data.info,function(){
					window.location.href=data.jump;
				});
			}
			if(data.status==1){
				$.toast(data.info);	
				$("i.icon-collection").removeClass("isCollection");
				if(data.collect_count>0){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+data.collect_count+"</em></div>");
				}else{
					$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
				}
				$(".j-head-collect").attr("data-isdel",0);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			}
		},
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}

$(document).on("pageInit", "#events", function(e, pageId, $page) {
	init_listscroll(".j_ajaxlist_"+cate_id_1,".j_ajaxadd_"+cate_id_1);
	function tab_line() {
		var init_width=$(".m-events-tab a:first-child").width();
		var init_left=$(".m-events-tab a:first-child").offset().left;
		$(".events-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	function item_width() {
	}
	var tab_length =$(".m-events-tab li").length;
	if (tab_length<3) {
		$(".m-events-tab").hide();
	} else if(tab_length<6){
		$(".m-events-tab ul").addClass('flex-box');
		$(".m-events-tab ul li").addClass('flex-1');
	}
	else{
		var w_width=$(window).width();
		var item_width=w_width/5.5;
		$(".m-events-tab li").css('width', item_width);
		$(".m-events-tab ul").css('width', item_width*tab_length);
		$(".m-events-tab ul li").addClass('tab-item');
	}
	tab_line();
	$(".m-events-tab li:first-child").addClass('active');
	$(".m-events-tab a").click(function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		$(".m-events-tab a").removeClass('active');
		$(this).addClass('active');
		$(".m-events-list").hide();
		var item_width=$(this).width();
		var item_left=$(this).offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: item_width,
			left: item_left
		});
		var url=$(this).attr("data-src");
		var cate_id=$(this).attr("cate-id");
		$(".j_ajaxlist_"+cate_id).show();
		$(".content").scrollTop(1); 
		//alert($(".j_ajaxlist_"+cate_id).html());return false;
		//console.log($(".j_ajaxlist_"+cate_id).html());
		if($(".j_ajaxlist_"+cate_id).html()==null){
			//alert(111111);return false;
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			    },
			    error:function()
			    {
			    	
			    	$(".j_ajaxlist_"+cate_id).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			}
        }
	});
});
$(document).on("pageInit", "#user_getpassword", function(e, pageId, $page)  {
	clear_input($('#phonenumer'),$('.j-phone-clear'));
	clear_input($('#sms_verify'),$('.j-verify-clear'));
	clear_input($('#password'),$('.j-password-clear'));

	$("#getpassword").click(function () {
    	$("#ph_getpassword").submit();
    });
	
	
	$("#ph_getpassword").bind("submit",function(){
		var mobile = $.trim($(this).find("input[name='user_mobile']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());

		if(mobile=="")
		{
			$.toast("请输入手机号");
			return false;
		}
		if(user_pwd=="")
		{
			$.toast("请输入密码");
			return false;
		}
		if (user_pwd.length < 4) {
			$.toast('密码过短');
			return false;
		}
		if(sms_verify=="")
		{
			$.toast("请输入收到的验证码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status) {
					// 先清理当前页的信息
					$("input[name='user_mobile']").val('');
					$("input[name='user_pwd']").val('');
					$('input[name=sms_verify]').val('');
					$('#btn').attr('lesstime', 0);

					// 执行跳转
					// $.alert(obj.info,function(){
					// 	location.href = obj.jump;
					// });
					// 转弱提示跳转
					$.toast(obj.info);
					setTimeout(function() {
						location.href = obj.jump;
					}, 1500);		
				} else {
					$.toast(obj.info);
				}
			}
		});
		
		return false;
	});
});

$(document).on("pageInit", "#goods", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist",".j-ajaxadd");
	$(document).on("click",".dropdown-navlist",function() {
		screen_bar_close();
	});
	$(".m-screen-bar").on("click",".screen-link",function() {
		screen_bar_close();
		$(".screen-link").removeClass('active');
		$(this).addClass('active');
	});
	//筛选
	//标签
	$(".screen-item a").click(function(){
		$(".m-screen-list").removeClass('active');
		$(".arrow-up").hide();
		$(".arrow-down").show();
	});
	$(".m-screen-bar").on("click",".screen-item a",function(){
		$(".m-screen-list").find('.mask').removeClass('mask-active');
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".m-screen-list").removeClass('active');
	});
	//全部
	function screen_open() {
		$(".content").css('overflow', 'hidden');
		$(".m-screen-list").addClass('active');
	}
	function screen_close() {
		$(".content").css('overflow', 'auto');
		$(".m-screen-list").removeClass('active');
	}
	$(".m-screen-bar").on("click",".screen-all",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
		if ($(this).hasClass('active')) {
			$(".screen-brand").removeClass('active');
			$(".brand-screen").removeClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			screen_open();
			$("#all-goods").addClass('active');
		} else {
			screen_close();
			$("#all-goods").removeClass('active');
		}
	});
	$(".m-screen-list").on("click",".goods-type li",function() {
		$(".goods-type li").removeClass('active');
		$(this).addClass('active');
		$(".type-detail ul").hide();
		if ($(".goods-type li").hasClass('active')) {
			var type_id = $(this).attr('data-id');
			$(this).parent().parent().find(".type-detail ul").eq(type_id).show();
		}
	});
	$("#all-goods").on('click', '.type-detail li a', function() {
		$("#all-goods .type-detail li a").removeClass('active');
		$(this).addClass('active');
		$(".screen-all p").html($(this).find('p').html());
		$(".screen-all").attr('data-cid', $(this).parent().parent().attr("data-id"));
		$(".screen-link").removeClass('active');
	});
	$("#all-goods").on('click', '.type-detail li:first-child a', function() {
		var type_id = $(this).parent().parent().attr('data-id');
		$(".screen-all p").html($("#all-goods .goods-type li").eq(type_id).html());
	});
	//品牌
	$(".m-screen-bar").on("click",".screen-brand",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
		$(".screen-all").removeClass('active');
		$("#all-goods").removeClass('active');
		if ($(this).hasClass('active')) {
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			$(".m-screen-list").addClass('active');
			$(".brand-screen").addClass('active');
			$(".content").css('overflow', 'hidden');
			$(".m-screen-list").find('.mask').addClass('mask-active');
		} else {
			$(".m-screen-list").find('.mask').removeClass('mask-active');
			$(".brand-screen").removeClass('active');
			$(".content").css('overflow', 'auto');
			$(".m-screen-list").removeClass('active');
		}
	});
	$(".m-screen-list").on("click",".brand-screen li",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
	});
	$(".m-screen-list").on("click",".brand-reset",function() {
		$(".brand-screen li").removeClass('active');
	});
	$(".m-screen-list").on("click",".brand-comfirm",function() {
		var ids = '';
		$(".screen-brand").removeClass('active');
		$(".brand-screen").find('.active').each(function(){
		    ids += $(this).attr("data-id")+",";
		  });
		ids = ids.substring(0,ids.length-1);
		url = $(this).attr('date-href');
		$(this).attr('date-href', url);
		if(ids!=''){
			url +='&bid='+ids;
			$(this).attr('date-href', url);
		}
	});
	//价格
	$(".m-screen-bar").on("click",".screen-price",function() {
		$(this).addClass('active');
		if ($(this).find(".arrow-up").hasClass('active')) {  //降序
			$(this).find(".arrow-up").removeClass('active');
			$(this).find(".arrow-down").addClass('active');
		} else {  //升序
			$(this).find(".arrow-down").removeClass('active');
			$(this).find(".arrow-up").addClass('active');
		}
	});
	//销量
	$(".m-screen-bar").on("click",".screen-sales",function() {
		$(".arrow-up").removeClass('active');
		$(".arrow-down").removeClass('active');
	});
	//背景
	$(".m-screen-list").on("click",".m-screen-list .mask",function() {
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".screen-brand").removeClass('active');
		$(".content").css('overflow', 'auto');
		//$(".screen-item a").removeClass('active');
		$(".m-screen-list").find('.mask').removeClass('mask-active');
		$(".m-screen-list").removeClass('active');
		$(".brand-screen li").removeClass('active');
		$(".brand-screen").removeClass('active');
	});
	$(document).on("click",".j-listchoose",function() {
		var url=$(this).attr("date-href");
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-pp").html($(html).find(".j-pp").html());
				$(".j-xl").html($(html).find(".j-xl").html());
				$("#all-goods").html($(html).find("#all-goods").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					$(document).off('infinite', '.infinite-scroll-bottom');
					init_list_scroll_bottom();
				};
				if ($("#type-cube").css('display')=='none') {
					$(".m-goods-list ul").addClass('type-list').removeClass('type-cube');
				}
				if ($("#type-list").css('display')=='none') {
					$(".m-goods-list ul").removeClass('type-list').addClass('type-cube');
				}
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		screen_bar_close();
	});
//	//品牌搜索
//	var all_brand=new Array();
//	$.each(brand_list,function(i,obj){
//		if(obj.id > 0){
//			all_brand.push(obj.id);
//		}	
//	});
//
//	
//	$(".brand-screen .brand-comfirm").bind("click",function(){
//		var brand_arr=new Array();
//		$(".brand-screen .flex-1 li").each(function(){
//			if($(this).hasClass("active")){
//				var data_id = $(this).attr("data-id");
//				if(data_id==0){	
//					brand_arr = all_brand;
//					return false;
//				}else{
//					brand_arr.push(data_id);
//				}
//			}	
//		});
//		
//	});
//	
	
});
$(document).on("pageInit", "#help", function(e, pageId, $page) {
	var nav_num=$(".j-nav-item").length;
	var m_width=$(".m-nav-tab").width();
	if (nav_num>5) {
		$(".m-nav-tab .nav-tab").css('width',m_width*.22*nav_num);
	} else {
		$(".m-nav-tab .nav-tab").css('width', '100%');
	}
	if ($(".m-nav-tab").length!==0) {
		tab_line_init();
		nav_tab();
	}
	$(".j-nav-item").on('click', function() {
		$(".bar-list").removeClass('active');
		$(".bar-list").eq($(this).index()).addClass('active');
		/* Act on the event */
	});

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
			goods_id:'',
			goods_showURL:'',
			goodsTitle: '',
			goodsPrice: '',
			goods_URL: '',
			settingid: settingid,
			appGoods_type: '0',
		};
		xnOptions = JSON.stringify(xnOptionsObj);
		try {
			App.xnOpenSdk(xnOptions);
		} catch (e) {
			$.toast(e);
		}
	})
});
$(document).on("pageInit", "#help_search", function(e, pageId, $page) {
	clear_input($('.search-input'),$('.j-clear'));


	var origkey = '';
	$('.help-search-btn').bind('click', function() {
		var skey = $.trim($('.search-input').val());
		if (!skey) {
			$.toast('搜索关键字不能为空');
			return false;
		}
		if (skey == origkey) {
			return false;
		}
		origkey = skey;

		var query = {'keyword': skey};
		$.ajax({
			url: searchurl,
			data: query,
			dataType: "json",
			type: "post",
			success: function(data){
				if (data.status) {
					var list = data.list;
					var html = '';
					for (var key in list) {
						html += '<li class="b-line"><a href="'+list[key].wap_url+'" class="flex-box">'+
						'<p class="flex-1 bar-tit">'+list[key].title+'</p><div class="iconfont">&#xe607;</div></a></li>';
					}
					$('.bar-list').html(html);
				} else {
					$.toast(data.info);
				}
			}
		});
	});

});
$(document).on("pageInit","#idvalidate",function(){
	$("form[name='idvalidate_scanId']").unbind("submit");
	$("form[name='idvalidate_scanId']").bind("submit", function(event){
		var action = $(this).attr('action');
		var query = $(this).serialize();
		$.ajax({
			url:action,
			data:query,
			type:"POST",
			dataType:"json",
			success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					$.loadPage(location.href);
				}else{
					$.toast(obj.info);
				}
			}
		});
		return false;
	});
	$(".idvalidate_del").unbind("click");
	$(".idvalidate_del").bind("click", function(event){
		$.ajax({
			url:$(this).attr('data-url'),
			type:"POST",
			dataType:"json",
			success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					$.loadPage(location.href);
				}else{
					$.toast(obj.info);
				}
			}
		});
	});
});
$(document).on("pageInit", "#index", function(e, pageId, $page) {
	// 初始化回到头部
	

	headerScroll();/*导航条变化*/
	init_auto_load_data();
/*首页广告图轮播*/
var mySwiper = new Swiper('.j-index-banner', {
    speed: 400,
    spaceBetween: 0,
    pagination: '.swiper-pagination',
     autoplay: 2500
});
/*商家设置头部列表*/
var mySwiper = new Swiper('.j-sort_nav', {
    speed: 400,
    spaceBetween: 0
});
/*方维头条*/
var swiper = new Swiper('.j-headlines', {
        pagination: '',
        direction: 'vertical',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        mousewheelControl: true,
        autoplay: 2000,
        loop: true
    });
/*首页小轮播*/
var mySwiper = new Swiper('.j-index-lb', {
    speed: 400,
    spaceBetween: 0,
    autoplay: 2500
});
/*跑马灯*/
var swiper = new Swiper('.j-horse-lamp', {
    scrollbarHide: true,
    slidesPerView: 'auto',
    centeredSlides: false,
    grabCursor: true
});

if($.fn.cookie("cancel_geo")!=1){
	position();
}
});


 $(document).on("pageInit", "#location", function(e, pageId, $page)  {
	 init_list_scroll_bottom();
 });
/**
 * Created by Administrator on 2016/10/13.
 */
$(document).on("pageInit", "#user_login", function(e, pageId, $page)  {
	clear_input($('#phonenumer'),$('.j-phone-clear'));
	clear_input($('#sms_verify'),$('.j-verify-clear'));
	clear_input($('#user_key'),$('.j-name-clear'));
	clear_input($('#password'),$('.j-password-clear'));
	$(document).on('click','.open-popup', function () {
	var url=$(".open-popup").attr("data-src");
	  $.ajax({
	    url:url,
	    type:"POST",
	    success:function(html)
	    {
	      //console.log("成功");

	      $(".popup-agreement .protocol").html($(html).find(".content").html());
	      $(".popup-agreement .title").html($(html).find(".title").html());
	    },
	    error:function()
	    {

	    	$(".popup-agreement").html("网络被风吹走啦~");
	      //console.log("加载失败");
	    }
	  });
	});
   $(".tab-ways li").click(function () {
       var index=$(this).index();
       $(this).addClass("active").siblings("li").removeClass("active");
       $(this).removeClass("b-line").siblings("li").addClass("b-line");
       $(".phone-login").hide();
       $(".phone-login").eq(index).show();
   });


    
    var _cli=0;
    $(".eyes").click(function () {
        _cli++;

       if(_cli==1){
           $(".eyes-no").hide();
           $(".eyes-yes").show();
           $(".password").attr("type","text");
       }
        if(_cli==2){
            $(".eyes-no").show();
            $(".eyes-yes").hide();
            $(".password").attr("type","password");
        }
        if(_cli>=2){
            _cli=0;
        }
    });

    

    
    //var wait=60;
    
    
    
    
    //if($("#btn").attr("lesstime")>0){
    	//wait = $("#btn").attr("lesstime");
    //	time($("#btn"));
    //}
    
    var lock = 0; // 防止频繁提交

    //账号密码登录
    $("#com_login_box").bind("submit",function(){
		var user_key = $.trim($(this).find("input[name='user_key']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		if(user_key=="")
		{
			$.toast("请输入登录帐号");
			return false;
		}
		if(user_pwd=="")
		{
			$.toast("请输入密码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		if (!lock) {
			lock = 1;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj) {
					if(obj.status) {
						$("#prohibit").show();
						$.toast(obj.info);
						window.setTimeout(function(){ 
							if(obj.url!="")
								location.href = obj.url;
							else
								location.href = obj.jump;
							},1500); 			
					} else {
						$.toast(obj.info);
					}
					setTimeout(function() {
						lock = 0;
					}, 3000);
				}
			});
		}
		
		return false;
	});
    //手机快捷登录
    
    $("#ph_login_box").bind("submit",function(){
		
		var mobile = $.trim($(this).find("input[name='mobile']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());
		if(mobile=="")
		{
			$.toast("请输入手机号");
			return false;
		}
		if(sms_verify=="")
		{
			$.toast("请输入收到的验证码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		if (!lock) {
			lock = 1;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj){
					if(obj.status) {
						$("#prohibit").show();
						$.toast(obj.info);
						window.setTimeout(function(){
							location.href = obj.jump;
							},1500);				
					} else {
						$.toast(obj.info);
					}
					setTimeout(function() {
						lock = 0;
					}, 3000);
				}
			});
		}
		
		return false;
	});
    
});

$(document).on("pageInit", "#login_out", function(e, pageId, $page)  {

    //退出登录
	$(".btn-con").click(function(){
		var cookarr=$.fn.cookie('cookobj');
		$.fn.cookie('cookobj',cookarr,{ expires: -1 });
		if(app_index=='app'){
			App.logout();
			return false;
		}
		var exit_url=$(this).attr("data-url");
		var query = new Object();
		query.act='loginout';
		$.ajax({
			url:exit_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.toast(obj.info);
					setTimeout(function(){
						window.location.href=obj.jump;
					},1500);
				}
				else
				{
					$.toast(obj.info);
				}
			}
		});
	});

});
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else {
		var a = factory();
		for(var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
	}
})(this, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	__webpack_require__(7);
	__webpack_require__(8);

	module.exports = __webpack_require__(9);


	/**
	 *
	 * 　　　┏┓　　　┏┓
	 * 　　┏┛┻━━━┛┻┓
	 * 　　┃　　　　　　　┃
	 * 　　┃　　　━　　　┃
	 * 　　┃　┳┛　┗┳　┃
	 * 　　┃　　　　　　　┃
	 * 　　┃　　　┻　　　┃
	 * 　　┃　　　　　　　┃
	 * 　　┗━┓　　　┏━┛Code is far away from bug with the animal protecting
	 * 　　　　┃　　　┃    神兽保佑,代码无bug
	 * 　　　　┃　　　┃
	 * 　　　　┃　　　┗━━━┓
	 * 　　　　┃　　　　　 ┣┓
	 * 　　　　┃　　　　 ┏┛
	 * 　　　　┗┓┓┏━┳┓┏┛
	 * 　　　　　┃┫┫　┃┫┫
	 * 　　　　　┗┻┛　┗┻┛
	 *
	 */


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(setImmediate) {(function (root) {

	    // Use polyfill for setImmediate for performance gains
	    var asap = (typeof setImmediate === 'function' && setImmediate) ||
	        function (fn) {
	            setTimeout(fn, 1);
	        };

	    // Polyfill for Function.prototype.bind
	    function bind (fn, thisArg) {
	        return function () {
	            fn.apply(thisArg, arguments);
	        }
	    }

	    var isArray = Array.isArray || function (value) {
	            return Object.prototype.toString.call(value) === "[object Array]"
	        };

	    function Promise (fn) {
	        if (typeof this !== 'object') throw new TypeError('Promises must be constructed via new');
	        if (typeof fn !== 'function') throw new TypeError('not a function');
	        this._state     = null;
	        this._value     = null;
	        this._deferreds = []

	        doResolve(fn, bind(resolve, this), bind(reject, this))
	    }

	    function handle (deferred) {
	        var me = this;
	        if (this._state === null) {
	            this._deferreds.push(deferred);
	            return
	        }
	        asap(function () {
	            var cb = me._state ? deferred.onFulfilled : deferred.onRejected
	            if (cb === null) {
	                (me._state ? deferred.resolve : deferred.reject)(me._value);
	                return;
	            }
	            var ret;
	            try {
	                ret = cb(me._value);
	            }
	            catch (e) {
	                deferred.reject(e);
	                return;
	            }
	            deferred.resolve(ret);
	        })
	    }

	    function resolve (newValue) {
	        try { //Promise Resolution Procedure: https://github.com/promises-aplus/promises-spec#the-promise-resolution-procedure
	            if (newValue === this) throw new TypeError('A promise cannot be resolved with itself.');
	            if (newValue && (typeof newValue === 'object' || typeof newValue === 'function')) {
	                var then = newValue.then;
	                if (typeof then === 'function') {
	                    doResolve(bind(then, newValue), bind(resolve, this), bind(reject, this));
	                    return;
	                }
	            }
	            this._state = true;
	            this._value = newValue;
	            finale.call(this);
	        } catch (e) {
	            reject.call(this, e);
	        }
	    }

	    function reject (newValue) {
	        this._state = false;
	        this._value = newValue;
	        finale.call(this);
	    }

	    function finale () {
	        for (var i = 0, len = this._deferreds.length; i < len; i++) {
	            handle.call(this, this._deferreds[i]);
	        }
	        this._deferreds = null;
	    }

	    function Handler (onFulfilled, onRejected, resolve, reject) {
	        this.onFulfilled = typeof onFulfilled === 'function' ? onFulfilled : null;
	        this.onRejected  = typeof onRejected === 'function' ? onRejected : null;
	        this.resolve     = resolve;
	        this.reject      = reject;
	    }

	    /**
	     * Take a potentially misbehaving resolver function and make sure
	     * onFulfilled and onRejected are only called once.
	     *
	     * Makes no guarantees about asynchrony.
	     */
	    function doResolve (fn, onFulfilled, onRejected) {
	        var done = false;
	        try {
	            fn(function (value) {
	                if (done) return;
	                done = true;
	                onFulfilled(value);
	            }, function (reason) {
	                if (done) return;
	                done = true;
	                onRejected(reason);
	            })
	        } catch (ex) {
	            if (done) return;
	            done = true;
	            onRejected(ex);
	        }
	    }

	    Promise.prototype['catch'] = function (onRejected) {
	        return this.then(null, onRejected);
	    };

	    Promise.prototype.then = function (onFulfilled, onRejected) {
	        var me = this;
	        return new Promise(function (resolve, reject) {
	            handle.call(me, new Handler(onFulfilled, onRejected, resolve, reject));
	        })
	    };

	    Promise.all = function () {
	        var args = Array.prototype.slice.call(arguments.length === 1 && isArray(arguments[0]) ? arguments[0] : arguments);

	        return new Promise(function (resolve, reject) {
	            if (args.length === 0) return resolve([]);
	            var remaining = args.length;

	            function res (i, val) {
	                try {
	                    if (val && (typeof val === 'object' || typeof val === 'function')) {
	                        var then = val.then;
	                        if (typeof then === 'function') {
	                            then.call(val, function (val) {
	                                res(i, val)
	                            }, reject);
	                            return;
	                        }
	                    }
	                    args[i] = val;
	                    if (--remaining === 0) {
	                        resolve(args);
	                    }
	                } catch (ex) {
	                    reject(ex);
	                }
	            }

	            for (var i = 0; i < args.length; i++) {
	                res(i, args[i]);
	            }
	        });
	    };

	    Promise.resolve = function (value) {
	        if (value && typeof value === 'object' && value.constructor === Promise) {
	            return value;
	        }

	        return new Promise(function (resolve) {
	            resolve(value);
	        });
	    };

	    Promise.reject = function (value) {
	        return new Promise(function (resolve, reject) {
	            reject(value);
	        });
	    };

	    Promise.race = function (values) {
	        return new Promise(function (resolve, reject) {
	            for (var i = 0, len = values.length; i < len; i++) {
	                values[i].then(resolve, reject);
	            }
	        });
	    };

	    /**
	     * Set the immediate function to execute callbacks
	     * @param fn {function} Function to execute
	     * @private
	     */
	    Promise._setImmediateFn = function _setImmediateFn (fn) {
	        asap = fn;
	    };


	    Promise.prototype.always = function (callback) {
	        var constructor = this.constructor;

	        return this.then(function (value) {
	            return constructor.resolve(callback()).then(function () {
	                return value;
	            });
	        }, function (reason) {
	            return constructor.resolve(callback()).then(function () {
	                throw reason;
	            });
	        });
	    };

	    if (typeof module !== 'undefined' && module.exports) {
	        module.exports = Promise;
	    } else if (!root.Promise) {
	        root.Promise = Promise;
	    }

	})(this);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(2).setImmediate))

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(setImmediate, clearImmediate) {var apply = Function.prototype.apply;

	// DOM APIs, for completeness

	exports.setTimeout = function() {
	  return new Timeout(apply.call(setTimeout, window, arguments), clearTimeout);
	};
	exports.setInterval = function() {
	  return new Timeout(apply.call(setInterval, window, arguments), clearInterval);
	};
	exports.clearTimeout =
	exports.clearInterval = function(timeout) {
	  if (timeout) {
	    timeout.close();
	  }
	};

	function Timeout(id, clearFn) {
	  this._id = id;
	  this._clearFn = clearFn;
	}
	Timeout.prototype.unref = Timeout.prototype.ref = function() {};
	Timeout.prototype.close = function() {
	  this._clearFn.call(window, this._id);
	};

	// Does not start the time, just sets up the members needed.
	exports.enroll = function(item, msecs) {
	  clearTimeout(item._idleTimeoutId);
	  item._idleTimeout = msecs;
	};

	exports.unenroll = function(item) {
	  clearTimeout(item._idleTimeoutId);
	  item._idleTimeout = -1;
	};

	exports._unrefActive = exports.active = function(item) {
	  clearTimeout(item._idleTimeoutId);

	  var msecs = item._idleTimeout;
	  if (msecs >= 0) {
	    item._idleTimeoutId = setTimeout(function onTimeout() {
	      if (item._onTimeout)
	        item._onTimeout();
	    }, msecs);
	  }
	};

	// setimmediate attaches itself to the global object
	__webpack_require__(3);
	exports.setImmediate = setImmediate;
	exports.clearImmediate = clearImmediate;

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(2).setImmediate, __webpack_require__(2).clearImmediate))

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global, process) {(function (global, undefined) {
	    "use strict";

	    if (global.setImmediate) {
	        return;
	    }

	    var nextHandle = 1; // Spec says greater than zero
	    var tasksByHandle = {};
	    var currentlyRunningATask = false;
	    var doc = global.document;
	    var registerImmediate;

	    function setImmediate(callback) {
	      // Callback can either be a function or a string
	      if (typeof callback !== "function") {
	        callback = new Function("" + callback);
	      }
	      // Copy function arguments
	      var args = new Array(arguments.length - 1);
	      for (var i = 0; i < args.length; i++) {
	          args[i] = arguments[i + 1];
	      }
	      // Store and register the task
	      var task = { callback: callback, args: args };
	      tasksByHandle[nextHandle] = task;
	      registerImmediate(nextHandle);
	      return nextHandle++;
	    }

	    function clearImmediate(handle) {
	        delete tasksByHandle[handle];
	    }

	    function run(task) {
	        var callback = task.callback;
	        var args = task.args;
	        switch (args.length) {
	        case 0:
	            callback();
	            break;
	        case 1:
	            callback(args[0]);
	            break;
	        case 2:
	            callback(args[0], args[1]);
	            break;
	        case 3:
	            callback(args[0], args[1], args[2]);
	            break;
	        default:
	            callback.apply(undefined, args);
	            break;
	        }
	    }

	    function runIfPresent(handle) {
	        // From the spec: "Wait until any invocations of this algorithm started before this one have completed."
	        // So if we're currently running a task, we'll need to delay this invocation.
	        if (currentlyRunningATask) {
	            // Delay by doing a setTimeout. setImmediate was tried instead, but in Firefox 7 it generated a
	            // "too much recursion" error.
	            setTimeout(runIfPresent, 0, handle);
	        } else {
	            var task = tasksByHandle[handle];
	            if (task) {
	                currentlyRunningATask = true;
	                try {
	                    run(task);
	                } finally {
	                    clearImmediate(handle);
	                    currentlyRunningATask = false;
	                }
	            }
	        }
	    }

	    function installNextTickImplementation() {
	        registerImmediate = function(handle) {
	            process.nextTick(function () { runIfPresent(handle); });
	        };
	    }

	    function canUsePostMessage() {
	        // The test against `importScripts` prevents this implementation from being installed inside a web worker,
	        // where `global.postMessage` means something completely different and can't be used for this purpose.
	        if (global.postMessage && !global.importScripts) {
	            var postMessageIsAsynchronous = true;
	            var oldOnMessage = global.onmessage;
	            global.onmessage = function() {
	                postMessageIsAsynchronous = false;
	            };
	            global.postMessage("", "*");
	            global.onmessage = oldOnMessage;
	            return postMessageIsAsynchronous;
	        }
	    }

	    function installPostMessageImplementation() {
	        // Installs an event handler on `global` for the `message` event: see
	        // * https://developer.mozilla.org/en/DOM/window.postMessage
	        // * http://www.whatwg.org/specs/web-apps/current-work/multipage/comms.html#crossDocumentMessages

	        var messagePrefix = "setImmediate$" + Math.random() + "$";
	        var onGlobalMessage = function(event) {
	            if (event.source === global &&
	                typeof event.data === "string" &&
	                event.data.indexOf(messagePrefix) === 0) {
	                runIfPresent(+event.data.slice(messagePrefix.length));
	            }
	        };

	        if (global.addEventListener) {
	            global.addEventListener("message", onGlobalMessage, false);
	        } else {
	            global.attachEvent("onmessage", onGlobalMessage);
	        }

	        registerImmediate = function(handle) {
	            global.postMessage(messagePrefix + handle, "*");
	        };
	    }

	    function installMessageChannelImplementation() {
	        var channel = new MessageChannel();
	        channel.port1.onmessage = function(event) {
	            var handle = event.data;
	            runIfPresent(handle);
	        };

	        registerImmediate = function(handle) {
	            channel.port2.postMessage(handle);
	        };
	    }

	    function installReadyStateChangeImplementation() {
	        var html = doc.documentElement;
	        registerImmediate = function(handle) {
	            // Create a <script> element; its readystatechange event will be fired asynchronously once it is inserted
	            // into the document. Do so, thus queuing up the task. Remember to clean up once it's been called.
	            var script = doc.createElement("script");
	            script.onreadystatechange = function () {
	                runIfPresent(handle);
	                script.onreadystatechange = null;
	                html.removeChild(script);
	                script = null;
	            };
	            html.appendChild(script);
	        };
	    }

	    function installSetTimeoutImplementation() {
	        registerImmediate = function(handle) {
	            setTimeout(runIfPresent, 0, handle);
	        };
	    }

	    // If supported, we should attach to the prototype of global, since that is where setTimeout et al. live.
	    var attachTo = Object.getPrototypeOf && Object.getPrototypeOf(global);
	    attachTo = attachTo && attachTo.setTimeout ? attachTo : global;

	    // Don't get fooled by e.g. browserify environments.
	    if ({}.toString.call(global.process) === "[object process]") {
	        // For Node.js before 0.9
	        installNextTickImplementation();

	    } else if (canUsePostMessage()) {
	        // For non-IE10 modern browsers
	        installPostMessageImplementation();

	    } else if (global.MessageChannel) {
	        // For web workers, where supported
	        installMessageChannelImplementation();

	    } else if (doc && "onreadystatechange" in doc.createElement("script")) {
	        // For IE 6–8
	        installReadyStateChangeImplementation();

	    } else {
	        // For older browsers
	        installSetTimeoutImplementation();
	    }

	    attachTo.setImmediate = setImmediate;
	    attachTo.clearImmediate = clearImmediate;
	}(typeof self === "undefined" ? typeof global === "undefined" ? this : global : self));

	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }()), __webpack_require__(4)))

/***/ },
/* 4 */
/***/ function(module, exports) {

	// shim for using process in browser
	var process = module.exports = {};

	// cached from whatever global is present so that test runners that stub it
	// don't break things.  But we need to wrap it in a try catch in case it is
	// wrapped in strict mode code which doesn't define any globals.  It's inside a
	// function because try/catches deoptimize in certain engines.

	var cachedSetTimeout;
	var cachedClearTimeout;

	function defaultSetTimout() {
	    throw new Error('setTimeout has not been defined');
	}
	function defaultClearTimeout () {
	    throw new Error('clearTimeout has not been defined');
	}
	(function () {
	    try {
	        if (typeof setTimeout === 'function') {
	            cachedSetTimeout = setTimeout;
	        } else {
	            cachedSetTimeout = defaultSetTimout;
	        }
	    } catch (e) {
	        cachedSetTimeout = defaultSetTimout;
	    }
	    try {
	        if (typeof clearTimeout === 'function') {
	            cachedClearTimeout = clearTimeout;
	        } else {
	            cachedClearTimeout = defaultClearTimeout;
	        }
	    } catch (e) {
	        cachedClearTimeout = defaultClearTimeout;
	    }
	} ())
	function runTimeout(fun) {
	    if (cachedSetTimeout === setTimeout) {
	        //normal enviroments in sane situations
	        return setTimeout(fun, 0);
	    }
	    // if setTimeout wasn't available but was latter defined
	    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
	        cachedSetTimeout = setTimeout;
	        return setTimeout(fun, 0);
	    }
	    try {
	        // when when somebody has screwed with setTimeout but no I.E. maddness
	        return cachedSetTimeout(fun, 0);
	    } catch(e){
	        try {
	            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
	            return cachedSetTimeout.call(null, fun, 0);
	        } catch(e){
	            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
	            return cachedSetTimeout.call(this, fun, 0);
	        }
	    }


	}
	function runClearTimeout(marker) {
	    if (cachedClearTimeout === clearTimeout) {
	        //normal enviroments in sane situations
	        return clearTimeout(marker);
	    }
	    // if clearTimeout wasn't available but was latter defined
	    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
	        cachedClearTimeout = clearTimeout;
	        return clearTimeout(marker);
	    }
	    try {
	        // when when somebody has screwed with setTimeout but no I.E. maddness
	        return cachedClearTimeout(marker);
	    } catch (e){
	        try {
	            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
	            return cachedClearTimeout.call(null, marker);
	        } catch (e){
	            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
	            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
	            return cachedClearTimeout.call(this, marker);
	        }
	    }



	}
	var queue = [];
	var draining = false;
	var currentQueue;
	var queueIndex = -1;

	function cleanUpNextTick() {
	    if (!draining || !currentQueue) {
	        return;
	    }
	    draining = false;
	    if (currentQueue.length) {
	        queue = currentQueue.concat(queue);
	    } else {
	        queueIndex = -1;
	    }
	    if (queue.length) {
	        drainQueue();
	    }
	}

	function drainQueue() {
	    if (draining) {
	        return;
	    }
	    var timeout = runTimeout(cleanUpNextTick);
	    draining = true;

	    var len = queue.length;
	    while(len) {
	        currentQueue = queue;
	        queue = [];
	        while (++queueIndex < len) {
	            if (currentQueue) {
	                currentQueue[queueIndex].run();
	            }
	        }
	        queueIndex = -1;
	        len = queue.length;
	    }
	    currentQueue = null;
	    draining = false;
	    runClearTimeout(timeout);
	}

	process.nextTick = function (fun) {
	    var args = new Array(arguments.length - 1);
	    if (arguments.length > 1) {
	        for (var i = 1; i < arguments.length; i++) {
	            args[i - 1] = arguments[i];
	        }
	    }
	    queue.push(new Item(fun, args));
	    if (queue.length === 1 && !draining) {
	        runTimeout(drainQueue);
	    }
	};

	// v8 likes predictible objects
	function Item(fun, array) {
	    this.fun = fun;
	    this.array = array;
	}
	Item.prototype.run = function () {
	    this.fun.apply(null, this.array);
	};
	process.title = 'browser';
	process.browser = true;
	process.env = {};
	process.argv = [];
	process.version = ''; // empty string to avoid regexp issues
	process.versions = {};

	function noop() {}

	process.on = noop;
	process.addListener = noop;
	process.once = noop;
	process.off = noop;
	process.removeListener = noop;
	process.removeAllListeners = noop;
	process.emit = noop;

	process.binding = function (name) {
	    throw new Error('process.binding is not supported');
	};

	process.cwd = function () { return '/' };
	process.chdir = function (dir) {
	    throw new Error('process.chdir is not supported');
	};
	process.umask = function() { return 0; };


/***/ },
/* 5 */
/***/ function(module, exports) {

	//@source https://xts.so/demo/compress/index.html

	// 早期版本的浏览器需要用BlobBuilder来构造Blob，创建一个通用构造器来兼容早期版本
	var BlobConstructor = ((function () {
	    try {
	        new Blob();
	        return true;
	    } catch (e) {
	        return false;
	    }
	})()) ? window.Blob : function (parts, opts) {
	    var bb = new (
	        window.BlobBuilder
	        || window.WebKitBlobBuilder
	        || window.MSBlobBuilder
	        || window.MozBlobBuilder
	    );
	    parts.forEach(function (p) {
	        bb.append(p);
	    });

	    return bb.getBlob(opts ? opts.type : undefined);
	};

	// Android上的AppleWebKit 534以前的内核存在一个Bug，
	// 导致FormData加入一个Blob对象后，上传的文件是0字节
	function hasFormDataBug () {
	    var bCheck = ~navigator.userAgent.indexOf('Android')
	        && ~navigator.vendor.indexOf('Google')
	        && !~navigator.userAgent.indexOf('Chrome');

	    // QQ X5浏览器也有这个BUG
	    return bCheck && navigator.userAgent.match(/AppleWebKit\/(\d+)/).pop() <= 534 || /MQQBrowser/g.test(navigator.userAgent);
	}
	var FormDataShim=(function(){
	    var formDataShimNums = 0;
	    function FormDataShim () {
	        var
	        // Store a reference to this
	        o        = this,
	    
	        // Data to be sent
	        parts = [],
	    
	        // Boundary parameter for separating the multipart values
	        boundary = Array(21).join('-') + (+new Date() * (1e16 * Math.random())).toString(36),
	    
	        // Store the current XHR send method so we can safely override it
	        oldSend  = XMLHttpRequest.prototype.send;
	        this.getParts = function () {
	            return parts.toString();
	        };
	        this.append   = function (name, value, filename) {
	            parts.push('--' + boundary + '\r\nContent-Disposition: form-data; name="' + name + '"');
	    
	            if (value instanceof Blob) {
	                parts.push('; filename="' + (filename || 'blob') + '"\r\nContent-Type: ' + value.type + '\r\n\r\n');
	                parts.push(value);
	            }
	            else {
	                parts.push('\r\n\r\n' + value);
	            }
	            parts.push('\r\n');
	        };
	    
	        formDataShimNums++;
	        XMLHttpRequest.prototype.send = function (val) {
	            var fr,
	                data,
	                oXHR = this;
	    
	            if (val === o) {
	                // Append the final boundary string
	                parts.push('--' + boundary + '--\r\n');
	                // Create the blob
	                data = new BlobConstructor(parts);
	    
	                // Set up and read the blob into an array to be sent
	                fr         = new FileReader();
	                fr.onload  = function () {
	                    oldSend.call(oXHR, fr.result);
	                };
	                fr.onerror = function (err) {
	                    throw err;
	                };
	                fr.readAsArrayBuffer(data);
	    
	                // Set the multipart content type and boudary
	                this.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
	                formDataShimNums--;
	                if(formDataShimNums == 0){
	                    XMLHttpRequest.prototype.send = oldSend;
	                }
	            }
	            else {
	                oldSend.call(this, val);
	            }
	        };
	    };
	    FormDataShim.prototype = Object.create(FormData.prototype);
	    return FormDataShim;
	})();


	module.exports = {
	    Blob    : BlobConstructor,
	    FormData: hasFormDataBug() ? FormDataShim : FormData
	};


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* exif */
	(function () {

	    var debug = false;

	    var root = this;

	    var EXIF = function (obj) {
	        if (obj instanceof EXIF) return obj;
	        if (!(this instanceof EXIF)) return new EXIF(obj);
	        this.EXIFwrapped = obj;
	    };

	    if (true) {
	        if (typeof module !== 'undefined' && module.exports) {
	            exports = module.exports = EXIF;
	        }
	        exports.EXIF = EXIF;
	    } else {
	        root.EXIF = EXIF;
	    }

	    var ExifTags = EXIF.Tags = {

	        // version tags
	        0x9000: "ExifVersion",             // EXIF version
	        0xA000: "FlashpixVersion",         // Flashpix format version

	        // colorspace tags
	        0xA001: "ColorSpace",              // Color space information tag

	        // image configuration
	        0xA002: "PixelXDimension",         // Valid width of meaningful image
	        0xA003: "PixelYDimension",         // Valid height of meaningful image
	        0x9101: "ComponentsConfiguration", // Information about channels
	        0x9102: "CompressedBitsPerPixel",  // Compressed bits per pixel

	        // user information
	        0x927C: "MakerNote",               // Any desired information written by the manufacturer
	        0x9286: "UserComment",             // Comments by user

	        // related file
	        0xA004: "RelatedSoundFile",        // Name of related sound file

	        // date and time
	        0x9003: "DateTimeOriginal",        // Date and time when the original image was generated
	        0x9004: "DateTimeDigitized",       // Date and time when the image was stored digitally
	        0x9290: "SubsecTime",              // Fractions of seconds for DateTime
	        0x9291: "SubsecTimeOriginal",      // Fractions of seconds for DateTimeOriginal
	        0x9292: "SubsecTimeDigitized",     // Fractions of seconds for DateTimeDigitized

	        // picture-taking conditions
	        0x829A: "ExposureTime",            // Exposure time (in seconds)
	        0x829D: "FNumber",                 // F number
	        0x8822: "ExposureProgram",         // Exposure program
	        0x8824: "SpectralSensitivity",     // Spectral sensitivity
	        0x8827: "ISOSpeedRatings",         // ISO speed rating
	        0x8828: "OECF",                    // Optoelectric conversion factor
	        0x9201: "ShutterSpeedValue",       // Shutter speed
	        0x9202: "ApertureValue",           // Lens aperture
	        0x9203: "BrightnessValue",         // Value of brightness
	        0x9204: "ExposureBias",            // Exposure bias
	        0x9205: "MaxApertureValue",        // Smallest F number of lens
	        0x9206: "SubjectDistance",         // Distance to subject in meters
	        0x9207: "MeteringMode",            // Metering mode
	        0x9208: "LightSource",             // Kind of light source
	        0x9209: "Flash",                   // Flash status
	        0x9214: "SubjectArea",             // Location and area of main subject
	        0x920A: "FocalLength",             // Focal length of the lens in mm
	        0xA20B: "FlashEnergy",             // Strobe energy in BCPS
	        0xA20C: "SpatialFrequencyResponse",    //
	        0xA20E: "FocalPlaneXResolution",   // Number of pixels in width direction per FocalPlaneResolutionUnit
	        0xA20F: "FocalPlaneYResolution",   // Number of pixels in height direction per FocalPlaneResolutionUnit
	        0xA210: "FocalPlaneResolutionUnit",    // Unit for measuring FocalPlaneXResolution and FocalPlaneYResolution
	        0xA214: "SubjectLocation",         // Location of subject in image
	        0xA215: "ExposureIndex",           // Exposure index selected on camera
	        0xA217: "SensingMethod",           // Image sensor type
	        0xA300: "FileSource",              // Image source (3 == DSC)
	        0xA301: "SceneType",               // Scene type (1 == directly photographed)
	        0xA302: "CFAPattern",              // Color filter array geometric pattern
	        0xA401: "CustomRendered",          // Special processing
	        0xA402: "ExposureMode",            // Exposure mode
	        0xA403: "WhiteBalance",            // 1 = auto white balance, 2 = manual
	        0xA404: "DigitalZoomRation",       // Digital zoom ratio
	        0xA405: "FocalLengthIn35mmFilm",   // Equivalent foacl length assuming 35mm film camera (in mm)
	        0xA406: "SceneCaptureType",        // Type of scene
	        0xA407: "GainControl",             // Degree of overall image gain adjustment
	        0xA408: "Contrast",                // Direction of contrast processing applied by camera
	        0xA409: "Saturation",              // Direction of saturation processing applied by camera
	        0xA40A: "Sharpness",               // Direction of sharpness processing applied by camera
	        0xA40B: "DeviceSettingDescription",    //
	        0xA40C: "SubjectDistanceRange",    // Distance to subject

	        // other tags
	        0xA005: "InteroperabilityIFDPointer",
	        0xA420: "ImageUniqueID"            // Identifier assigned uniquely to each image
	    };

	    var TiffTags = EXIF.TiffTags = {
	        0x0100: "ImageWidth",
	        0x0101: "ImageHeight",
	        0x8769: "ExifIFDPointer",
	        0x8825: "GPSInfoIFDPointer",
	        0xA005: "InteroperabilityIFDPointer",
	        0x0102: "BitsPerSample",
	        0x0103: "Compression",
	        0x0106: "PhotometricInterpretation",
	        0x0112: "Orientation",
	        0x0115: "SamplesPerPixel",
	        0x011C: "PlanarConfiguration",
	        0x0212: "YCbCrSubSampling",
	        0x0213: "YCbCrPositioning",
	        0x011A: "XResolution",
	        0x011B: "YResolution",
	        0x0128: "ResolutionUnit",
	        0x0111: "StripOffsets",
	        0x0116: "RowsPerStrip",
	        0x0117: "StripByteCounts",
	        0x0201: "JPEGInterchangeFormat",
	        0x0202: "JPEGInterchangeFormatLength",
	        0x012D: "TransferFunction",
	        0x013E: "WhitePoint",
	        0x013F: "PrimaryChromaticities",
	        0x0211: "YCbCrCoefficients",
	        0x0214: "ReferenceBlackWhite",
	        0x0132: "DateTime",
	        0x010E: "ImageDescription",
	        0x010F: "Make",
	        0x0110: "Model",
	        0x0131: "Software",
	        0x013B: "Artist",
	        0x8298: "Copyright"
	    };

	    var GPSTags = EXIF.GPSTags = {
	        0x0000: "GPSVersionID",
	        0x0001: "GPSLatitudeRef",
	        0x0002: "GPSLatitude",
	        0x0003: "GPSLongitudeRef",
	        0x0004: "GPSLongitude",
	        0x0005: "GPSAltitudeRef",
	        0x0006: "GPSAltitude",
	        0x0007: "GPSTimeStamp",
	        0x0008: "GPSSatellites",
	        0x0009: "GPSStatus",
	        0x000A: "GPSMeasureMode",
	        0x000B: "GPSDOP",
	        0x000C: "GPSSpeedRef",
	        0x000D: "GPSSpeed",
	        0x000E: "GPSTrackRef",
	        0x000F: "GPSTrack",
	        0x0010: "GPSImgDirectionRef",
	        0x0011: "GPSImgDirection",
	        0x0012: "GPSMapDatum",
	        0x0013: "GPSDestLatitudeRef",
	        0x0014: "GPSDestLatitude",
	        0x0015: "GPSDestLongitudeRef",
	        0x0016: "GPSDestLongitude",
	        0x0017: "GPSDestBearingRef",
	        0x0018: "GPSDestBearing",
	        0x0019: "GPSDestDistanceRef",
	        0x001A: "GPSDestDistance",
	        0x001B: "GPSProcessingMethod",
	        0x001C: "GPSAreaInformation",
	        0x001D: "GPSDateStamp",
	        0x001E: "GPSDifferential"
	    };

	    var StringValues = EXIF.StringValues = {
	        ExposureProgram     : {
	            0: "Not defined",
	            1: "Manual",
	            2: "Normal program",
	            3: "Aperture priority",
	            4: "Shutter priority",
	            5: "Creative program",
	            6: "Action program",
	            7: "Portrait mode",
	            8: "Landscape mode"
	        },
	        MeteringMode        : {
	            0  : "Unknown",
	            1  : "Average",
	            2  : "CenterWeightedAverage",
	            3  : "Spot",
	            4  : "MultiSpot",
	            5  : "Pattern",
	            6  : "Partial",
	            255: "Other"
	        },
	        LightSource         : {
	            0  : "Unknown",
	            1  : "Daylight",
	            2  : "Fluorescent",
	            3  : "Tungsten (incandescent light)",
	            4  : "Flash",
	            9  : "Fine weather",
	            10 : "Cloudy weather",
	            11 : "Shade",
	            12 : "Daylight fluorescent (D 5700 - 7100K)",
	            13 : "Day white fluorescent (N 4600 - 5400K)",
	            14 : "Cool white fluorescent (W 3900 - 4500K)",
	            15 : "White fluorescent (WW 3200 - 3700K)",
	            17 : "Standard light A",
	            18 : "Standard light B",
	            19 : "Standard light C",
	            20 : "D55",
	            21 : "D65",
	            22 : "D75",
	            23 : "D50",
	            24 : "ISO studio tungsten",
	            255: "Other"
	        },
	        Flash               : {
	            0x0000: "Flash did not fire",
	            0x0001: "Flash fired",
	            0x0005: "Strobe return light not detected",
	            0x0007: "Strobe return light detected",
	            0x0009: "Flash fired, compulsory flash mode",
	            0x000D: "Flash fired, compulsory flash mode, return light not detected",
	            0x000F: "Flash fired, compulsory flash mode, return light detected",
	            0x0010: "Flash did not fire, compulsory flash mode",
	            0x0018: "Flash did not fire, auto mode",
	            0x0019: "Flash fired, auto mode",
	            0x001D: "Flash fired, auto mode, return light not detected",
	            0x001F: "Flash fired, auto mode, return light detected",
	            0x0020: "No flash function",
	            0x0041: "Flash fired, red-eye reduction mode",
	            0x0045: "Flash fired, red-eye reduction mode, return light not detected",
	            0x0047: "Flash fired, red-eye reduction mode, return light detected",
	            0x0049: "Flash fired, compulsory flash mode, red-eye reduction mode",
	            0x004D: "Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected",
	            0x004F: "Flash fired, compulsory flash mode, red-eye reduction mode, return light detected",
	            0x0059: "Flash fired, auto mode, red-eye reduction mode",
	            0x005D: "Flash fired, auto mode, return light not detected, red-eye reduction mode",
	            0x005F: "Flash fired, auto mode, return light detected, red-eye reduction mode"
	        },
	        SensingMethod       : {
	            1: "Not defined",
	            2: "One-chip color area sensor",
	            3: "Two-chip color area sensor",
	            4: "Three-chip color area sensor",
	            5: "Color sequential area sensor",
	            7: "Trilinear sensor",
	            8: "Color sequential linear sensor"
	        },
	        SceneCaptureType    : {
	            0: "Standard",
	            1: "Landscape",
	            2: "Portrait",
	            3: "Night scene"
	        },
	        SceneType           : {
	            1: "Directly photographed"
	        },
	        CustomRendered      : {
	            0: "Normal process",
	            1: "Custom process"
	        },
	        WhiteBalance        : {
	            0: "Auto white balance",
	            1: "Manual white balance"
	        },
	        GainControl         : {
	            0: "None",
	            1: "Low gain up",
	            2: "High gain up",
	            3: "Low gain down",
	            4: "High gain down"
	        },
	        Contrast            : {
	            0: "Normal",
	            1: "Soft",
	            2: "Hard"
	        },
	        Saturation          : {
	            0: "Normal",
	            1: "Low saturation",
	            2: "High saturation"
	        },
	        Sharpness           : {
	            0: "Normal",
	            1: "Soft",
	            2: "Hard"
	        },
	        SubjectDistanceRange: {
	            0: "Unknown",
	            1: "Macro",
	            2: "Close view",
	            3: "Distant view"
	        },
	        FileSource          : {
	            3: "DSC"
	        },

	        Components: {
	            0: "",
	            1: "Y",
	            2: "Cb",
	            3: "Cr",
	            4: "R",
	            5: "G",
	            6: "B"
	        }
	    };

	    function addEvent (element, event, handler) {
	        if (element.addEventListener) {
	            element.addEventListener(event, handler, false);
	        } else if (element.attachEvent) {
	            element.attachEvent("on" + event, handler);
	        }
	    }

	    function imageHasData (img) {
	        return !!(img.exifdata);
	    }


	    function base64ToArrayBuffer (base64, contentType) {
	        contentType = contentType || base64.match(/^data\:([^\;]+)\;base64,/mi)[1] || ''; // e.g. 'data:image/jpeg;base64,...' => 'image/jpeg'
	        base64     = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
	        var binary = atob(base64);
	        var len    = binary.length;
	        var buffer = new ArrayBuffer(len);
	        var view   = new Uint8Array(buffer);
	        for (var i = 0; i < len; i++) {
	            view[i] = binary.charCodeAt(i);
	        }
	        return buffer;
	    }

	    function objectURLToBlob (url, callback) {
	        var http          = new XMLHttpRequest();
	        http.open("GET", url, true);
	        http.responseType = "blob";
	        http.onload       = function (e) {
	            if (this.status == 200 || this.status === 0) {
	                callback(this.response);
	            }
	        };
	        http.send();
	    }

	    function getImageData (img, callback) {
	        function handleBinaryFile (binFile) {
	            var data     = findEXIFinJPEG(binFile);
	            var iptcdata = findIPTCinJPEG(binFile);
	            img.exifdata = data || {};
	            img.iptcdata = iptcdata || {};
	            if (callback) {
	                callback.call(img);
	            }
	        }

	        if (img.src) {
	            if (/^data\:/i.test(img.src)) { // Data URI
	                var arrayBuffer = base64ToArrayBuffer(img.src);
	                handleBinaryFile(arrayBuffer);

	            } else if (/^blob\:/i.test(img.src)) { // Object URL
	                var fileReader    = new FileReader();
	                fileReader.onload = function (e) {
	                    handleBinaryFile(e.target.result);
	                };
	                objectURLToBlob(img.src, function (blob) {
	                    fileReader.readAsArrayBuffer(blob);
	                });
	            } else {
	                var http          = new XMLHttpRequest();
	                http.onload       = function () {
	                    if (this.status == 200 || this.status === 0) {
	                        handleBinaryFile(http.response);
	                    } else {
	                        callback(new Error("Could not load image"));
	                    }
	                    http = null;
	                };
	                http.open("GET", img.src, true);
	                http.responseType = "arraybuffer";
	                http.send(null);
	            }
	        } else if (window.FileReader && (img instanceof window.Blob || img instanceof window.File)) {
	            var fileReader    = new FileReader();
	            fileReader.onload = function (e) {
	                if (debug) console.log("Got file of length " + e.target.result.byteLength);
	                handleBinaryFile(e.target.result);
	            };

	            fileReader.readAsArrayBuffer(img);
	        }
	    }

	    function findEXIFinJPEG (file) {
	        var dataView = new DataView(file);

	        if (debug) console.log("Got file of length " + file.byteLength);
	        if ((dataView.getUint8(0) != 0xFF) || (dataView.getUint8(1) != 0xD8)) {
	            if (debug) console.log("Not a valid JPEG");
	            return false; // not a valid jpeg
	        }

	        var offset = 2,
	            length = file.byteLength,
	            marker;

	        while (offset < length) {
	            if (dataView.getUint8(offset) != 0xFF) {
	                if (debug) console.log("Not a valid marker at offset " + offset + ", found: " + dataView.getUint8(offset));
	                return false; // not a valid marker, something is wrong
	            }

	            marker = dataView.getUint8(offset + 1);
	            if (debug) console.log(marker);

	            // we could implement handling for other markers here,
	            // but we're only looking for 0xFFE1 for EXIF data

	            if (marker == 225) {
	                if (debug) console.log("Found 0xFFE1 marker");

	                return readEXIFData(dataView, offset + 4, dataView.getUint16(offset + 2) - 2);

	                // offset += 2 + file.getShortAt(offset+2, true);

	            } else {
	                offset += 2 + dataView.getUint16(offset + 2);
	            }

	        }

	    }

	    function findIPTCinJPEG (file) {
	        var dataView = new DataView(file);

	        if (debug) console.log("Got file of length " + file.byteLength);
	        if ((dataView.getUint8(0) != 0xFF) || (dataView.getUint8(1) != 0xD8)) {
	            if (debug) console.log("Not a valid JPEG");
	            return false; // not a valid jpeg
	        }

	        var offset = 2,
	            length = file.byteLength;


	        var isFieldSegmentStart = function (dataView, offset) {
	            return (
	                dataView.getUint8(offset) === 0x38 &&
	                dataView.getUint8(offset + 1) === 0x42 &&
	                dataView.getUint8(offset + 2) === 0x49 &&
	                dataView.getUint8(offset + 3) === 0x4D &&
	                dataView.getUint8(offset + 4) === 0x04 &&
	                dataView.getUint8(offset + 5) === 0x04
	            );
	        };

	        while (offset < length) {

	            if (isFieldSegmentStart(dataView, offset)) {

	                // Get the length of the name header (which is padded to an even number of bytes)
	                var nameHeaderLength = dataView.getUint8(offset + 7);
	                if (nameHeaderLength % 2 !== 0) nameHeaderLength += 1;
	                // Check for pre photoshop 6 format
	                if (nameHeaderLength === 0) {
	                    // Always 4
	                    nameHeaderLength = 4;
	                }

	                var startOffset   = offset + 8 + nameHeaderLength;
	                var sectionLength = dataView.getUint16(offset + 6 + nameHeaderLength);

	                return readIPTCData(file, startOffset, sectionLength);

	                break;

	            }


	            // Not the marker, continue searching
	            offset++;

	        }

	    }

	    var IptcFieldMap = {
	        0x78: 'caption',
	        0x6E: 'credit',
	        0x19: 'keywords',
	        0x37: 'dateCreated',
	        0x50: 'byline',
	        0x55: 'bylineTitle',
	        0x7A: 'captionWriter',
	        0x69: 'headline',
	        0x74: 'copyright',
	        0x0F: 'category'
	    };

	    function readIPTCData (file, startOffset, sectionLength) {
	        var dataView        = new DataView(file);
	        var data            = {};
	        var fieldValue, fieldName, dataSize, segmentType, segmentSize;
	        var segmentStartPos = startOffset;
	        while (segmentStartPos < startOffset + sectionLength) {
	            if (dataView.getUint8(segmentStartPos) === 0x1C && dataView.getUint8(segmentStartPos + 1) === 0x02) {
	                segmentType = dataView.getUint8(segmentStartPos + 2);
	                if (segmentType in IptcFieldMap) {
	                    dataSize    = dataView.getInt16(segmentStartPos + 3);
	                    segmentSize = dataSize + 5;
	                    fieldName   = IptcFieldMap[segmentType];
	                    fieldValue  = getStringFromDB(dataView, segmentStartPos + 5, dataSize);
	                    // Check if we already stored a value with this name
	                    if (data.hasOwnProperty(fieldName)) {
	                        // Value already stored with this name, create multivalue field
	                        if (data[fieldName] instanceof Array) {
	                            data[fieldName].push(fieldValue);
	                        }
	                        else {
	                            data[fieldName] = [data[fieldName], fieldValue];
	                        }
	                    }
	                    else {
	                        data[fieldName] = fieldValue;
	                    }
	                }

	            }
	            segmentStartPos++;
	        }
	        return data;
	    }


	    function readTags (file, tiffStart, dirStart, strings, bigEnd) {
	        var entries = file.getUint16(dirStart, !bigEnd),
	            tags    = {},
	            entryOffset, tag,
	            i;

	        for (i = 0; i < entries; i++) {
	            entryOffset = dirStart + i * 12 + 2;
	            tag         = strings[file.getUint16(entryOffset, !bigEnd)];
	            if (!tag && debug) console.log("Unknown tag: " + file.getUint16(entryOffset, !bigEnd));
	            tags[tag] = readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd);
	        }
	        return tags;
	    }


	    function readTagValue (file, entryOffset, tiffStart, dirStart, bigEnd) {
	        var type        = file.getUint16(entryOffset + 2, !bigEnd),
	            numValues   = file.getUint32(entryOffset + 4, !bigEnd),
	            valueOffset = file.getUint32(entryOffset + 8, !bigEnd) + tiffStart,
	            offset,
	            vals, val, n,
	            numerator, denominator;

	        switch (type) {
	            case 1: // byte, 8-bit unsigned int
	            case 7: // undefined, 8-bit byte, value depending on field
	                if (numValues == 1) {
	                    return file.getUint8(entryOffset + 8, !bigEnd);
	                } else {
	                    offset = numValues > 4 ? valueOffset : (entryOffset + 8);
	                    vals   = [];
	                    for (n = 0; n < numValues; n++) {
	                        vals[n] = file.getUint8(offset + n);
	                    }
	                    return vals;
	                }

	            case 2: // ascii, 8-bit byte
	                offset = numValues > 4 ? valueOffset : (entryOffset + 8);
	                return getStringFromDB(file, offset, numValues - 1);

	            case 3: // short, 16 bit int
	                if (numValues == 1) {
	                    return file.getUint16(entryOffset + 8, !bigEnd);
	                } else {
	                    offset = numValues > 2 ? valueOffset : (entryOffset + 8);
	                    vals   = [];
	                    for (n = 0; n < numValues; n++) {
	                        vals[n] = file.getUint16(offset + 2 * n, !bigEnd);
	                    }
	                    return vals;
	                }

	            case 4: // long, 32 bit int
	                if (numValues == 1) {
	                    return file.getUint32(entryOffset + 8, !bigEnd);
	                } else {
	                    vals = [];
	                    for (n = 0; n < numValues; n++) {
	                        vals[n] = file.getUint32(valueOffset + 4 * n, !bigEnd);
	                    }
	                    return vals;
	                }

	            case 5:    // rational = two long values, first is numerator, second is denominator
	                if (numValues == 1) {
	                    numerator       = file.getUint32(valueOffset, !bigEnd);
	                    denominator     = file.getUint32(valueOffset + 4, !bigEnd);
	                    val             = new Number(numerator / denominator);
	                    val.numerator   = numerator;
	                    val.denominator = denominator;
	                    return val;
	                } else {
	                    vals = [];
	                    for (n = 0; n < numValues; n++) {
	                        numerator           = file.getUint32(valueOffset + 8 * n, !bigEnd);
	                        denominator         = file.getUint32(valueOffset + 4 + 8 * n, !bigEnd);
	                        vals[n]             = new Number(numerator / denominator);
	                        vals[n].numerator   = numerator;
	                        vals[n].denominator = denominator;
	                    }
	                    return vals;
	                }

	            case 9: // slong, 32 bit signed int
	                if (numValues == 1) {
	                    return file.getInt32(entryOffset + 8, !bigEnd);
	                } else {
	                    vals = [];
	                    for (n = 0; n < numValues; n++) {
	                        vals[n] = file.getInt32(valueOffset + 4 * n, !bigEnd);
	                    }
	                    return vals;
	                }

	            case 10: // signed rational, two slongs, first is numerator, second is denominator
	                if (numValues == 1) {
	                    return file.getInt32(valueOffset, !bigEnd) / file.getInt32(valueOffset + 4, !bigEnd);
	                } else {
	                    vals = [];
	                    for (n = 0; n < numValues; n++) {
	                        vals[n] = file.getInt32(valueOffset + 8 * n, !bigEnd) / file.getInt32(valueOffset + 4 + 8 * n, !bigEnd);
	                    }
	                    return vals;
	                }
	        }
	    }

	    function getStringFromDB (buffer, start, length) {
	        var outstr = "", n;
	        for (n = start; n < start + length; n++) {
	            outstr += String.fromCharCode(buffer.getUint8(n));
	        }
	        return outstr;
	    }

	    function readEXIFData (file, start) {
	        if (getStringFromDB(file, start, 4) != "Exif") {
	            if (debug) console.log("Not valid EXIF data! " + getStringFromDB(file, start, 4));
	            return false;
	        }

	        var bigEnd,
	            tags, tag,
	            exifData, gpsData,
	            tiffOffset = start + 6;

	        // test for TIFF validity and endianness
	        if (file.getUint16(tiffOffset) == 0x4949) {
	            bigEnd = false;
	        } else if (file.getUint16(tiffOffset) == 0x4D4D) {
	            bigEnd = true;
	        } else {
	            if (debug) console.log("Not valid TIFF data! (no 0x4949 or 0x4D4D)");
	            return false;
	        }

	        if (file.getUint16(tiffOffset + 2, !bigEnd) != 0x002A) {
	            if (debug) console.log("Not valid TIFF data! (no 0x002A)");
	            return false;
	        }

	        var firstIFDOffset = file.getUint32(tiffOffset + 4, !bigEnd);

	        if (firstIFDOffset < 0x00000008) {
	            if (debug) console.log("Not valid TIFF data! (First offset less than 8)", file.getUint32(tiffOffset + 4, !bigEnd));
	            return false;
	        }

	        tags = readTags(file, tiffOffset, tiffOffset + firstIFDOffset, TiffTags, bigEnd);

	        if (tags.ExifIFDPointer) {
	            exifData = readTags(file, tiffOffset, tiffOffset + tags.ExifIFDPointer, ExifTags, bigEnd);
	            for (tag in exifData) {
	                switch (tag) {
	                    case "LightSource" :
	                    case "Flash" :
	                    case "MeteringMode" :
	                    case "ExposureProgram" :
	                    case "SensingMethod" :
	                    case "SceneCaptureType" :
	                    case "SceneType" :
	                    case "CustomRendered" :
	                    case "WhiteBalance" :
	                    case "GainControl" :
	                    case "Contrast" :
	                    case "Saturation" :
	                    case "Sharpness" :
	                    case "SubjectDistanceRange" :
	                    case "FileSource" :
	                        exifData[tag] = StringValues[tag][exifData[tag]];
	                        break;

	                    case "ExifVersion" :
	                    case "FlashpixVersion" :
	                        exifData[tag] = String.fromCharCode(exifData[tag][0], exifData[tag][1], exifData[tag][2], exifData[tag][3]);
	                        break;

	                    case "ComponentsConfiguration" :
	                        exifData[tag] =
	                            StringValues.Components[exifData[tag][0]] +
	                            StringValues.Components[exifData[tag][1]] +
	                            StringValues.Components[exifData[tag][2]] +
	                            StringValues.Components[exifData[tag][3]];
	                        break;
	                }
	                tags[tag] = exifData[tag];
	            }
	        }

	        if (tags.GPSInfoIFDPointer) {
	            gpsData = readTags(file, tiffOffset, tiffOffset + tags.GPSInfoIFDPointer, GPSTags, bigEnd);
	            for (tag in gpsData) {
	                switch (tag) {
	                    case "GPSVersionID" :
	                        gpsData[tag] = gpsData[tag][0] +
	                            "." + gpsData[tag][1] +
	                            "." + gpsData[tag][2] +
	                            "." + gpsData[tag][3];
	                        break;
	                }
	                tags[tag] = gpsData[tag];
	            }
	        }

	        return tags;
	    }

	    EXIF.getData = function (img, callback) {
	        if ((img instanceof Image || img instanceof HTMLImageElement) && !img.complete) return false;

	        if (!imageHasData(img)) {
	            getImageData(img, callback);
	        } else {
	            if (callback) {
	                callback.call(img);
	            }
	        }
	        return true;
	    }

	    EXIF.getTag = function (img, tag) {
	        if (!imageHasData(img)) return;
	        return img.exifdata[tag];
	    }

	    EXIF.getAllTags = function (img) {
	        if (!imageHasData(img)) return {};
	        var a,
	            data = img.exifdata,
	            tags = {};
	        for (a in data) {
	            if (data.hasOwnProperty(a)) {
	                tags[a] = data[a];
	            }
	        }
	        return tags;
	    }

	    EXIF.pretty = function (img) {
	        if (!imageHasData(img)) return "";
	        var a,
	            data      = img.exifdata,
	            strPretty = "";
	        for (a in data) {
	            if (data.hasOwnProperty(a)) {
	                if (typeof data[a] == "object") {
	                    if (data[a] instanceof Number) {
	                        strPretty += a + " : " + data[a] + " [" + data[a].numerator + "/" + data[a].denominator + "]\r\n";
	                    } else {
	                        strPretty += a + " : [" + data[a].length + " values]\r\n";
	                    }
	                } else {
	                    strPretty += a + " : " + data[a] + "\r\n";
	                }
	            }
	        }
	        return strPretty;
	    }

	    EXIF.readFromBinaryFile = function (file) {
	        return findEXIFinJPEG(file);
	    }

	    if (true) {
	        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function () {
	            return EXIF;
	        }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    }
	}.call(this));

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/**
	 * Mega pixel image rendering library for iOS6 Safari
	 *
	 * Fixes iOS6 Safari's image file rendering issue for large size image (over mega-pixel),
	 * which causes unexpected subsampling when drawing it in canvas.
	 * By using this library, you can safely render the image with proper stretching.
	 *
	 * Copyright (c) 2012 Shinichi Tomita <shinichi.tomita@gmail.com>
	 * Released under the MIT license
	 */
	(function () {

	    /**
	     * Detect subsampling in loaded image.
	     * In iOS, larger images than 2M pixels may be subsampled in rendering.
	     */
	    function detectSubsampling (img) {
	        var iw = img.naturalWidth, ih = img.naturalHeight;
	        if (iw * ih > 1024 * 1024) { // subsampling may happen over megapixel image
	            var canvas   = document.createElement('canvas');
	            canvas.width = canvas.height = 1;
	            var ctx = canvas.getContext('2d');
	            ctx.drawImage(img, -iw + 1, 0);
	            // subsampled image becomes half smaller in rendering size.
	            // check alpha channel value to confirm image is covering edge pixel or not.
	            // if alpha value is 0 image is not covering, hence subsampled.
	            return ctx.getImageData(0, 0, 1, 1).data[3] === 0;
	        } else {
	            return false;
	        }
	    }

	    /**
	     * Detecting vertical squash in loaded image.
	     * Fixes a bug which squash image vertically while drawing into canvas for some images.
	     */
	    function detectVerticalSquash (img, iw, ih) {
	        var canvas    = document.createElement('canvas');
	        canvas.width  = 1;
	        canvas.height = ih;
	        var ctx       = canvas.getContext('2d');
	        ctx.drawImage(img, 0, 0);
	        var data      = ctx.getImageData(0, 0, 1, ih).data;
	        // search image edge pixel position in case it is squashed vertically.
	        var sy = 0;
	        var ey = ih;
	        var py = ih;
	        while (py > sy) {
	            var alpha = data[(py - 1) * 4 + 3];
	            if (alpha === 0) {
	                ey = py;
	            } else {
	                sy = py;
	            }
	            py = (ey + sy) >> 1;
	        }
	        var ratio = (py / ih);
	        return (ratio === 0) ? 1 : ratio;
	    }

	    /**
	     * Rendering image element (with resizing) and get its data URL
	     */
	    function renderImageToDataURL (img, options, doSquash) {
	        var canvas = document.createElement('canvas');
	        renderImageToCanvas(img, canvas, options, doSquash);
	        return canvas.toDataURL("image/jpeg", options.quality || 0.8);
	    }

	    /**
	     * Rendering image element (with resizing) into the canvas element
	     */
	    function renderImageToCanvas (img, canvas, options, doSquash) {
	        var iw         = img.naturalWidth, ih = img.naturalHeight;
	        var width      = options.width, height = options.height;
	        var ctx        = canvas.getContext('2d');
	        ctx.save();
	        transformCoordinate(canvas, ctx, width, height, options.orientation);
	        var subsampled = detectSubsampling(img);
	        if (subsampled) {
	            iw /= 2;
	            ih /= 2;
	        }
	        var d = 1024; // size of tiling canvas
	        var tmpCanvas   = document.createElement('canvas');
	        tmpCanvas.width = tmpCanvas.height = d;
	        var tmpCtx          = tmpCanvas.getContext('2d');
	        var vertSquashRatio = doSquash ? detectVerticalSquash(img, iw, ih) : 1;
	        var dw              = Math.ceil(d * width / iw);
	        var dh              = Math.ceil(d * height / ih / vertSquashRatio);
	        var sy              = 0;
	        var dy              = 0;
	        while (sy < ih) {
	            var sx = 0;
	            var dx = 0;
	            while (sx < iw) {
	                tmpCtx.clearRect(0, 0, d, d);
	                tmpCtx.drawImage(img, -sx, -sy);
	                ctx.drawImage(tmpCanvas, 0, 0, d, d, dx, dy, dw, dh);
	                sx += d;
	                dx += dw;
	            }
	            sy += d;
	            dy += dh;
	        }
	        ctx.restore();
	        tmpCanvas           = tmpCtx = null;
	    }

	    /**
	     * Transform canvas coordination according to specified frame size and orientation
	     * Orientation value is from EXIF tag
	     */
	    function transformCoordinate (canvas, ctx, width, height, orientation) {
	        switch (orientation) {
	            case 5:
	            case 6:
	            case 7:
	            case 8:
	                canvas.width  = height;
	                canvas.height = width;
	                break;
	            default:
	                canvas.width  = width;
	                canvas.height = height;
	        }
	        switch (orientation) {
	            case 2:
	                // horizontal flip
	                ctx.translate(width, 0);
	                ctx.scale(-1, 1);
	                break;
	            case 3:
	                // 180 rotate left
	                ctx.translate(width, height);
	                ctx.rotate(Math.PI);
	                break;
	            case 4:
	                // vertical flip
	                ctx.translate(0, height);
	                ctx.scale(1, -1);
	                break;
	            case 5:
	                // vertical flip + 90 rotate right
	                ctx.rotate(0.5 * Math.PI);
	                ctx.scale(1, -1);
	                break;
	            case 6:
	                // 90 rotate right
	                ctx.rotate(0.5 * Math.PI);
	                ctx.translate(0, -height);
	                break;
	            case 7:
	                // horizontal flip + 90 rotate right
	                ctx.rotate(0.5 * Math.PI);
	                ctx.translate(width, -height);
	                ctx.scale(-1, 1);
	                break;
	            case 8:
	                // 90 rotate left
	                ctx.rotate(-0.5 * Math.PI);
	                ctx.translate(-width, 0);
	                break;
	            default:
	                break;
	        }
	    }


	    /**
	     * MegaPixImage class
	     */
	    function MegaPixImage (srcImage) {
	        if (window.Blob && srcImage instanceof Blob) {
	            var img = new Image();
	            var URL = window.URL && window.URL.createObjectURL ? window.URL :
	                window.webkitURL && window.webkitURL.createObjectURL ? window.webkitURL :
	                    null;
	            if (!URL) {
	                throw Error("No createObjectURL function found to create blob url");
	            }
	            img.src   = URL.createObjectURL(srcImage);
	            this.blob = srcImage;
	            srcImage  = img;
	        }
	        if (!srcImage.naturalWidth && !srcImage.naturalHeight) {
	            var _this               = this;
	            srcImage.onload         = function () {
	                var listeners = _this.imageLoadListeners;
	                if (listeners) {
	                    _this.imageLoadListeners = null;
	                    for (var i = 0, len = listeners.length; i < len; i++) {
	                        listeners[i]();
	                    }
	                }
	            };
	            this.imageLoadListeners = [];
	        }
	        this.srcImage = srcImage;
	    }

	    /**
	     * Rendering megapix image into specified target element
	     */
	    MegaPixImage.prototype.render = function (target, options, callback) {
	        if (this.imageLoadListeners) {
	            var _this = this;
	            this.imageLoadListeners.push(function () {
	                _this.render(target, options, callback);
	            });
	            return;
	        }
	        options       = options || {};
	        var srcImage  = this.srcImage,
	            src       = srcImage.src,
	            srcLength = src.length,
	            imgWidth  = srcImage.naturalWidth, imgHeight = srcImage.naturalHeight,
	            width     = options.width, height = options.height,
	            maxWidth  = options.maxWidth, maxHeight = options.maxHeight,
	            doSquash  = this.blob && this.blob.type === 'image/jpeg' ||
	                src.indexOf('data:image/jpeg') === 0 ||
	                src.indexOf('.jpg') === srcLength - 4 ||
	                src.indexOf('.jpeg') === srcLength - 5;
	        if (width && !height) {
	            height = (imgHeight * width / imgWidth) << 0;
	        } else if (height && !width) {
	            width = (imgWidth * height / imgHeight) << 0;
	        } else {
	            width  = imgWidth;
	            height = imgHeight;
	        }
	        if (maxWidth && width > maxWidth) {
	            width  = maxWidth;
	            height = (imgHeight * width / imgWidth) << 0;
	        }
	        if (maxHeight && height > maxHeight) {
	            height = maxHeight;
	            width  = (imgWidth * height / imgHeight) << 0;
	        }
	        var opt = {width: width, height: height};
	        for (var k in options) opt[k] = options[k];

	        var tagName = target.tagName.toLowerCase();
	        if (tagName === 'img') {
	            target.src = renderImageToDataURL(this.srcImage, opt, doSquash);
	        } else if (tagName === 'canvas') {
	            renderImageToCanvas(this.srcImage, target, opt, doSquash);
	        }
	        if (typeof this.onrender === 'function') {
	            this.onrender(target);
	        }
	        if (callback) {
	            callback();
	        }
	    };

	    /**
	     * Export class to global
	     */
	    if (true) {
	        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function () {
	            return MegaPixImage;
	        }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)); // for AMD loader
	    } else {
	        this.MegaPixImage = MegaPixImage;
	    }

	})();


/***/ },
/* 8 */
/***/ function(module, exports) {

	function JPEGEncoder (l) {
	    var o = this;
	    var s = Math.round;
	    var k = Math.floor;
	    var O = new Array(64);
	    var K = new Array(64);
	    var d = new Array(64);
	    var Z = new Array(64);
	    var u;
	    var h;
	    var G;
	    var T;
	    var n = new Array(65535);
	    var m = new Array(65535);
	    var P = new Array(64);
	    var S = new Array(64);
	    var j = [];
	    var t = 0;
	    var a = 7;
	    var A = new Array(64);
	    var f = new Array(64);
	    var U = new Array(64);
	    var e = new Array(256);
	    var C = new Array(2048);
	    var x;
	    var i = [0, 1, 5, 6, 14, 15, 27, 28, 2, 4, 7, 13, 16, 26, 29, 42, 3, 8, 12, 17, 25, 30, 41, 43, 9, 11, 18, 24, 31, 40, 44, 53, 10, 19, 23, 32, 39, 45, 52, 54, 20, 22, 33, 38, 46, 51, 55, 60, 21, 34, 37, 47, 50, 56, 59, 61, 35, 36, 48, 49, 57, 58, 62, 63];
	    var g = [0, 0, 1, 5, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0];
	    var c = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
	    var w = [0, 0, 2, 1, 3, 3, 2, 4, 3, 5, 5, 4, 4, 0, 0, 1, 125];
	    var E = [1, 2, 3, 0, 4, 17, 5, 18, 33, 49, 65, 6, 19, 81, 97, 7, 34, 113, 20, 50, 129, 145, 161, 8, 35, 66, 177, 193, 21, 82, 209, 240, 36, 51, 98, 114, 130, 9, 10, 22, 23, 24, 25, 26, 37, 38, 39, 40, 41, 42, 52, 53, 54, 55, 56, 57, 58, 67, 68, 69, 70, 71, 72, 73, 74, 83, 84, 85, 86, 87, 88, 89, 90, 99, 100, 101, 102, 103, 104, 105, 106, 115, 116, 117, 118, 119, 120, 121, 122, 131, 132, 133, 134, 135, 136, 137, 138, 146, 147, 148, 149, 150, 151, 152, 153, 154, 162, 163, 164, 165, 166, 167, 168, 169, 170, 178, 179, 180, 181, 182, 183, 184, 185, 186, 194, 195, 196, 197, 198, 199, 200, 201, 202, 210, 211, 212, 213, 214, 215, 216, 217, 218, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250];
	    var v = [0, 0, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0];
	    var Y = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
	    var J = [0, 0, 2, 1, 2, 4, 4, 3, 4, 7, 5, 4, 4, 0, 1, 2, 119];
	    var B = [0, 1, 2, 3, 17, 4, 5, 33, 49, 6, 18, 65, 81, 7, 97, 113, 19, 34, 50, 129, 8, 20, 66, 145, 161, 177, 193, 9, 35, 51, 82, 240, 21, 98, 114, 209, 10, 22, 36, 52, 225, 37, 241, 23, 24, 25, 26, 38, 39, 40, 41, 42, 53, 54, 55, 56, 57, 58, 67, 68, 69, 70, 71, 72, 73, 74, 83, 84, 85, 86, 87, 88, 89, 90, 99, 100, 101, 102, 103, 104, 105, 106, 115, 116, 117, 118, 119, 120, 121, 122, 130, 131, 132, 133, 134, 135, 136, 137, 138, 146, 147, 148, 149, 150, 151, 152, 153, 154, 162, 163, 164, 165, 166, 167, 168, 169, 170, 178, 179, 180, 181, 182, 183, 184, 185, 186, 194, 195, 196, 197, 198, 199, 200, 201, 202, 210, 211, 212, 213, 214, 215, 216, 217, 218, 226, 227, 228, 229, 230, 231, 232, 233, 234, 242, 243, 244, 245, 246, 247, 248, 249, 250];

	    function M (ag) {
	        var af = [16, 11, 10, 16, 24, 40, 51, 61, 12, 12, 14, 19, 26, 58, 60, 55, 14, 13, 16, 24, 40, 57, 69, 56, 14, 17, 22, 29, 51, 87, 80, 62, 18, 22, 37, 56, 68, 109, 103, 77, 24, 35, 55, 64, 81, 104, 113, 92, 49, 64, 78, 87, 103, 121, 120, 101, 72, 92, 95, 98, 112, 100, 103, 99];
	        for (var ae = 0; ae < 64; ae++) {
	            var aj = k((af[ae] * ag + 50) / 100);
	            if (aj < 1) {
	                aj = 1
	            } else {
	                if (aj > 255) {
	                    aj = 255
	                }
	            }
	            O[i[ae]] = aj
	        }
	        var ah = [17, 18, 24, 47, 99, 99, 99, 99, 18, 21, 26, 66, 99, 99, 99, 99, 24, 26, 56, 99, 99, 99, 99, 99, 47, 66, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99, 99];
	        for (var ad = 0; ad < 64; ad++) {
	            var ai = k((ah[ad] * ag + 50) / 100);
	            if (ai < 1) {
	                ai = 1
	            } else {
	                if (ai > 255) {
	                    ai = 255
	                }
	            }
	            K[i[ad]] = ai
	        }
	        var ac = [1, 1.387039845, 1.306562965, 1.175875602, 1, 0.785694958, 0.5411961, 0.275899379];
	        var ab = 0;
	        for (var ak = 0; ak < 8; ak++) {
	            for (var aa = 0; aa < 8; aa++) {
	                d[ab] = (1 / (O[i[ab]] * ac[ak] * ac[aa] * 8));
	                Z[ab] = (1 / (K[i[ab]] * ac[ak] * ac[aa] * 8));
	                ab++
	            }
	        }
	    }

	    function q (ae, aa) {
	        var ad = 0;
	        var ag = 0;
	        var af = new Array();
	        for (var ab = 1; ab <= 16; ab++) {
	            for (var ac = 1; ac <= ae[ab]; ac++) {
	                af[aa[ag]]    = [];
	                af[aa[ag]][0] = ad;
	                af[aa[ag]][1] = ab;
	                ag++;
	                ad++
	            }
	            ad *= 2
	        }
	        return af
	    }

	    function W () {
	        u = q(g, c);
	        h = q(v, Y);
	        G = q(w, E);
	        T = q(J, B)
	    }

	    function z () {
	        var ac = 1;
	        var ab = 2;
	        for (var aa = 1; aa <= 15; aa++) {
	            for (var ad = ac; ad < ab; ad++) {
	                m[32767 + ad]    = aa;
	                n[32767 + ad]    = [];
	                n[32767 + ad][1] = aa;
	                n[32767 + ad][0] = ad
	            }
	            for (var ae = -(ab - 1); ae <= -ac; ae++) {
	                m[32767 + ae]    = aa;
	                n[32767 + ae]    = [];
	                n[32767 + ae][1] = aa;
	                n[32767 + ae][0] = ab - 1 + ae
	            }
	            ac <<= 1;
	            ab <<= 1
	        }
	    }

	    function V () {
	        for (var aa = 0; aa < 256; aa++) {
	            C[aa]               = 19595 * aa;
	            C[(aa + 256) >> 0]  = 38470 * aa;
	            C[(aa + 512) >> 0]  = 7471 * aa + 32768;
	            C[(aa + 768) >> 0]  = -11059 * aa;
	            C[(aa + 1024) >> 0] = -21709 * aa;
	            C[(aa + 1280) >> 0] = 32768 * aa + 8421375;
	            C[(aa + 1536) >> 0] = -27439 * aa;
	            C[(aa + 1792) >> 0] = -5329 * aa
	        }
	    }

	    function X (aa) {
	        var ac = aa[0];
	        var ab = aa[1] - 1;
	        while (ab >= 0) {
	            if (ac & (1 << ab)) {
	                t |= (1 << a)
	            }
	            ab--;
	            a--;
	            if (a < 0) {
	                if (t == 255) {
	                    F(255);
	                    F(0)
	                } else {
	                    F(t)
	                }
	                a = 7;
	                t = 0
	            }
	        }
	    }

	    function F (aa) {
	        j.push(e[aa])
	    }

	    function p (aa) {
	        F((aa >> 8) & 255);
	        F((aa) & 255)
	    }

	    function N (aZ, ap) {
	        var aL, aK, aJ, aI, aH, aD, aC, aB;
	        var aN   = 0;
	        var aR;
	        const aq = 8;
	        const ai = 64;
	        for (aR = 0; aR < aq; ++aR) {
	            aL         = aZ[aN];
	            aK         = aZ[aN + 1];
	            aJ         = aZ[aN + 2];
	            aI         = aZ[aN + 3];
	            aH         = aZ[aN + 4];
	            aD         = aZ[aN + 5];
	            aC         = aZ[aN + 6];
	            aB         = aZ[aN + 7];
	            var aY     = aL + aB;
	            var aO     = aL - aB;
	            var aX     = aK + aC;
	            var aP     = aK - aC;
	            var aU     = aJ + aD;
	            var aQ     = aJ - aD;
	            var aT     = aI + aH;
	            var aS     = aI - aH;
	            var an     = aY + aT;
	            var ak     = aY - aT;
	            var am     = aX + aU;
	            var al     = aX - aU;
	            aZ[aN]     = an + am;
	            aZ[aN + 4] = an - am;
	            var ax     = (al + ak) * 0.707106781;
	            aZ[aN + 2] = ak + ax;
	            aZ[aN + 6] = ak - ax;
	            an         = aS + aQ;
	            am         = aQ + aP;
	            al         = aP + aO;
	            var at     = (an - al) * 0.382683433;
	            var aw     = 0.5411961 * an + at;
	            var au     = 1.306562965 * al + at;
	            var av     = am * 0.707106781;
	            var ah     = aO + av;
	            var ag     = aO - av;
	            aZ[aN + 5] = ag + aw;
	            aZ[aN + 3] = ag - aw;
	            aZ[aN + 1] = ah + au;
	            aZ[aN + 7] = ah - au;
	            aN += 8
	        }
	        aN = 0;
	        for (aR = 0; aR < aq; ++aR) {
	            aL          = aZ[aN];
	            aK          = aZ[aN + 8];
	            aJ          = aZ[aN + 16];
	            aI          = aZ[aN + 24];
	            aH          = aZ[aN + 32];
	            aD          = aZ[aN + 40];
	            aC          = aZ[aN + 48];
	            aB          = aZ[aN + 56];
	            var ar      = aL + aB;
	            var aj      = aL - aB;
	            var az      = aK + aC;
	            var ae      = aK - aC;
	            var aG      = aJ + aD;
	            var ac      = aJ - aD;
	            var aW      = aI + aH;
	            var aa      = aI - aH;
	            var ao      = ar + aW;
	            var aV      = ar - aW;
	            var ay      = az + aG;
	            var aF      = az - aG;
	            aZ[aN]      = ao + ay;
	            aZ[aN + 32] = ao - ay;
	            var af      = (aF + aV) * 0.707106781;
	            aZ[aN + 16] = aV + af;
	            aZ[aN + 48] = aV - af;
	            ao          = aa + ac;
	            ay          = ac + ae;
	            aF          = ae + aj;
	            var aM      = (ao - aF) * 0.382683433;
	            var ad      = 0.5411961 * ao + aM;
	            var a1      = 1.306562965 * aF + aM;
	            var ab      = ay * 0.707106781;
	            var a0      = aj + ab;
	            var aA      = aj - ab;
	            aZ[aN + 40] = aA + ad;
	            aZ[aN + 24] = aA - ad;
	            aZ[aN + 8]  = a0 + a1;
	            aZ[aN + 56] = a0 - a1;
	            aN++
	        }
	        var aE;
	        for (aR = 0; aR < ai; ++aR) {
	            aE    = aZ[aR] * ap[aR];
	            P[aR] = (aE > 0) ? ((aE + 0.5) | 0) : ((aE - 0.5) | 0)
	        }
	        return P
	    }

	    function b () {
	        p(65504);
	        p(16);
	        F(74);
	        F(70);
	        F(73);
	        F(70);
	        F(0);
	        F(1);
	        F(1);
	        F(0);
	        p(1);
	        p(1);
	        F(0);
	        F(0)
	    }

	    function r (aa, ab) {
	        p(65472);
	        p(17);
	        F(8);
	        p(ab);
	        p(aa);
	        F(3);
	        F(1);
	        F(17);
	        F(0);
	        F(2);
	        F(17);
	        F(1);
	        F(3);
	        F(17);
	        F(1)
	    }

	    function D () {
	        p(65499);
	        p(132);
	        F(0);
	        for (var ab = 0; ab < 64; ab++) {
	            F(O[ab])
	        }
	        F(1);
	        for (var aa = 0; aa < 64; aa++) {
	            F(K[aa])
	        }
	    }

	    function H () {
	        p(65476);
	        p(418);
	        F(0);
	        for (var ae = 0; ae < 16; ae++) {
	            F(g[ae + 1])
	        }
	        for (var ad = 0; ad <= 11; ad++) {
	            F(c[ad])
	        }
	        F(16);
	        for (var ac = 0; ac < 16; ac++) {
	            F(w[ac + 1])
	        }
	        for (var ab = 0; ab <= 161; ab++) {
	            F(E[ab])
	        }
	        F(1);
	        for (var aa = 0; aa < 16; aa++) {
	            F(v[aa + 1])
	        }
	        for (var ah = 0; ah <= 11; ah++) {
	            F(Y[ah])
	        }
	        F(17);
	        for (var ag = 0; ag < 16; ag++) {
	            F(J[ag + 1])
	        }
	        for (var af = 0; af <= 161; af++) {
	            F(B[af])
	        }
	    }

	    function I () {
	        p(65498);
	        p(12);
	        F(3);
	        F(1);
	        F(0);
	        F(2);
	        F(17);
	        F(3);
	        F(17);
	        F(0);
	        F(63);
	        F(0)
	    }

	    function L (ad, aa, al, at, ap) {
	        var ag   = ap[0];
	        var ab   = ap[240];
	        var ac;
	        const ar = 16;
	        const ai = 63;
	        const ah = 64;
	        var aq   = N(ad, aa);
	        for (var am = 0; am < ah; ++am) {
	            S[i[am]] = aq[am]
	        }
	        var an = S[0] - al;
	        al     = S[0];
	        if (an == 0) {
	            X(at[0])
	        } else {
	            ac = 32767 + an;
	            X(at[m[ac]]);
	            X(n[ac])
	        }
	        var ae = 63;
	        for (; (ae > 0) && (S[ae] == 0); ae--) {
	        }
	        if (ae == 0) {
	            X(ag);
	            return al
	        }
	        var ao = 1;
	        var au;
	        while (ao <= ae) {
	            var ak = ao;
	            for (; (S[ao] == 0) && (ao <= ae); ++ao) {
	            }
	            var aj = ao - ak;
	            if (aj >= ar) {
	                au = aj >> 4;
	                for (var af = 1; af <= au; ++af) {
	                    X(ab)
	                }
	                aj = aj & 15
	            }
	            ac = 32767 + S[ao];
	            X(ap[(aj << 4) + m[ac]]);
	            X(n[ac]);
	            ao++
	        }
	        if (ae != ai) {
	            X(ag)
	        }
	        return al
	    }

	    function y () {
	        var ab = String.fromCharCode;
	        for (var aa = 0; aa < 256; aa++) {
	            e[aa] = ab(aa)
	        }
	    }

	    this.encode = function (an, aj, aB) {
	        var aa = new Date().getTime();
	        if (aj) {
	            R(aj)
	        }
	        j                       = new Array();
	        t                       = 0;
	        a                       = 7;
	        p(65496);
	        b();
	        D();
	        r(an.width, an.height);
	        H();
	        I();
	        var al                  = 0;
	        var aq                  = 0;
	        var ao                  = 0;
	        t                       = 0;
	        a                       = 7;
	        this.encode.displayName = "_encode_";
	        var at                  = an.data;
	        var ar                  = an.width;
	        var aA                  = an.height;
	        var ay                  = ar * 4;
	        var ai                  = ar * 3;
	        var ah, ag              = 0;
	        var am, ax, az;
	        var ab, ap, ac, af, ae;
	        while (ag < aA) {
	            ah = 0;
	            while (ah < ay) {
	                ab = ay * ag + ah;
	                ap = ab;
	                ac = -1;
	                af = 0;
	                for (ae = 0; ae < 64; ae++) {
	                    af = ae >> 3;
	                    ac = (ae & 7) * 4;
	                    ap = ab + (af * ay) + ac;
	                    if (ag + af >= aA) {
	                        ap -= (ay * (ag + 1 + af - aA))
	                    }
	                    if (ah + ac >= ay) {
	                        ap -= ((ah + ac) - ay + 4)
	                    }
	                    am    = at[ap++];
	                    ax    = at[ap++];
	                    az    = at[ap++];
	                    A[ae] = ((C[am] + C[(ax + 256) >> 0] + C[(az + 512) >> 0]) >> 16) - 128;
	                    f[ae] = ((C[(am + 768) >> 0] + C[(ax + 1024) >> 0] + C[(az + 1280) >> 0]) >> 16) - 128;
	                    U[ae] = ((C[(am + 1280) >> 0] + C[(ax + 1536) >> 0] + C[(az + 1792) >> 0]) >> 16) - 128
	                }
	                al = L(A, d, al, u, G);
	                aq = L(f, Z, aq, h, T);
	                ao = L(U, Z, ao, h, T);
	                ah += 32
	            }
	            ag += 8
	        }
	        if (a >= 0) {
	            var aw = [];
	            aw[1]  = a + 1;
	            aw[0]  = (1 << (a + 1)) - 1;
	            X(aw)
	        }
	        p(65497);
	        if (aB) {
	            var av = j.length;
	            var aC = new Uint8Array(av);
	            for (var au = 0; au < av; au++) {
	                aC[au] = j[au].charCodeAt()
	            }
	            j      = [];
	            var ak = new Date().getTime() - aa;
	            return aC
	        }
	        var ad = "data:image/jpeg;base64," + btoa(j.join(""));
	        j      = [];
	        var ak = new Date().getTime() - aa;
	        return ad
	    };
	    function R (ab) {
	        if (ab <= 0) {
	            ab = 1
	        }
	        if (ab > 100) {
	            ab = 100
	        }
	        if (x == ab) {
	            return
	        }
	        var aa = 0;
	        if (ab < 50) {
	            aa = Math.floor(5000 / ab)
	        } else {
	            aa = Math.floor(200 - ab * 2)
	        }
	        M(aa);
	        x      = ab;
	    }

	    function Q () {
	        var aa = new Date().getTime();
	        if (!l) {
	            l = 50
	        }
	        y();
	        W();
	        z();
	        V();
	        R(l);
	        var ab = new Date().getTime() - aa;
	    }

	    Q()
	}

	module.exports = JPEGEncoder;

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	// 保证按需加载的文件路径正确
	__webpack_require__.p = getJsDir('lrz') + '/';
	window.URL              = window.URL || window.webkitURL;

	var Promise          = __webpack_require__(1),
	    BlobFormDataShim = __webpack_require__(5),
	    exif             = __webpack_require__(6);


	var UA = (function (userAgent) {
	    var ISOldIOS     = /OS (\d)_.* like Mac OS X/g.exec(userAgent),
	        isOldAndroid = /Android (\d.*?);/g.exec(userAgent) || /Android\/(\d.*?) /g.exec(userAgent);

	    // 判断设备是否是IOS7以下
	    // 判断设备是否是android4.5以下
	    // 判断是否iOS
	    // 判断是否android
	    // 判断是否QQ浏览器
	    return {
	        oldIOS    : ISOldIOS ? +ISOldIOS.pop() < 8 : false,
	        oldAndroid: isOldAndroid ? +isOldAndroid.pop().substr(0, 3) < 4.5 : false,
	        iOS       : /\(i[^;]+;( U;)? CPU.+Mac OS X/.test(userAgent),
	        android   : /Android/g.test(userAgent),
	        mQQBrowser: /MQQBrowser/g.test(userAgent)
	    }
	})(navigator.userAgent);


	function Lrz (file, opts) {
	    var that = this;

	    if (!file) throw new Error('没有收到图片，可能的解决方案：https://github.com/think2011/localResizeIMG/issues/7');

	    opts = opts || {};

	    that.defaults = {
	        width    : null,
	        height   : null,
	        fieldName: 'file',
	        quality  : 0.7
	    };

	    that.file = file;

	    for (var p in opts) {
	        if (!opts.hasOwnProperty(p)) continue;
	        that.defaults[p] = opts[p];
	    }

	    return this.init();
	}

	Lrz.prototype.init = function () {
	    var that         = this,
	        file         = that.file,
	        fileIsString = typeof file === 'string',
	        fileIsBase64 = /^data:/.test(file),
	        img          = new Image(),
	        canvas       = document.createElement('canvas'),
	        blob         = fileIsString ? file : URL.createObjectURL(file);

	    that.img    = img;
	    that.blob   = blob;
	    that.canvas = canvas;

	    if (fileIsString) {
	        that.fileName = fileIsBase64 ? 'base64.jpg' : (file.split('/').pop());
	    } else {
	        that.fileName = file.name;
	    }

	    if (!document.createElement('canvas').getContext) {
	        throw new Error('浏览器不支持canvas');
	    }

	    return new Promise(function (resolve, reject) {
	        img.onerror = function () {
	            var err = new Error('加载图片文件失败');
	            reject(err);
	            throw err;
	        };

	        img.onload = function () {
	            that._getBase64()
	                .then(function (base64) {
	                    if (base64.length < 10) {
	                        var err = new Error('生成base64失败');
	                        reject(err);
	                        throw err;
	                    }

	                    return base64;
	                })
	                .then(function (base64) {
	                    var formData = null;

	                    // 压缩文件太大就采用源文件,且使用原生的FormData() @source #55
	                    if (typeof that.file === 'object' && base64.length > that.file.size) {
	                        formData = new FormData();
	                        file     = that.file;
	                    } else {
	                        formData = new BlobFormDataShim.FormData();
	                        file     = dataURItoBlob(base64);
	                    }

	                    formData.append(that.defaults.fieldName, file, (that.fileName.replace(/\..+/g, '.jpg')));

	                    resolve({
	                        formData : formData,
	                        fileLen : +file.size,
	                        base64  : base64,
	                        base64Len: base64.length,
	                        origin   : that.file,
	                        file   : file
	                    });

	                    // 释放内存
	                    for (var p in that) {
	                        if (!that.hasOwnProperty(p)) continue;

	                        that[p] = null;
	                    }
	                    URL.revokeObjectURL(that.blob);
	                });
	        };

	        // 如果传入的是base64在移动端会报错
	        !fileIsBase64 && (img.crossOrigin = "*");

	        img.src = blob;
	    });
	};

	Lrz.prototype._getBase64 = function () {
	    var that   = this,
	        img    = that.img,
	        file   = that.file,
	        canvas = that.canvas;

	    return new Promise(function (resolve) {
	        try {
	            // 传入blob在android4.3以下有bug
	            exif.getData(typeof file === 'object' ? file : img, function () {
	                that.orientation = exif.getTag(this, "Orientation");

	                that.resize = that._getResize();
	                that.ctx    = canvas.getContext('2d');

	                canvas.width  = that.resize.width;
	                canvas.height = that.resize.height;

	                // 设置为白色背景，jpg是不支持透明的，所以会被默认为canvas默认的黑色背景。
	                that.ctx.fillStyle = '#fff';
	                that.ctx.fillRect(0, 0, canvas.width, canvas.height);

	                // 根据设备对应处理方式
	                if (UA.oldIOS) {
	                    that._createBase64ForOldIOS().then(resolve);
	                }
	                else {
	                    that._createBase64().then(resolve);
	                }
	            });
	        } catch (err) {
	            // 这样能解决低内存设备闪退的问题吗？
	            throw new Error(err);
	        }
	    });
	};


	Lrz.prototype._createBase64ForOldIOS = function () {
	    var that        = this,
	        img         = that.img,
	        canvas      = that.canvas,
	        defaults    = that.defaults,
	        orientation = that.orientation;

	    return new Promise(function (resolve) {
	        !/* require */(/* empty */function() { var __WEBPACK_AMD_REQUIRE_ARRAY__ = [__webpack_require__(7)]; (function (MegaPixImage) {
	            var mpImg = new MegaPixImage(img);

	            if ("5678".indexOf(orientation) > -1) {
	                mpImg.render(canvas, {
	                    width      : canvas.height,
	                    height     : canvas.width,
	                    orientation: orientation
	                });
	            } else {
	                mpImg.render(canvas, {
	                    width      : canvas.width,
	                    height     : canvas.height,
	                    orientation: orientation
	                });
	            }

	            resolve(canvas.toDataURL('image/jpeg', defaults.quality));
	        }.apply(null, __WEBPACK_AMD_REQUIRE_ARRAY__));}());
	    });
	};

	Lrz.prototype._createBase64 = function () {
	    var that        = this,
	        resize      = that.resize,
	        img         = that.img,
	        canvas      = that.canvas,
	        ctx         = that.ctx,
	        defaults    = that.defaults,
	        orientation = that.orientation;

	    // 调整为正确方向
	    switch (orientation) {
	        case 3:
	            ctx.rotate(180 * Math.PI / 180);
	            ctx.drawImage(img, -resize.width, -resize.height, resize.width, resize.height);
	            break;
	        case 6:
	            ctx.rotate(90 * Math.PI / 180);
	            ctx.drawImage(img, 0, -resize.width, resize.height, resize.width);
	            break;
	        case 8:
	            ctx.rotate(270 * Math.PI / 180);
	            ctx.drawImage(img, -resize.height, 0, resize.height, resize.width);
	            break;

	        case 2:
	            ctx.translate(resize.width, 0);
	            ctx.scale(-1, 1);
	            ctx.drawImage(img, 0, 0, resize.width, resize.height);
	            break;
	        case 4:
	            ctx.translate(resize.width, 0);
	            ctx.scale(-1, 1);
	            ctx.rotate(180 * Math.PI / 180);
	            ctx.drawImage(img, -resize.width, -resize.height, resize.width, resize.height);
	            break;
	        case 5:
	            ctx.translate(resize.width, 0);
	            ctx.scale(-1, 1);
	            ctx.rotate(90 * Math.PI / 180);
	            ctx.drawImage(img, 0, -resize.width, resize.height, resize.width);
	            break;
	        case 7:
	            ctx.translate(resize.width, 0);
	            ctx.scale(-1, 1);
	            ctx.rotate(270 * Math.PI / 180);
	            ctx.drawImage(img, -resize.height, 0, resize.height, resize.width);
	            break;

	        default:
	            ctx.drawImage(img, 0, 0, resize.width, resize.height);
	    }

	    return new Promise(function (resolve) {
	        if (UA.oldAndroid || UA.mQQBrowser || !navigator.userAgent) {
	            !/* require */(/* empty */function() { var __WEBPACK_AMD_REQUIRE_ARRAY__ = [__webpack_require__(8)]; (function (JPEGEncoder) {
	                var encoder = new JPEGEncoder(),
	                    img     = ctx.getImageData(0, 0, canvas.width, canvas.height);

	                resolve(encoder.encode(img, defaults.quality * 100));
	            }.apply(null, __WEBPACK_AMD_REQUIRE_ARRAY__));}())
	        }
	        else {
	            resolve(canvas.toDataURL('image/jpeg', defaults.quality));
	        }
	    });
	};

	Lrz.prototype._getResize = function () {
	    var that        = this,
	        img         = that.img,
	        defaults    = that.defaults,
	        width       = defaults.width,
	        height      = defaults.height,
	        orientation = that.orientation;

	    var ret = {
	        width : img.width,
	        height: img.height
	    };

	    if ("5678".indexOf(orientation) > -1) {
	        ret.width  = img.height;
	        ret.height = img.width;
	    }

	    // 如果原图小于设定，采用原图
	    if (ret.width < width || ret.height < height) {
	        return ret;
	    }

	    var scale = ret.width / ret.height;

	    if (width && height) {
	        if (scale >= width / height) {
	            if (ret.width > width) {
	                ret.width  = width;
	                ret.height = Math.ceil(width / scale);
	            }
	        } else {
	            if (ret.height > height) {
	                ret.height = height;
	                ret.width  = Math.ceil(height * scale);
	            }
	        }
	    }
	    else if (width) {
	        if (width < ret.width) {
	            ret.width  = width;
	            ret.height = Math.ceil(width / scale);
	        }
	    }
	    else if (height) {
	        if (height < ret.height) {
	            ret.width  = Math.ceil(height * scale);
	            ret.height = height;
	        }
	    }

	    // 超过这个值base64无法生成，在IOS上
	    while (ret.width >= 3264 || ret.height >= 2448) {
	        ret.width *= 0.8;
	        ret.height *= 0.8;
	    }

	    return ret;
	};

	/**
	 * 获取当前js文件所在路径，必须得在代码顶部执行此函数
	 * @returns {string}
	 */
	function getJsDir (src) {
	    var script = null;

	    if (src) {
	        script = [].filter.call(document.scripts, function (v) {
	            return v.src.indexOf(src) !== -1;
	        })[0];
	    } else {
	        script = document.scripts[document.scripts.length - 1];
	    }

	    if (!script) return null;

	    return script.src.substr(0, script.src.lastIndexOf('/'));
	}


	/**
	 * 转换成formdata
	 * @param dataURI
	 * @returns {*}
	 *
	 * @source http://stackoverflow.com/questions/4998908/convert-data-uri-to-file-then-append-to-formdata
	 */
	function dataURItoBlob (dataURI) {
	    // convert base64/URLEncoded data component to raw binary data held in a string
	    var byteString;
	    if (dataURI.split(',')[0].indexOf('base64') >= 0)
	        byteString = atob(dataURI.split(',')[1]);
	    else
	        byteString = unescape(dataURI.split(',')[1]);

	    // separate out the mime component
	    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

	    // write the bytes of the string to a typed array
	    var ia = new Uint8Array(byteString.length);
	    for (var i = 0; i < byteString.length; i++) {
	        ia[i] = byteString.charCodeAt(i);
	    }

	    return new BlobFormDataShim.Blob([ia.buffer], {type: mimeString});
	}

	window.lrz = function (file, opts) {
	    return new Lrz(file, opts);
	};

	// 版本号来自package.json，构建时自动填充
	window.lrz.version = '__packageJSON.version__';

	module.exports = window.lrz;

	/**
	 *
	 * 　　　┏┓　　　┏┓
	 * 　　┏┛┻━━━┛┻┓
	 * 　　┃　　　　　　　┃
	 * 　　┃　　　━　　　┃
	 * 　　┃　┳┛　┗┳　┃
	 * 　　┃　　　　　　　┃
	 * 　　┃　　　┻　　　┃
	 * 　　┃　　　　　　　┃
	 * 　　┗━┓　　　┏━┛Code is far away from bug with the animal protecting
	 * 　　　　┃　　　┃    神兽保佑,代码无bug
	 * 　　　　┃　　　┃
	 * 　　　　┃　　　┗━━━┓
	 * 　　　　┃　　　　　 ┣┓
	 * 　　　　┃　　　　 ┏┛
	 * 　　　　┗┓┓┏━┳┓┏┛
	 * 　　　　　┃┫┫　┃┫┫
	 * 　　　　　┗┻┛　┗┻┛
	 *
	 */




/***/ }
/******/ ])
});
;
$(document).on("pageInit", "#main", function(e, pageId, $page) {
	init_auto_load_data();
	/*首页广告图轮播*/
	var mySwiper = new Swiper('.j-index-banner', {
	    speed: 400,
	    spaceBetween: 0,
	    pagination: '.swiper-pagination',
     	autoplay: 2500
	});
	/*商家设置头部列表*/
	var mySwiper = new Swiper('.j-sort_nav', {
	    speed: 400,
	    spaceBetween: 0,
	    pagination: '.sort-pagination'
	});
	var mySwiper = new Swiper('.j-index-lb', {
	    speed: 400,
	    spaceBetween: 0
	});
/*地址定位
if($.fn.cookie("cancel_geo")!=1)
	{
		if(navigator.geolocation)
		{
			 var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
			 navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
		}
	}
	*/
if($.fn.cookie("cancel_geo")!=1){
	position();
}
});



$(document).on("pageInit", "#nodata", function(e, pageId, $page) {

    if (typeof suijump === 'function') {
        suijump();
    }
});
$(document).on("pageInit", "#notice_index", function(e, pageId, $page) {

init_list_scroll_bottom();
});



/**
 * Created by Administrator on 2016/11/14.
 */

$(document).on("pageInit", "#uc_order", function(e, pageId, $page) {

    var _width=$(".buttons-tab .tab-link.active").find("span").width();
    var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;

    var btm_line=$(".buttons-tab .bottom_line");
    btm_line.css({"width":_width+"px","left":_left+"px"});

    var _tabs=$(".tabBox .tab_box");
	var tab_link=new Array();
	tab_link[0] = true;
	tab_link[1] = true;
	tab_link[2] = true;
	tab_link[3] = true;
	tab_link[4] = true;
    $(".buttons-tab .tab-link").click(function () {
        $(document).off('infinite', '.infinite-scroll-bottom');
    	$(".content").scrollTop(1);
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;
        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();
        //加载内容
        if($.trim($(".j_ajaxlist_"+_index).html())==""&&tab_link[_index]){
			tab_link[_index]=false;
            var ajax_url =url[_index];
            $.ajax({
                url:ajax_url,
                type:"POST",
                success:function(html)
                {
                    //alert($(html).find(".j_ajaxlist_"+_index).html());
                    $(".j_ajaxlist_"+_index).append($(html).find(".j_ajaxlist_"+_index).html());
                    manageOrder();

                    //$(ajaxlist).find(".pages").html($(html).find(ajaxlist).find(".pages").html());
                    //init_listscroll(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
                	if($(".content").scrollTop()>0){
                		init_listscroll(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
                	}
                },
                error:function()
                {
                    $(".j_ajaxlist_"+_index).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
                }
            });
        }else{
        	if($(".content").scrollTop()>0){
                infinite(".j_ajaxlist_"+_index,".j_ajaxadd_"+_index,"",manageOrder);
        	}
        }
        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");

        var swiperBox=_tabs.eq(_index).find(".j-order-lamp");


        var swiper = new Swiper(swiperBox, {
            scrollbarHide: true,
            slidesPerView: 'auto',
            centeredSlides: false,
            observer:true,
            grabCursor: true
        });
    });
    function manageOrder(){
        $(".manage-order").unbind("click").bind("click",function(){
              var message=$(this).attr("message");
              var url=$(this).attr("ajaxUrl");
             $.confirm(message, function () {
                 $.showIndicator();
                 $.ajax({
                     url:url,
                     dataType:"json",
                     success:function(data){
                         if(data.status==0){
                             $.toast(data.info);
                         }else{
//                             $.alert(data.info,function(){
//                                 window.location.href=data.jump;
//                             })
                        	 $.toast(data.info);
                        	 window.setTimeout(function(){
                        		 window.location.href=data.jump;
         					},1500);
                         }
                     }
                 });
             });
        });
    }
    var swiperm = new Swiper(".j-order-lamp1", {
        scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        observer:true,
        grabCursor: true
    });
    init_listscroll(".j_ajaxlist_"+pay_status,".j_ajaxadd_"+pay_status,"",manageOrder);
    manageOrder();

    
});
/**
 * Created by Administrator on 2016/11/16.
 */
$(document).on("pageInit", "#uc_order_dp", function(e, pageId, $page) {
   $(".j-stars .iconfont").click(function () {
       var _index=$(this).index();
       var val=parseInt(_index)+1;
       $(this).parent().parent().find(".star").val(val);
       var $icon=$(this).parent().find(".iconfont");
        $icon.each(function (i) {
           if(i<=_index){
               $icon.eq(i).addClass("active");
           }else {
               $icon.eq(i).removeClass("active");
           }
        });

   });
   
   $(".send_dp").click(function(){
	   $("form[name='dp_submit_form']").unbind("submit");
	   do_dp_form();
	});
   function do_dp_form()
   {
		$("form[name='dp_submit_form']").bind("submit",function(){
			
			var evaluate=$(this).find(".evaluate li");
			
			for(var i=0;i<evaluate.length;i++){
				if(evaluate.eq(i).find(".dp_centent").val()==""){
					$.toast("请填写评价内容");
					return false;
				}else if(evaluate.eq(i).find(".star").val()==""){
					$.toast("请选择评分");
					return false;
				}
			}
			
			/*var i=0;
			$(this).find(".dp_centent").each(function(){
				if($.trim($(this).val())==""){
					$.toast("请填写评价内容");
					return;
				}
				i++;
			});
			$(this).off;
			var k=0;
			$(this).find(".star").each(function(){
				if($.trim($(this).val())==""){
					$.toast("请选择评分");
					return;
				}
				k++;
			});*/
			
//			if(i>=$(this).find(".dp_centent").length  && k>=$(this).find(".star").length){
				var url = $(this).attr("action");
				var query = $(this).serialize();
				$.ajax({
					url:url,
					data:query,
					dataType:"json",
					type:"POST",
					success:function(obj){
						$.toast(obj.info);
						$("form[name='dp_submit_form']").unbind("submit");
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1000);
						}
					}
				});
//			}

			return false;
		});
	}
});
$(document).on("pageInit", "#order_view", function(e, pageId, $page) {
    $(".cancel_order").unbind("click").bind("click",function(){
        var message=$(this).attr("message");
        var url=$(this).attr("ajaxUrl");
		var button_type=$(this).attr("button-type");
        $.confirm(message, function () {
            $.showIndicator();
            $.ajax({
                url:url,
                dataType:"json",
                success:function(data){
                    if(data.status==0){
                        $.toast(data.info);
                    }else{
//                        $.alert(data.info,function(){
//                            window.location.href=data.jump;
//                        })
                    	$.toast(data.info);
                   	 	window.setTimeout(function(){
							if(button_type=="j-cancel"){
								window.location.href=location.href;
							}else{
								window.location.href=data.jump;
							}
    					},1500);
                    }
                }
            });
        });
    });

});
/**
 * Created by Administrator on 2016/9/7.
 */

$(document).on("pageInit", "#pay", function(e, pageId, $page) {
	count_order_total();
	function count_order_total_change(){
		$("input[name='all_account_money']").unbind('change');
		$("input[name='all_account_money']").bind("change",function () {


			if($("#all_account_money").hasClass("active")){
				$("#all_account_money").removeClass("active");
			}else{
				$("#all_account_money").addClass("active");
			}
			$("input[name='payment']").prop("checked",false);
			count_order_total();

		});

		$(".payment").unbind("click");
		$(".payment").bind("click",function(){
			$("input[name='payment']").prop("checked",false);
			$(this).siblings("input[name='payment']").prop("checked",true);
			$("#all_account_money").removeClass("active");
			count_order_total();
		});
		$(".u-sure-pay.j_pay_button").unbind("click");
		$(".u-sure-pay.j_pay_button").bind("click",function(){

			submit_order($(this));

		});
	}
	function count_order_total()
	{
		var is_ajax = 1;
		var query = new Object();

		//全额支付
		if($("#all_account_money").hasClass("active"))
		{
			query.all_account_money = 1;
		}
		else
		{
			query.all_account_money = 0;
		}

		//支付方式
		var payment = $("input[name='payment']:checked").val();
		if(!payment)
		{
			payment = 0;
		}
		var rel=$("input[name='payment']:checked").attr("rel");
		query.payment = payment;
		query.rel = rel;
		query.id = order_id;
		query.is_ajax = is_ajax;
		query.act = "pay";
		$.ajax({
			url: CART_URL,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				$(".content").html(data.html);
				count_order_total_change();
			},
			error:function(ajaxobj)
			{
//    			if(ajaxobj.responseText!='')
//    			alert(LANG['REFRESH_TOO_FAST']);
			}
		});
	}
	function submit_order(obj)
	{
		
		$(obj).removeClass('j_pay_button');
		var is_ajax = 1;
		var query = new Object();

		//全额支付
		if($("#all_account_money").hasClass("active"))
		{
			query.all_account_money = 1;
		}
		else
		{
			query.all_account_money = 0;
		}

		//支付方式
		var payment = $("input[name='payment']:checked").val();
		if(!payment)
		{
			payment = 0;
		}
		var rel=$("input[name='payment']:checked").attr("rel");
		query.payment = payment;
		query.rel = rel;
		query.id = order_id;
		query.is_ajax = is_ajax;
		query.act = "order_done";
		$.ajax({
			url: CART_URL,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.status==1){

					if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
						if(data.pay_status==1){
							$.router.load(data.jump, true);
						}else{
							location.href=data.jump;
						}
					} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
						 $.router.load(data.jump, true);

					} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
						if(data.online_pay==3){
							try {

								var str = pay_sdk_json(data.sdk_code);
								App.pay_sdk(str);
								$(obj).addClass('j_pay_button');
							} catch (ex) {

								$.toast(ex);
								$.loadPage(location.href);
							}
						}else{
							var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

							try {
								App.open_type(pay_json);
								$.confirm('已支付完成？', function () {
									$.loadPage(location.href);

								},function(){
									$.loadPage(location.href);

								});
							} catch (ex) {
								$.toast(ex);
								$.loadPage(location.href);
							}
						}
					}



				}else{
					
					$.alert(data.info);
					$(obj).addClass('j_pay_button');
				}

			},
			error:function(ajaxobj)
			{
				$(obj).addClass('j_pay_button');

			}
		});
	}
	

	
});


/**
 * Created by Administrator on 2016/10/11.
 */
$(document).on("pageInit", "#payment_done", function(e, pageId, $page) {
	loadScript(jia_url);
	
    var lent=$(".order-replay").length;
    if (lent>3){
        $(".loadMore").show();
    }else if(lent<=3){
        $(".loadMore").hide();
    }



    var _click=0;
    $(".loadMore").click(function () {
        _click++;
        if(_click==1){
            $(".down_btn").show();
            $(".up_btn").hide();
            $(".j-moreThan").show();
        }
        if(_click==2){
            $(".down_btn").hide();
            $(".up_btn").show();
            $(".j-moreThan").hide();
        }
        if(_click>=2){
            _click=0;
        }

    });



    $(".j-showCode").click(function () {
        $(".codeShowBox").addClass("codeShow");
        $(".codeImgBox").removeClass("transi").addClass("trans");
        var $this=$(this);
        var codeNum=$this.parents(".order-replay").find(".j-codeNum").text();
        var codeSrc=$this.parents(".order-replay").find(".hiddenBox").attr("data-src");
        $(".codeShowBox .codeName").text(codeNum);
        $(".codeShowBox .codeImg").attr("src",codeSrc);
    });

    $(".blackBox").click(function () {
        $(".codeImgBox").removeClass("trans").addClass("transi");
        setTimeout(function () {
            $(".codeShowBox").removeClass("codeShow");
        },150);
    });
});

/**
 * 
 */
$(document).on("pageInit", "#payment_done", function(e, pageId, $page){
	$(".deal-share").click(function(){
		var url_data=$(this).attr("url");
		var that = this;
		if($("#share").is(':checked')){
			var query = new Object();
			query.act="order_shere";
			query.id = id;
			query.url_data
				$.ajax({
					url:AJAX_URL,
					data:query,
					dataType:"json",
					type:"post",
					global:false,
					success:function(obj){
						if(obj.status){
							if (app_index == 'app') {

							//	var pay_json = '{"id":"830","url":"'+data.jump+'","title":"'+data.title+'"}';
								App.app_detail(type,json_parma);
							} else {

								$.router.load(url_data, true);
							}
						}else{
							$.toast(obj.info);
						}
					}
				});
		}else{

		 	if (app_index == 'app') {
	

		 		if(type > 0){

		 			//App.app_detail(type,json_parma);
		 		}else{
		 			$.router.load(url_data, true);
		 		}
		 	} else {

				$.router.load(url_data, true);
		 	}
		}
	});
});
$(document).on("beforePageSwitch", "#publish", function(e, pageId, $page) {
	upfile_data = [];
});

$(document).on("pageInit", "#publish", function(e, pageId, $page) {
	$(".add_expression").addClass("curr");
	bind_publish_item_textarea_set_expression();

	if (ifimgup==0){
		imgup();//图片上传
		ifimgup=1;
	};

	var mySwiper = new Swiper('.swiper-container')
	 /*表单提交事件*/
	 $("form[name='publish_form']").submit(function(){
		var form = $("form[name='publish_form']");
		var content = $("#publish_item_textarea").val();
		if(content.length>0){
			 $(".publish_btn").css("background-color","#6D6D6D");
			 $(".publish_btn").attr("disabled","disabled");
			 var url = $(form).attr("action");
			 var query = new Object();
			 query.content = content;
			 query.img_data = upfile_data;
			 $.ajax({
				url:url,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					$.toast(data.info);
					if (data.status) {
						setTimeout(function() {
							$.router.load(data.jump, true);
						}, 2000);
					}	
				},
				error:function(){
					$.toast("服务器提交错误");
				}
			});
			/*setTimeout(function() {
				$(".publish_btn").css("background-color", '');
				$(".publish_btn").removeAttr("disabled");
			}, 2000);*/
			
			return false;
		}else{
			$.toast("发表的内容不能为空");
		}
		 return false;
	 });
});



/*图片上传*/
var ifimgup=0;
function imgup(){
	var img_index = 0;	
	$("#file-btn").live("change",function(){
		if(this.files[0].type=='image/png'||this.files[0].type=='image/jpeg'||this.files[0].type=='image/gif'){	 
		 	img_box_show();
		 	var demo_box = $(".img-show-box");
	     	var item_box = '<div class="img_load img-item img-index-'+img_index+'" data-index="'+img_index+'"><img src="'+LOADING_IMG+'"></div>';
	     	if($(".img-show-box .img-item").length >0){
	     		$(".img-show-box .img-item").last().after(item_box);
	     		if($(".img-show-box .img-item").length==3){
	     			$(".img-show-box .item-add").remove();
	     		}
	     	}else{
	     		demo_box.html(item_box);
	     		$(".add_img .file-btn").remove();
	     		$(".img-show-box").append('<div class="item-add"><img src="'+add_img_icon+'"/><input class="file-btn" id="file-btn" type="file" capture="camera" /></div>');
	     		$(".add_img").bind("click",function(){img_box_show();});
	     	}
	        lrz(this.files[0], {width:1200, height:900})
		        .then(function(results) {
	        		var data = {
	                    base64: results.base64,
	                    size: results.base64Len // 校验用，防止未完整接收
	                };
	        		upfile_data[img_index] = JSON.stringify(data);
	        		// console.log(img_index);
	        		// console.log(upfile_data.length);
	        		demo_report(results.base64, results.origin.size);
	        		img_index++;
		        })
		        .catch(function(err) {
		        	$.toast('图片获取失败');
		        })
	 	}else{
	 		$.toast("上传的文件格式有误");
	 	}
	});
	 
}
/*图片base64 数组*/
var upfile_data = new Array();
function demo_report(base64,size) {
    var img = new Image();

    if(size === 'NaNKB') size = '';
    if(size>0){
    	var span_html = '<span class="item_span" style="background-image: url('+base64+');background-size: cover;background-position: 50% 20%;background-repeat: no-repeat;"></span><a class="close-btn" href="javascript:void(0);" onclick="del_img_box(this)"><i class="iconfont" style="font-size:0.65rem;">&#xe635;</i></a>';
    	$(".img_load").html(span_html);
    	$(".img_load").removeClass('img_load');
    	
    }
}

function add_img(){
	img_box_show();
	if(type==1 && $(".img-show-box .img-item").length<3){
		return $("#file-btn").click();
	}
	if($(".img-show-box .img-item").length == 0){
 		return $("#file-btn").click();
 	}
}

function add_expression(){
	expression_show();
}
function expression_show(){
	$(".add_expression").addClass("curr");
	$(".expression").show();
	$(".add_img").removeClass("curr");
	$(".img-show-box").hide();
}
function img_box_show(){
	$(".add_img").addClass("curr");
	$(".img-show-box").show();
	$(".add_expression").removeClass("curr");
	$(".expression").hide();
}

function del_img_box(obj){
	var index = $(obj).parent().attr("data-index");
	delete upfile_data[index];
	$(".img-index-"+index).remove();
	setTimeout(function(){
		$(".img-index-"+index).remove();
		if($(".img-show-box .img-item").length<3 && $(".img-show-box .item-add").length==0){
			$(".img-show-box").append('<div class="item-add"><img src="'+add_img_icon+'"/><input class="file-btn" id="file-btn" type="file" capture="camera" /></div>');
		}
	},500);
}


/*表情事件*/
function bind_publish_item_textarea_set_expression()
{
	$(".emotion_publish_item_textarea").find("a").bind("click",function(){
		var o = $(this);
		insert_publish_item_textarea_cnt("["+$(o).attr("rel")+"]");	
	});
	
}

function insert_publish_item_textarea_cnt(cnt)
{
	var val = $("#publish_item_textarea").val();
//	var pos = $("#publish_item_textarea").attr("position");
//	var bpart = val.substr(0,pos);
//	var epart = val.substr(pos,val.length);
//	$("#publish_item_textarea").val(bpart+cnt+epart);
	$("#publish_item_textarea").val(val+cnt);
	// $.weeboxs.close("form_pop_box");
	
}


/**
 * Created by Administrator on 2016/10/14.
 */
$(document).on("pageInit", "#user_register", function(e, pageId, $page)  {
	clear_input($('#phonenumer'),$('.j-phone-clear'));
	clear_input($('#sms_verify'),$('.j-verify-clear'));
	clear_input($('#password'),$('.j-password-clear'));

    var _cli=0;
    $(".eyes").click(function () {
        _cli++;

        if(_cli==1){
            $(".eyes-no").hide();
            $(".eyes-yes").show();
            $(".password").attr("type","text");
        }
        if(_cli==2){
            $(".eyes-no").show();
            $(".eyes-yes").hide();
            $(".password").attr("type","password");
        }
        if(_cli>=2){
            _cli=0;
        }
    });

    $(".userBtn-yellow").click(function () {
    	$("#ph_register").submit();
    });
    //手机注册
    $("#ph_register").bind("submit",function(){
		
		var mobile = $.trim($(this).find("input[name='user_mobile']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());
		if(mobile=="")
		{
			$.toast("请输入手机号");
			return false;
		}
		if(user_pwd=="")
		{
			$.toast("请输入密码");
			return false;
		}
		if(sms_verify=="")
		{
			$.toast("请输入收到的验证码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$("#prohibit").show();
					$.toast(obj.info);
					window.setTimeout(function(){
						location.href = obj.jump;
						},1500);
				}
				else
				{
					$.toast(obj.info);
				}
			}
		});
		
		return false;
	});


    /*var _input=$("input");
    _input.each(function (e) {
        $(this)[0].addEventListener("blur",function () {
            
            document.querySelector(".third-login").style.display="block";
        },false);

        $(this)[0].addEventListener("focus",function () {
            document.querySelector(".third-login").style.display="none";
        },false);
    });*/

});
$(document).on("pageInit", "#rsorder_index", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	//打开评论
	$(document).on('click', '.j-open-comment', function() {
		$(".img-comment-1").attr("src",$(this).parents('li').find(".img-comment").attr('src'));
		$(".name-comment-1").html($(this).parents('li').find(".name-comment").html());
		$("input[name='order_id_1']").val($(this).parents('li').find("input[name='order_id']").val());
		$("input[name='location_id_1']").val($(this).parents('li').find("input[name='location_id']").val());
		$.popup('.popup-comment');
	});
	//关闭当前弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	});
	$(".comment-stars").on('click', '.j-point', function() {
		$(".j-point").removeClass('active');
		$(this).addClass('active');
		$(this).prevAll().addClass('active');
		$("#star-value").attr('value', $(this).attr('value'));
	});
	
	
	//发表评论
	$('.j-comment-sub').bind('click',function(){
		
    	var is_pass=1;
    	var dp_points=$("#star-value").val();
			if(dp_points==0){
				$.toast('请给出您宝贵的评分！');
				is_pass=0;
				return false;
			}
    	if(is_pass==1){

	    	if($("textarea[name='content']").val()==''){
	    		$.toast('请填写您的宝贵意见！');
	    		is_pass=0;
	    		return false;
	    	}
    	}
    		
		if(is_pass==0){
			return false;
		}


    	var url=$(this).attr('action');
    	
		var query = new Object();
		query.location_id = $("input[name='location_id_1']").val();
		query.order_id = $("input[name='order_id_1']").val();
		 
		query.dp_points=dp_points;

    	query.content = $("textarea[name='content']").val(); 
    	query.is_rs = 1;
     	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json', 
			success:function(data){
			
				if(data.status==1){
				$.showIndicator();
			      setTimeout(function () {
			    	  close_comment();
			      }, 2000);
					
				}else{
					$.toast(data.info);
				}
				
				function close_comment(){
					$.toast(data.info);
					$(".popup-comment").removeClass('modal-in').addClass('modal-out');
					$.hideIndicator();
					$(".j-point").removeClass('active');
					$("#star-value").attr('value', '');
					$("textarea[name='content']").val('');
					var AJAX_URL = data.jump;
					var is_ajax  = 1;
					$.ajax({
						url:AJAX_URL,
						data:{"is_ajax":is_ajax},
						type:'post',
						dataType:'json', 
						success:function(obj){
							$(".infinite-scroll-bottom").html(obj.html);
							init_list_scroll_bottom();//下拉刷新加载
						}
					});
				}
			}
    	});
    	   
          
    });
	
	$(document).on('click','.to-pay',function(){
		var url = $(this).attr('data_url');
		var jump_url = $(this).attr('jump_url');
		var query = new Object();
		query.is_rs = 1;
		$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					location.href = jump_url;
				}else{
					$.toast(data.info);
				}
			}
		});
		
	});
	
});
$(document).on("pageInit", "#rsorder_view", function(e, pageId, $page) {
	//打开评论
	$(document).on('click', '.j-open-comment', function() {
		$(".img-comment-1").attr("src",$(".img-comment").attr('src'));
		$(".name-comment-1").html($(".name-comment").html());
		$("input[name='order_id_1']").val($("input[name='order_id']").val());
		$("input[name='location_id_1']").val($("input[name='location_id']").val());
		$.popup('.popup-comment');
	});
	//关闭当前弹层
	$(document).on('click', '.j-close-popup', function() {
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
	});
	$(".comment-stars").on('click', '.j-point', function() {
		$(".j-point").removeClass('active');
		$(this).addClass('active');
		$(this).prevAll().addClass('active');
		$("#star-value").attr('value', $(this).attr('value'));
	});
	
	
	
	//发表评论
	$('.j-comment-sub').bind('click',function(){
		
    	var is_pass=1;
    	var dp_points=$("#star-value").val();
			if(dp_points==0){
				$.toast('请给出您宝贵的评分！');
				is_pass=0;
				return false;
			}
    	if(is_pass==1){

	    	if($("textarea[name='content']").val()==''){
	    		$.toast('请填写您的宝贵意见！');
	    		is_pass=0;
	    		return false;
	    	}
    	}
    		
		if(is_pass==0){
			return false;
		}


    	var url=$(this).attr('action');
    	
		var query = new Object();
		query.location_id = $("input[name='location_id_1']").val();
		query.order_id = $("input[name='order_id_1']").val();
		
		query.dp_points=dp_points;

    	query.content = $("textarea[name='content']").val(); 
    	$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json', 
			success:function(data){
			
				if(data.status==1){
//				$.showIndicator();
				$.toast(data.info);
			      setTimeout(function () {
			    	  close_comment();
			      }, 2000);
					
				}else{
					$.toast(data.info);
				}
				
				function close_comment(){
					location.reload(); 
					$(".popup-comment").removeClass('modal-in').addClass('modal-out');
					$.hideIndicator();
					$(".j-point").removeClass('active');
					$("#star-value").attr('value', '');
					$("textarea[name='content']").val('');
					
//					var AJAX_URL = data.jump;
//					var is_ajax  = 1;
//					$.ajax({
//						url:AJAX_URL,
//						data:{"is_ajax":is_ajax},
//						type:'post',
//						dataType:'json', 
//						success:function(obj){
//							$(".infinite-scroll-bottom").html(obj.html);
//							init_list_scroll_bottom();//下拉刷新加载
//						}
//					});
				}
			}
    	});
    	   
          
    });
	var lock = true;
	
	$(".dc-view-bar").on('click', '.j-cancle', function() {
		if(!lock){
			return;
		}else{
			lock = false;
			var url = $(this).attr('data_url');
			var query = new Object();
			//取消订单
			$.confirm('确定要取消订单吗？', function () {
		          $.ajax({
		        	  url:url,
		        	  type:'post',
		        	  dataType:'json',
		        	  success:function(data){
		        		  if(data.status == 1){
		        			  $.toast(data.info);
		        			  setTimeout(function () {
		        				  location.reload(); 
		    			      }, 2000);
		        			  
		        		  }else{
		        			  $.confirm(data.info,function(){
		        				  lock = true;
		        				  window.location.href = "tel:"+data.location_tel;
		        			  },function(){
		        				  location.reload(); 
		        			  });
		        		  }
		        	  }
		          });
		      },function () {
	        	  lock = true;
	          });
		}
	});
	
	$(".dc-view-bar").on('click','.to-pay',function(){
		var url = $(this).attr('data_url');
		var jump_url = $(this).attr('jump_url');
		var query = new Object();
		query.is_rs = 1;
		$.ajax({
			url:url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					location.href = jump_url;
				}else{
					$.toast(data.info);
				}
			}
		});
		
	});
	
});
$(document).on("pageInit", "#scores", function(e, pageId, $page) {
    init_list_scroll_bottom();
});
$(document).on("pageInit", "#scores_index", function(e, pageId, $page) {
	init_auto_load_data();
	 var mySwiper = new Swiper ('.j-score-type', {
		scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        grabCursor: true
	});
	 
	$(".signin").live("click",function(){
        var query = new Object();
        query.act="signin";
        $.ajax({
                url: INDEX_URL,
                data: query,
                type: "POST",
                dataType: "json",
                success: function (obj) {
                	if(obj.status==1){
                		$(".sign").removeClass('signin').find("span").html(obj.info);
                		$(".sign-day").html(obj.sign_info);
                		$(".user-info .score em").html(obj.score);
                		
                	}else{
                		$.alert(obj.info);
                	}
                },
        });
	});
	$(".ulogin").unbind("click");
	$(".ulogin").bind("click",function () {
		if(is_login==0){
			if(app_index=="app"){
				App.login_sdk();
			}else{
				$.router.load(login_url, true);
			}
		}
	});



});


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
/**
 * Created by Administrator on 2016/11/4.
 */

$(document).on("pageInit", "#login_out", function(e, pageId, $page) {

   $(".fun-check-login").bind("click",function () {
       if(app_index=='app'){
           App.login_sdk();
       }
   });
   
   $(document).on('click','.open-about', function () {
	   $.popup('.popup-about');
   });
   
});
$(document).on("pageInit", "#shop", function(e, pageId, $page) {
	init_auto_load_data();
	var mySwiper = new Swiper('.j-index-banner', {
		speed: 400,
		spaceBetween: 0,
		pagination: '.swiper-pagination',
		autoplay: 2500
	});
	var mySwiper = new Swiper('.j-index-lb', {
	    speed: 400,
	    spaceBetween: 0,
		autoplay: 2500
	});
/*商家设置头部列表*/
var mySwiper = new Swiper('.j-sort_nav', {
    speed: 400,
    spaceBetween: 0
});

});
$(document).on("pageInit", ".page", function(e, pageId, $page) {
	var lesstime = 0;
	is_bind_ts=0;
	time($("#btn"));
	if ($('#phonenumer').val() == '') {
		$("#btn").addClass("noUseful").removeClass("isUseful");
	}
	
	/*手机号码正则验证*/
	
	if($("#phonenumer").length>0){
	    document.getElementById("phonenumer").oninput=function () {
	    	if(parseInt($("#btn").attr("lesstime"))==0){
	    		var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
	
	            var text=$(this).val();
	            if(reg.test(text)){
	                $(".j-sendBtn").addClass("isUseful").removeClass("noUseful");
	                $(".j-sendBtn").prop("disabled",false);
	                /*发送验证码倒计时*/
	                $(".j-sendBtn .isUseful").click(function () {
	                	do_send($("#btn"));
	                });
	            }
	            else {
	                $(".j-sendBtn").addClass("noUseful").removeClass("isUseful");
	                $(".j-sendBtn").prop("disabled", true);
	            }
	    	} 
	    };
	}
	//$("#btn").bind("click",function(){
		//alert("111");
	//	do_send($("#btn"));
	//});
	 
	$("#verify_image_box").find(".verify_close_btn").bind("click",function(){
        $("#verify_image_box").hide();
    });
});
var is_bind_ts=0;
function do_send(btn)
{
	if($.trim($("#phonenumer").val())=="")
	{
		$.toast("请输入手机号码");
		return false;
	}
	if(lesstime>0)return;
	var query = new Object();
	query.mobile = $("#phonenumer").val();
	query.act = "send_sms_code";
	query.unique = $(btn).attr("unique");
	query.verify_code = (btn).attr("verify_code");
	$.ajax({
		url:AJAX_URL,
		data:query,
		type:"POST",
		dataType:"json",
		success:function(obj){
			if(obj.is_bind&&is_bind_ts==0){
				$.alert(obj.bind_ts,function(){
					is_bind_ts=1;
				});
			}
			if(obj.status==1)
			{
				$(btn).attr("lesstime",obj.lesstime);
				time($("#btn"));
				$.toast(obj.info);
				
			}
			else
			{
				if(obj.status==-1)
				{
					$("#verify_image_box .verify_form_box .verify_content").html("");
                    var html_str = '<div class="v_input_box"><input type="text" class="v_txt" placeholder="图形码" id="verify_image"/><img src="'+obj.verify_image+"&r="+Math.random()+'"  /></div>'+
                                    '<div class="blank"></div><div class="blank"></div>'+
                                    '<div class="v_btn_box"><input style="-webkit-appearance: none;"  type="button" class="v_btn" name="confirm_btn" value="确认"/></div>';
                    $("#verify_image_box .verify_form_box .verify_content").html(html_str);
                    $("#verify_image_box").show();

					$("#verify_image_box").find("img").bind("click",function(){
						$(this).attr("src",obj.verify_image+"&r="+Math.random());
					});
					$("#verify_image_box").find("input[name='confirm_btn']").unbind("click");
					$("#verify_image_box").find("input[name='confirm_btn']").bind("click",function(){
						var verify_code = $.trim($("#verify_image_box").find("#verify_image").val());
						if(verify_code=="")
						{
							$.toast("请输入图形验证码");
						}
						else
						{
							$(btn).attr("verify_code",verify_code);
							$("#verify_image_box .verify_form_box .verify_content").html("");
                            $("#verify_image_box").hide();
                            do_send(btn);

						}
					});
					if($(btn).attr("verify_code")&&$(btn).attr("verify_code")!="")
					{
						$.alert(obj.info,function(){
							$(btn).attr("verify_code","");
						});
					}
				}
				else
				{
					$.toast(obj.info);
				}
				
			}
		}
	});
}
function time(obj) {
	wait=parseInt(obj.attr("lesstime"));
    if (wait == 0) {
        obj.prop("disabled",false);
        obj.addClass("isUseful").removeClass("noUseful");
        obj.val("发送验证码");
        obj.attr("lesstime",0);
        $(".j-sendBtn.isUseful").click(function () {
        	do_send($("#btn"));
        });
        $("#btn").attr("verify_code","");
        //wait = 60;
    } else {
        obj.prop("disabled", true);
        obj.addClass("noUseful").removeClass("isUseful");
        obj.val("重新发送(" + (wait-1) + ")");
        obj.attr("lesstime",wait-1);
        //wait--;
        setTimeout(function() {
                time(obj)
            }, 1000);
    }
}
$(document).on("pageInit", "#store", function(e, pageId, $page) {
	qrcode_box();
	$(".j-open-store-detail").click(function(){
		var con_height = $(".content").height();
		var top_height = $(".banner-con").height();
		var margin_height = parseInt($(".m-store-banner").css("margin-bottom").replace("px"));
		var height = parseInt(con_height) - parseInt(top_height) -margin_height;
		if($(".store-detail-info").height() == 0){
			$(".store-detail-info").height(height);
			$(this).addClass("isOver");
			setTimeout('$(".other-content").addClass("hide");',200);
			$(".store-detail-info").scroller('refresh');
			$(".content").scroller('refresh');
			$(".store-detail-info").scrollTop(0);
		}else{
			$(".store-detail-info").height(0);
			$(this).removeClass("isOver");
			$(".other-content").removeClass("hide");
			$(".store-detail-info").scroller('refresh');
		}
	});


	$(".youhui-item").bind("click",function(){
		var url=$(this).attr("url");
		$.ajax({
			url: url,
			dataType: "json",
			type: "POST",
			success: function(obj){
				if(obj.status==0){
					$.toast(obj.info);
					if(obj.jump){
						$.router.load(obj.jump, true);
					}
				}else if(obj.status==1){
					$.toast(obj.info);
				}
			},
			error:function()
			{
				$.toast("服务器提交错误");
			}
		});
	});



});
$(document).on("pageInit", "#stores", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	//星星评分
	$(".stores-item").each(function(){
	    $(this).find(".start-num").css("width",$(this).find(".start-num").parent().parent().attr("data")+"%");
	});
	//隐藏数量为0的2级分类
	/*$(".goods-num").filter(function(index){
　　　　return $(this).text()=="0";
　　	}).parent().hide();*/
	screen_bar();
	if(address==""){
		position();
	}
	$(".address-info").click(function() {
		position();
	});
});




$(document).on("pageInit", "#store_pay_index", function(e, pageId, $page) {
    $("input[name='money']").val('');
    $("input[name='other_money']").val('');
    $('.discount_money .integer').text('0');
    $('.discount_money .point').text('00');
    $('.actual_pay .integer').text('0');
    $('.actual_pay .point').text('00');
	count_amount();
    init_money_change();
    order_submit();
});

// 监听输入金额的变动
function init_money_change(){

    $("input[name='money']").bind('input propertychange', function() {
        pre_check('money');
        count_amount();
    });

    $("input[name='other_money']").bind('input propertychange', function() {
        pre_check('other_money');
        count_amount();
    });
	$("input[name='all_score']").bind('change', function() {
		count_amount();
	});
}

function pre_check(type) {
    var money = $.trim($("input[name='"+type+"']").val());
    var pattern = /^(\d+(\.\d{0,2})?)/;
    if (money != '') {
        var re = money.toString().match(pattern);
        $("input[name='"+type+"']").val(re[1]);
    }
}


// 计算最终应支付的金额
function count_amount() {
    var final_pay = 0;
    var money = $.trim($("input[name='money']").val());
    var other_money = $.trim($("input[name='other_money']").val());
    /*var pattern = /^(\d+(\.\d{0,2})?)$/;
    if (money != '' && !pattern.test(money)) {
        $.toast('输入的金额格式错误');
        money = 0;
    }
    if (other_money != '' && !pattern.test(other_money)) {
        $.toast('输入的金额格式错误');
        other_money = 0;
    }*/
    // money = Number(money).toFixed(2);
    var pay_money = money - other_money;
    var discount = count_discount(pay_money);
    final_pay = money - discount;
	//积分抵现new
	if(pay_money>0){
		var query = new Object();
		query.pay_money=pay_money;
		query.final_pay=final_pay;
		if($('input[name="all_score"]:checked').length>0){
			var all_score=1;
		}else{
			var all_score=0;
		}
		query.all_score=all_score;
		query.act='score_purchase_count';
		$.ajax({ 
			url: custom_ajax_url,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.score_purchase_switch==1&&data.exchange_money>0){
					$(".score_purchase").show();
					$(".score_purchase .u-score").text(data.user_score);
					$(".score_purchase .u-use-score").text(data.user_use_score);
					$(".score_purchase .u-money").text(data.exchange_money);
					if(all_score ==1){
						final_pay = final_pay - data.exchange_money;
					}
					count_amount_continuation(discount,final_pay);
				}else{
					$("input[name='all_score']").prop("checked",false);
					$(".score_purchase").hide();
					count_amount_continuation(discount,final_pay);
				}
			},
			error:function(ajaxobj)
			{
				$("input[name='all_score']").prop("checked",false);
				$(".score_purchase").hide();
				count_amount_continuation(discount,final_pay);
			}
		});
	}else{
		$("input[name='all_score']").prop("checked",false);
		$(".score_purchase").hide();
		count_amount_continuation(discount,final_pay);
	}//end
    
	
}
function count_amount_continuation(discount,final_pay) {
	var discount_integer, discount_point;
    discount = discount.toString();
    var i = discount.toString().indexOf('.');
    if (i == -1) {
        discount_integer = discount;
        discount_point = '00';
    } else {
        discount_integer = discount.substring(0, i);
        discount_point = discount.substring(i + 1);
    }
    var final_pay_integer, final_pay_point;
    final_pay_s = Math.round(final_pay * 100).toString();
    final_pay_point = final_pay_s.substr(-2);
    if (final_pay_point.length == 1) {
        final_pay_point = '0' + final_pay_point;
    }
    final_pay_integer = final_pay_s.substr(0, final_pay_s.length - 2);
    final_pay_integer = final_pay_integer ? final_pay_integer : 0;
    $('.discount_money .integer').text(discount_integer);
    $('.discount_money .point').text(discount_point);
    $('.actual_pay .integer').text(final_pay_integer);
    $('.actual_pay .point').text(final_pay_point);
}
// 计算支付金额的可优惠部分
function count_discount(pay_money) {
    var discount = 0;
    var limit = 0;
    $('.discount').each(function(index, domEle) {
        limit = $(domEle).find('.limit').text();
        if (limit <= pay_money) {
            discount += Number($(domEle).find('.amount').text());
        }
        
    });
    return discount.toFixed(2);
}

function order_submit(){
    $(".btn-con").bind("click",function(){
		if($('input[name="all_score"]:checked').length>0){
			var all_score=1;
		}else{
			var all_score=0;
		}
		var exchange_money= $(".score_purchase .u-money").text();
		
        var pay = $('.actual_pay .integer').text();
        var point = $('.actual_pay .point').text();
        if ((pay != 0 || point !=0)||(pay == 0 && point ==0 && all_score==1 && exchange_money>0)) {
            var query=$("#submit_dp").serialize();
            var url=$("#submit_dp").attr('action');
            $.ajax({ 
                url: url,
                data:query,
                type: "POST",
                dataType: "json",
                success: function(data){
                    if (data.user_login_status == 0) {
                        if (app_index == 'app') {
                            App.login_sdk();
                        } else {
                            $.router.load(data.jump, true);
                        }
                    } else {
                        if (data.status == 1) {
                            $.router.load(data.jump, true);
                        } else {
                            $.toast(data.info);
                            if (data.jump) {
                                setTimeout(function() {
                                    $.router.load(data.jump, true);
                                }, 2000);
                            }
                        }
                    }
                },

            });
        } else if ($('input[name=money]').val() != '') {
            $.toast('输入的金额格式错误');
        } else {
            $.toast('请输入消费金额');
        }
        return false;

    });
}
/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#store_payment_done", function(e, pageId, $page) {
    
    $('.deal-share').click(function() {
        // 
    })
});

// $(function(){
//     //init_payment_input();

//     init_pay_btn();

// });

$(document).on("pageInit", "#store_pay_check", function(e, pageId, $page) {
	$('.fee_count').hide();
	init_payment_input();
	init_pay_btn();
	

	function init_payment_input(){
		$("input[name='all_account_money']").live("change",function () {

			if($("#all_account_money").hasClass("active")){
				$("#all_account_money").removeClass("active");
			}else{
				$("#all_account_money").addClass("active");
				$("input[name='payment']").prop("checked",false);
			}
			//count_buy_total();
			$('.fee_count').hide();
			$('.fee_count .payment_fee').text(0);
			local_count()
		});
		
		
		$(".payment").live("click",function(){
			$("input[name='payment']").prop("checked",false);
			$(".payment").removeClass('active');
			$(this).siblings("input[name='payment']").prop("checked",true);
			$(this).addClass("active");

			$("#all_account_money").removeClass("active");
			$("input[name='all_account_money']").prop("checked",false);
			var fee = Number($('.active .fee_amount').text());
			if (fee > 0) {
				$('.fee_count .payment_fee').text(fee.toFixed(2));
				$('.fee_count').show();
			} else {
				$('.fee_count .payment_fee').text(0);
				$('.fee_count').hide();
			}
			local_count()
		});

	}

	function local_count() {
		var total= $('.total_count').text();
		if (total) {
			total = total.replace(",", "");
		}
		var payment_fee= $('.payment_fee').text();
		if (payment_fee) {
			payment_fee = payment_fee.replace(",","")
		}
		var discount= $('.discount').text();
		if (discount) {
			discount = discount.replace(",","")
		}
		var ready_pay = Number(total) - Number(discount) + Number(payment_fee);
		$('.ready_pay').text(ready_pay.toFixed(2));
	}

	function count_buy_total()
	{
		ajaxing = true;
		var query = new Object();
		
		//全额支付
		//if($("input[name='all_account_money']").attr("checked")) {
		if($("#all_account_money").hasClass("active")) {
			query.all_account_money = 1;
		} else {
			query.all_account_money = 0;
		}
		
		//支付方式
		var payment = $("input[name='payment']:checked").val();
		if(!payment) {
			payment = 0;
		}
		query.ajax = 1;
		query.payment = payment;
	    query.order_id = order_id;
		query.bank_id = $("input[name='payment']:checked").attr("value");


		if(!isNaN(order_id)&&order_id>0){
			query.act = "count_store_pay_total";
		} else {
			query.act = "check";
		}

		$.ajax({ 
			url: custom_ajax_url,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.status == -1) {  //未登录，请先登录
					$.alert("未登录，请先登录",function(){location.href=login_url;});
					
				}
				if (data.info) {
					$.toast(data.info);
				}

		        $(".pay_info").html(data.html);
	                        
				ajaxing = false;
			}
		});
	        
	}
	function init_pay_btn(){
	    $(".u-sure-pay").bind("click",function(){
	        var payment = $("input[name='payment']:checked").val();
	        var bank_id=0;
	        if(payment>0 || $("input[name='all_account_money']").attr("checked")){

	        	var query = new Object();
	        	if($("input[name='all_account_money']").attr("checked")) {
	        		query.all_account_money = 1;
	        	} else {
	        		query.all_account_money = 0;
	        	}
	            query.order_id = order_id;
	            query.bank_id = bank_id;
	            query.payment = payment;
	            query.act = "done";
	            $.ajax({
	                url:custom_ajax_url,
	                data:query,
	                type:"POST",
	                dataType:"json",
	                success:function(data){	                	

	    				if(data.status==1){

	    					if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
	    						if(data.pay_status==1){
	    							$.router.load(data.jump, true);
	    						}else{
	    							location.href=data.jump;
	    						}
	    					} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
	    						 $.router.load(data.jump, true);

	    					} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
	    						if(data.online_pay==3){
	    							try {

	    								var str = pay_sdk_json(data.sdk_code);
	    								App.pay_sdk(str);
	    							} catch (ex) {
	    								$.toast(ex);
	    								$.loadPage(location.href);
	    							}
	    						}else{
	    							var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

	    							try {
	    								App.open_type(pay_json);
	    								$.confirm('已支付完成？', function () {
	    									$.loadPage(location.href);

	    								},function(){

	    									$.loadPage(location.href);
	    								});
	    							} catch (ex) {
	    								$.toast(ex);
	    								$.loadPage(location.href);
	    							}
	    						}
	    					}



	    				}else{
	    					
	    					$.alert(data.info);
	    				}
	    			
	                }			
	            });
	        }else{
	            $.toast("请选择支付方式");
	        }
	    });
	}
});

$(document).on("pageInit", "#store_reviews", function(e, pageId, $page) {
	init_list_scroll_bottom();
});
$(document).on("pageInit", "#store_shop", function(e, pageId, $page) {
	$(document).on("click",".dropdown-navlist",function() {
		screen_bar_close();
	});
	$(".m-screen-bar").on("click",".screen-link",function() {
		screen_bar_close();
		$(".screen-link").removeClass('active');
		$(this).addClass('active');
	});
	//筛选
	//标签
	$(".m-screen-bar").on("click",".screen-item a",function(){
		$(".arrow-up").hide();
		$(".arrow-down").show();
		$(".m-screen-list").removeClass('active');
		$(".goods-type li").removeClass('active');
	});
	//全部
	function screen_open() {
		$(".content").css('overflow', 'hidden');
		$(".m-screen-list").addClass('active');
	}
	function screen_close() {
		$(".content").css('overflow', 'auto');
		$(".m-screen-list").removeClass('active');
	}
	$(".m-screen-bar").on("click",".screen-all",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#all-goods").removeClass('active');
		} else {
			$(this).addClass('active');
			$(".screen-area").removeClass('active');
			$("#area-screen").removeClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			screen_open();
			$("#all-goods").addClass('active');
			$("#all-goods .goods-type li").eq($(this).attr("data-cid")).addClass('active');
			$("#all-goods .type-detail ul").eq($(this).attr("data-cid")).show();
		}
	});
	$(".m-screen-list").on("click",".goods-type li",function() {
		$(".goods-type li").removeClass('active');
		$(this).addClass('active');
		$(".type-detail ul").hide();
		if ($(".goods-type li").hasClass('active')) {
			var type_id = $(this).attr('data-id');
			$(this).parent().parent().find(".type-detail ul").eq(type_id).show();
		}
	});
	$("#all-goods").on('click', '.type-detail li a', function() {
		$("#all-goods .type-detail li a").removeClass('active');
		$(this).addClass('active');
		$(".screen-all p").html($(this).find('p').html());
		$(".screen-all").attr('data-cid', $(this).parent().parent().attr("data-id"));
		$(".screen-link").removeClass('active');
	});
	$("#all-goods").on('click', '.type-detail li:first-child a', function() {
		var type_id = $(this).parent().parent().attr('data-id');
		$(".screen-all p").html($("#all-goods .goods-type li").eq(type_id).html());
	});
	//全城
	$(".m-screen-bar").on("click",".screen-area",function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			screen_close();
			$("#area-screen").removeClass('active');
		} else {
			$(this).addClass('active');
			$(this).find('.arrow-down').hide();
			$(this).find('.arrow-up').show();
			$(".screen-all").removeClass('active');
			$("#all-goods").removeClass('active');
			screen_open();
			$("#area-screen").addClass('active');
			$(".goods-type li").removeClass('acitve');
			$("#area-screen .goods-type li").eq($(this).attr("data-qid")).addClass('active');
			$("#area-screen .type-detail ul").eq($(this).attr("data-qid")).show();
		}
	});
	$("#area-screen").on('click', '.type-detail li a', function() {
		$("#area-screen .type-detail li a").removeClass('active');
		$(this).addClass('active');
		$(".screen-area p").html($(this).find('p').html());
		$(".screen-area").attr('data-qid', $(this).parent().parent().attr("data-id"));
		$(".screen-link").removeClass('active');
	});
	$("#area-screen").on('click', '.type-detail li:first-child a', function() {
		var type_id = $(this).parent().parent().attr('data-id');
		$(".screen-area p").html($("#area-screen .goods-type li").eq(type_id).html());
	});


	$(document).off("click",".j-listchoose");
	$(document).on("click",".j-listchoose",function() {
		var url=$(this).attr("date-href");
		var nidate="<div class='tipimg no_data'>"+"没有数据啦"+"</div>";
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajaxlist").html($(html).find(".j-ajaxlist").html());
				$(".j-pj").html($(html).find(".j-pj").html());
				$(".j-jg").html($(html).find(".j-jg").html());
				$(".j-zj").html($(html).find(".j-zj").html());
				$(".j-zx").html($(html).find(".j-zx").html());
				if ($(html).find(".j-ajaxlist").html()==null) {
					$(".j-ajaxlist").html(nidate);
				}else{
					init_list_scroll_bottom();
				};
				if ($("#type-cube").css('display')=='none') {
					$(".m-goods-list ul").addClass('type-list').removeClass('type-cube');
				}
				if ($("#type-list").css('display')=='none') {
					$(".m-goods-list ul").removeClass('type-list').addClass('type-cube');
				}
			},
			error:function()
			{
				$.toast("加载失败咯~");
			}
		});
		$.showIndicator();
		setTimeout(function () {
			$.hideIndicator();
		}, 800);
		screen_bar_close();
	});
});

$(document).on("pageInit", "#tuan", function(e, pageId, $page) {
	screen_bar();
	init_list_scroll_bottom();//下拉刷新加载
	//
	//星星评分
	//$(".tuan-item").each(function(){
	//    $(this).find(".start-num").css("width",$(this).find(".start-num").parent().parent().attr("data")+"%");
	//});
	//隐藏数量为0的2级分类
	$(".goods-num").filter(function(index){
		if($(this).parent().attr("data-cid")=='0'&&$(this).parent().attr("data-tid")=='0')
			return false;
　　　　return $(this).text()=="0";
　　	}).parent().hide();

	//团购列表展开
	$(document).on("click",".tuan-list-more",function() {
		var height = $(this).parent().find('.tuan-list li').height();
		var num = $(this).parent().find('.tuan-list li').length;
		$(this).parent().find('.tuan-list').css('max-height', height*num);
		$(this).hide();
	});
	if(address==""){
		//if(navigator.geolocation)
		//{
			 //var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
			 //navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
			 
		//}
		position();
	}
	$(document).on("click",".address-info",function() {
		$(".refresh").addClass('rotate');
		//if(navigator.geolocation)
		//{
		//	 var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
		//	 navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
		//}
		position();
	});
	
	function getPositionSuccess(p){
		has_location = 1;//定位成功;
	    m_latitude = p.coords.latitude; //纬度
	    m_longitude = p.coords.longitude;
		userxypoint(m_latitude, m_longitude);
	}

	function getPositionError(error){
		switch(error.code){
		    case error.TIMEOUT:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>定位连接超时，请重试");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		        //alert("定位连接超时，请重试");
		        break;
		    case error.PERMISSION_DENIED:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>您拒绝了使用位置共享服务，查询已取消");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		        //alert("您拒绝了使用位置共享服务，查询已取消");
		        break;
		    default:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>定位失败");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		    	//alert("定位失败");
		}
	}
});


$(document).on("pageInit", "#uc_account", function(e, pageId, $page) {
	$(".progress-bar-inner").each(function() {
		var progress_width = $(this).attr('data-width');
		$(this).css('width', progress_width);
	});

	// 修改密码前先验证用户是否已绑定手机号
	$('.bindphone').bind('click', function() {
		if (($('input[name=phone]').val() != 1)) {
			$.toast('请先绑定手机号');
			$.router.load($(this).attr('phone-href'));
		} else {
			$.router.load($(this).attr('href-data'));
		}
	});
	//请求绑定微信，获得微信授权
	if(is_weixin_bind){
		js_weixin_login("",1);
	}
	//解绑微信
	$('.wx_unbind').bind('click', function () {
		
		var ajax_url = $(this).attr("action");
		var query = '';
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.toast(obj.info);
					if(obj.jump){
						$.loadPage(REFRESH_URL);
						//location.href=REFRESH_URL;
						//$.router.load(obj.jump,true);
					}		
				}
				else
				{
					$.toast(obj.info);
					if(obj.jump){
						$.router.load(obj.jump,true);
					}
							
				}
			}
		});
		
		return false;
	});


	// 修改头像。
	$('#up_avatar').bind('change', function() {
		lrz(this.files[0], {width: 200})
			.then(function(rst) {
				// 处理上传到后端的逻辑
				rst.formData.append('fileLen', rst.fileLen);

				$.ajax({
					url: UPLOAD_URL,
					data: rst.formData,
					processData: false,
					contentType: false,
					type: 'POST',
					success: function(obj) {
						var data = JSON.parse(obj);
						if (data.error == 1000) {
							$.router.load(LOGIN_URL, true);
						} else if (data.error == 2000) {
							$.toast('图片上传发生错误,跟换浏览器重试');
						} else if (data.error > 0) {
							$.toast('图片上传发生错误');
						} else {
							$('#user_avatar').attr('src', rst.base64);
							$.toast('头像已修改');
						}
					},
					error: function(msg) {
						$.toast('网络被风吹走了～');
					}
				})
			})
			.catch(function(err) {
				// 捕获错误
				$.toast('数据异常,请重试');
			})
			.always(function() {
				// 总是会发生。要发生什么
			});
	});
	$("#app_up_avatar").on("click",function () {
		App.CutPhoto();
	});

});





$(document).on("pageInit", "#uc_account_phone", function(e, pageId, $page) {
	$('.userBtn').on('click', function(){

		$("form").submit( function () {
			return false;
		});
		
		var mobile = $('input[name=mobile]').val();
		var sms_verify = $('input[name=sms_verify]').val();

		if ($.trim(mobile) == '') {
			$.toast('请输入要绑定的手机号码');
			return false;
		}
		if ($.trim(sms_verify) == '') {
			$.toast('请输入收到的验证码');
			return false;
		}

		if (!/^[0-9]{6}$/.test(sms_verify)) {
			$.toast('验证码格式有误');
			return false;
		} else if (!/^1[34578][0-9]{9}/.test(mobile)) {
			$.toast('手机格式有误');
			return false;
		}

		var step = $('input[name=step]').val();
		if (!/^\d$/.test(step)) {
			$.alert('网络异常请刷新重试', function() {
				window.location.reload();
				return false;
			})
			
		}
		var is_luck = $('input[name=is_luck]').val();
		var query = new Object();
		query.mobile = mobile;
		query.sms_verify = sms_verify;
		var is_fx = $('input[name=is_fx]').val();
		query.is_fx = is_fx;
		query.step = step;
		if(step==2)
			query.is_luck = is_luck;
		query.act = 'bindPhone';
		$.ajax({
			url: bind_url,
			data: query,
			dataType: "json",
			type: "post",
			success: function(obj){
				if(obj.user_login_status==0){
					$.alert(obj.info,function(){
						$.router.load(obj.jump);
					});
				} else if(obj.status == 0) {
					$.toast(obj.info);
				} else {
					// 处理页面跳转逻辑
					if (step == 2) { // 绑定成功跳掉用户中心
						// $('.sendBtn').attr('lesstime', 0);
						// $.alert(obj.info, function() {
						// 	window.location.href = obj.jump;
						// });
						$.toast(obj.info);
						setTimeout(function() {
			                // $.router.load(obj.jump, true);
			                // 跳转回上一页
			                if (obj.jump) {
			                	$.router.load(obj.jump, true);
			                } else if (referer_url) {
			                	$.router.load(referer_url, true);
			                } else {
			                	$.router.back();
			                }
			            }, 2000);
					} else { // 绑定新的手机号码
						$('input[name=mobile]').attr('type','tel');
						$('input[name=mobile]').attr('value', '');
						$('input[name=format_mobile]').attr('type','hidden');
						
						$('input[name=mobile]').val('');
						$('input[name=mobile]').attr('placeholder', '请输入新的手机号码');
						$('input[name=sms_verify]').val('');
						$('.title').text(obj.page_title);
						$('input[name=step]').attr('value', 2);
						$('.sendBtn').removeClass("isUseful");
						$('.sendBtn').addClass("noUseful");
						$('.sendBtn').attr('lesstime', 0);
						$('input[name=is_luck]').attr('value', obj.is_luck);
						$('.sendBtn').attr('unique',4);

						$('.userBtn').val('确定');
					}
				}
				
			},
			error:function(ajaxobj) {
				$.toast('网络异常');
			}
		});
	})
})

$(document).on("pageInit", "#uc_banklist", function(e, pageId, $page) {
	
	$('.del').on('click', function () {
		var obj = $(this);
		var id = new Array();
		var id = obj.parents("li").attr("data-id");
//		alert(id);
		$.confirm('确定删除这张银行卡？', function () {
		  del_bank(id);
		  obj.parents("li").remove();
		});
	});

	function del_bank(id){
		var query = new Object();
		query.id = id;
			$.ajax({
				url: ajax_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					
					$.toast(obj.info);
		//			if(obj.status==0 && obj.user_login_status==0){
		//				$.alert(obj.info,function(){
		//					window.location.href=obj.jump;
		//				});
		//			}
		//			if(obj.status == 1){
		//				$.toast(obj.info);
		//				//setTimeout("location.reload()",1000);
		//				
		//			}
				},
				error:function(ajaxobj)
				{
					
		//			if(ajaxobj.responseText!='')
		//			alert(ajaxobj.responseText);
				}
		});
	}
});

$(document).on("pageInit", "#uc_charge", function(e, pageId, $page) {

	$("form[name='do_charge']").bind("submit",function(){

		var money = $("input[name='money']").val();
		var payment_id = $("input[name='payment_id']:checked").val();		

		if($.trim(payment_id)==""||isNaN(payment_id)||parseFloat(payment_id)<=0)
		{
			$.alert("请选择支付方式");
			return false;
		}
		
		if($.trim(money)==""||isNaN(money)||parseFloat(payment_id)<=0)
		{
			$.alert("请选择正确的充值金额");
			return false;
		}		
		
		
		var query = $(this).serialize();
		var action = $(this).attr("action");

		$.ajax({
			url:action,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(data){
				if(data.status==1){

					if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
						if(data.pay_status==1){
							$.router.load(data.jump, true);
						}else{
							location.href=data.jump;
						}
					} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
						 $.router.load(data.jump, true);

					} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
						if(data.online_pay==3){
							try {

								var str = pay_sdk_json(data.sdk_code);
								App.pay_sdk(str);
							} catch (ex) {
								$.toast(ex);
								$.loadPage(location.href);
							}
						}else{
							var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

							try {
								App.open_type(pay_json);
								$.confirm('已支付完成？', function () {
									$.loadPage(location.href);

								},function(){

									$.loadPage(location.href);
								});
							} catch (ex) {
								$.toast(ex);
								$.loadPage(location.href);
							}
						}
					}



				}else{
					
					$.alert(data.info);
				}
			
				return false;
			}
		});
		
		return false;

	});
	
	
	$(".select_num").bind("click",function(){
		$(".money_number").removeClass("selected");
		$(this).addClass("selected");
		$("input[name='money']").val($(this).attr('data'));
	});
    $(".first_number").trigger("click");
    if(money_number_array_other){
        $(".select_other").picker({
            toolbarTemplate: '<header class="bar bar-nav">\
          <button class="button button-link pull-right close-picker">确定</button>\
          <h1 class="title">请选择金额</h1>\
          </header>',
            cols: [
                {
                    textAlign: 'center',
                    values: money_number_array_other
                }
            ],
            onClose:function(){
                $(".money_number").removeClass("selected");
                $(".select_other").addClass("selected");
                $("input[name='money']").val(intval($(".select_other").val()));
            }
        });
    }
	$(".select_num1").focus(function(){
		$(".select_num").removeClass("selected");
	});
	$(".select_num1").blur(function(){
		//$(".select_num").removeClass("selected");
		$("input[name='money']").val($(this).val());
	});
    function intval(p){
        if(!p)return 0;
        if(typeof p=="number"){
            return parseInt(p);
        }else if(typeof p=="string"){
            return parseInt(p.replace(/[^0-9\.]*/ig,""));
        }
    }
	$(".pay_select").bind("click",function(){
		$(".pay_select .j-selected-icon").removeClass("checked");
		$(this).find(".j-selected-icon").addClass("checked");		
		$(".pay_select").find("input[name='payment_id']").prop("checked",false);
		$(this).find("input[name='payment_id']").prop("checked",true);
	});
});

$(document).on("pageInit", "#uc_collect", function(e, pageId, $page) {
	refreshdata([".uc_collect_change"]);

/*
 *初始化tab的下划线
*/	
	init_listscroll(".j_ajaxlist_"+sc_status,".j_ajaxadd_"+sc_status);
	
	bottom_line(0);
	$(".m-collect-list").addClass("hide");
	$("#tb0").removeClass("hide");
	$(".j-tab-btn").removeClass("active");
	$(".j-tab-btn").eq(0).addClass("active");

/*
 *tab切换
 *参数说明：left  点击的tab距离左边的距离，width  点击的tab的宽度，
 *rel 对应的类别  0为商品团购 1为优惠券 2为活动 3为店铺，isload  标识出所选择的类别内容是否已经加载
*/

	$(".j-tab-btn").click(function(){
		var rel = $(this).attr("rel");
		
		var isload = $(this).attr("data-isload");
		var isEdit = $(this).parent(".m-tab-btn-list").attr("data-isedit");
		
		if (isEdit == 0) {
			$(document).off('infinite', '.infinite-scroll-bottom');
			 
			$(".j-tab-btn").removeClass("active");
			$(this).addClass("active");
			
			bottom_line(rel);
			
			$(".m-collect-list").addClass("hide");
			$("#tb"+rel).removeClass("hide");
			
			$('.content').scrollTop(1);
			if($.trim($("#tb"+rel).html()) == ""){
				var ajax_url =url[rel];
				$.ajax({
					url:ajax_url,
					type:"POST",
					success:function(html)
					{
						$(".content").append($(html).find(".content").html());
						init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
					},
					error:function()
					{
						$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
					}
				});
			} else{
				if($(".content").scrollTop()>0){
					infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				}
			}
			
			if (isload == 0) {
				//ajax加载内容
				console.log("ajax加载内容");
				$(this).attr("data-isload",1);
			}else{
				console.log("我加载完了····");
			}

			$(".j-edit").attr("rel",rel);
			$(".j-all-check").attr("rel",rel);
			$(".j-cancel").attr("rel",rel);
			now_sc = rel;
			console.log(now_sc);
		}else{
			console.log("编辑状态不能切换");
		}
	});


/*
 *编辑按钮
*/
	$(".j-edit").click(function(){
		var rel = $(this).attr("rel");
		var isEdit = $(this).attr("data-isedit");
		if(isEdit == 0){
			var item_length = $('.m-collect-list[rel = "'+rel+'"]').find(".collect-item").length;
			if (item_length > 0) {
				$('.m-collect-list[rel = "'+rel+'"]').addClass("isEdit");
				$('.m-collect-list[rel = "'+rel+'"]').find(".no-href").show();
				$(this).html("完成");
				$(this).attr("data-isedit",1);
				$(".m-operation").addClass("isShow");
				$(".m-tab-btn-list").attr("data-isedit",1);
			}else{
				$.toast("当前没有收藏！！");
			}
		}else{
			$('.m-collect-list[rel = "'+rel+'"]').removeClass("isEdit");
			$(".j-all-check").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".j-check-box").removeClass("isCheck");
			$(this).html("编辑");
			$(this).attr("data-isedit",0);
			$(".m-operation").removeClass("isShow");
			$(".m-tab-btn-list").attr("data-isedit",0);
			$('.m-collect-list[rel = "'+rel+'"]').children(".collect-item").children(".j-check-box").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".no-href").hide();
		}
	});

/*
 *勾选
*/
	$(".page").on("click",".j-check-box" , (function(){
		var isCheck = $(this).children(".iconfont").hasClass("isCheck");
			if(isCheck){
				$(this).children(".iconfont").removeClass("isCheck");
				$(".j-all-check").children(".iconfont").removeClass("isCheck");
			}else{
				$(this).children(".iconfont").addClass("isCheck");
				var rel = $(this).parents(".m-collect-list").attr("rel");
				var check_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").length;
				var item_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").length;
				if (check_length == item_length) {
					$(".j-all-check").children(".iconfont").addClass("isCheck");
				}
			}
		})
	);

/*
 *全选
*/
	$(".j-all-check").click(function(){
		var isCheck = $(this).children(".iconfont").hasClass("isCheck");
		var rel = $(this).attr("rel");
		if(isCheck){
			$(this).children(".iconfont").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").children(".iconfont").removeClass("isCheck");
		}else{
			$(this).children(".iconfont").addClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").children(".iconfont").addClass("isCheck");
		}
	});

/*
 *取消收藏
 *参数说明： data  数组  保存已选择的子项的id，type  保存已选择的子项类别
*/
	$(".j-cancel").on("click",function(){
		var rel = $(this).attr("rel");
		if($('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").length != 0){
			var data = new Array();
			var type = $('.m-collect-list[rel = "'+rel+'"]').attr("data-type");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").each(function(index){
				data[index] = $(this).parent(".j-check-box").attr("data-id");
			});
			var id=data.join(","); 
			uc_del_collect(type,id);
			console.log(type);
			console.log(data);
			//console.log(id);


			//还原页面到未编辑状态
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").parents(".collect-item").remove();
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").parents(".collect-item").attr("data-isdel",1);
			$('.m-collect-list[rel = "'+rel+'"]').removeClass("isEdit");
			$(".j-all-check").children(".iconfont").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".j-check-box").children(".iconfont").removeClass("isCheck");
			$(".j-edit").html("编辑");
			$(".j-edit").attr("data-isedit",0);
			$(".m-operation").removeClass("isShow");
			$(".m-tab-btn-list").attr("data-isedit",0);
			$('.m-collect-list[rel = "'+rel+'"]').children(".collect-item").children(".j-check-box").children(".iconfont").removeClass("isCheck");
			
			//判断是否全部删除，如果全部删除这显示无内容文本
			var del_length = 0;
			var item_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".collect-item").length;
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".collect-item").each(function(){
				if ($(this).attr("data-isdel") == 1) {
					del_length++;
				}
			});
			if (del_length == item_length) {
				$('.m-collect-list[rel = "'+rel+'"]').append('<div class="tipimg no_data">暂无收藏</div>');
			}
		}else{
			$.toast("请选择要取消的收藏！！");
		}
		
	});
});/*页面结束初始化*/


/*
 *初始化tab的下划线
*/
function bottom_line(index){
	var left = $(".j-tab-btn").eq(index).children("em").offset().left;
	var width = $(".j-tab-btn").eq(index).children("em").width();
	$(".bottom-line").css({
		"left" : left,
		"width" : width
	});
}

function uc_del_collect(type,id){
	var query = new Object();
	query.id = id;
	query.type = type;
	//alert(ajax_url);
	$.ajax({
				url: ajax_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					if(obj.status==0 && obj.user_login_status==0){
						$.alert(obj.info,function(){
							window.location.href=obj.jump;
						});
					}
					if(obj.status == 1){
						$.toast(obj.info);
					}
				},
				error:function(ajaxobj)
				{
					
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
				}
	});
}

/**
 * Created by lynn on 2016/11/17.
 * Update by YXM on 2016/11/28. 路由改版
 */
$(document).on("pageInit", "#uc_coupon", function(e, pageId, $page) {
   
    var item_width = $(".j-tab-link[rel='"+eq+"']").width();
	var item_left = $(".j-tab-link[rel='"+eq+"']").offset().left;
	$(".tab-line").css({
		width: item_width,
		left: item_left
	});
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j_ajaxlist_"+status,".j_ajaxadd_"+status);
	}
    
    $(".page").on('click',".j-tab-link",function(){
    	$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		var item_width=$(this).width();
		var item_left=$(this).offset().left;
		$(".tab-line").css({
			width: item_width,
			left: item_left
		});
		if($.trim($("#tab"+rel).html()) == ""){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".tabs").find(".tab").removeClass("active");
					$(".tabs").append($(html).find(".tabs").html());
					$(".content").scrollTop(1);
					if($(".content").scrollTop()>0){
						init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
					}
				},
				error:function()
				{
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			$(".content").scrollTop(1);
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
	});
    
});
$(document).on("pageInit", "#uc_ecv", function(e, pageId, $page) {
	//alert(2);
	function tab_line() {
		var init_width=$(".uc-ecv-tab .active").width();
		var init_left=$(".uc-ecv-tab .active").offset().left;
		$(".ecv-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	init_listscroll(".j_ajaxlist_"+valid,".j_ajaxadd_"+valid);
	tab_line();
	
	$(".uc-ecv-tab a").click(function() {
		//alert(1);
		$(".uc-ecv-tab a").removeClass('active');
		$(this).addClass('active');
		tab_line();
		$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		$(".m-ecv-list").removeClass('hide');
		if(rel==0)
		$("#tab1").addClass('hide');
		if(rel==1)
		$("#tab0").addClass('hide');
		$('.content').scrollTop(1);
		if($.trim($("#tab"+rel).html()) == "" && $("#tab"+rel).length==0 ){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".content").append($(html).find(".content").html());
					init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				},
				error:function()
				{
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
		
	});
	/*$(".can-use").click(function() {
		$(".m-ecv-list").removeClass('hide');
		$(".used-ecv").addClass('hide');
	});
	$(".cant-use").click(function() {
		$(".m-ecv-list").addClass('hide');
		$(".used-ecv").removeClass('hide');
	});*/

	$(".page").on('click',".j-open-ecv-exchange",function(){
		$(".pop-up").addClass("open");
		$(".pop-up").children(".img-box").addClass("open");
		$(".content").addClass("noscroll");
		$(".close-pop,.j-close-pop-btn").attr("rel","ecv");
	});

	$(".page").on('click',".j-ecv-exchange",function(){
		var sn = $(".input-ecv-exchange").val();
		if(sn.length < 1){
			$.toast("请输入红包兑换码");
		}else{
			var form = $("form[name='exchange_form']");
			var url=$(form).attr('action');
			var query = new Object();
			query.sn = sn;
			$.ajax({
				url:url,
				data:query,
				type:'post',
				dataType:'json',
				success:function(obj){
					if(obj.status==1){
						console.log(obj.info);
						$.toast(obj.info);
						$(".pop-up").children(".img-box").removeClass("open");
						$(".pop-up").removeClass("open");
						$(".input-ecv-exchange").val("");
					}else{
						$.toast(obj.info);
						$(".input-ecv-exchange").val("");
					}
					return false;
				}
			});
			
		}
		return false;
	});
});
$(document).on("pageInit", "#uc_ecv_exchange", function(e, pageId, $page) {
	$(".j-exchange-input").on("change keyup",function(){
		if($(this).val()){
			$(".j-exchange-btn").removeClass('disable');
		}else{
			$(".j-exchange-btn").addClass('disable');
		}
	});
	
	$(".j-exchange-btn").bind("click",function(){
		if(!$(this).hasClass('disable')){
			$("form[name='exchange_form']").submit();
		}
	});
	
	$("form[name='exchange_form']").bind('submit',function(){

		var sn = $(".j-exchange-input").val();
		if(sn==""){
			$.toast("口令不能为空");
			return false;
		}else{
			var form = $("form[name='exchange_form']");
			var url=$(form).attr('action');
			var query = new Object();
			query.sn = sn;
			$.ajax({
				url:url,
				data:query,
				type:'post',
				dataType:'json',
				success:function(obj){
					if(obj.status==1){
						console.log(obj.info);
						$.toast(obj.info);
						setTimeout(function(){
							window.location.reload();
						},1000); 
					}else{
						$.toast(obj.info);
						/*$("input[name='sn']").val("");*/
						if(obj.jump){
							setTimeout(function(){
								window.location.href=obj.jump;
							},1000);
						}
					}
					return false;
				}
			});
		}
		return false;
	});

	
	/*兑换红包功能*/	
	$(".j-receive").on('click',function(){
		var id = $(this).attr('data-id');
		
		var query = new Object();
		query.id = id;
		query.act = 'do_exchange';
		$.ajax({
			url:ajax_url,
			data:query,
			type:'post',
			dataType:'json',
			success:function(obj){
				console.log(obj);
				if(obj.status==1){
					$.toast(obj.info);
					setTimeout(function(){
						window.location.reload();
					},1000); 
				}else{
					$.toast(obj.info);
				}
				return false;
			}
		});

	});
});

$(document).on("pageInit", "#uc_fx", function(e, pageId, $page) {
	loadScript(jia_url);
	init_list_scroll_bottom();
	$(".j-openshare").click(function(){
		var id=$(this).attr("data_id");
		var img_url=deal_json[id]['icon'];
		var share_url=deal_json[id]['share_url'];
		var title=deal_json[id]['name'];
		jiathis_config = {
		    siteNum:6,
		    sm:"weixin,tssina,cqq,qzone,douban,copy",
		    url:share_url,
		    title:title,
		    pic:img_url
		}
	});
	$(".social_share").find(".flex-1").click(function(){
		$(".flippedout").removeClass("z-open").removeClass("showflipped");
		$(".box_share").removeClass("z-open");
	});
	
	$("#uc_fx").on("click",".j-app-share-btn",function(){
		
		var share_data={};
		share_data["share_content"]=$(this).attr("data-content");
		share_data["share_url"]=$(this).attr("data-url");;
		share_data["key"]='';
		share_data['sina_app_api']=1;
		share_data['qq_app_api']=1;
		share_data["share_imageUrl"]=$(this).attr("data-img");;
		share_data['share_title'] = $(this).attr("data-title");;
		share_data=JSON.stringify(share_data);
		try{
			App.sdk_share(share_data);
		}catch(e){

		}
	});
	
	$("#uc_fx").on('click',".goods-down",function(){
		var id=$(this).attr("data_id");
		
		var data_url=$(this).attr("data-url");
		var data_img=$(this).attr("data-img");
		var data_title=$(this).attr("data-title");
		
		var query = new Object();
		query.act="do_is_effect";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$(".goods-down[data_id='"+id+"']").html("上架");
					
					if(APP_INDEX=="app"){
						$(".goods-down[data_id='"+id+"']").parent().find(".j-app-share-btn").remove();
					}else{
						$(".goods-down[data_id='"+id+"']").parent().find(".j-openshare").remove();
					}
					var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 cancle-fx' data_id='"+id+"' data-url='"+data_url+"' data-img='"+data_img+"' data-title='"+data_title+"'>取消网宝</a>");
					$(".goods-down[data_id='"+id+"']").parent().append($content);
					$(".goods-down[data_id='"+id+"']").removeClass("goods-down").addClass("goods-up");
				}
			}
		});
	});
	
	$("#uc_fx").on('click',".goods-up",function(){
		var id=$(this).attr("data_id");
		var query = new Object();
		
		var data_url=$(this).attr("data-url");
		var data_img=$(this).attr("data-img");
		var data_title=$(this).attr("data-title");
		
		query.act="do_is_effect";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$(".goods-up[data_id='"+id+"']").html("下架");
					$(".goods-up[data_id='"+id+"']").parent().find(".cancle-fx").remove();
					
					if(APP_INDEX=="app"){
						var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 share j-app-share-btn' data_id='"+id+"' data-url='"+data_url+"' data-img='"+data_img+"' data-title='"+data_title+"'>分享</a>");
					}else{
						var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 share j-openshare' data_id='"+id+"'>分享</a>");
					}
					$(".goods-up[data_id='"+id+"']").parent().append($content);
					$(".goods-up[data_id='"+id+"']").removeClass("goods-up").addClass("goods-down");
				}
			}
		});
	});
	
	$("#uc_fx").on("click",".cancle-fx",function(){
		var id=$(this).attr("data_id");
		var query = new Object();
		query.act="del_user_deal";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$.toast(obj.info);
					if(obj.status==1){
						$(".fx-list").find("li[data_id='"+id+"']").remove();
					}
				}
			}
		});
	});
});
$(document).on("pageInit", "#uc_fxinvite", function(e, pageId, $page) {
	init_list_scroll_bottom();
});
$(document).on("pageInit", "#uc_fxwithdraw", function(e, pageId, $page) {
	/*$(".bank-select").click(function() {
		$(".select-bank").addClass('active');
	});*/
	$(".mask").click(function() {
		$(".select-bank").removeClass('active');
	});
	$(".bank-list li").click(function() {
		$(".bank-list li .iconfont").removeClass('selected');
		$(this).find('.iconfont').addClass('selected');
		$(".bank-select .bank-info").html($(this).find('.bank-info').html());
		$(".select-bank").removeClass('active');
		$("input[name='bank_id']").val($(this).attr("bank_id"));
	});
	
	$(".select-bank .add-bank").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$(".select-bank .close-btn").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$("form[name='withdraw']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>fx_money){
			$.toast("提现超额");
			$(this).val(fx_money);
		}
	});

	var wfeeObj = $('input.withdraw-rate');
	if (wfeeObj) {
		$('input[name=money]').on('input propertychange', function() {
			var money = parseFloat($('input[name="money"]').val());
			if (!money) {
				wfeeObj.val('');
				return false;
			}
			if (money > 0) {
				var rate = parseFloat(wfeeObj.attr('rate-data'));
				var fee = Math.ceil((money * rate) / 10) / 100;
				if (fee < 0) {
					fee = 0;
				}
				wfeeObj.val(fee);
			}
		});
	}

	
	$("form[name='withdraw']").bind("submit",function(){		
		var bank_id = $("form[name='withdraw']").find("input[name='bank_id']").val();
		var money = $("form[name='withdraw']").find("input[name='money']").val();
		var pwd = $("form[name='withdraw']").find("input[name='pwd']").val();
		if($.trim(pwd)=="")
		{
			$.toast("请输入登录密码");
			return false;
		}

		if($.trim(bank_id)==""||isNaN(bank_id)||parseFloat(bank_id)<0)
		{
			$.toast("请选择提现账户");
			return false;
		}
		if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
		{
			$.toast("请输入正确的提现金额");
			return false;
		}
		
		if(fx_money<parseFloat(money)){
			$.toast("提现超额");
			return false;
		}
		
		var ajax_url = $("form[name='withdraw']").attr("action");
		var query = $("form[name='withdraw']").serialize();
		$.ajax({
			url:ajax_url,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status==1){
					$.toast("提现申请成功，请等待管理员审核");
					if(obj.url){
						setTimeout(function(){
							location.href = obj.url;
						},1000);
					}
				}else if(obj.status==0){
					if(obj.info)
					{
						$.toast(obj.info);
						if(obj.url){
							setTimeout(function(){
								location.href = obj.url;
							},1000);
						}
					}
					else
					{
						if(obj.url)location.href = obj.url;
					}
					
				}else{
					
				}
			}
		});		
		return false;
	});

	function init_bank(){
		var bank_name=$(".bank").find(".checked").attr("bank_name");
		var bank_id=$(".bank").find(".checked").attr("rel");
		$("input[name='bank_name']").val(bank_name);
		$("input[name='bank_id']").val(bank_id);
	}


});

$(document).on("pageInit", "#uc_fx_buy_check", function(e, pageId, $page) {
	$('.fee_count').hide();
	init_payment_input();
	//init_pay_btn();
	
	$(".u-sure-pay").bind("click",function(){
		var is_ajax = 1;
		var query = new Object();

		//全额支付
		if($("#all_account_money").hasClass("active"))
		{
			query.all_account_money = 1;
		}
		else
		{
			query.all_account_money = 0;
		}

		//支付方式
		var payment = $("input[name='payment']:checked").val();
		if(!payment)
		{
			payment = 0;
		}
		query.payment = payment;
		query.order_id = order_id;
		query.is_ajax = is_ajax;
		query.act = "pay_done";
		$.ajax({
			url: custom_ajax_url,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.status==1){

					if(data.app_index=='wap' ){  //SKD支付做好后，用 App.pay_sdk支付
						if(data.pay_status==1){
							$.router.load(data.jump, true);
						}else{
							location.href=data.jump;
						}
					} else if( data.app_index=='app' && data.pay_status==1){  //APP余额支付
						 $.router.load(data.jump, true);

					} else if( data.app_index=='app' && data.pay_status==0){  //APP第三方支付
						if(data.online_pay==3){
							try {

								var str = pay_sdk_json(data.sdk_code);
								//console.log(str);
								//$.showErr(str);
								App.pay_sdk(str);
							} catch (ex) {

								$.toast(ex);
								//window.location.reload();
								$.loadPage(location.href);
							}
						}else{
							var pay_json = '{"open_url_type":"1","url":"'+data.jump+'","title":"'+data.title+'"}';

							try {
								App.open_type(pay_json);
								$.confirm('已支付完成？', function () {
//		   							$.showIndicator();
//			   					      setTimeout(function () {
//			   					      	  window.location.reload();
//			   					          $.hideIndicator();
//			   					    }, 500);
									$.loadPage(location.href);

								},function(){
//	   							$.showIndicator();
//		   					      setTimeout(function () {
//		   					      	  window.location.reload();
//		   					          $.hideIndicator();
//		   					    }, 500);
									$.loadPage(location.href);
									// $.toast('cancel');
								});
							} catch (ex) {
								$.toast(ex);
								$.loadPage(location.href);
								//window.location.reload();
							}
						}
					}



				}else{
					
					$.toast(data.info);
				}
			},
			error:function(ajaxobj)
			{

			}
		});
	});
	
});

function init_payment_input(){

	$("input[name='all_account_money']").live("change",function () {

		if($("#all_account_money").hasClass("active")){
			$("#all_account_money").removeClass("active");
		}else{
			$("#all_account_money").addClass("active");
			$("input[name='payment']").prop("checked",false);
		}
		//count_buy_total();
		$('.fee_count').hide();
		$('.fee_count .payment_fee').text(0);
		local_count()
	});
	
	
	$(".payment").live("click",function(){
		$("input[name='payment']").prop("checked",false);
		$(".payment").removeClass('active');
		$(this).siblings("input[name='payment']").prop("checked",true);
		$(this).addClass("active");

		$("#all_account_money").removeClass("active");
		$("input[name='all_account_money']").prop("checked",false);
		var fee = Number($('.active .fee_amount').text());
		if (fee > 0) {
			$('.fee_count .payment_fee').text(fee.toFixed(2));
			$('.fee_count').show();
		} else {
			$('.fee_count .payment_fee').text(0);
			$('.fee_count').hide();
		}
		local_count()
	});

}

function local_count() {
	var total= $('.total_count').text().replace(",","");
	var payment_fee= $('.payment_fee').text().replace(",","");
	var ready_pay = Number(total) + Number(payment_fee);
	$('.ready_pay').text(ready_pay.toFixed(2));
}
$(document).on("pageInit", "#uc_fx_deal", function(e, pageId, $page) {
	
	$(".goods-bd").on('click', '.j-dealed', function() {
		$.toast("您已经代理了此商品");
	});

	init_list_scroll_bottom();
	add_fx_deal();
	data_format_check();

	$('.search').bind('click', function() {
		var fx_search_key = $.trim($('input[name="fx_seach_key"]').val());
		if (fx_search_key == '') {
			$.toast('请输入要搜索的关键字');
			return;
		}
		var param = {
			act: 'deal_fx',
			fx_seach_key: fx_search_key
		};
		$.ajax({
			url: fx_ajax_url,
			data: param,
			success: function(html) {
				$('.j-ajaxlist').html($(html).find('.j-ajaxlist').html());
				init_list_scroll_bottom();
				add_fx_deal();
				data_format_check();
			},
			error: function(err) {
				console.log(err);
			}
		});
	});
	
});

function add_fx_deal() {
	$(".goods-bd").on('click', '.j-deal', function() {
		var that = this;
		var param = {
			act: 'add_user_fx_deal',
			deal_id: $(that).attr('data-id'),
		};

		$.ajax({
			url: fx_ajax_url,
			data: param,
			dataType: 'json',
			success: function(obj) {
				if (obj.status == 1) {
					$.toast(obj.info);
					$(that).unbind('click');
					$(that).addClass('j-dealed').removeClass('j-deal');
					$(that).text('已代理');
					$.toast('代理成功');
					setTimeout(function() {
						($(that).parents('.b-line')).remove();
						if($(".j-ajaxlist li").length==0)
						$(".j-ajaxlist").html('<div class="tipimg no_data">暂无数据</div>');
					}, 2000);
					data_format_check();
				} else if (obj.user_login_status == -1) {
					$.toast(obj.info);
					setTimeout(function() {
						$.router.load(obj.jump);
					}, 2000);
				} else {
					$.toast(obj.info);
				}
			},
			error: function(obj) {
				$.toast('网络异常');
			}
		});
	});
}

function data_format_check() {
	var nodata = '<div class="tipimg no_data">暂无数据</div>';
	var li_len = $('.deal-list').find('li').length;
	if (li_len == 0) {
		if ($('.fx-deal-list').find('.no_data').length == 0) {
			$('.fx-deal-list').html(nodata);
		}
		$('.fx-deal-list .page-load').remove();
	} else if (li_len < 4) {
		$('.fx-deal-list .page-load').remove();
	}
}

$(document).on("pageInit", "#uc_fx_mall", function(e, pageId, $page) {
	var rel = $('.mall-tab .active').attr('rel');
	init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);

	$(".mall-tab a").click(function() {
		$(".mall-tab-item").removeClass('active');
		$(this).addClass('active');
		$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = Number($(this).attr("rel"));
		var hidetab = '#tab' + (rel ? 0 : 1);
		var showtab = '#tab' + rel;
		// console.log(hidetab + '' + showtab);
		$(showtab).removeClass('hide');
		$(hidetab).addClass('hide');
		$('.content').scrollTop(1);
		if(!$.trim($("#tab"+rel).html())){
			var param = {
				type: rel,
				act: 'mall'
			};
			$.ajax({
				url:ajax_url,
				data: param,
				type:"GET",
				success:function(html) {
					$('#item-content').append($(html).find('#item-content').html());
					// $('.content').append($(html).find('.content').html());
					init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				},
				error:function() {
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
	});
});
/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#uc_fx_qrcode", function(e, pageId, $page) {	


    /*提交订单选择配送方式点击事件*/
    var _hei=$(".j-trans-way").height();
    var _rehei=$(".j-red-reward").height();
    $(".popup-box .j-trans-way").css({"bottom":-_hei});
    $(".popup-box .j-red-reward").css({"bottom":-_rehei});
    var _bhei=$(".pup-box-bg").height();


    $(document).on('click',".j-cancel",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });


    $(document).on('click',".j-trans",function () {
    	var index = $(".j-trans").index($(this));
        $(".totop").addClass("vible");
        $(".popup-box .j-red-reward").css({"bottom":-_rehei});
        $(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
        $(".popup-box .j-trans-way").eq(index).css({"transition":"bottom 0.3s linear","bottom":"0"});
        $(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
    });
    $("#uc_fx_qrcode .j-reward").unbind('click').bind('click',function () {
		if($(".totop").hasClass("vible")){
			popupTransition();
			setTimeout(function () {
				$(".totop").removeClass("vible");
			},300);
		}else{
			$(".totop").addClass("vible");
			$(".popup-box .j-trans-way").css({"bottom":-_hei});
			$(".popup-box").css({"transition":"all 0.3s linear","opacity":"1","z-index":"9999"});
			$(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":"0"});
			$(".popup-box .pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0.6"});
		}
    });


    /*弹出层动画效果*/
    function popupTransition() {
        /* $(".j-cancel").parents(".m-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});*/
        $(".popup-box .j-trans-way").css({"transition":"bottom 0.3s linear","bottom":-_hei});
        $(".popup-box .j-red-reward").css({"transition":"bottom 0.3s linear","bottom":-_rehei});
        $(".j-cancel").parents(".popup-box").find(".pup-box-bg").css({"transition":"opacity 0.3s linear","opacity":"0"});
        $(".j-cancel").parents(".popup-box").css({"transition":"all 0.3s linear 0.3s","opacity":"0","z-index":"100"});
		setTimeout(function () {
                $(".j-cancel").parents(".popup-box").css({"z-index":"-1"});
            },300);
    }
    /*弹出层动画效果*/
	var is_luck=false;
    $(document).on('click',".j-reward-list li",function () {
		if(is_luck)return ;
		if($("input[name='qrcode_type']:checked").val()==$(this).find("input[name='qrcode_type']").val())return ;
		
		is_luck=true;
        var lue_name=$(this).find(".pay-way-name .j-company-name").text();
        var lue_momey=$(this).find(".pay-way-name .j-company-money").text();
        var lue_reward=$(this).find(".pay-way-name").text();
		var qrcode=$(this).find(".pay-way-name").attr("qrcode");
		var qrcode_urls=$(this).find(".pay-way-name").attr("qrcode_urls");

        $(this).parents("ul").find("input").prop("checked",false);
		
		$(this).find("input[name='qrcode_type']").prop("checked",true);
		var query = new Object();
		query.qrcode_type=$("input[name='qrcode_type']:checked").val();
		$.ajax({
            url: ajax_url,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					var query2 = new Object();
					query2.is_ajax=1;
					$.ajax({
						url:location.href,
						data:query2,
						type: "POST",
						dataType: "json",
						success:function(obj)
						{
							$(".qrcode img").attr('src',obj.share_mall_qrcode);
							$(".qrcode-info .j-app-share-btn,.qrcode-info .j-openshare").attr('data-share-url',obj.user_data.share_mall_url);
						},
						error:function()
						{
							$.toast('错误');
							//location.href=location.href;
						}
					 });
				}else{
					$.toast(obj.info);
				}
				setTimeout(function () {
					$(".totop").removeClass("vible");
				},500);
				popupTransition();
				is_luck=false;
				//location.href=location.href
			 }
		});
        //setTimeout(function () {
        //    $(".totop").removeClass("vible");
        //},500);
        //popupTransition();
        //count_buy_total();
    });




    /*弹层开始*/
    $(".choose-list .j-choose").click(function(){
        $(this).siblings(".j-choose").removeClass("active");
        $(this).addClass("active");
        setSpecgood();
        var data_value= $(".j-choose.active").attr("data-value");
        var data_value = []; // 定义一个空数组
        var txt = $('.j-choose.active'); // 获取所有文本框
        for (var i = 0; i < txt.length; i++) {
            data_value.push(txt.eq(i).attr("data-value")); // 将文本框的值添加到数组中
        }
        $(".good-specifications span").empty();
        $(".good-specifications span").addClass("isChoose");
        $(".good-specifications span").append("已选规格：");
        $.each(data_value,function(i){
            $(".good-specifications span").append("<em class='tochooseda'>" + data_value[i] + "</em>");
            //传值可以考虑更改这里
            $(".spec-data").attr("data-value"+[i],data_value[i]);
        });
    });


    $(document).on('click',".j-box-bg",function () {
        popupTransition();
        setTimeout(function () {
            $(".totop").removeClass("vible");
        },300);
    });

    function cssAnition() {
        $(".flippedout").removeClass("z-open");
        $(".spec-choose").removeClass("z-open");
        $(".j-flippedout-close").removeClass("showflipped");
        $(".j-open-choose").bind("click",open_choose);
        setTimeout("$('.flippedout').removeClass('showflipped')",300);
    }
});
$(document).on("pageInit", "#uc_fx_vip_buy", function(e, pageId, $page) {
	
    $(".content").scroller('refresh');
	$(".fx_buy").click(function(){
		$.ajax({ 
            url: ajax_url,
            type: "POST",
            dataType: "json",
            success: function(data){
                if(data.status==1){
                    if(data.free){
                    	$.toast(data.info);
	                    setTimeout(function(){
	                    	$.router.load(data.jump, true);
	                    },1000);
                    }else{
                    	$.router.load(data.jump, true);
                    }
                }else{
                    $.toast(data.info);
                    if(data.jump){
	                    setTimeout(function(){
	                    	$.router.load(data.jump, true);
	                    },1000);
                    }
                }
            },
        });
	});
	
	$(document).on('click','.open-protocol', function () {
	    $.popup('.popup-protocol');
    });
});
$(document).on("pageInit", "#uc_home", function(e, pageId, $page) {

	init_list_scroll_bottom();

	_initform('', '');

    /*赞和评论弹出事件*/
    $(".reply-btn").click(function(){
		var act_item_box=$(this).parent().find(".act-item-box");

		var act_item=$(".act-box .act-item-box");

		act_item.each(function(){
            if(act_item.hasClass("trans_late")){
            	act_item.removeClass("trans_late");
            }
        });

        if(act_item_box.hasClass("trans_late")){
        	act_item_box.removeClass("trans_late");
        }else{
        	act_item_box.addClass("trans_late");
      	}
    });

    /*其他区域点击时，如果评论出现，则关闭*/
    $(document).click(function(e){

        if($(e.target).parents(".reply-btn").length==0){
            var act_item=$(".act-box .act-item-box");
            act_item.each(function(){
                if(act_item.hasClass("trans_late")){
                    act_item.removeClass("trans_late");
                }
            });
        };
        if($(e.target).parents(".reply-input-box").length==0){
            var reply_input_box=$(".reply-input-box");
            if(reply_input_box.hasClass("trans_reply")){
                reply_input_box.removeClass("trans_reply");
            }
        };
        if($(e.target).parents(".reply-act-box").length==0){
            var reply_act_box=$(".reply-act-box");
            if(reply_act_box.hasClass("trans_act")){
                reply_act_box.removeClass("trans_act");
            }
        };
        // _initform('', '');

    });


    /*点击回复事件*/
    $(".act-item-box .act-table .act-dp").click(function(e){
        e.stopPropagation();
        $(".reply-act-box").removeClass("trans_act");
        $(".reply-input-box").addClass("trans_reply");
        $(".act-box .act-item-box").removeClass("trans_late");

        var tid = $(this).parents('.item_box').attr('data_id');
        var rid = '';
        _initform(tid, rid);
    });

    /*点击赞事件*/
    $(".act-item-box .act-table .act-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_fav_topic(tid);
    });

    /* 取消点赞 */
    $(".act-item-box .act-table .cancel-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_cancel_fav(tid);
    });


    /*点击取消事件*/
    $(".r-input-btn-box .c_btn").click(function(){
        $(".reply-input-box").removeClass("trans_reply");

    });

    /*评论列表点击事件*/
    $(".reply-list .r-con").click(function(e){
        e.stopPropagation();
        // 回复对象名称
        var reply_name=$(this).parent().find(".name_link").text();
        var reply_act_box=$(".reply-act-box");
        // 主题ID
        var tid = $(this).parents('.item_box').attr('data_id');
        // 评论ID
        var rid = $(this).parent().attr('data-id');

        $(".act-box .act-item-box").removeClass("trans_late");

        if(reply_name == user_name){
            $(".reply-input-box").removeClass("trans_reply");
            reply_act_box.addClass("trans_act");
            reply_act_box.find('.del_r_data').off('click');
            reply_act_box.find('.del_r_data').on('click', function() {
            	del_reply(tid, rid);
            });
        }else{
            reply_act_box.removeClass("trans_act");
            $(".reply-input-box").addClass("trans_reply");
            $("input[name='reply_txt']").attr('placeholder', "回复@"+reply_name+":");
            _initform(tid, rid);
        }
    });

    /*取消按钮点击事件*/
    $(".r-act-item .cancel_act").click(function(){
        $(".reply-act-box").removeClass("trans_act");
    });

    // 没有回复对象的留言
    $("form[name='reply_form']").bind("submit",function(){
		var tid = $("input[name='reply_tid']").val();
		var rid = $("input[name='reply_rid']").val();
		var r_txt = $.trim($("input[name='reply_txt']").val());
		if(r_txt != ''){
			if (rid != '') {
				$("input[name='reply_txt']").val($("input[name='reply_txt']").attr('placeholder') + r_txt);
			}
			var url = $("form[name='reply_form']").attr('action');
			var query = $("form[name='reply_form']").serialize();
			$.ajax({
				url:url,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj){
					if(obj.status) {
						$(".r_data_"+tid).append(obj.reply_html);
						if($(".r_data_"+tid).find(".r-item").length>0) {
							$(".r_data_"+tid).parent().show();
						}
						$(window).scrollTop($(".r_sub_data_id_"+obj.reply_data.reply_id).offset().top-($(window).height()/2));
					} else {
						$.toast(obj.info);
					}
				}
			});
		}
		$(".reply-input-box").removeClass("trans_reply");
		$("input[name=reply-txt]").val('');
		_initform('', '');
		return false;
	});

    var imglight2 = new Swiper ('.img-light', {
		onSlideChangeStart: function(swiper){
			var index = $(".img-light-box .swiper-slide-active").attr("rel");
			$(".light-index .now-index").html(index);
		}
	});

	/*
	 *评论图点击显示当前评论所有图片集
	*/
	$(".j_open_img").click(function(){
	    imglight2.removeAllSlides();
		$(".flippedout").addClass("z-open-black");
		$(".flippedout").addClass("showflipped");
		$(".light-txt").addClass("z-open");
		$(".j-flippedout-close").attr("rel","light_firend");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
		var index = 0;
		$(this).parents(".images_box").find(".j_open_img").each(function(index){//动态为查看器添加内容
		console.log(0);
			var url = $(this).children("img").attr("data-lingtsrc");
			index = parseInt(index) + 1;
			imglight2.appendSlide('<div class="swiper-slide" rel="'+ index +'"><img class="j-slide-img2" src="'+ url +'" width="100%"></div>');
		});
		var index = parseInt($(this).attr("data"))-1;//获取点击的是第几张图片
		imglight2.slideTo(index,0);//设置查看器图片为点击的图片
		$(".light-index .light-count").html($(this).parent().children(".j_open_img").length); //设置图片索引总数
		$(".light-index .now-index").html($(this).attr("data"));//设置当前图片索引
	});

    $(".swiper-wrapper").on("click",".j-slide-img2",function(){
    		$(".flippedout").removeClass("z-open-black").removeClass("showflipped");
    		$(".light-txt").removeClass("z-open");
    		$(".img-light-box .j-flippedout-close").removeClass("showflipped");
    		imglight2.removeAllSlides();
    		$(".totop").removeClass("vhide");
    	});

    $(document).on("click",".j-flippedout-close",function(){
        var rel = $(this).attr("rel");
        $(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
        		$(".cancel-shoucan").removeClass("z-open");
        		if(rel == "light_firend"){
        			//关闭图片查看器
        			$(".flippedout").removeClass("z-open-black");
        			$(".light-txt").removeClass("z-open");
        			$(".img-light-box .j-flippedout-close").removeClass("showflipped");
        			imglight2.removeAllSlides();
        }
    });
});

// 回复表单的主题ID和被回复ID设置
function _initform(tid, rid) {
	$('input[name=reply_tid]').attr('value', tid);
    $('input[name=reply_rid]').attr('value', rid);
    $('input[name=reply_txt]').val('');
}

// 评论点击事件
function _reply_click(rid, e) {
	e.stopPropagation();
	// 回复对象名称
	var objclass = '.r_sub_data_id_'+rid;
    var reply_name = $(objclass).find(".name_link").text();
    var reply_act_box = $(".reply-act-box");
    // 主题ID
    var tid = $(objclass).parents('.item_box').attr('data_id');
    // 评论ID
    // var rid = $(objclass).parent().attr('data-id');

    $(".act-box .act-item-box").removeClass("trans_late");

    if(reply_name == user_name){
        $(".reply-input-box").removeClass("trans_reply");
        reply_act_box.addClass("trans_act");
        reply_act_box.find('.del_r_data').off('click');
        reply_act_box.find('.del_r_data').on('click', function() {
        	del_reply(tid, rid);
        });
    }else{
        reply_act_box.removeClass("trans_act");
        $(".reply-input-box").addClass("trans_reply");
        $("input[name='reply_txt']").attr('placeholder', "回复@"+reply_name+":");
        _initform(tid, rid);
    }
}


// 点赞
function do_fav_topic(tid){
	var item_box_id = '.item_box_' + tid;
	var zan_list = $(item_box_id).find(".zan_list");
	var zan_status = $(item_box_id).find(".act-zan");
	
	var query = new Object();
	query.id = tid;
	query.act = "do_fav_topic";
	$.ajax({
		url:ajax_url,
		data:query,
		type:"POST",
		dataType:"json",
		success:function(obj){ 
			if (obj.status == -1) {
				// 未登录处理
				$.router.load($obj.jump, true);
				return false;
			} else if (obj.status) {
				if(zan_list.hasClass("zan_list_show")){
		            // 
		        }else{
		            zan_list.addClass("zan_list_show");
		        }
		        zan_list.append('<i class="iconfont">&#xe8ef;</i><span class="zan_name zan_name_'+obj.do_fav.id+'">'+obj.do_fav.user_name+'</span>');
		        zan_status.off('click');
		        zan_status.addClass('cancel-zan').removeClass('act-zan');
		        zan_status.attr('onclick', 'do_cancel_fav('+tid+');');
		        $(item_box_id).find('.zan_text').text('取消');
			}
			$('.act-item-box').css({'right':'-160px'});
		}
	});
}
// 取消赞
function do_cancel_fav(tid) {
	var item_box_id = '.item_box_' + tid;
	var zan_list = $(item_box_id).find(".zan_list");
	var zan_status = $(item_box_id).find(".cancel-zan");
	//console.log(zan_list);return false;
	var query = new Object();
	query.id = tid;
	query.act = "cancel_fav";
	$.ajax({
		url:ajax_url,
		data:query,
		type:"POST",
		dataType:"json",
		success:function(obj){ 
			if (obj.status == -1) {
				// 未登录处理
				$.router.load($obj.jump, true);
				return false;
			} else if (obj.status) {
				if (zan_list.find('i').length <= 1) {
					// 如果只有一个人赞
					zan_list.removeClass('zan_list_show');
				}
				var fav_id = '.zan_name_'+obj.do_fav.id;
		        zan_list.find(fav_id).prev().remove();
		        zan_list.find(fav_id).remove();
		        zan_status.off('click');
		        zan_status.removeClass('cancel-zan').addClass('act-zan');
		        zan_status.attr('onclick', 'do_fav_topic('+tid+');');
		        $(item_box_id).find('.zan_text').text('赞');
			}
			$('.act-item-box').css({'right':'-160px'});
		}
	});
}


function cancel_act(){
	var reply_act_box=$(".reply-act-box");
    if(reply_act_box.hasClass("trans_act")){
        reply_act_box.removeClass("trans_act");
    }
}

// 删除评论
function del_reply(id,reply_id){
	var query = new Object();
	query.act="del_reply";
	query.reply_id = reply_id;
	$.ajax({
		url:ajax_url,
		data:query,
		type:"POST",
		dataType:"json",
		success:function(obj){
			if(obj.status==1) {
				cancel_act();
				// $(".r_sub_data_id_"+reply_id).fadeOut();
				$(".r_sub_data_id_"+reply_id).remove();
				if($(".r_data_"+id).find(".r-item").length==0 && $('.r_data_'+id).find('.zan_name') == 0){
					$(".r_data_"+id).parent().hide();
				}
					
			}else if(obj.status==-1){
				$.toast(obj.info);
				setTimeout(function(){
					$.toast(obj.jump, true);
				}, 2000);
			}else{
				$.toast(obj.info);
				setTimeout(function(){
					console.log(id+':'+reply_id);
				}, 2000);
			}
		}
	});
}

// 关注和取消关注
function focus_user(uid,o)
{
	var query = new Object();
	query.act = "focus";
	query.uid = uid;
	$.ajax({ 
		url: AJAX_URL,
		data: query,
		dataType: "json",
		success: function(obj){	
			var tag = obj.tag;
			var html = obj.html;
			if(tag==1) { //取消关注
				$(o).html(html);
			}
			if(tag==2) { //关注TA
				$(o).html(html);
			} if(tag==3) {//不能关注自己
				$.toast(html);
			} if(tag==4) { // 未登录
				$.toast(obj.info);
				setTimeout(function() {
					$.router.load(obj.jump, true);
				}, 2000);
			}
				
		},
		error:function(ajaxobj) {
			$.toast('网络被风吹走了');
		}
	});	
}

// 做一个无限下拉的效果
function downTopic(uid, page) {
	var query = {
		act: 'downTopic',
		page: page,
		uid: uid, // 意义不明
	};
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: 'json',
		success: function(obj) {
			console.log(obj.status);
			switch (obj.status) {
				case -1:
					$.toast(obj.info);
						setTimeout(function() {
							$.router.load(obj.jump);
						}, 2000);
					break;
				case 0: // 没有更多消息
					$.toast('no data');
					break;
				case 1:
					$(obj.html).appendTo($('.data_list'));
			}
		}
	});
}
$(document).on("pageInit", "#uc_home_show", function(e, pageId, $page) {

  _initform('', '');

    /*赞和评论弹出事件*/
    $(".reply-btn").click(function(){
    var act_item_box=$(this).parent().find(".act-item-box");

    var act_item=$(".act-box .act-item-box");

    act_item.each(function(){
            if(act_item.hasClass("trans_late")){
              act_item.removeClass("trans_late");
            }
        });

        if(act_item_box.hasClass("trans_late")){
          act_item_box.removeClass("trans_late");
        }else{
          act_item_box.addClass("trans_late");
        }
    });

    /*其他区域点击时，如果评论出现，则关闭*/
    $(document).click(function(e){
        if($(e.target).parents(".reply-btn").length==0){
            var act_item=$(".act-box .act-item-box");
            act_item.each(function(){
                if(act_item.hasClass("trans_late")){
                    act_item.removeClass("trans_late");
                }
            });
        };
        if($(e.target).parents(".reply-input-box").length==0){
            var reply_input_box=$(".reply-input-box");
            if(reply_input_box.hasClass("trans_reply")){
                reply_input_box.removeClass("trans_reply");
            }
        };
        if($(e.target).parents(".reply-act-box").length==0){
            var reply_act_box=$(".reply-act-box");
            if(reply_act_box.hasClass("trans_act")){
                reply_act_box.removeClass("trans_act");
            }
        };
        // _initform('', '');
    });


    /*点击回复事件*/
    $(".act-item-box .act-table .act-dp").click(function(e){
        e.stopPropagation();
        $(".reply-act-box").removeClass("trans_act");
        $(".reply-input-box").addClass("trans_reply");
        $(".act-box .act-item-box").removeClass("trans_late");

        var tid = $(this).parents('.item_box').attr('data_id');
        var rid = '';
        _initform(tid, rid);
    });

    /*点击赞事件*/
    $(".act-item-box .act-table .act-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_fav_topic(tid);
    });

    /* 取消点赞 */
    $(".act-item-box .act-table .cancel-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_cancel_fav(tid);
    });


    /*点击取消事件*/
    $(".r-input-btn-box .c_btn").click(function(){
        $(".reply-input-box").removeClass("trans_reply");

    });

    /*评论列表点击事件*/
    $(".reply-list .r-con").click(function(e){
        e.stopPropagation();
        // 回复对象名称
        var reply_name=$(this).parent().find(".name_link").text();
        var reply_act_box=$(".reply-act-box");
        // 主题ID
        var tid = $(this).parents('.item_box').attr('data_id');
        // 评论ID
        var rid = $(this).parent().attr('data-id');

        $(".act-box .act-item-box").removeClass("trans_late");

        if(reply_name == user_name){
            $(".reply-input-box").removeClass("trans_reply");
            reply_act_box.addClass("trans_act");
            reply_act_box.find('a').on('click', function() {
              del_reply(tid, rid);
            });
        }else{
            reply_act_box.removeClass("trans_act");
            $(".reply-input-box").addClass("trans_reply");
            $("input[name='reply_txt']").attr('placeholder', "回复@"+reply_name+":");
            _initform(tid, rid);
        }
    });

    /*取消按钮点击事件*/
    $(".r-act-item .cancel_act").click(function(){
        $(".reply-act-box").removeClass("trans_act");
    });

    // 回复留言
    $("form[name='reply_form']").bind("submit",function(){
        var tid = $("input[name='reply_tid']").val();
        var rid = $("input[name='reply_rid']").val();
        var r_txt = $.trim($("input[name='reply_txt']").val());
        if(r_txt != ''){
            if (rid != '') {
                $("input[name='reply_txt']").val($("input[name='reply_txt']").attr('placeholder') + r_txt);
            }
            var url = $("form[name='reply_form']").attr('action');
            var query = $("form[name='reply_form']").serialize();
            $.ajax({
                url:url,
                data:query,
                type:"POST",
                dataType:"json",
                success:function(obj){
                    if(obj.status) {
                        $(".r_data_"+tid).append(obj.reply_html);
                        if($(".r_data_"+tid).find(".r-item").length>0) {
                            $(".r_data_"+tid).parent().show();
                        }
                        $(window).scrollTop($(".r_sub_data_id_"+obj.reply_data.reply_id).offset().top-($(window).height()/2));
                    } else {
                        $.toast(obj.info);
                    }
                }
            });
        }
        $(".reply-input-box").removeClass("trans_reply");
        $("input[name=reply-txt]").val('');
        _initform('', '');
        return false;
    });

    var imglight2 = new Swiper ('.img-light', {
        onSlideChangeStart: function(swiper){
            var index = $(".img-light-box .swiper-slide-active").attr("rel");
            $(".light-index .now-index").html(index);
        }
    });

  /*
   *评论图点击显示当前评论所有图片集
  */
  $(".j_open_img").click(function(){
      imglight2.removeAllSlides();
    $(".flippedout").addClass("z-open-black");
    $(".flippedout").addClass("showflipped");
    $(".light-txt").addClass("z-open");
    $(".j-flippedout-close").attr("rel","light_firend");
    $(".totop").addClass("vhide");//隐藏回到头部按钮
    var index = 0;
    $(this).parents(".images_box").find(".j_open_img").each(function(index){//动态为查看器添加内容
    console.log(0);
      var url = $(this).children("img").attr("data-lingtsrc");
      index = parseInt(index) + 1;
      imglight2.appendSlide('<div class="swiper-slide" rel="'+ index +'"><img class="j-slide-img2" src="'+ url +'" width="100%"></div>');
    });
    var index = parseInt($(this).attr("data"))-1;//获取点击的是第几张图片
    imglight2.slideTo(index,0);//设置查看器图片为点击的图片
    $(".light-index .light-count").html($(this).parent().children(".j_open_img").length); //设置图片索引总数
    $(".light-index .now-index").html($(this).attr("data"));//设置当前图片索引
  });

    $(".swiper-wrapper").on("click",".j-slide-img2",function(){
        $(".flippedout").removeClass("z-open-black").removeClass("showflipped");
        $(".light-txt").removeClass("z-open");
        $(".img-light-box .j-flippedout-close").removeClass("showflipped");
        imglight2.removeAllSlides();
        $(".totop").removeClass("vhide");
      });

    $(document).on("click",".j-flippedout-close",function(){
        var rel = $(this).attr("rel");
        $(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
            $(".cancel-shoucan").removeClass("z-open");
            if(rel == "light_firend"){
              //关闭图片查看器
              $(".flippedout").removeClass("z-open-black");
              $(".light-txt").removeClass("z-open");
              $(".img-light-box .j-flippedout-close").removeClass("showflipped");
              imglight2.removeAllSlides();
        }
    });

    /*加载更多操作*/
    var load_page = 2;
    $(".load-move").bind("click",function(){
      var id = $(this).attr("data-id");
      var query = new Object();
      query.id = id;
      query.page = load_page;
      query.act = "load_move_reply";
      $.ajax({
        url:ajax_url,
        data:query,
        type:"POST",
        dataType:"json",
        success:function(obj){
          if(obj.status==1) {
            $(".r_data_"+id).append(obj.reply_html);
            if($(".r_data_"+id).find(".r-item").length>0)
              $(".r_data_"+id).parent().show();
            
            if(obj.is_lock==1){
              $(".load-move").unbind();
              $(".load-move").css("background-color","#A6A6A6");
            }
            load_page++;
          } else if(obj.status==-1) {
            $.toast(obj.info);
            setTimeout(function() {
              $.router.load(obj.jump, true);
            }, 2000);
          }
        }
      });
    });
});




$(document).on("pageInit", "#uc_logistic", function(e, pageId, $page) {
	
	if($(".buttons-tab .tab-link").length>0){
	    var _width=$(".buttons-tab .tab-link.active").find("span").width();
	    var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;
	
	    var btm_line=$(".buttons-tab .bottom_line");
	    btm_line.css({"width":_width+"px","left":_left+"px"});
	
	    var _tabs=$(".tabBox .tab_box");
	}
    $(".buttons-tab .tab-link").click(function () {
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;

        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();

        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");
        init_confirm_button();

    });
    
    if($(".no_delivery").hasClass("active") &&
	   $("input[type='checkbox']").length==$("input[disabled='disabled']").length
	){
		$("#uc_logistic nav.bar-tab .confirm_order").hide();
		$("#uc_logistic nav.bar-tab").addClass('line-white');
	}else{
		init_confirm_button();
	}

	$(".no_delivery_deal").click(function(){
    	if($("input[type='checkbox']").length==$("input[disabled='disabled']").length){
			$("#uc_logistic nav.bar-tab .confirm_order").hide();
			$("#uc_logistic nav.bar-tab").addClass('line-white');
		}else{
			$("#uc_logistic nav.bar-tab .confirm_order").show();
			$("#uc_logistic nav.bar-tab").removeClass('line-white');
		}
    });
	
	var is_confirm=0;
	$(this).find(".confirm_order").unbind("click");
	$(this).find(".confirm_order").bind("click",function(){
		if(is_confirm){
			$.toast("请勿重复点击！");
			return false;
		}
		is_confirm=1
		$.confirm('确认收货？', function() {
			var data_id = $(".tabBox .tab_box.active").attr("data_id");	
			var query = new Object();
			if(data_id){
				query.item_id = data_id;
				query.act = 'verify_delivery';
			}else{
				var order_ids=new Array();
				$(".tabBox .tab_box.active").find("input[name='my-radio']").each(function(){
					order_ids.push($(this).attr("data_id"));
				});
				query.order_ids=JSON.stringify(order_ids);
				query.act = 'verify_no_delivery';
			}
			$.ajax({
				url: order_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					if(obj.status==0){

						$.toast(obj.info);
						is_confirm=0;
					}else if(obj.status == 1){
						$.toast(obj.info);
						window.setTimeout(function(){
							$("#uc_logistic .tabBox .tab_box.active").attr("is_arrival",1);
							init_confirm_button();
							window.location.href=obj.jump;
						},1500);
					}
				},
				error:function(ajaxobj)
				{
					is_confirm=0;
					//if(ajaxobj.responseText!='')
					//alert(ajaxobj.responseText);
				}
						
			});
		},function() {is_confirm=0;})
		
	});
});

function init_confirm_button(){
	var status = $("#uc_logistic .tabBox .tab_box.active").attr("status");
	if(status==1){
		$("#uc_logistic nav.bar-tab .confirm_order").hide();
		$("#uc_logistic nav.bar-tab").addClass('line-white');
	}else{
		$("#uc_logistic nav.bar-tab .confirm_order").show();
		$("#uc_logistic nav.bar-tab").removeClass('line-white');
	}
}
$(document).on("pageInit", "#uc_lottery", function(e, pageId, $page) {
	$(".j-close-warning").click(function(){
		$(this).parent(".m-warning").height(0);
	});
});
/**
 * 
 */
$(document).on("pageInit", "#uc_money_index", function(e, pageId, $page) {
	refreshdata([".uc_money_change"]);
});

var lesstime = 0;
$(document).on("pageInit", "#uc_money_withdraw", function(e, pageId, $page) {
	$(".bank-select").click(function() {
		if(bank==1){
			$(".select-bank").addClass('active');
		}
	});
	$(".mask").click(function() {
		$(".select-bank").removeClass('active');
	});
	$(".bank-list li").click(function() {
		$(".bank-list li .iconfont").removeClass('selected');
		$(this).find('.iconfont').addClass('selected');
		$(".bank-select .bank-info").html($(this).find('.bank-info').html());
		$(".select-bank").removeClass('active');
		$("input[name='bank_id']").val($(this).attr("bank_id"));
	});
	
	$(".select-bank .add-bank").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$(".select-bank .close-btn").click(function(){
		$(".select-bank").removeClass('active');
	});
	
	$("form[name='withdraw']").find("input[name='money']").change(function(){
		var money=parseFloat($(this).val());
		if(money>all_money){
			$.toast("提现超额");
			$(this).val(all_money);
		}
	});

	// 绑定删除用户银行卡的事件
	$('.del_bank').bind('click', function() {
		var bank_id = $(this).attr('data-id');
		var ajax_url = $(this).attr('data-action');
		// if_confirm??
		$.ajax({
			url: ajax_url,
			data: {'bank_id':bank_id},
			dataType: 'json',
			success: function(obj) {
				if (obj.status == 1) {
					$.toast('删除成功');
					// 移除前台展示的DOM
				} else {
					$.toast(obj.info);
				}
			}
		});
		return false;
	});
	function init_bank(){
		var bank_name=$(".bank").find(".checked").attr("bank_name");
		var bank_id=$(".bank").find(".checked").attr("rel");
		$("input[name='bank_name']").val(bank_name);
		$("input[name='bank_id']").val(bank_id);
	}


	submit();
	
	function submit(){
		$(".withdraw_submit").bind("click",function(){	
			$(".withdraw_submit").attr('disabled',"true");
			setTimeout(function(){
				$(".withdraw_submit").removeAttr("disabled");
			},3000);
			var bank_id = $("form[name='withdraw']").find("input[name='bank_id']").val();
			var money = $("form[name='withdraw']").find("input[name='money']").val();
			var pwd = $("form[name='withdraw']").find("input[name='pwd']").val();
			if($.trim(pwd)=="")
			{
				$.toast("请输入登录密码");
				return false;
			}

			if($.trim(bank_id)==""||isNaN(bank_id)||parseFloat(bank_id)<=0)
			{
				$.toast("请选择提现账户");
				setTimeout(function(){
					load_page($(".load_page"));
				},1000);
				return false;
			}
			if($.trim(money)==""||isNaN(money)||parseFloat(money)<=0)
			{
				$.toast("请输入正确的提现金额");
				return false;
			}
			
			var ajax_url = $("form[name='withdraw']").attr("action");
			var query = $("form[name='withdraw']").serialize();
			//console.log(query);
			$.ajax({
				url:ajax_url,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					if(obj.status==1){
						$.toast("提现申请成功，请等待管理员审核");
						if(obj.url){
							setTimeout(function(){
								location.href = obj.url;
							},1500);
						}
					}else if(obj.status==0){
						if(obj.info)
						{
							$.toast(obj.info);
							if(obj.url){
								setTimeout(function(){
									location.href = obj.url;
								},1500);
							}
						}
						else
						{
							if(obj.url)location.href = obj.url;
						}
						
					}
				}
			});		
			return false;
		});
	}
});



$(document).on("pageInit", "#uc_msg_index", function(e, pageId, $page) {
 refreshdata([".uc_msg_change"]);
});



$(document).on("pageInit", "#uc_refund_list", function(e, pageId, $page) {

	init_list_scroll_bottom();

	$(document).on('click', '.refund_view', function() {
		$.router.load($(this).attr('data-src'));
	})
});
/**
 * Created by lynn on 2016/11/17.
 * Update by YXM on 2016/11/28. 路由改版
 */
$(document).on("pageInit", "#uc_review", function(e, pageId, $page) {
    $(".tab-link").click(function () {
         $('.content').scrollTop(1);
    });

    var item_width = $(".j-tab-link[rel='"+eq+"']").width();
	var item_left = $(".j-tab-link[rel='"+eq+"']").offset().left;
	$(".tab-line").css({
		width: item_width,
		left: item_left
	});
	
    init_listscroll(".j_ajaxlist_1",".j_ajaxadd_1");
    init_listscroll(".j_ajaxlist_2",".j_ajaxadd_2");
    
    $(".page").on('click',".j-tab-link",function(){
    	$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		var item_width=$(this).width();
		var item_left=$(this).offset().left;
		$(".tab-line").css({
			width: item_width,
			left: item_left
		});
		if($.trim($("#tab"+rel).html()) == ""){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".tabs").find(".tab").removeClass("active");
					$(".tabs").append($(html).find(".tabs").html());
					init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				},
				error:function()
				{
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
	});
    
});
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 吴庆祥
// +----------------------------------------------------------------------
// | FileName: 
// +----------------------------------------------------------------------
// | DateTime: 2017-05-08 19:07
// +----------------------------------------------------------------------
$(document).on("pageInit", "#uc_score_buy_score", function(e, pageId, $page){
    //支付金额点击绑定
    $(".select_num").bind("click",function(){
        var money=getMoney($(this));
        if(!judge_account_money(money,1))return;
        $(".money_number").removeClass("selected");
        $(this).addClass("selected");
        setScore(money);

    });
    $(".first_number").trigger("click");
   if(score_number_array_other){
       $(".select_other").picker({
           toolbarTemplate: '<header class="bar bar-nav">\
          <button class="button button-link pull-right close-picker">确定</button>\
          <h1 class="title">请选择金额</h1>\
          </header>',
           cols: [
               {
                   textAlign: 'center',
                   values: score_number_array_other
               }
           ],
           onClose:function(){
              var money=getMoney($(".select_other"));
              if(!judge_account_money(money,0)){
                  $(".select_other").val("");
                  return;
              }else{
                  setScore(money);
              }
           },
           onOpen:function(){
               var money=getMoney($(".select_other"));
               if(!judge_account_money(money,0)){
                   $(".select_other").picker("close");
                   return;
               };
               $(".money_number").removeClass("selected");
               $(".select_other").addClass("selected");
               setScore(money);
           }
       });
   }


    //支付按钮绑定
    $("input[name='all_account_money']").bind("click",function () {
        if($("#all_account_money").hasClass("active")){
            $("#all_account_money").removeClass("active");
            $("input[name='all_account_money']").prop("checked",false);
        }else{
            var money=getMoney();
            if(!judge_account_money(money,1)){
                $("input[name='all_account_money']").prop("checked",false);
                return;
            }else{
                $("#all_account_money").addClass("active");
            }

        }
        $("input[name='payment_id']").prop("checked",false);
    });
    $(".payment").bind("click",function(){
        $("input[name='payment_id']").prop("checked",false);
        $(this).siblings("input[name='payment_id']").prop("checked",true);
        $("input[name='all_account_money']").prop("checked",false);
        $("#all_account_money").removeClass("active");
    });
    $("#submit").bind("click",function(){
        var $form=$("form[name=buy_score]");
        //判断数据
        if($(".money_number.selected").length<1){
            $.toast("请选择充值金额!");
            return;
        }
        if(!$("input[name=all_account_money]:checked").val()&&!$("input[name=payment_id]:checked").val()){
            $.toast("请选择充值方式!");
            return;
        }
        var url=$form.attr("data-action");
        var data=$form.serialize();
        $.ajax({
            url:url,
            data:data,
            type:"POST",
            dataType:"json",
            success:function(data){
                if(data.status == 1){
                  if(data.info){
                      $.alert(data.info,"成功",function(){
                          if(data.jump)
                              window.location=data.jump;
                      });
                  }else{
                      if(data.jump)
                          window.location=data.jump;
                  }

                }else{
                    if(data.info){
                        $.alert(data.info,"失败",function(){
                            if(data.jump)
                                window.location=data.jump;
                        });
                    }else{
                        if(data.jump)
                            window.location=data.jump;
                    }

                }
            }
            ,error:function(){
                $.showErr("服务器提交错误");
            }
        })

    });
    function intval(p){
        if(!p)return 0;
        if(typeof p=="number"){
            return parseInt(p);
        }else if(typeof p=="string"){
            return parseInt(p.replace(/[^0-9\.]*/ig,""));
        }
    }
    function getMoney($this){
        if(!$this){
            $this=$(".money_number.selected");
        }
        var money=0;
        if($this.hasClass("select_other")){
            money=intval($this.val())
        }else{
            money=intval($this.attr("data-money"));
        }
        return money;
    }
    function setScore(val){
       if(val){
           $("input[name=money]").val(val);
           var usable=val*usable_score+"积分";
           var frozen=val*frozen_score+"积分";
       }else{
           $("input[name=money]").val(0);
           var usable="请选择购买金额";
           var frozen="请选择购买金额";
       }
        $(".usable").html(usable);
        $(".frozen").html(frozen);

    }
    function judge_account_money(money,money_number_selected){
        if($("input[name=all_account_money]:checked").val()){
            if(intval($("input[name=all_account_money]:checked").val())>money){
                return 1;
            }else{
                $.toast("会员余额不足");
                if(!money_number_selected){
                    setScore(0);
                }
                return 0;
            }
        }else{
            return 1;
        }
    }
});

$(document).on("pageInit", "#uc_share", function(e, pageId, $page) {
	loadScript(jia_url);
	$(".content").scroller('refresh');
	$(".social_share").find(".flex-1").click(function(){
		$(".weixin-share-close").hide();
		$(".weixin-share-tip").hide();
		$(".flippedout").removeClass("z-open").removeClass("showflipped");
		$(".box_share").removeClass("z-open");
	});
	$(".j-weixin-share").on('click', function() {
		$(".weixin-share-close").show();
		$(".weixin-share-tip").show();
		$(".flippedout").addClass("z-open").addClass("showflipped");
	});
	$(".j-flippedout-close").on('click', function() {
		$(".weixin-share-close").hide();
		$(".weixin-share-tip").hide();
	});
});

$(document).on("pageInit", "#uc_store_pay_order", function(e, pageId, $page) {
	init_list_scroll_bottom();
});
$(document).on("pageInit", ".page", function(e, pageId, $page) {
	$(".page").on('click',".j-tab-link",function(){
		var rel = $(this).attr("rel");
		var con_width = $(this).parent().width();
		var item_width = $(this).width();
		var left = con_width - item_width;
		if(rel != 1){
			$(".float-line").css("left",left);
		}else{
			$(".float-line").css("left",0);
		}
	});

	$(".content").on('click',".j-show-more-quan",function(){
		var isOpen = $(this).hasClass("isOpen");
		if (isOpen) {
			$(this).removeClass("isOpen");
			var con_height = $(this).height();
			$(this).siblings(".quan-show").height(con_height);
			$.refreshScroller();
			$(".j-show-more-quan em").html("点击展开");

		} else {
			$(this).addClass("isOpen");
			var con_height = $(this).siblings(".quan-show").children(".quan-list").height();
			$(this).siblings(".quan-show").height(con_height);
			$.refreshScroller();
			$(".content").scroller('refresh');
			$(this).children("em").html("点击收起");

		}
	});

	$(".content").on('click',".j-open-quaninfo",function(){
		$(".pop-up").addClass("open");
		var src = $(this).attr("data");
		var id = $(this).attr("data-id");
		$(".pop-up").children(".img-box").addClass("open");
		$(".j-pop-img").attr("src",src);
		$(".j-quan-id").html(id);
		$(".content").addClass("noscroll");
	});

	$(".page").on('click',".close-pop,.j-close-pop-btn",function(){
		var rel = $(this).attr("rel");
		$(".pop-up").children(".img-box").removeClass("open");
		$(".pop-up").removeClass("open");
		if (rel == "ecv") {
			$(".input-ecv-exchange").val("");
		}else{
			$(".j-quan-id").html("");
			$(".content").removeClass("noscroll");
			setTimeout(function(){
				$(".j-pop-img").attr("src","");
			},300);
		}
	});
});
$(document).on("pageInit", "#uc_youhui", function(e, pageId, $page) {
	
	$(".content").scrollTop(1);
	if($(".content").scrollTop()>0){
		init_listscroll(".j-ajaxlist-"+type,".j-ajaxadd-"+type);
	}
	
	$(".page").on('click',".j-list-choose",function(){
    	$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		$(".j-list-choose").removeClass('active');
	    $(this).addClass('active');
	    tab_line();
		if($.trim($(".j-ajaxlist-"+rel).html()) == ""){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".m-youhui-list").addClass("hide");
					$(".content").append($(html).find(".content").html());
					$(".content").scrollTop(1);
					if($(".content").scrollTop()>0){
						init_listscroll(".j-ajaxlist-"+rel,".j-ajaxadd-"+rel);
					}
				},
				error:function()
				{
					$(".j-ajaxlist-"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			$(".m-youhui-list").addClass("hide");
			$(".j-ajaxlist-"+rel).removeClass("hide");
			$(".content").scrollTop(1);
			if($(".content").scrollTop()>0){
				infinite(".j-ajaxlist-"+rel,".j-ajaxadd-"+rel);
			}
		}
	});
	
    function tab_line() {
	    var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
		      width: init_width,
		      left: init_left
		    });
    }
	tab_line();
	    
	$(".j-youhui").on('click', function() {
	  $(".youhui-link").removeClass('hide');
	  $(".ecv-link").addClass('hide')
	});
	$(".j-ecv").on('click', function() {
	  $(".ecv-link").removeClass('hide');
	  $(".youhui-link").addClass('hide')
	});
	//打开弹层
	$("#uc_youhui").on('click', '.j-support-shop', function() {
	  $(".youhui-mask").addClass('active');
	  $(".support-shop-box").addClass('active');
	  var url=$(this).attr("ajax-url");
	  get_location(url);
	});
	$("#uc_youhui").on('click', '.j-qrcode', function() {
	  $(".youhui-mask").addClass('active');
	  $(".qrcode-box").addClass('active');
	  $(".qrcode-box").find(".youhui-code").html("券码："+$(this).attr("data-sn"));
	  $(".qrcode-box").find(".qrcode img").attr("src",$(this).attr("img-url"));
	  
	  var url=$(this).attr("ajax-url");
	  get_location(url);
	  
	});
	$("#uc_youhui").on('click', '.j-close-mask', function() {
	  $(".youhui-mask").removeClass('active');
	  $(".support-shop-box").removeClass('active');
	  $(".qrcode-box").removeClass('active');
	});
});
function get_location(url){
	$.ajax({
        url:url,
        type:"POST",
        dataType:"json",
        success:function(obj)
        {
        	$(".support-list").empty();
      
        	if(obj.location_info){
        		var length=obj.location_info.length;
        		$(".support-hd").html('本券限以下实体店到店消费使用');
        		var location_li="";
	        	for(var i=0;i<length;i++){
	        		location_li+="<li class='flex-box'>"
									+"<div class='shop-info flex-1 r-line'>"
									+"<a href='"+obj['location_info'][i]['jump']+"'><p class='shop-name'>"+obj['location_info'][i]['name']+"</p></a>"
									+"<p class='shop-address'>"+obj['location_info'][i]['address']+"</p>"
									+"</div><a href='tel:"+obj['location_info'][i]['tel']+"' class='iconfont'>&#xe618;</a></li>";
	        	}
	        	$(location_li).appendTo($(".support-list"));
        	}else{
        		$(".support-hd").html('');
        	}
        }
    });
}
/**
 * Created by Administrator on 2016/11/4.
 */

$(document).on("pageInit", "#user_center", function(e, pageId, $page) {

    refreshdata([".j-order-lamp .swiper-wrapper",".cenList"]);
    $(".u-prompt .pro_close_btn").click(function () {
        $(".u-prompt").addClass("u-trans");
    });
    
    
    var order_child=$(".j-order-lamp .orderShow").length;
    var _width=$(".cenList .list_href").width();
    
    if(order_child<6){
        $("#user_center .orderBox").addClass("orderthan");
    }

    var swiper = new Swiper('.j-order-lamp', {
        scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        grabCursor: true
    });

   /* $(".is_read").click(function(){
    	$(this).find(".s_number").remove();
    });*/
    $(".content").off("click", ".fun-check-login");
    $(".content").on("click", ".fun-check-login",function () {
        var data_url = $(this).attr("data-url");
        if(is_login==0){
            if(app_index=="app"){
                App.login_sdk();
            }else{
                $.router.load(login_url, true);
            }
        }else{

            $.router.load(data_url, true);
            //window.location = data_url;
            //window.location=data_url;
        }
    });
   //  $(".fun-check-login").off("click");
   // $(".fun-check-login").on("click",".content .commonBox",function () {
   //     alert(22);
   //     var data_url = $(this).attr("data-url");
   //     if(is_login==0){
   //         if(app_index=="app"){
   //             $.toast("清先登录");
   //             App.login_sdk();
   //         }else{
   //             $.router.load(login_url, true);
   //         }
   //     }else{
   //         $.loadPage(data_url);
   //         //window.location=data_url;
   //     }
   // });
});
function setTab(name,cursel,n){
	 for(i=1;i<=n;i++){
	  var menu=document.getElementById(name+i);
	  var con=document.getElementById("con_"+name+"_"+i);
	  menu.className=i==cursel?"hover":"";
	  con.style.display=i==cursel?"block":"none";
	 }
}

$(document).on("pageInit", "#user_login_old", function(e, pageId, $page) {
	$(document).on('click','.open-popup', function () {
	var url=$(".open-popup").attr("data-src");
	  $.ajax({
	    url:url,
	    type:"POST",
	    success:function(html)
	    {
	      //console.log("成功");

	      $(".popup-agreement .protocol").html($(html).find(".content .protocol").html());
	      $(".popup-agreement .title").html($(html).find(".title").html());
	    },
	    error:function()
	    {

	    	$(".popup-agreement").html("网络被风吹走啦~");
	      //console.log("加载失败");
	    }
	  });
	});
	$("#com_login_box").bind("submit",function(){

		var user_key = $.trim($(this).find("input[name='user_key']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		if(user_key=="")
		{
			$.showErr("请输入登录帐号");
			return false;
		}
		if(user_pwd=="")
		{
			$.showErr("请输入密码");
			return false;
		}

		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.showSuccess(obj.info,function(){
						location.href = obj.jump;
					});
				}
				else
				{
					$.showErr(obj.info);
				}
			}
		});

		return false;
	});
	$("#ph_login_box").bind("submit",function(){

		var mobile = $.trim($(this).find("input[name='mobile']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());
		if(mobile=="")
		{
			$.showErr("请输入手机号");
			return false;
		}
		if(sms_verify=="")
		{
			$.showErr("请输入收到的验证码");
			return false;
		}

		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.showSuccess(obj.info,function(){
						location.href = obj.jump;
					});
				}
				else
				{
					$.showErr(obj.info);
				}
			}
		});

		return false;
	});



});
$(document).on("pageInit", "#user_register", function(e, pageId, $page) {
	$(document).on('click','.open-popup', function () {
	var url=$(".open-popup").attr("data-src");
	  $.ajax({
	    url:url,
	    type:"POST",
	    success:function(html)
	    {
	      //console.log("成功");

	      $(".popup-agreement .protocol").html($(html).find(".content").html());
	      $(".popup-agreement .title").html($(html).find(".title").html());
	    },
	    error:function()
	    {

	    	$(".popup-agreement").html("网络被风吹走啦~");
	      //console.log("加载失败");
	    }
	  });
	});
	$("#register_box").bind("submit",function(){

		var email = $.trim($(this).find("input[name='email']").val());
		var user_name = $.trim($(this).find("input[name='user_name']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		var cfm_user_pwd = $.trim($(this).find("input[name='cfm_user_pwd']").val());
		if(user_pwd=="")
		{
			$.showErr("请输入密码");
			return false;
		}
		if(user_pwd!=cfm_user_pwd)
		{
			$.showErr("密码输入不匹配，请确认");
			return false;
		}
		if(email=="")
		{
			$.showErr("请输入邮箱地址");
			return false;
		}
		if(user_name=="")
		{
			$.showErr("请输入用户名");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.showSuccess(obj.info,function(){
						location.href = obj.jump;
					});					
				}
				else
				{
					$.showErr(obj.info);
				}
			}
		});
		
		return false;
	});
	
	
	
});
$(document).on("pageInit", "#youhui", function(e, pageId, $page) {
	
	loadScript(jia_url);
	/*倒计时*/

	var nowtime = parseInt($(".j-LeftTime").attr("nowtime"));
	var endtime = parseInt($(".j-LeftTime").attr("endtime"));
	// var leftTime = (endtime - nowtime) / 1000;
	var leftTime = endtime - nowtime;
	leftTimeAct();
	setInterval(leftTimeAct,1000);
	
	function leftTimeAct(){
		if(leftTime > 0)
		{
			var day  = parseInt(leftTime / 24 /3600);
			var hour = parseInt((leftTime % (24 *3600)) / 3600);
			var min  = parseInt((leftTime % 3600) / 60);
			var sec  = parseInt((leftTime % 3600) % 60);
			$(".j-LeftTime").find(".day").html(day);
			$(".j-LeftTime").find(".hour").html(hour);
			$(".j-LeftTime").find(".min").html(min);
			$(".j-LeftTime").find(".sec").html(sec);
			leftTime--;
		}
	}
	// 优惠券领取方法
	$('.isActive').click(function() {
		var ajax_url = $(this).attr("data-src");
		$.ajax({
			url:ajax_url,
			data:'',
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.user_login_status==0){
					$.toast(obj.info);
					setTimeout(function(){
						$.router.load(obj.jump, true);
					}, 2000);
				}
				if(obj.status) {
					$.toast(obj.info);			
				} else {
					$.toast(obj.info);
					$('.isActive').addClass('isOver').removeClass('.isActive');
					setTimeout(function() {
						window.location.reload();
					}, 2000);
				}
			}
		});
	});

	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		youhui_del_collect(youhui_id);
		$(".cancel-shoucan").removeClass("z-open");
	});

	/*
	 *取消收藏按钮弹出后的取消
	 */
	$(".cancel-shoucan .j-cancel").click(function(){
		$(".cancel-shoucan").removeClass("z-open");
		$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
		$(".m-nav-dropdown").removeClass("showdropdown");
		$(".nav-dropdown-con").removeClass("dropdown-open");
	});

	$(".j-head-collect").on("click",function(){
		var is_del = $(this).attr("data-isdel");
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
				youhui_add_collect(youhui_id);
			}
		}
	});
});

// 收藏和取消收藏。。不确定是否需要
function youhui_add_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "add_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(obj){
			if (obj.user_login_status) {
				if(obj.status == 1){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+obj.collect_count+"</em></div>");
					$.toast(obj.info);	
					$(".j-head-collect").attr("data-isdel",1);
					$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
					$(".m-nav-dropdown").removeClass("showdropdown");
					$(".nav-dropdown-con").removeClass("dropdown-open");
				}else{
					$.toast(obj.info);
				}
			} else {
				$.toast("请先登录");
				setTimeout(function(){
					window.location.href=obj.jump;
				},1000);	
			}
		},
		error:function(ajaxobj) {
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}
function youhui_del_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(obj){
			if(obj.status == 1){
				$.toast(obj.info);
				if(obj.collect_count>0){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+obj.collect_count+"</em></div>");
				}else{
					$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
				}
				$(".j-head-collect").attr("data-isdel",0);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			} else{
				$.toast(obj.info);
			}
		},
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}


$(document).on("pageInit", "#youhuis", function(e, pageId, $page) {
	init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);//下拉刷新加载
	function tab_line() {
		var init_width=$(".m-events-tab .active").width();
		var init_left=$(".m-events-tab .active").offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	var tab_length =$(".m-events-tab li").length;
	if(tab_length<6){
		$(".m-events-tab ul").addClass('flex-box');
		$(".m-events-tab ul li").addClass('flex-1');
	}
	else{
		var w_width=$(window).width();
		var item_width=w_width/5.5;
		$(".m-events-tab li").css('width', item_width);
		$(".m-events-tab ul").css('width', item_width*tab_length);
		$(".m-events-tab ul li").addClass('tab-item');
	}
	tab_line();
    $(document).on('click','.j-choose-cate', function () {
    	$(".j-choose-cate").removeClass('active');
		$(this).addClass('active');
		tab_line();
    });


	$(".m-events-tab a").click(function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		$(".m-events-tab a").removeClass('active');
		$(this).addClass('active');
		$(".m-youhui-list").hide();
		var item_width=$(this).width();
		var item_left=$(this).offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: item_width,
			left: item_left
		});
		var url=$(this).attr("data-src");
		var cate_id=$(this).attr("cate-id");
		$(".j_ajaxlist_"+cate_id).show();
		$(".content").scrollTop(1);
		if($(".j_ajaxlist_"+cate_id).html()==null){
			$.ajax({
				url:url,
				type:"POST",
				success:function(html)
				{
					//console.log("成功");
					$(".content").append($(html).find(".content").html());
					init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
				},
				error:function()
				{
					$(".j_ajaxlist_"+cate_id).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
					//console.log("加载失败");
				}
			});
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			}
		}
	});


	var lock = false;
	if(!lock){
	$(document).on("click",".youhui-item",function(){
		if(is_login==0 && app_index=="app"){
            App.login_sdk();
            return false;
        }
		
			if(lock)return ;

			lock  = true;
		var data_id=$(this).attr("data-id");
			var url=$(this).attr("url");
		if(url){
			$.ajax({
				url: url,
				dataType: "json",
				type: "POST",
				success: function(obj){
					$.toast(obj.info);
					if(obj.status==0){
						if(obj.jump){
							$.router.load(obj.jump, true);
						}
					}else if(obj.status==8){
						if(obj.jump){
							$(".youhui-item[data-id='"+data_id+"']").html("立即使用");
							$(".youhui-item[data-id='"+data_id+"']").removeClass("youhui-item");
							$(".youhui-btn[data-id='"+data_id+"']").removeAttr("url");
							$(".youhui-btn[data-id='"+data_id+"']").attr("href",obj.jump);
						}
					}
				},
				error:function()
				{
					$.toast("服务器提交错误");
				}
			});
				lock = false;
		}
	});
	}

});
$(document).on("pageInit", "#youhui_detail", function(e, pageId, $page) {
	
	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		youhui_detail_del_collect(youhui_id);
		$(".cancel-shoucan").removeClass("z-open");
		
	});

	/*
	 *取消收藏按钮弹出后的取消
	 */
	$(".cancel-shoucan .j-cancel").click(function(){
		$(".cancel-shoucan").removeClass("z-open");
		$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
		$(".m-nav-dropdown").removeClass("showdropdown");
		$(".nav-dropdown-con").removeClass("dropdown-open");
	});

	$(".j-head-collect").on("click",function(){

		var is_del = $(this).attr("data-isdel");
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
				youhui_detail_add_collect(youhui_id);
			}

		}
	});
});

// 收藏和取消收藏。。不确定是否需要
function youhui_detail_add_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "add_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(obj){
			if (obj.user_login_status) {
				if(obj.status == 1){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+obj.collect_count+"</em></div>");
					$.toast(obj.info);	
					$(".j-head-collect").attr("data-isdel",1);
					$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
					$(".m-nav-dropdown").removeClass("showdropdown");
					$(".nav-dropdown-con").removeClass("dropdown-open");
				}else{
					$.toast(obj.info);
				}
			} else {
				$.toast("请先登录");
				setTimeout(function(){
					window.location.href=obj.jump;
				},1000);	
			}
		},
		error:function(ajaxobj) {
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}
function youhui_detail_del_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "get",
		success: function(obj){
			if(obj.status == 1){
				$.toast(obj.info);
				if(obj.collect_count>0){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+obj.collect_count+"</em></div>");
				}else{
					$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
				}
				$(".j-head-collect").attr("data-isdel",0);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			} else{
				$.toast(obj.info);
			}
		},
		error:function(ajaxobj){
			$.toast('网络异常..')
		}
	});
}

