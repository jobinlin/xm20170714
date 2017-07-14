$(document).ready(function(){
	
	Date.prototype.format =function(format, date){
        var o = {
	        "M+" : date.getMonth()+1, //month
			"d+" : date.getDate(),    //day
			"h+" : date.getHours(),   //hour
			"m+" : date.getMinutes(), //minute
			"s+" : date.getSeconds(), //second
			"q+" : Math.floor((date.getMonth()+3)/3),  //quarter
			"S" : date.getMilliseconds() //millisecond
        };
        
        if( /(y+)/.test(format) ) {
        	format=format.replace( RegExp.$1, (this.getFullYear()+"").substr(4- RegExp.$1.length) );
        }
        	
        for(var k in o){
        	if( new RegExp("("+ k +")").test(format) ){
        		format = format.replace( RegExp.$1, RegExp.$1.length==1 ? o[k] :("00"+ o[k]).substr((""+ o[k]).length));
        	}
        }
        	
        return format;
    };
	
	
	$(".date_today").click(function(){
		var d =new Date().format('yyyy-MM-dd', new Date());
		$("input[name='begin_time']").val(d);
		$("input[name='end_time']").val(d);
         
	});
	
	$(".date_yesterday").click(function(){
		var end_date = new Date().format('yyyy-MM-dd', new Date());
		
		// 获取当前时间戳(以s为单位)
		var timestamp = Date.parse(new Date());
		// 必须除1000, 否则秒杀都加到尾数的000中去了
		timestamp = timestamp / 1000;
		timestamp = timestamp - 1*24*60*60;
		 
		var newDate = new Date();
		newDate.setTime(timestamp * 1000);
		 
		var begin_date =new Date().format('yyyy-MM-dd', newDate);
 
		$("input[name='begin_time']").val(begin_date);
		$("input[name='end_time']").val(begin_date);
         
	});
	
	
	// 最近7天
	$(".date_week").click(function(){
		
		var end_date = new Date().format('yyyy-MM-dd', new Date());
		
		// 获取当前时间戳(以s为单位)
		var timestamp = Date.parse(new Date());
		// 必须除1000, 否则秒杀都加到尾数的000中去了
		timestamp = timestamp / 1000;
		timestamp = timestamp - 7*24*60*60;
		 
		var newDate = new Date();
		newDate.setTime(timestamp * 1000);
		 
		var begin_date =new Date().format('yyyy-MM-dd', newDate);
 
		$("input[name='begin_time']").val(begin_date);
		$("input[name='end_time']").val(end_date);
         
	});
	
	// 最近1月，就算这个月1号开始
	$(".date_month").click(function(){
		var end_date = new Date().format('yyyy-MM-dd', new Date());
		 
//		var newDate = new Date();
//		newDate.setDate(1);
//		var begin_date =new Date().format('yyyy-MM-dd', newDate);
		
		
		// 获取当前时间戳(以s为单位)
		var timestamp = Date.parse(new Date());
		// 必须除1000, 否则秒杀都加到尾数的000中去了
		timestamp = timestamp / 1000;
		timestamp = timestamp - 30*24*60*60;
		 
		var newDate = new Date();
		newDate.setTime(timestamp * 1000);
		 
		var begin_date =new Date().format('yyyy-MM-dd', newDate);
		

		$("input[name='begin_time']").val(begin_date);
		$("input[name='end_time']").val(end_date);
		
	});
	
	$(".text_font_color").click(function(){
		$(this).css({ color: "#FF4400", fontWeight: "bold" });
		$(this).siblings('a').not($(this)).not(".clear_input_btn").css({ color: "#f80", fontWeight: "normal" });
	});
	
});


