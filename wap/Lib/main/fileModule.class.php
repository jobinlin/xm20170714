<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class fileModule extends MainBaseModule
{
		
	/**
	 * 上传头像， 错误返回 error!=0,message错误消息 error=1000表示未登录
	 * 正确时返回error = 0, small_url,middle_url,big_url(暂时不返回)
	 */
	public function upload_avatar()
	{
		global_run();

		$data = call_api_core('file', 'upload_avatar');
		if($data['user_login_status'] != LOGIN_STATUS_LOGINED) {
			login_is_app_jump();
			//app_redirect(wap_url("index","user#login"));
		}
		
		ajax_return($data);
	}
	
}
?>