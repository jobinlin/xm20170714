$(document).ready(function(){
	$("select[name='pid']").bind("change",function(){
		init_seo();
	});

	init_seo();
	
	$(".search").click(function(){
		var url=$(this).attr("url");
		var query=new Object();
		var val=$(".search_name").val();
		if(val==""){
			alert("请输入城市名");
		}
		query.key=val;
		
		$.ajax({ 
			url: url, 
			data: query,
			type:"post",
			dataType: "json",
			success: function(obj){
				if(obj.status==1){
					$("#city").empty();
					
					$("<option value='0'>==地区==</option>").appendTo($("#city"));
					
					var length=obj.list.length;
					
					for(var i=0;i<length;i++){
						$option=$("<option code='"+obj['list'][i]['code']+"' value='"+obj['list'][i]['name']+"'>"+obj['list'][i]['name']+"</option>");
						$option.appendTo($("#city"));
					}
					
				}else{
					alert(obj.info);
				}
			}
		});
		
	});
	
	$("#city").bind("change",function(){
		$code=$('#city option:selected').attr('code');
		$("input[name='code']").val($code);
		if($code.substr(2,4)=="0000"){
			$("select[name='pid']").val(0);
			init_seo();
		}
		else{
			$("#province").find(".item_input").empty();
			$("#province").find(".item_input").html(province_list);
			
			$("select[name='pid']").bind("change",function(){
				init_seo();
			});
			init_seo();
			$pcode=$code.substr(0,2)+"0000";
			
			if($("select[name='pid']").find("option[code='"+$pcode+"']").length>0){
				$("select[name='pid']").find("option[code='"+$pcode+"']").attr("selected",true);

				init_seo();
			}else{
				
				alert("该地区所属的省份还未添加，请先添加省份");
			}
			
		}
	});
});

function init_seo()
{
	console.log(33);
	if($("select[name='pid']").val()==0 || $("select[name='pid']").length==0)
	{ console.log(11);
		$(".seo").hide();
		$("input[name='citycode']").removeClass('require');
		$(".citycode").hide();
	}
	else
	{console.log(22);
		$(".seo").show();
		$("input[name='citycode']").addClass('require');
		$(".citycode").show();
	}
}