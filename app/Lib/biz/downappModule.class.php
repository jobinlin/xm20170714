<?php 
/**
 * 商户手机端应用下载
 * @author jobin.lin
 *
 */
class downappModule extends BizBaseModule{
    public function index(){
        $down_url="";
        if(isWeixin())
        {
        	if (isios()){
        	
        		//$str = '请使用浏览器打开下载：<br>';
        	///	$str = $str.'1.点击右上角的按钮<br>';
        		//$str = $str.'2.选择 在Safari中打开 即可下载app';
        	//	header("Content-Type:text/html; charset=utf-8");
        		//echo $str;
        		echo $GLOBALS['tmpl']->fetch("downapp.html");
        		exit;
        	}else{
        		
        		//$str = '请使用浏览器打开下载：<br>';
        		//$str = $str.'1.点击右上角的按钮<br>';
        		//$str = $str.'2.选择 在浏览器中打开 即可下载app';
        		//header("Content-Type:text/html; charset=utf-8");
        		//echo $str;
        		echo $GLOBALS['tmpl']->fetch("downapp.html");
        		exit;
        	}
        }
        else
        {
	        //商家app下载地址连接
	        if (isios()){
	           // $down_url = app_conf("BIZ_APPLE_PATH");
	            $down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_biz_down_url'");
	        }else{
	            //$down_url = app_conf("BIZ_ANDROID_PATH");
	        	$down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_biz_filename'");
	        }
        }
        app_redirect($down_url);
    }
}

?>