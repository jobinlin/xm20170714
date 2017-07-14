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