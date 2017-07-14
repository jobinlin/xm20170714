$(document).ready(function(){
	
	upfile_data = [];
	imgup();//图片上传

	$(".img-show-box .img-item .item_span").live('click',function(){
		var style = $(this).attr('style');
		$(".big_demo_img").attr('style',style).show();
		$(".img_bg_box").show();
	});
	
	$(".big_demo_img .close_demo_img").live('click',function(){
		$(".big_demo_img").hide();
		$(".img_bg_box").hide();
	});

});



/*图片上传*/

function imgup(){
	
	$("#file-btn").live("change",function(){
		if(this.files[0].type=='image/png'||this.files[0].type=='image/jpeg'||this.files[0].type=='image/gif'){	 

		 	var demo_box = $(".img-show-box");
	     	var item_box = '<div ondrop="drop(event,this)" ondragover="allowDrop(event)" draggable="true" ondragstart="drag(event, this)" class="img_load img-item img-index-'+img_index+'" data-index="'+img_index+'"></div>';
	     	if($(".img-show-box .img-item").length >0){
	     		//demo_box.prepend(item_box);
	     		$(item_box).insertBefore($(".img-show-box .add_img"));
	     		if($(".img-show-box .img-item").length==9){
	     			$(".img-show-box .add_img").hide();
	     		}
	     	}else{
	     		$(item_box).insertBefore($(".img-show-box .add_img"));
	     		//demo_box.prepend(item_box);
	     	}
	        lrz(this.files[0], {width:1200, height:900})
		        .then(function(results) {
	        		var data = {
	                    base64: results.base64,
	                    size: results.base64Len // 校验用，防止未完整接收
	                };
	        		upfile_data[img_index] = JSON.stringify(data);
	        		//console.log(JSON.stringify(data));
	        		// console.log(upfile_data.length);
	        		demo_report(results.base64, results.origin.size,img_index);        		
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
function demo_report(base64,size,img_index) {
    var img = new Image();

    if(size === 'NaNKB') size = '';
    if(size>0){
    	var span_html = '<span class="item_span"></span><a class="close-btn" href="javascript:void(0);" onclick="del_img_box(this)"><img src="'+delete_icon+'" /></a>';
    	$(".img_load").html(span_html);
    	$(".img_load").removeClass('img_load');
    	upload_img(img_index);
    }
}

function add_img(){

	if(type==1 && $(".img-show-box .img-item").length<3){
		return $("#file-btn").click();
	}
	if($(".img-show-box .img-item").length == 0){
 		return $("#file-btn").click();
 	}
}

function del_img_box(obj){
	var index = $(obj).parent().attr("data-index");
	delete upfile_data[index];
	$(".img-index-"+index).remove();
	if($(".img-show-box .img-item").length < 9){
		$(".img-show-box .add_img").show();
	}
}

function allowDrop(ev){  
	ev.preventDefault();  
}  
  
var srcdiv = null;  
function drag(ev,divdom){  
	srcdiv=divdom;  
	ev.dataTransfer.setData("text/html",divdom.innerHTML);  
}  
  
function drop(ev,divdom){  
	ev.preventDefault();  
	if(srcdiv != divdom){  
		srcdiv.innerHTML = divdom.innerHTML;  
		divdom.innerHTML=ev.dataTransfer.getData("text/html");  
	}  
} 

function upload_img(img_index){
	 var query = new Object();
	 var img_data = upfile_data[img_index]
	 query.img_data = img_data;
	 query.ctl="file";
	 query.act="upload_img";
	 $.ajax({
		url:AJAX_URL,
		data:query,
		type:"post",
		dataType:"json",
		success:function(data){
			var img_path = '<input type="hidden" name="img[]" value="' + data.file_path + '" />';
			if(data.status==1){
				$(".img-show-box .img-index-"+img_index).append(img_path);
				var style="background-image:url('"+data.file_path_url+"');background-size: cover;background-position: 50% 20%;background-repeat: no-repeat;"
				$(".img-show-box .img-index-"+img_index+" .item_span").attr('style',style);
			}else{
				alert(data.info);
			}

		},
		error:function(){
			alert("服务器提交错误");
		}
	});
}	

