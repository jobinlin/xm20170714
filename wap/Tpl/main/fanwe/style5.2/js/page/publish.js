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

