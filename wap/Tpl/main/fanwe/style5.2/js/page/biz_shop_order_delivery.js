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