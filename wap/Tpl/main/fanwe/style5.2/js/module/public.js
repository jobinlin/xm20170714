
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

