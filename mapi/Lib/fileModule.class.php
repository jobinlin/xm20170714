<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class fileApiModule extends MainBaseApiModule
{
	/**
	 * 上传头像， 错误返回 error!=0,message错误消息 error=1000表示未登录
	 * 正确时返回error = 0, small_url,middle_url,big_url(暂时不返回)
	 */
	public function upload_avatar()
	{

		$user_login_status = check_login();
		if ($user_login_status == LOGIN_STATUS_LOGINED) {

			if (empty($_FILES)) {
				$data['error'] = 2000;
				goto end;
			}

			$user = $GLOBALS['user_info'];
			$avatar_dir = APP_ROOT_PATH."public/avatar/";
			$avatar_temp_dir = $avatar_dir."temp/";
		
			//创建avatar临时目录
			if (!is_dir($avatar_dir)) {
				@mkdir($avatar_dir);
				@chmod($avatar_dir, 0777);
			}
			if (!is_dir($avatar_temp_dir)) {
				@mkdir($avatar_temp_dir);
				@chmod($avatar_temp_dir, 0777);
			}
			$upd_id = $id = intval($user['id']);
			if (is_animated_gif($_FILES['file']['tmp_name']))
			{
				$rs = save_image_upload($_FILES,"file","avatar/temp",$whs=array());
					
				$im = get_spec_gif_anmation($rs['file']['path'],48,48);
				$temp_name = $avatar_temp_dir.md5(get_gmtime().$upd_id);
				$file_name = $temp_name."_small.jpg";
				file_put_contents($file_name,$im);
				$img_result['file']['thumb']['small']['path'] = $file_name;
		
				$im = get_spec_gif_anmation($rs['file']['path'],120,120);
				$file_name = $temp_name."_middle.jpg";
				file_put_contents($file_name,$im);
				$img_result['file']['thumb']['middle']['path'] = $file_name;
		
				$im = get_spec_gif_anmation($rs['file']['path'],200,200);
				$file_name = $temp_name."_big.jpg";
				file_put_contents($file_name,$im);
				$img_result['file']['thumb']['big']['path'] = $file_name;
			} else{
				$img_result = save_image_upload($_FILES,"file","avatar/temp",$whs=array('small'=>array(48,48,1,0),'middle'=>array(120,120,1,0),'big'=>array(200,200,1,0)));
			}
			if(intval($img_result['error'])!=0)
			{
				$data['errInfo'] = $img_result;
				goto end;
			}
				
			//开始移动图片到相应位置
		
			$uid = sprintf("%09d", $id);
			$dir1 = substr($uid, 0, 3);
			$dir2 = substr($uid, 3, 2);
			$dir3 = substr($uid, 5, 2);
			$path = $dir1.'/'.$dir2.'/'.$dir3;
		
			//创建相应的目录

			if (!is_dir($avatar_dir.$path)) {
				$mkdir = mkdir($avatar_dir.$path, 0777, true);
				if (!$mkdir) {
					logger::write('创建头像目录失败,权限不足');
				}
			}
		
			$id = str_pad($id, 2, "0", STR_PAD_LEFT);
			$id = substr($id,-2);
			$avatar_file_big = $avatar_dir.$path."/".$id."virtual_avatar_big.jpg";
			$avatar_file_middle = $avatar_dir.$path."/".$id."virtual_avatar_middle.jpg";
			$avatar_file_small = $avatar_dir.$path."/".$id."virtual_avatar_small.jpg";
		
		
			@file_put_contents($avatar_file_big, file_get_contents($img_result['file']['thumb']['big']['path']));
			@file_put_contents($avatar_file_middle, file_get_contents($img_result['file']['thumb']['middle']['path']));
			@file_put_contents($avatar_file_small, file_get_contents($img_result['file']['thumb']['small']['path']));
			@unlink($img_result['file']['thumb']['big']['path']);
			@unlink($img_result['file']['thumb']['middle']['path']);
			@unlink($img_result['file']['thumb']['small']['path']);
			@unlink($img_result['file']['path']);
			
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
			{			
				syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_big.jpg");
				syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_middle.jpg");
				syn_to_remote_image_server("./public/avatar/".$path."/".$id."virtual_avatar_small.jpg");
			}
	
			//上传成功更新用户头像的动态缓存
			// update_avatar($upd_id);
			$data['error'] = 0;
		}
		end:

		$data['user_login_status'] = LOGIN_STATUS_LOGINED;

		return output($data);
	}
}