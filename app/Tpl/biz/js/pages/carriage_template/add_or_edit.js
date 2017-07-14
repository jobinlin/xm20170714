var valuation_type = 1;
var region_obj = null;
var lang = ["件", "kg"];
var unit = ["件", "重"];
var ban_citys_data = [];
var rule_index = 0;
var now_index=0;
$(document).ready(function () {
    rule_index=get_rule_index();
    $("#add_region_conf").bind("click", function () {
        add_region_row();
    });
    $("input[name=valuation_type]").bind("click", function (event) {
        var $me=$(this);
        $.showConfirm("改变计价方式则会清空地区配置！", function () {
            valuation_type = parseInt($("input[name=valuation_type]:checked").val()) - 1;
            clear_tpl_form();
            clear_error();
            init_lang();
        }, function () {
            ui_radiobox_off($me);
        });
    });
    $(".citys-input").bind("checkon checkoff", function () {
        $(this).trigger("count", "up");
    });
    $("input[name='carriage_type']").bind("click", function () {
        var $me=$(this);
        $.showConfirm("改变包邮方式则会清空地区配置！",function () {
            init_carriage_type();
            clear_error();
            clear_tpl_form();
        }, function () {
           ui_radiobox_off($me);
        });

    });
    $(".dialog-close").bind("click", function () {
        close_window();
    });
    init();
    init_province_select();
    init_province_count();
    init_check_big_sub();
    init_check_province_sub();
    init_area_count();
    init_city_select();
    init_form();
    init_ban_province_action();
    init_input_after_err_click();
//    init_area_form();
});
/**
 * 模拟打开窗口修改区域城市名
 */
//function init_area_form(){
//    $(".tbl-except tbody .cell-area a").each(function(){
//          select_delivery_regions(this,true);
//          do_submit_opform();
//    });
//}
function decimal_limit(e,len) {

    var obj=e.srcElement || e.target;
    var dot=obj.value.indexOf(".");//alert(e.which);

    len =(typeof(len)=="undefined")?2:len;
    var  key=e.keyCode|| e.which;
    if(key==46&&(!len||(obj.value.match(/\./g)&&obj.value.match(/\./g).length>0))){//整数就不让他输入了小数点
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

function clear_tpl_form() {
    $(".cell-area").parent().remove();
    $(".tbl-except").hide();
    $("input[name='express_start']").val(1);
    $("input[name='express_plus']").val(1);
    $("input[name='express_postage']").val("");
    $("input[name='express_postage_plus']").val("");
}
function init_area_count() {
    $(".region_level_0").bind("checkstatus", function () {
        var $province_input_all = $(this).find(".province-input").length;
        var $province_input = $(this).find(".province-input:checked").length;
        var $area_input = $(this).find(".area-input");
        if ($province_input != 0 && $province_input == $province_input_all) {
            if (!$area_input.attr("checked")) {
                ui_checkbox_on($area_input);
            }
        } else {
            if ($area_input.attr("checked")) {
                ui_checkbox_off($area_input);
            }
        }
    });
}
function init_province_count() {
    $(".province-list .ecity").bind("count", function (e, p) {
        var number = $(this).find(".citys-input:checked").length;
        var $province_input = $(this).find(".province-input");
        var $citys_all = $(this).find(".citys-input");
        var $citys = $(this).find(".citys-input:checked");
        var $check_number = $(this).find(".check_num");
        if (number) {
            $check_number.html("(" + number + ")");
        } else {
            $check_number.html(" ");
        }

        if ($citys.length != 0 && $citys.length == $citys_all.length) {
            if (!$province_input.attr("checked")) {
                ui_checkbox_on($province_input);
            }
        } else {
            if ($province_input.attr("checked")) {
                ui_checkbox_off($province_input);
            }
        }
        if (p == "up") {
            $(this).trigger("checkstatus")
        }
    });
}
function init_form(){
    $(".confirm_form_btn button").bind("click",function() {
        check_deal_publish_form(function(data){
            if(data['status']==0){
               if(data['info']){
                   $.showErr(data['info']+"不能为空!");
               }
            }else if(data['status']==1){
                form_submit();
            }
        });
    });
}
function form_submit() {
    var form = $("form[name='deal_publish_form']");
    var query = $(form).serialize();
    var url = $(form).attr("data-url");
    $.ajax({
        url: url,
        data: query,
        type: "post",
        dataType: "json",
        success: function (data) {
            if (data.status == 0) {
                $.showErr(data.info, function () {
                    if (data.jump && data.jump != "") {
                        location.href = data.jump;
                    }
                });
            } else if (data.status == 1) {
                $.showSuccess(data.info, function () {
                    if (data.jump && data.jump != "") {
                        location.href = data.jump;
                    }
                });
            }
            return false;
        }
    });

}
function check_deal_publish_form(callback){
    var data={};
    var $input;
    var val;
    data['status']=1;
    if($("input[name=name]").val()==""){
        data['status']=0;
        data['info']="模板名称";
        callback(data);
        return;
    }
    if($("select[name=province]").val()==0){
        data['status']=0;
        data['info']="地址中省份";
        callback(data);
        return;
    }
    if($("select[name=city]").val()==0){
        data['status']=0;
        data['info']="地址中城市";
        callback(data);
        return;
    }
    if($("select[name=area]").val()==0){
        data['status']=0;
        data['info']="地址中县区";
        callback(data);
        return;
    }
    $input=$("input[name=carriage_type]:checked");
    if($input.val()==2){
        callback(data);
        return;
    }
    $input=$("input[name=express_start]");
    val=parseFloat($input.val());
    if($input.val()==""||$input.val()==" "){
        data['status']=0;
        $input.addClass("error");
        if(valuation_type){
            $(".default .error5").addClass("active");
        }else{
            $(".default .error1").addClass("active");
        }
    }else{
        if(valuation_type){
           if(!(0.1<=val&&val<10000)){
               data['status']=0;
               $input.addClass("error");
               $(".default .error5").addClass("active");
           }
        }else{
           if(!(1<=val&&val<10000)||!isInt(val)){
               data['status']=0;
               $input.addClass("error");
               $(".default .error1").addClass("active");
           }
        }
    }
    $input=$("input[name=express_postage]");
    if(($input.val()==""||$input.val()==" ")||!(0<=$input.val()&&$input.val()<1000)){
        data['status']=0;
        $input.addClass("error");
        $(".default .error2").addClass("active");
    }
    $input=$("input[name=express_plus]");
    val=parseFloat($input.val());
    if($input.val()==""||$input.val()==" "){
        data['status']=0;
        $input.addClass("error");
        if(valuation_type){
            $(".default .error6").addClass("active");
        }else{
            $(".default .error3").addClass("active");
        }
    }else{
        if(valuation_type){
            if(!(0.1<=val&&val<10000)){
                data['status']=0;
                $input.addClass("error");
                $(".default .error6").addClass("active");
            }
        }else{
            if(!(1<=val&&val<10000)||!isInt(val)){
                data['status']=0;
                $input.addClass("error");
                $(".default .error3").addClass("active");
            }
        }
    }
    $input=$("input[name=express_postage_plus]");
    if(($input.val()==""||$input.val()==" ")||!(0<=$input.val()&&$input.val()<1000)){
        data['status']=0;
        $input.addClass("error");
        $(".default .error4").addClass("active");
    }
    var length=$("#region_conf .tbl-except input.require").length;
    $("#region_conf .tbl-except input.require").each(function(i){
        var $me=$(this);
        var name=$me.attr('name');
        var val=parseFloat($me.val());
            if(name=="region_support_region[]"&&($me.val()==""||$me.val()==" ")){
                data['status']=0;
                $me.parent().find(".J_EditArea").addClass("error");
                $(".tbl-except .error1").addClass("active");
            }
            else if(name=="region_first_weight[]"){
                if(val==""||val==" "){
                    data['status']=0;
                    $me.addClass("error");
                    if(valuation_type){
                        $(".tbl-except .error6").addClass("active");
                    }else{
                        $(".tbl-except .error2").addClass("active");
                    }
                }else{
                    if(valuation_type){
                        if(!(0.1<=val&&val<10000)){
                            data['status']=0;
                            $me.addClass("error");
                            $(".tbl-except .error6").addClass("active");
                        }
                    }else{
                        if(!(1<=val&&val<10000)||!isInt(val)){
                            data['status']=0;
                            $me.addClass("error");
                            $(".tbl-except .error2").addClass("active");
                        }
                    }
                }
            }else if(name=="region_first_fee[]"&&(($me.val()==""||$me.val()==" ")||!(0<=val&&val<1000))){
                data['status']=0;
                $me.addClass("error");
                $(".tbl-except .error3").addClass("active");
            }else if(name=="region_continue_weight[]"){
                if(val==""||val==" "){
                    data['status']=0;
                    $me.addClass("error");
                    if(valuation_type){
                        $(".tbl-except .error7").addClass("active");
                    }else{
                        $(".tbl-except .error4").addClass("active");
                    }
                }else{
                    if(valuation_type){
                        if(!(0.1<=val&&val<10000)){
                            data['status']=0;
                            $me.addClass("error");
                            $(".tbl-except .error7").addClass("active");
                        }
                    }else{
                        if(!(1<=val&&val<10000)||!isInt(val)){
                            data['status']=0;
                            $me.addClass("error");
                            $(".tbl-except .error4").addClass("active");
                        }
                    }
                }
            }
            else if(name=="region_continue_fee[]"&&(($me.val()==""||$me.val()==" ")||!(0<=val&&val<1000))){
                data['status']=0;
                $me.addClass("error");
                $(".tbl-except .error5").addClass("active");
            }
        if((length-1)==i){
            callback(data);
        }
    });
    if(!length){
        callback(data);
    }
}
function init_input_after_err_click(){
     $(".tbl-except input.error").live("click",function(){
          $(this).removeClass("error");
         $(".tbl-except span.error").removeClass("active");
     });
     $(".default input.error").live("click",function(){
         $(this).removeClass("error");
         $(".default span.error").removeClass("active");
     });
    $(".J_EditArea.error").live("click",function(){
        $(this).removeClass("error");
        $(".tbl-except .error1").removeClass("active");
    });
}
function clear_error(){
    $(".error.active").removeClass("active");
    $("input.error").removeClass("error");
}
function init_city_select() {
    $("select[name=city]").change(function () {
        if (parseInt($(this).val()) == 0) {
            var option = '<option value="0">==未选择==</option>';
            $("select[name=area]").html(option);
            $("select[name=area]").removeAttr("init");
            $("dl[name=area]").remove();
            init_ui_select();
            return;
        }
        var query = {};
        query['pid'] = $(this).val();
        $.ajax({
            url: delivery_region_url,
            data: query,
            type: "post",
            dataType: "json",
            success: function (data) {
                var option = '<option value="0">==未选择==</option>';
                if (data.status) {
                    for (var i in data.data) {
                        option += '<option value="' + data.data[i].id + '">' + data.data[i].name + '</option>';
                    }
                }
                $("select[name=area]").html(option);
                $("select[name=area]").removeAttr("init");
                $("dl[name=area]").remove();
                init_ui_select();
            }
        });
    });
}
function init_province_select() {
    $("select[name=province]").change(function () {
        var option = '<option value="0">==未选择==</option>';
        $("select[name=area]").html(option);
        $("select[name=area]").removeAttr("init");
        $("dl[name=area]").remove();
        if (parseInt($(this).val()) == 0) {
            $("select[name=city]").html(option);
            $("select[name=city]").removeAttr("init");
            $("dl[name=city]").remove();
            init_ui_select();
            return;
        }
        var query = {};
        query['pid'] = $(this).val();
        $.ajax({
            url: delivery_region_url,
            data: query,
            type: "post",
            dataType: "json",
            success: function (data) {
                var option = '<option value="0">==未选择==</option>';
                if (data.status) {
                    for (var i in data.data) {
                        option += '<option value="' + data.data[i].id + '">' + data.data[i].name + '</option>';
                    }
                }
                $("select[name=city]").html(option);
                $("select[name=city]").removeAttr("init");
                $("dl[name=city]").remove();
                init_ui_select();
            }
        });
    });
}
function init() {
    valuation_type = parseInt($("input[name=valuation_type]:checked").val()) - 1;
    init_lang();
    init_carriage_type();
    $(".onkeypress").attr("onkeypress","return decimal_limit(event,"+valuation_type+")");
}

function init_lang() {
    $("span.lang").html(lang[valuation_type]);
    $("span.unit").html(unit[valuation_type]);
}
function init_carriage_type() {
    var type = parseInt($("input[name=carriage_type]:checked").val());
    if (type == 1) {
        $("#tr_add_region_conf").show();
        $("#tr_valuation_type").show();
    } else {
        $("#tr_add_region_conf").hide();
        $("#tr_valuation_type").hide();
    }
}
function add_region_row() {
    var tp1_html = '<tr data-group="n' + rule_index + '" >' +
        '<td class="cell-area">' +
        '<a href="#" class="acc_popup edit J_EditArea" title="编辑运送区域" onclick="select_delivery_regions(this);">编辑</a>' +
        '<div class="area-group">' +
        '<p>未添加地区</p>' +
        '</div>' +
        '<input type="hidden" class="require" name="region_support_region[]" data-group="n' + rule_index + '" value=""/>' +
        '</td>' +
        '<td><input type="text" name="region_first_weight[]" data-field="start" value="1" class="input-text require" autocomplete="off" onkeypress="return decimal_limit(event,'+(valuation_type)+')" maxlength="6" ></td>' +
        '<td><input type="text" name="region_first_fee[]" data-field="postage" value=" " class="input-text require" autocomplete="off" onkeypress="return decimal_limit(event)" maxlength="6" ></td>' +
        '<td><input type="text" name="region_continue_weight[]" data-field="plus" value="1" class="input-text require" autocomplete="off" onkeypress="return decimal_limit(event,'+(valuation_type)+')" maxlength="6" ></td>' +
        '<td><input type="text" name="region_continue_fee[]" data-field="postageplus" value=" " class="input-text require" autocomplete="off" onkeypress="return decimal_limit(event)" maxlength="6" ></td>' +
        '<td><a href="#" class="J_DeleteRule"  onclick="delete_form(this.parentNode.parentNode);">删除</a></td>' +
        '</tr>';
    if ($("#region_conf .tbl-except").is(":hidden")) {
        $("#region_conf .tbl-except table").append(tp1_html);
        $("#region_conf .tbl-except").show();
    } else {
        $("#region_conf .tbl-except table").append(tp1_html);
    }
    rule_index++;
    load_tbl_except_group();
}
function select_delivery_regions(obj,is_not_open) {

    region_obj = obj;
    now_index = $(obj).parent().parent().data("group");
    init_other_city_data();
    clear_window();
    if(!is_not_open){
        open_window();
    }
    ban_checkbox();
    init_weeboxs_data(region_obj);
}
function delete_form(obj){
    $(obj).remove();
    if($(".tbl-except tbody>tr").length==0){
        $(".tbl-except").hide();
    }
}
function init_other_city_data() {
    ban_citys_data = [];
    $("input[name='region_support_region[]']").each(function () {
        var $me = $(this);
        if ($me.val() && $me.data("group") != now_index) {
            var val = $me.val().split(",");
            for (var i in val) {
                ban_citys_data.push(val[i]);
            }
        }
    });
}
function init_weeboxs_data(obj) {
    var data = $(obj.parentNode).find("input[name='region_support_region[]']").val();
    if (data) {
        data = data.split(",");
        for (var i in data) {
            var $province = $("#region_id_" + parseInt(data[i]));
            $province.parent().removeClass("common_cbo").addClass("common_cbo_checked");
            $province.attr("checked", true);
        }
        $(".region_level_0").trigger("checkstatus");
        $(".province-input").trigger("checkon");
    }

}
function switch_region(obj) {
    var $citys = $(obj);
    var status = $(obj).hasClass("active");
    $(".ecity.active").removeClass("active");
    if (!status) {
        $citys.addClass("active");
    }
}
function switch_city_region(obj) {
    var $citys = $(obj);
    $citys.removeClass("active");
}
function init_check_big_sub() {
    $(".check_big_sub").bind("checkon checkoff", function () {
        var obj = this;
        var region_id = $(obj).val();
        if (!$(obj).attr("checked")) {
            $(".region_level_" + region_id).find(".ui-checkbox input").each(function () {
                var $me = $(this);
                ui_checkbox_off($me);
            });
            $(".region_level_" + region_id).trigger("count", "down");
        }
        else {
            $(".region_level_" + region_id).find(".ui-checkbox input").each(function () {
                var $me = $(this);
                ui_checkbox_on($me);
            });
            $(".region_level_" + region_id).trigger("count", "down");
        }
    })

}
function init_ban_province_action() {
    $(".province-input").bind("banup", function () {
        var $me = $(this);
        var region_id = $me.val();
        var $city_all = $(".region_level_" + region_id).find(".ui-checkbox input");
        var $city = $(".region_level_" + region_id).find(".ui-checkbox input:disabled");
        if ($city_all.length == $city.length) {
            $me.attr("disabled", true);
        }
    })
}
function init_check_province_sub() {
    $(".province-input").bind("checkoff", function () {
        var obj = this;
        var region_id = $(obj).val();
        if (!$(obj).attr("checked")) {
            $(".region_level_" + region_id).find(".ui-checkbox input").each(function () {
                var $me = $(this);
                ui_checkbox_off($me);
            });
        }
        $(obj).trigger("count", "up");
    });
    $(".province-input").bind("checkon", function () {
        var obj = this;
        var region_id = $(obj).val();
        if ($(obj).attr("checked")) {
            $(".region_level_" + region_id).find(".ui-checkbox input").each(function () {
                var $me = $(this);
                ui_checkbox_on($me);
            });
        }
        $(obj).trigger("count", "up");
    })
}
function close_pop() {
    close_window();
}
function do_submit_opform() {
    select_region_ok();
    close_window();
}
function select_region_ok() {
    var $cbo = $(".province-list");
    var ids = '';
    var names = '';
    $cbo.find(".province-input").each(function () {
        var $province_input = $(this);
        if ($province_input.attr("checked")) {
            $(".region_level_" + parseInt($province_input.val())).find(".citys-input:checked").each(function () {
                var $city_input = $(this);
                var id = $city_input.val();
                ids += id + ",";
            });
            names += $province_input.parent().find("span").html() + ",";
        } else {
            $(".region_level_" + parseInt($province_input.val())).find(".citys-input:checked").each(function () {
                var $city_input = $(this);
                var id = $city_input.val();
                ids += id + ",";
                names += $city_input.parent().find("span").html() + ",";
            });
        }
    });
    ids = ids.substr(0, ids.length - 1);
    names = names.substr(0, names.length - 1);
    if (!names) {
        names = "未添加地区";
    }
    $(region_obj.parentNode.parentNode).find("input[name='region_support_region[]']").val(ids);
    $(region_obj.parentNode.parentNode).find(".area-group p").html(names);
}
function open_window() {
    $("#dialog-box").show();
    $("body").prepend('<div style=" left: 0px; top: 0px; width:100%;height: 100%; position: fixed; visibility: visible;" class="ks-ext-mask"></div>');
}
function clear_window(){
    $(".ecity.active").removeClass("active");
    $("label.ui-checkbox.common_cbo_checked").removeClass("common_cbo_checked").addClass("common_cbo");
    $("input[name='region_id[]']:checked").attr("checked", false);
    $("#delivery_form").find("input[name='region_id[]']").attr("disabled", false);
    $("span.check_num").html(" ");
}
function ban_checkbox() {
    for (var i in ban_citys_data) {
        $("#region_id_" + parseInt(ban_citys_data[i])).attr("disabled", true);
    }
    $(".province-input").trigger("banup");
}
function close_window() {
    $("#dialog-box").hide();
    $(".ks-ext-mask").remove();
}
function ui_checkbox_off($me) {
    var val = $me.attr("disabled");
    if (val != "disabled") {
        $me.parent().removeClass("common_cbo_checked").addClass("common_cbo");
        $me.attr("checked", false);
    }
}
function ui_checkbox_on($me) {
    var val = $me.attr("disabled");
    if (val != "disabled") {
        $me.parent().removeClass("common_cbo").addClass("common_cbo_checked");
        $me.attr("checked", true);
    }

}
function ui_radiobox_off($me){
    $me.parent().parent().find(".common_rdo").trigger("click");
}
//更新现在的地区城市设置运费的组编号
function load_tbl_except_group() {
    //获取现在的地区城市设置运费的组编号
    var tbl_except_group_arr = new Array();
    $(".region_conf .tbl-except table tr[data-group]").each(function (i, o) {
        tbl_except_group_arr.push($(o).attr("data-group"));
    });
    $("input[name='tbl_except_group']").val(tbl_except_group_arr.join(","));
}
function get_rule_index(){
   var l=$("#region_conf tbody>tr").length;
   var data='';
    if(l){
        data=$("#region_conf tbody>tr:last").data("group");
        data=data.replace(/[^0-9]/ig,"");
        data++;
    }else{
        data=0;
    }
   return data;
}
function isInt(obj) {
    return obj%1 === 0
}