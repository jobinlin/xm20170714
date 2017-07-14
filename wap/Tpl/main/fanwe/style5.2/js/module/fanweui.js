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




