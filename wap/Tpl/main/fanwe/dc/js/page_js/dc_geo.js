$(document).ready(function(){
	//if(getCookie("cancel_geo")!=1)
	//{
	//	if(navigator.geolocation)
	//	{
	//		var html = '44444';
			//$.weeboxs.open(html, {boxid:'get_geo_box',contentType:'text',showButton:true, showCancel:true, showOk:false,title:'定位',width:280,type:'wee',onopen:function(){init_ui_button();}});
	//		 var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};		 
	//		 navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
	//	}
	//}	
	var options = {timeout: 8000};
	var geolocation = new qq.maps.Geolocation(TENCENT_MAP_APPKEY, "myapp");
	geolocation.getLocation(showPosition, showErr, options);
});
function showPosition(p){ 
	has_location = 1;//定位成功;
    m_latitude = p.lat; //纬度
    m_longitude = p.lng;
	userxypoint(m_latitude, m_longitude,'GCJ02');
}
function showErr(p){
	//alert("定位失败");
	console.log("定位失败");
}


function getPositionSuccess(p){  
	has_location = 1;//定位成功; 
    m_latitude = p.coords.latitude; //纬度
    m_longitude = p.coords.longitude;
	userxypoint(m_latitude, m_longitude,'BD09');
}

function getPositionError(error){
    //$.weeboxs.close("get_geo_box");

	switch(error.code){  
	    case error.TIMEOUT:  
	        alert("定位连接超时，请重试");  
	        break;  
	    case error.PERMISSION_DENIED:  
	        alert("您拒绝了使用位置共享服务，查询已取消");  
	        break;  
	    default:
	    	alert("定位失败");		       
	}  
}	 
//将坐标返回到服务端;
function userxypoint(latitude,longitude,type){	 	
		var query = new Object();
		query.m_latitude = latitude;
		query.m_longitude = longitude;
		query.m_type=type;
		//alert(latitude+":"+longitude);
		//return;


		$.ajax({
			url:geo_url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				
				if(data.status==1)
				{

			       $.weeboxs.open("当前位置："+data.info, {boxid:'geo_success_box',contentType:'text',showButton:true, showCancel:false, showOk:true,okBtnName:'使用该地址',title:'定位成功',width:280,type:'wee',onopen:function(){
				   init_ui_button();
				   },onok:function(){
                      location.href = dc_url;
			       }});
		        
				}
				else
				{
                    alert(data.info);
				}
			}
			,error:function(){					
			}
		});		 		
} 	


function setCookie(name, value, iDay){   

    /* iDay 表示过期时间   

    cookie中 = 号表示添加，不是赋值 */   

    var oDate=new Date();   

    oDate.setDate(oDate.getDate()+iDay);       

    document.cookie=name+'='+value+';expires='+oDate;

}

function getCookie(name){

    /* 获取浏览器所有cookie将其拆分成数组 */   

    var arr=document.cookie.split('; ');  

    

    for(var i=0;i<arr.length;i++)    {

        /* 将cookie名称和值拆分进行判断 */       

        var arr2=arr[i].split('=');               

        if(arr2[0]==name){           

            return arr2[1];       

        }   

    }       

    return '';

}