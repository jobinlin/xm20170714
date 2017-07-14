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