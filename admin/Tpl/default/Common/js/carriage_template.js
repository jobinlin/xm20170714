$(document).ready(function(){
    load_consignee();
    load_carriage_detail();
    load_carriage();
    load_tbl_except_group();
    //是否包邮
    $("input[name='carriage_type']").bind("click",function () {
        if($(this).val()==2){
            if(!confirm("选择\"卖家承担运费\"后，所有区域的运费将设置为0元且原运费设置无法恢复，确定继续么?")){
                return false;
            }
        }

        load_carriage();
    });
    //计价方式切换
    $("input[name='valuation_type']").bind("click",function (){

        if(!confirm("计价方式切换后，所设置当前模板的运费详情将被清空，确定继续么?")){
            return false;
        }

        load_carriage_detail();
        clear_carriage_detail();
    });

    var rule_index = $(".postage-detail .tbl-except table tr[data-group]").length+1;
    $(".J_AddRule").live("click",function () {
        var valuation_type = $("input:radio[name='valuation_type']:checked").val();
        var tp1_html = '<tr data-group="n'+rule_index+'">'+
            '<td class="cell-area">'+
            '<a href="#" class="acc_popup edit J_EditArea" title="编辑运送区域" >编辑</a>'+
            '<div class="area-group">'+
            '<p>未添加地区</p>'+
            '</div>'+
            '<input type="hidden"  data-field="express_areas"  name="express_areas_n'+rule_index+'" value=""/>'+
            '</td>'+
            '<td><input type="text" name="express_start_n'+rule_index+'" data-field="start" value="1" class="input-text " autocomplete="off" maxlength="6" onkeypress="return myNumberic(event,'+(valuation_type-1)+')"></td>'+
            '<td><input type="text" name="express_postage_n'+rule_index+'" data-field="postage" value="" class="input-text " autocomplete="off" maxlength="6" onkeypress="return myNumberic(event)" ></td>'+
            '<td><input type="text" name="express_plus_n'+rule_index+'" data-field="plus" value="1" class="input-text " autocomplete="off" maxlength="6" onkeypress="return myNumberic(event,'+(valuation_type-1)+')" ></td>'+
            '<td><input type="text" name="express_postage_plus_n'+rule_index+'" data-field="postage_plus" value="" class="input-text " maxlength="6" autocomplete="off" onkeypress="return myNumberic(event)" ></td>'+
            '<td><a href="#" class="J_DeleteRule">删除</a></td>'+
            '</tr>';
        if($(".postage-detail .tbl-except").is(":hidden")){
            $(".postage-detail .tbl-except table").append(tp1_html);
            $(".postage-detail .tbl-except").show();
        }else{
            $(".postage-detail .tbl-except table").append(tp1_html);
        }
        rule_index++;

        //更新现在的地区城市设置运费的组编号
        load_tbl_except_group();
    });

    //城市编辑
    $('.J_EditArea').live("click",function () {
        mTop = $(this).offset().top+25;
        sTop = $(window).scrollTop();
        result = mTop - sTop;
        $(".ks-dialog").attr('style','visibility: visible;left: 218px;top: '+result+'px;');

        $(".dialog-areas input[name='edit_citys_index']").val($(this).parent().parent().attr("data-group"));
        open_popup_mask();
        loac_area_box();
    });
    //关闭城市编辑
    $(".ks-ext-close-x,.J_Cancel").bind("click",function () {
        close_popup_mask();
    });
    //城市模块下拉内容
    $(".gareas img").bind("click",function (event) {
        event.stopPropagation();
        if($(this).parent().parent().hasClass("showCityPop")){
            close_city_list();
        }else{
            open_city_list($(this));
        }
    });
    //关闭城市下拉内容
    $(".citys .close_button,.dialog-areas").bind("click",function () {
        close_city_list();
    });
    //城市编辑页面，点击其他区域关闭城市下拉内容事件堵塞

    $("#citylist input[type='checkbox']").bind("click",function () {
        load_city_event_stopPropagation();
        load_check_box_event($(this));
    });



    //选择区域表单提交事件
    $(".dialog-areas form").submit(function(){
        var citys_ids = new Array();
        var citys_names = new Array();

        $("#citylist li .dcity").each(function (i,o) {
            $(o).find(".province-list .ecity .J_Province").each(function (p_i,p_o) {
                if($(p_o).is(':checked')){
                    if($(p_o).parent().parent().find(".citys .J_City:checked").length == $(p_o).parent().parent().find(".citys .J_City").length){
                        citys_names.push($(p_o).parent().find("label").html());
                    }else{
                        $(p_o).parent().parent().find(".citys .J_City").each(function (c_i,c_o) {
                            if($(c_o).is(':checked')){
                                citys_names.push($(c_o).parent().find("label").html());
                            }
                        });
                    }

                    $(p_o).parent().parent().find(".citys .J_City:checked").each(function (c_i,c_o) {
                        citys_ids.push($(c_o).val());
                    });
                }else{
                    $(p_o).parent().parent().find(".citys .J_City:checked").each(function (c_i,c_o) {
                        citys_ids.push($(c_o).val());
                        citys_names.push($(c_o).parent().find("label").html());
                    });
                }
            });
        });
        var citys_ids_str = citys_ids.join(",");
        var citys_names_str = citys_names.join("、");

        var group_data = $(".dialog-areas input[name='edit_citys_index']").val();
        $(".postage-detail .tbl-except table tr[data-group='"+group_data+"']").find(".area-group p").html(citys_names_str);
        $(".postage-detail .tbl-except table tr[data-group='"+group_data+"']").find("input[name='express_areas_"+group_data+"']").val(citys_ids_str);

        close_popup_mask();
        return false;
    });

    //删除指定区域物流运费
    $(".postage-detail .J_DeleteRule").live("click",function () {
        if(confirm("确认要删除当前地区的设置么?")){
            if($(".postage-detail .tbl-except table tr[data-group]").length==1){
                $(".postage-detail .tbl-except").hide();
            }
            $(this).parent().parent().remove();
            load_tbl_except_group();
        }

    });
    $("form[name='edit']").unbind("submit");
    $(".submit_btn").bind("click",function (e) {
        if(check_form_is_err()){
            $("form[name='edit']").submit();
    }else{
            return false;
        }
    });

    $("form[name='add']").unbind("submit");
    $(".submit_btn1").bind("click",function (e) {
        if(check_form_is_err()){
            $("form[name='add']").submit();
    }else{
            return false;
        }
    });
});


function myNumberic(e,len) {

    var obj=e.srcElement || e.target;
    var dot=obj.value.indexOf(".");//alert(e.which);
    len =(typeof(len)=="undefined")?2:len;
    var  key=e.keyCode|| e.which;
    if(key==46&&!len){//整数就不让他输入了小数点
        return false;
    }
    if(key==8 || key==9 || key==46 || (key>=37  && key<=40))//这里为了兼容Firefox的backspace,tab,del,方向键
        return true;
    if (key<=57 && key>=48) { //数字
        if(dot==-1)//没有小数点
            return true;
        else if(obj.value.length<=dot+len)//小数位数
            return true;
    } else if((key==46) && dot==-1){//小数点
        return true;
    }
    return false;
}
//装载配送地区
function load_consignee()
{
    var consignee_id = $("select[name='region_lv1']").val();
    var carriage_id = $("select[name='region_lv1']").attr("data_id");
    if(carriage_id){
        var query = new Object();
        query.act = "load_consignee";
        query.id = consignee_id;
        query.data_id = carriage_id;
        $.ajax({
            url: ROOT+"?"+VAR_MODULE+"=CarriageTemplate"+"&"+VAR_ACTION+"=load_consignee",
            data:query,
            dataType:"json",
            type:"POST",
            success: function(data){
                $("#cart_consignee").html(data.html);

                init_region_ui_change();
            }
        });
    }else{
        init_region_ui_change();
    }


}
/**
 * 初始化地区切换事件
 */
function init_region_ui_change(){

    $.load_select = function(lv)
    {
        var name = "region_lv"+lv;
        var next_name = "region_lv"+(parseInt(lv)+1);
        var id = $("select[name='"+name+"']").val();
        var null_check_str = '';

        if(lv==1){
            var evalStr="regionConf.r"+id+".c";
            null_check_str = '请选择省';
        }

        if(lv==2){
            var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+id+".c";
            null_check_str = '请选择城市';
        }

        if(lv==3){
            var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+$("select[name='region_lv2']").val()+".c.r"+id+".c";
            null_check_str = '请选择区县';
        }


        if(id==0)
        {
            var html = "<option value='0'>"+null_check_str+"</option>";
        }
        else
        {
            var regionConfs=eval(evalStr);
            evalStr+=".";
            var html = "<option value='0'>"+null_check_str+"</option>";
            for(var key in regionConfs)
            {
                html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
            }
        }
        $("select[name='"+next_name+"']").html(html);

        if(regionConfs){
            $("select[name='"+next_name+"']").show();
        }else{
            $("select[name='"+next_name+"']").hide();
        }
        if(lv == 4)
        {
            //load_delivery();
        }
        else
        {
            $.load_select(parseInt(lv)+1);
        }
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

function load_carriage() {
    var carriage_type = $("input:radio[name='carriage_type']:checked").val();
    if(carriage_type == 1){
        $(".carriage_type_1").show();
    }else{
        $(".carriage_type_1").hide();
    }

    load_carriage_detail();
}

function load_carriage_detail() {
    var valuation_type = $("input:radio[name='valuation_type']:checked").val();
    $(".postage-detail").html($(".valuation_type_"+valuation_type).html());

}
function clear_carriage_detail() {
    $(".postage-detail .entity .default input[name='express_start']").val(1);
    $(".postage-detail .entity .default input[name='express_postage']").val('');
    $(".postage-detail .entity .default input[name='plus']").val(1);
    $(".postage-detail .entity .default input[name='express_postage_plus']").val('');
    $(".postage-detail .entity .tbl-except table tr[data-group]").remove();
    $(".postage-detail .entity .tbl-except").hide();
}
function add_rule(obj) {
    if($(".postage-detail .tbl-except").is(":hidden")){
        $(".postage-detail .tbl-except").show();
    }
}

function open_popup_mask() {
    $("body").prepend('<div style="width: 100%; left: 0px; top: 0px; height: 100%; position: fixed; z-index: 0; visibility: visible;" class="ks-ext-mask"></div>');
}
function close_popup_mask() {
    $(".ks-dialog").attr('style','display:none;');
    $(".ks-ext-mask").remove();
}
function open_city_list(obj) {
    $(".ecity").removeClass("showCityPop");
    $(".citys").hide();
    var parent_obj = $(obj).parent().parent();
    parent_obj.addClass("showCityPop");
    parent_obj.find(".citys").show();
}
function close_city_list() {
    $(".ecity").removeClass("showCityPop");
    $(".citys").hide();
}
function load_city_event_stopPropagation() {
    $(".showCityPop .gareas").bind("click",function (event) {
        event.stopPropagation();
    });

    $(".showCityPop .citys").bind("click",function (event) {
        event.stopPropagation();
    });
}

//城市复选框切换事件
function load_check_box_event(obj) {
    var parent_obj = new Object();
    var checked_city_count = 0;
    var checked_province_count = 0;
    //城市级别
    if($(obj).hasClass("J_City")){
        parent_obj = $(obj).parent().parent().parent();

        checked_city_count = parent_obj.find(".citys .areas .J_City:checked").length;

        //选中和取消整个省份
        if(parent_obj.find(".citys .areas .J_City").length==checked_city_count){
            parent_obj.find(".gareas .J_Province").attr("checked","checked");
        }else{
            parent_obj.find(".gareas .J_Province").attr("checked",false);
        }

        checked_province_count = parent_obj.parent().find(".ecity .gareas .J_Province:checked").length;

        //选中和取消整个组
        if(parent_obj.parent().find(".ecity .gareas .J_Province").length==checked_province_count){
            parent_obj.parent().parent().find(".gcity .group-label .J_Group").attr("checked","checked");
        }else{
            parent_obj.parent().parent().find(".gcity .group-label .J_Group").attr("checked",false);
        }
        //数量显示隐藏
        if(checked_city_count>0){
            parent_obj.find(".gareas .check_num").show();
            parent_obj.find(".gareas .check_num").html("("+checked_city_count+")");
        }else{
            parent_obj.find(".gareas .check_num").hide();
            parent_obj.find(".gareas .check_num").html("");
        }
    }
    //省份级别
    if($(obj).hasClass("J_Province")){
        parent_obj = $(obj).parent().parent();
        //选中下级所有
        if($(obj).is(':checked')){
            parent_obj.find(".citys .J_City:checkbox:enabled").attr("checked","checked");
        }else{
            parent_obj.find(".citys .J_City:checkbox:enabled").attr("checked",false);
        }

        checked_city_count = parent_obj.find(".citys .J_City:checked").length;
        checked_province_count = parent_obj.parent().find(".ecity .gareas .J_Province:checked").length;

        //组的选中和取消
        if(parent_obj.parent().find(".ecity .gareas .J_Province").length==checked_province_count){
            parent_obj.parent().parent().find(".gcity .group-label .J_Group").attr("checked","checked");
        }else{
            parent_obj.parent().parent().find(".gcity .group-label .J_Group").attr("checked",false);
        }
        //数量显示隐藏
        if(checked_city_count>0){
            parent_obj.find(".gareas .check_num").show();
            parent_obj.find(".gareas .check_num").html("("+checked_city_count+")");
        }else{
            parent_obj.find(".gareas .check_num").hide();
            parent_obj.find(".gareas .check_num").html("");
        }

    }
    //组级别
    if($(obj).hasClass("J_Group")){
        parent_obj = $(obj).parent().parent().parent();
        if($(obj).is(':checked')){
            parent_obj.find(".province-list .ecity .gareas .J_Province:checkbox:enabled").attr("checked","checked");
            parent_obj.find(".province-list .ecity .gareas .J_Province:checked").each(function(i, o) {
                checked_city_count = 0;
                $(o).parent().parent().find(".citys .areas .J_City:checkbox:enabled").attr("checked","checked");
                checked_city_count = $(o).parent().parent().find(".citys .areas .J_City:checked").length;
                //数量显示隐藏
                if(checked_city_count>0){
                    $(o).parent().find(".check_num").show();
                    $(o).parent().find(".check_num").html("("+checked_city_count+")");
                }else{
                    $(o).parent().find(".check_num").hide();
                    $(o).parent().find(".check_num").html("");
                }
            });
        }else{
            parent_obj.find(".province-list .ecity .gareas .J_Province:checkbox:enabled").attr("checked",false);
            parent_obj.find(".province-list .ecity .gareas .J_Province").each(function(i, o) {
                checked_city_count = 0;
                $(o).parent().parent().find(".citys .areas .J_City:checkbox:enabled").attr("checked",false);
                checked_city_count = $(o).parent().parent().find(".citys .areas .J_City:checked").length;
                //数量显示隐藏
                if(checked_city_count>0){
                    $(o).parent().find(".check_num").show();
                    $(o).parent().find(".check_num").html("("+checked_city_count+")");
                }else{
                    $(o).parent().find(".check_num").hide();
                    $(o).parent().find(".check_num").html("");
                }
            });
        }
    }
}

function loac_area_box() {
    //获取当前编辑的组
    var city_group_id = $(".dialog-areas input[name='edit_citys_index']").val();
    //当前组选中的城市
    var cur_check_city_ids = $(".postage-detail .tbl-except input[name='express_areas_"+city_group_id+"']").val();
    var other_check_city_ids_arr = new Array();
    var other_check_city_ids = '';

    //获取不包括当前组的已被选中的城市
    $(".postage-detail .tbl-except tr .cell-area input").not(".postage-detail .tbl-except tr .cell-area input[name='express_areas_"+city_group_id+"']").each(function (i,o) {
        if($(o).val()){
            other_check_city_ids_arr = $.merge(other_check_city_ids_arr, $(o).val().split(','));
        }
    });

    //清空城市选择框中的所有选项
    $("#citylist").find("input[type='checkbox']").attr("checked",false);
    $("#citylist").find(".check_num").html("");

    //禁用所有被其他组选中的
    $.each(other_check_city_ids_arr,function(n,v){
            $("#city_"+v).attr("disabled","disabled");
        }
    );

    //设置当前组已经选中的
    $.each(cur_check_city_ids.split(','),function(n,v){
            $("#city_"+v).attr("checked","checked");
        }
    );

    //判断如果省份下全被禁用则省份也禁用/有选中的显示数量
    $("#citylist .dcity").each(function (i,o) {
        $(o).find(".province-list .ecity").each(function (p_i,p_o) {
            if($(p_o).find(".citys .J_City:checkbox:disabled").length==$(p_o).find(".citys .J_City").length){
                $(p_o).find(".gareas .J_Province").attr("disabled","disabled");
            }

            if($(p_o).find(".citys .J_City:checkbox:checked").length==$(p_o).find(".citys .J_City").length){
                $(p_o).find(".gareas .J_Province").attr("checked","checked");
            }

            if($(p_o).find(".citys .J_City:checkbox:checked").length>0){
                $(p_o).find(".gareas .check_num").html("("+$(p_o).find(".citys .J_City:checkbox:checked").length+")");
            }

        });
        if($(o).find(".province-list .ecity .gareas .J_Province:checkbox:disabled").length==$(o).find(".province-list .ecity .gareas .J_Province").length){
            $(o).find(".gcity .group-label .J_Group").attr("disabled","disabled");
        }

        if($(o).find(".province-list .ecity .gareas .J_Province:checkbox:checked").length==$(o).find(".province-list .ecity .gareas .J_Province").length){
            $(o).find(".gcity .group-label .J_Group").attr("checked","checked");
        }

    });

}
//更新现在的地区城市设置运费的组编号
function load_tbl_except_group(){
    //获取现在的地区城市设置运费的组编号
    var tbl_except_group_arr = new Array();
    if($(".postage-detail .tbl-except table tr[data-group]").length>0){
        $(".postage-detail .tbl-except table tr[data-group]").each(function (i,o) {
            tbl_except_group_arr.push($(o).attr("data-group"));
        });
        $("input[name='tbl_except_group']").val(tbl_except_group_arr.join(","));
    }

}

function check_form_is_err() {

    var is_err=0;
    var valuation_type = $("input:radio[name='valuation_type']:checked").val();
    var carriage_type = $("input:radio[name='carriage_type']:checked").val();

    if($.trim($("input[name='name']").val())==''){
        alert("模板名称不能为空");
        return false;
    }

    if(carriage_type == 1){
        var v_t_name = '';
        if(valuation_type == 1){
            v_t_name = "件";
            var express_start_err = "首"+v_t_name+"应输入1至9999的整数";
            var express_plus_err = "续"+v_t_name+"应输入1至9999的整数";

        }else{
            v_t_name = "重";
            var express_start_err = "首"+v_t_name+"应输入0.1至9999.9的数字";
            var express_plus_err = "续"+v_t_name+"应输入0.1至9999.9的数字";
        }

        var express_postage_err = "首费应输入0.00至999.99的数字";
        var express_postage_plus_err = "续费应输入0.00至999.99的数字";
        var express_areas_err = "指定地区城市为空或错误";

        var err_html = '';
        $(".J_DefaultMessage").html("");
        //检测默认运费
        $(".postage-detail .default input[type='text']").each(function (i,o) {
            err_html = '';
            var input_val = $.trim($(this).val());
            //首件、首重
            if($(this).attr("data-field")=='start'){
                err_html='<span class="msg J_Message"><span class="error">'+express_start_err+'</span></span>';
                if(valuation_type==1){
                	if(input_val=='' || input_val<=0 || input_val>9999 || input_val%1!==0){
                        is_err++;
                        $(this).addClass("input-err");
                        $(".J_DefaultMessage").append(err_html);
                    }
                }else{
                	if(input_val=='' || input_val<0.1 || input_val>9999.9 ||!(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                        is_err++;
                        $(this).addClass("input-err");
                        $(".J_DefaultMessage").append(err_html);
                    }
                }

            }
            //首费
            if($(this).attr("data-field")=='postage'){
                err_html='<span class="msg J_Message"><span class="error">'+express_postage_err+'</span></span>';
                if(input_val == '' || input_val<0 || input_val>999.99 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                    is_err++;
                    $(this).addClass("input-err");
                    $(".J_DefaultMessage").append(err_html);
                }
            }
            //续件、续重
            if($(this).attr("data-field")=='plus'){
                err_html='<span class="msg J_Message"><span class="error">'+express_plus_err+'</span></span>';
                if(valuation_type==1){
                	if(input_val == ''|| input_val<=0 || input_val>9999 || input_val%1!==0){
                        is_err++;
                        $(this).addClass("input-err");
                        $(".J_DefaultMessage").append(err_html);
                    }
                }else{
                	if(input_val == ''|| input_val<0.1 || input_val>9999.9 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                        is_err++;
                        $(this).addClass("input-err");
                        $(".J_DefaultMessage").append(err_html);
                    }
                }
            }
            //续费
            if($(this).attr("data-field")=='postage_plus'){
                err_html='<span class="msg J_Message"><span class="error">'+express_postage_plus_err+'</span></span>';
                if(input_val == ''|| input_val<0 || input_val>999.99 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                    is_err++;
                    $(this).addClass("input-err");
                    $(".J_DefaultMessage").append(err_html);
                }
            }
        });

        //检测设定区域
        var start_err_count = 0;
        var postage_err_count = 0;
        var plus_err_count = 0;
        var postage_plus_err_count = 0;
        var express_areas_err_count = 0;
        $(".postage-detail .tbl-except input").each(function (i,o) {
            var input_val = $.trim($(this).val());
            //首件、首重
            if($(this).attr("data-field")=='start'){
            	if(valuation_type==1){
            		if(input_val==''||input_val<=0||input_val>9999 || input_val%1!==0){
                        is_err++;
                        start_err_count++;
                        $(this).addClass("input-err");
                    }
            	}else{
            		if(input_val==''||input_val<0||input_val>9999.9 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                        is_err++;
                        start_err_count++;
                        $(this).addClass("input-err");
                    }
            	}
            }
            //首费
            if($(this).attr("data-field")=='postage'){
                if(input_val==''||input_val<0||input_val>999.99 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                    is_err++;
                    postage_err_count++;
                    $(this).addClass("input-err");
                }
            }
            //续件、续重
            if($(this).attr("data-field")=='plus'){
            	if(valuation_type==1){
            		if(input_val==''||input_val<0||input_val>9999 || $(this).val()%1!==0){
                        is_err++;
                        plus_err_count++;
                        $(this).addClass("input-err");
                    }
            	}else{
            		if(input_val==''||input_val<0||input_val>9999.9 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                        is_err++;
                        plus_err_count++;
                        $(this).addClass("input-err");
                    }
            	}
            }
            //续费
            if($(this).attr("data-field")=='postage_plus'){
                if(input_val==''||input_val<0||input_val>999.99 || !(/^[0-9]+(\.[0-9]+)?$/.test( input_val ))){
                    is_err++;
                    postage_plus_err_count++;
                    $(this).addClass("input-err");
                }
            }
            //城市
            if($(this).attr("data-field")=='express_areas'){

                if(input_val==''){
                    is_err++;
                    express_areas_err_count++;
                    $(this).parent().find(".J_EditArea").addClass("areas-err");
                }
            }
        });

        $(".J_SpecialMessage").html("");
        err_html='';
        if(start_err_count>0){
            err_html='<span class="msg J_Message"><span class="error">'+express_start_err+'</span></span>';
            $(".J_SpecialMessage").append(err_html);
        }
        if(postage_err_count>0){
            err_html='<span class="msg J_Message"><span class="error">'+express_postage_err+'</span></span>';
            $(".J_SpecialMessage").append(err_html);
        }
        if(plus_err_count>0){
            err_html='<span class="msg J_Message"><span class="error">'+express_plus_err+'</span></span>';
            $(".J_SpecialMessage").append(err_html);
        }
        if(postage_plus_err_count>0){
            err_html='<span class="msg J_Message"><span class="error">'+express_postage_plus_err+'</span></span>';
            $(".J_SpecialMessage").append(err_html);
        }
        if(express_areas_err_count>0){
            err_html='<span class="msg J_Message"><span class="error">'+express_areas_err+'</span></span>';
            $(".J_SpecialMessage").append(err_html);
        }

        $(".postage-detail .default .input-err").bind("click",function () {
            $(this).removeClass("input-err");
            $(".J_DefaultMessage").html("");
        });

        $(".postage-detail .tbl-except .input-err").bind("click",function () {
            $(this).removeClass("input-err");
            $(".J_SpecialMessage").html("");
        });
        $(".postage-detail .tbl-except .areas-err").bind("click",function () {
            $(this).removeClass("areas-err");
            $(".J_SpecialMessage").html("");
            $(".J_DefaultMessage").html("");
        });
    }
    if(is_err>0){
    	$("input[name='valuation_type']").bind("click",function(){
    		$(".J_SpecialMessage").html("");
            $(".J_DefaultMessage").html("");
    	});
    	$(".postage-detail .J_DeleteRule").bind("click",function () {
            //清除错误提示
    		$(this).parent().parent().parent().find(".input-err").removeClass("input-err");
    		$(".J_SpecialMessage").html("");
            $(".J_DefaultMessage").html("");

        });
    	$("input[name='carriage_type']").bind("click",function(){
    		$(".J_SpecialMessage").html("");
            $(".J_DefaultMessage").html("");
    	});
        return false;
    }else{
        return true;
    }

}