/**
 * Created by Administrator on 2016/11/28.
 */
	$(document).off('click','.confirm_set');
    $(document).on('click','.confirm_set', function () {
        var _this=$(this);
		if($(this).siblings("input").is(':checked')) {
            $("input[name='is_default']").val('0');
		}else{
            $("input[name='is_default']").val('1');
		}
    });
    
    $(document).off('click','.u-edit_all.active');
    $(document).on('click','.u-edit_all.active', function () {
        $(".u-edit_all").removeClass("active");
        var check=$(".u-edit_all").attr('check');
        if(is_default==1&&$("input[name='is_default']").val()=='0'){
            $.toast("无法取消默认");
            $(".confirm_set").siblings("input").prop('checked',true);
			$("input[name='is_default']").val('1');
			$(".u-edit_all").addClass("active");
            return false;
        }

        var $form=$("#address-form");
        var query = $form.serialize();
        var action = $form.attr("action");
        $.ajax({
            url:action,
            data:query,
            type:"POST",
            dataType:"json",
            success:function(obj){
                if(obj.status==1)
                {
                    if($(".page").length==2){
                        window.location.reload();
                    }else{
                        $(".loadpage").remove();
                        $(".page").last().addClass('page-current');
                        $.ajax({
                            url:ajax_url,
                            type:"POST",
                            success:function(data){
                                $(".u-edit_all").addClass("active");
                                $(".page-current").find(".content").html($(data).find(".content").html());
                                if(is_pick){
                                    var shop_list_height = $(".pick-shop-list li").eq(0).height()+$(".pick-shop-list li").eq(1).height();
                                    $(".pick-shop-list").css('height', shop_list_height);
                                }
                            }
                        });
                    }
                }else
                {
                    $.toast(obj.info);
                    $(".u-edit_all").addClass("active");
                }
            }
        });
    });
    $.get('../public/runtime/region.js', '', function() {
    	init_region_ui_change();
        if(is_region_lv1_reset==''){
            $.load_select("1");
        }
    })
    



function init_region_ui_change(){

    $.load_select = function(lv)
    {
        var name = "region_lv"+lv;
        var next_name = "region_lv"+(parseInt(lv)+1);
        var id = $("select[name='"+name+"']").val();
        var region = '选择省份';
        if(lv==1) {
            var evalStr="regionConf.r"+id+".c";
        }
        if(lv==2) {
            region = '选择城市';
            var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+id+".c";
        }        
        if(lv==3) {
            region = '选择县区';
            var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+$("select[name='region_lv2']").val()+".c.r"+id+".c";
        }
        if(id==0) {
            var html = "<option value='0'>="+region+"=</option>";
        } else {
        	var regionConfs=eval(evalStr);
            // console.log(regionConfs);
            evalStr+=".";
            var html = "<option value='0'>="+region+"=</option>";
            if ($.trim(regionConfs) == '' && lv == 3) {
                
                $('select[name="'+next_name+'"]').parent().hide();
                
            } else {
                $('select[name="'+next_name+'"]').parent().show();
                for(var key in regionConfs) {
                    html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
                }
            }
            
        }
        $("select[name='"+next_name+"']").html(html);
        if(lv == 4) {
            //load_delivery();
        }
        else
        {
            $.load_select(parseInt(lv)+1);
        }
        $('input[name="street"]').val('');
        $('textarea[name="address"]').val('');
        $('input[name="doorplate"]').val('');
        $('input[name="xpoint"]').val('');
        $('input[name="ypoint"]').val('');
    };

    $("select[name='region_lv1']").bind("change",function(){
        $.load_select("1");
    });
    $("select[name='region_lv2']").bind("change",function(){
        $.load_select("2");
    });
    $("select[name='region_lv3']").bind("change",function(){
        $.load_select("3");
    });
    $("select[name='region_lv4']").bind("change",function(){
        $.load_select("4");
    });

}  
    function boxcancel() {
        $('#uc_address_map_pick').hide();
        $('#uc_address_map_pick').removeClass('baidu_mapBox');
        if ($('.map-pop').hasClass('close_page')) {
            $('.close_page').on('click', function() {
                close_page();
            });
        } else {
            $(".close_page2").on("click",function(){
                close_page2();
            });
        }
        $('.map-pop').removeClass('close_page3');
        $('#uc_address_add .u-edit_all').show();
        
    }

    function pickmap(street, addr, x, y) {
        boxcancel();
        $('input[name="street"]').val(street);
        var patt = /^([^(]*?省|)([^(]*?市|)([^(]*?(区|县)|)(.*)/;
        var mat = addr.match(patt);
        var addr1 = mat.pop();
        $('textarea[name="address"]').val(addr1);
        $('input[name=xpoint]').val(x);
        $('input[name=ypoint]').val(y);
    }
    $('.mappick').on('click', function() {

        // 先判断省市区是否选择
        var region = '';
        var r2id = $('select[name="region_lv2"]').val(); // 获取地区的信息先定位地图
        if (r2id != 0) {
            region += $('option[value="'+r2id+'"]').html();
        } else {
            $.toast('请先选择省市区信息');
            return;
        }
        var r3id = $('select[name="region_lv3"]').val(); 
        if (r3id != 0) {
            region += $('option[value="'+r3id+'"]').html();
        } else {
            $.toast('请先选择省市区信息');
            return;
        }
        var r4id = $('select[name="region_lv4"]').val(); 
        if (r4id != 0) {
            region += $('option[value="'+r4id+'"]').html();
        }
        /*var streetd = $('input[name="street"]').val();
        if (streetd) {
            region += streetd;
        }*/

        $('#uc_address_map_pick').show();
        $('#uc_address_map_pick').addClass('baidu_mapBox');

        if ($('.map-pop').hasClass('close_page')) {
            $(".close_page").unbind('click');
        } else {
            $(".close_page2").unbind('click');
        }

        $('.map-pop').addClass('close_page3');
        $('.close_page3').on('click', function() {
            boxcancel();
        })
        $('#uc_address_add .u-edit_all').hide();

        // 百度地图API功能
        var map = new BMap.Map("baidu_allmap");
        var orx = $('input[name="xpoint"]').val();
        var ory = $('input[name="ypoint"]').val();
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
            myValue = region;
            setPlace();
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
                res += '<div class="b-line" onclick="pickmap(\''+sstr+'\',\''+lstr+'\','+cx+','+cy+')"><li class="loca-curr"><h3><i class="search-icon iconfont">&#xe62f;</i><em>[当前]</em>'+sstr+'</h3><p class="loca-curr">'+lstr+'</p></li></div>';
                if (surrPois) {
                    // console.log(surrPois);
                    for (i in surrPois) {
                        var x = surrPois[i].point.lng;
                        var y = surrPois[i].point.lat;
                        res += '<div class="b-line" onclick="pickmap(\''+surrPois[i]['title']+'\',\''+surrPois[i]['address']+'\','+x+','+y+');"><li><h3><i class="search-icon iconfont">&#xe62f;</i>'+surrPois[i]['title']+'</h3><p>'+surrPois[i]['address']+'</p></li></div>';
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

        
        ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
        var _value = e.item.value;
            myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            $("#baidu_searchResultPanel").html("onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue);

            setPlace();
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
            // 定位成功事件
            /*var address = '';
            address += e.addressComponent.province;
            address += e.addressComponent.city;
            address += e.addressComponent.district;
            address += e.addressComponent.street;
            address += e.addressComponent.streetNumber;
            alert("当前定位地址为：" + address);*/
        });
        geolocationControl.addEventListener("locationError",function(e){
            // 定位失败事件
            alert(e.message);
        });
        map.addControl(geolocationControl);
    })