<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//开放的公共类，不需RABC验证
class PublicAction extends BaseAction{
	public function login()
	{		
		//验证是否已登录
		//管理员的SESSION
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$adm_id = intval($adm_session['adm_id']);
		
		if($adm_id != 0)
		{
			//已登录
			$this->redirect(u("Index/index"));			
		}
		else
		{
			$this->display();
		}
	}
	public function verify()
	{	
        Image::buildImageVerify(4,1);
    }
    
    //登录函数
    public function do_login()
    {		
    	$adm_name = strim($_REQUEST['adm_name']);
    	$adm_password = strim($_REQUEST['adm_password']);
    	$ajax = intval($_REQUEST['ajax']);  //是否ajax提交

    	if($adm_name == '')
    	{
    		$this->error(L('ADM_NAME_EMPTY',$ajax));
    	}
    	if($adm_password == '')
    	{
    		$this->error(L('ADM_PASSWORD_EMPTY',$ajax));
    	}
    	if(es_session::get("verify") != md5($_REQUEST['adm_verify'])) {
			$this->error(L('ADM_VERIFY_ERROR'),$ajax);
		}
		
		$condition['adm_name'] = $adm_name;
		$condition['is_effect'] = 1;
		$condition['is_delete'] = 0;
		$adm_data = M("Admin")->where($condition)->find();
		if($adm_data) //有用户名的用户
		{
			if($adm_data['adm_password']!=md5($adm_password))
			{
				save_log($adm_name.L("ADM_PASSWORD_ERROR"),0); //记录密码登录错误的LOG
				$this->error(L("ADM_PASSWORD_ERROR"),$ajax);
			}
			else
			{
				//登录成功
				$adm_session['adm_name'] = $adm_data['adm_name'];
				$adm_session['adm_id'] = $adm_data['id'];
				
				
				es_session::set(md5(conf("AUTH_KEY")),$adm_session);
				
				//重新保存记录
				$adm_data['login_ip'] = CLIENT_IP;
				$adm_data['login_time'] = NOW_TIME;
				M("Admin")->save($adm_data);
				save_log($adm_data['adm_name'].L("LOGIN_SUCCESS"),1);
				$this->success(L("LOGIN_SUCCESS"),$ajax);
			}
		}
		else
		{
			save_log($adm_name.L("ADM_NAME_ERROR"),0); //记录用户名登录错误的LOG
			$this->error(L("ADM_NAME_ERROR"),$ajax);
		}
    }
	
    //登出函数
	public function do_loginout()
	{
	//验证是否已登录
		//管理员的SESSION
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
		
		if($adm_id == 0)
		{
			//已登录
			$this->redirect(u("Public/login"));			
		}
		else
		{
			es_session::delete(md5(conf("AUTH_KEY")));
			$this->assign("jumpUrl",U("Public/login"));
			$this->assign("waitSecond",3);
			$this->success(L("LOGINOUT_SUCCESS"));
		}
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

                if(conf("WATER_MARK")!="")
                    $water_mark = get_real_path().conf("WATER_MARK");  //水印
                else
                    $water_mark = "";
                $alpha = conf("WATER_ALPHA");   //水印透明
                $place = conf("WATER_POSITION");  //水印位置
                
                if(file_exists($water_mark) && conf("IS_WATER_MARK"))
                {
                    Image::water($orgin_file_path,$water_mark,$orgin_file_path,$alpha,$place);
                }
                                 
                if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
                {
                    syn_to_remote_image_server($file_path);
                }
 
                $result['status'] = 1;
                $result['info'] = '上传文件成功';
                $result['file_path'] = $file_path;
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