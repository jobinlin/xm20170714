<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class fileModule extends BizBaseModule
{
	
	/**
	 * 通用上传，上传到attachments目录，按日期划分
	 * 错误返回 error!=0,message错误消息, error=1000表示未登录
	 * 正确时返回 error=0, url: ./public格式的文件相对路径  path:物理路径 name:文件名
	 */
	public function upload()
	{	
		global_run();
		if(empty($GLOBALS['account_info']))
		{
			$data['error'] = 1000;  //未登录
			$data['msg'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		
		//上传处理
		//创建comment目录
		if (!is_dir(APP_ROOT_PATH."public/attachment")) { 
	             @mkdir(APP_ROOT_PATH."public/attachment");
	             @chmod(APP_ROOT_PATH."public/attachment", 0777);
	        }
		
	    $dir = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	        
	    $dir = $dir."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	     
	    $dir = $dir."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	        
	    if(app_conf("IS_WATER_MARK")==1)
	    $img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),1,1);
	    else
		$img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),0,1);	
		if(intval($img_result['error'])!=0)	
		{
			ajax_return($img_result);
		}
		else 
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
        	{
        		syn_to_remote_image_server($img_result['file']['url']);
        	}
			
		}	
		
		$data_result['error'] = 0;
		$data_result['url'] = $img_result['file']['url'];
		$data_result['web_40'] = get_spec_image($data_result['url'],40,40,1);
		$data_result['path'] = $img_result['file']['path'];
		$data_result['name'] = $img_result['file']['name'];
		ajax_return($data_result);
		
	}
	
	
	/**
	 * 免登录调用上传
	 */
	public function nologin_upload()
	{
	    //上传处理
	    //创建comment目录
	    if (!is_dir(APP_ROOT_PATH."public/attachment")) {
	        @mkdir(APP_ROOT_PATH."public/attachment");
	        @chmod(APP_ROOT_PATH."public/attachment", 0777);
	    }
	
	    $dir = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	
	    $dir = $dir."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	
	    $dir = $dir."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	
	    if(app_conf("IS_WATER_MARK")==1)
	        $img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),1,1);
	    else
	        $img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),0,1);
	    if(intval($img_result['error'])!=0)
	    {
	        ajax_return($img_result);
	    }
	    else
	    {
	    	
	        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	        {
	            syn_to_remote_image_server($img_result['file']['url']);
	        }
	
	    }
	
	    $data_result['error'] = 0;
	    $data_result['url'] = $img_result['file']['url'];
	    $data_result['path'] = $img_result['file']['path'];
	    $data_result['name'] = $img_result['file']['name'];
	    ajax_return($data_result);
	
	}
	
	/**
	 * 用户注册免登录调用上传
	 */
	public function user_register_upload()
	{
	    //上传处理
	    //创建comment目录
	    if (!is_dir(APP_ROOT_PATH."public/attachment")) {
	        @mkdir(APP_ROOT_PATH."public/attachment");
	        @chmod(APP_ROOT_PATH."public/attachment", 0777);
	    }
	
	    $dir = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	     
	    $dir = $dir."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	
	    $dir = $dir."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	        @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	        @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	    }
	     
	    if(app_conf("IS_WATER_MARK")==1)
	        $img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),1,1);
	    else
	        $img_result = save_image_upload($_FILES,"file","attachment/".$dir,$whs=array(),0,1);
	    if(intval($img_result['error'])!=0)
	    {
	        ajax_return($img_result);
	    }
	    else
	    {
	        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	        {
	            syn_to_remote_image_server($img_result['file']['url']);
	        }
	        	
	    }
	
	    $data_result['error'] = 0;
	    $data_result['url'] = $img_result['file']['url'];
	    $data_result['small_url'] =  get_spec_image($data_result['url'],88,75,1);
	    $data_result['big_url'] = get_spec_image($data_result['url'],600,400);
	    $data_result['path'] = $img_result['file']['path'];
	    $data_result['name'] = $img_result['file']['name'];
	    ajax_return($data_result);
	
	}
	public function do_phone_upload_img()
	{

	    global_run();
		if(empty($GLOBALS['account_info']))
		{
			$data['error'] = 1000;  //未登录
			$data['msg'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			ajax_return($data);
		}
		
		//上传处理
		//创建comment目录
		if (!is_dir(APP_ROOT_PATH."public/attachment")) { 
	             @mkdir(APP_ROOT_PATH."public/attachment");
	             @chmod(APP_ROOT_PATH."public/attachment", 0777);
	        }
		
	    $dir = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	        
	    $dir = $dir."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	     
	    $dir = $dir."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
		$img_result = save_image_upload($_FILES,"imgFile","attachment/".$dir,$whs=array(),0,1);	
		if(intval($img_result['error'])!=0)	
		{
			$data['info']="2333";
	        $data['status']=0;
			ajax_return($data);
		}
		else 
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
        	{
        		syn_to_remote_image_server($img_result['imgFile']['url']);
        	}
			
		}	
		
		$data_result['status'] = 1;
		$data_result['file_url'] = str_replace("./", SITE_DOMAIN.APP_ROOT."/",$img_result['imgFile']['url']);
		$data_result['info']="上传成功";
		ajax_return($data_result);
	    
		/* if($result['status'] == 1)
	    {
	        $list = $result['data'];
	        if(intval($_REQUEST['upload_type'])==0)
	            $file_url = SITE_DOMAIN.APP_ROOT.$list[0]['recpath'].$list[0]['savename'];
	        else
	            $file_url = SITE_DOMAIN.APP_ROOT.$list[0]['bigrecpath'].$list[0]['savename'];

	        $data['file_url']=$file_url;
	        $data['info']=$result['info'];
	        $data['status']=1;
	        ajax_return($data);

	    }
	    else
	    {

	        $data['info']=$result['info'];
	        $data['status']=0;
	        ajax_return($data);
	    } */
	}
	public function upload_img(){
	    $img_data = $_REQUEST['img_data'];
	    $file_path = array();

	    if($img_data){
	
	        //上传处理
	        //创建attachment目录
	        if (!is_dir(APP_ROOT_PATH."public/attachment")) {
	            @mkdir(APP_ROOT_PATH."public/attachment");
	            @chmod(APP_ROOT_PATH."public/attachment", 0777);
	        }
	
	        $dir = to_date(NOW_TIME,"Ym");
	        if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	            @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	            @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	
	        $dir = $dir."/".to_date(NOW_TIME,"d");
	        if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	            @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	            @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	
	        $dir = $dir."/".to_date(NOW_TIME,"H");
	        if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
	            @mkdir(APP_ROOT_PATH."public/attachment/".$dir);
	            @chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
	        }
	        $site_path ="./public/attachment/".$dir;
	        $dir = APP_ROOT_PATH."public/attachment/".$dir;
	        $max_image_size = app_conf("MAX_IMAGE_SIZE");
	        

            $temp_arr = array();
            $json_arr = array();
            $json_arr = (array)json_decode($img_data);
            if ($json_arr['size']<=$max_image_size){
                preg_match("/data:image\/(jpg|jpeg|png|gif);base64,/i",$json_arr['base64'],$res);
                $temp_arr['ext'] = $res[1];
                if(!in_array($temp_arr['ext'],array("jpg","jpeg","png","gif"))){
                    $result['status'] = 0;
                    $result['info'] = '上传文件格式有误';
                    ajax_return($result);
                }
                $temp_arr['size'] = $json_arr['size'];
                $temp_arr['img_data'] = preg_replace("/data:image\/(jpg|jpeg|png|gif);base64,/i","",$json_arr['base64']);
                $temp_arr['file_name'] = time().md5(rand(0,100)).'.'.$temp_arr['ext'];
            }	      

            if (file_put_contents($dir.$temp_arr['file_name'], base64_decode($temp_arr['img_data']))===false) {
                $result['status'] = 0;
                $result['info'] = '上传文件失败';
                ajax_return($result);
            }else{
                $file_path = $site_path.$temp_arr['file_name'];
                
                
                $orgin_file_path = $dir.$temp_arr['file_name'];
                
                if(app_conf("WATER_MARK")!="")
                    $water_mark = get_real_path().app_conf("WATER_MARK");  //水印
                else
                    $water_mark = "";
                $alpha = app_conf("WATER_ALPHA");   //水印透明
                $place = app_conf("WATER_POSITION");  //水印位置
             
                if(file_exists($water_mark) && conf("IS_WATER_MARK"))
                {   
                    require_once(APP_ROOT_PATH."system/utils/es_imagecls.php");
                    $image = new es_imagecls();
                    $image->water($orgin_file_path,$water_mark,$alpha, $place);
                }
                 
                if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
                {
                    syn_to_remote_image_server($file_path);
                }
                
                
                $result['status'] = 1;
                $result['info'] = '上传文件成功';
                $result['file_path'] = $file_path;
                $result['file_path_url']=substr_replace($file_path, SITE_DOMAIN.APP_ROOT, 0,1);
                ajax_return($result);
            }	
	
	    }else{
	        $result['status'] = 0;
	        $result['info'] = '上传文件格式有误';
	        ajax_return($result);
	    }
	

	}
	
}
?>