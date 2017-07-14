/**
 * Created by Administrator on 2016/11/4.
 */

$(document).on("pageInit", "#login_out", function(e, pageId, $page) {

   $(".fun-check-login").bind("click",function () {
       if(app_index=='app'){
           App.login_sdk();
       }
   });
   
   $(document).on('click','.open-about', function () {
	   $.popup('.popup-about');
   });
   
});