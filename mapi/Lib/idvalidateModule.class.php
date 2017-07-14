<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class idvalidateApiModule extends MainBaseApiModule
{
    public function index(){
        $root = array();
        $user_data = $GLOBALS['user_info'];
        
        $user_login_status = check_login();
        $root['user_login_status'] = $user_login_status;
        if (!$root['user_login_status']) {
            return output($root,0,"请先登录");
        }
		
        $root['is_id_validate'] = $user_data['is_id_validate'];
        $card_info = $GLOBALS['db']->getRow( "select status,uid,front,bak,name,sex,idnum from ".DB_PREFIX."idcard_validate where uid={$user_data['id']}" );
        $root['card_info'] = $card_info;
    
        $root['page_title']="实名认证";
        return output($root);
    }
    
    
	public function scanId(){
        $root = array();
        $user_data = $GLOBALS['user_info'];
        
        $user_login_status = check_login();
        $root['user_login_status'] = $user_login_status;
        if (!$root['user_login_status']) {
            return output($root,0,"请先登录");
        }
            
        $name = strim($GLOBALS['request']['name']);
		$idvalidate = strim($GLOBALS['request']['idvalidate']);
		$sex = intval($GLOBALS['request']['sex']);
        
        $is_id_validate = $user_data['is_id_validate'];
        if ($is_id_validate == 1) {
            $root['already_validate'] = 1;
            return output($root, 1, '已经实名认证过');
        }
        
        $card_info = $GLOBALS['db']->getRow( "select status from ".DB_PREFIX."idcard_validate where uid={$user_data['id']}" );
		if($card_info){
			if($card_info['status']==1){
				return output($root, 1, '已经实名认证过');
			}elseif($card_info['status']==2){
				return output($root, 1, '审核中');
			}elseif($card_info['status']==3){
				return output($root, 1, '审核失败，重现提交');
			}
		}
		$is_open_idvalidate = app_conf("IS_OPEN_IDVALIDATE");
		// 更新认证表
		$update_data['name']    = $name;
		$update_data['idnum']   = $idvalidate;
		$update_data['sex']     = $sex==1?'男':'女';
		
		$update_data['validate_time'] = NOW_TIME;
		
		if($is_open_idvalidate==2){
			$update_data['status']  = 2;
		}else{
			$update_data['status']  = 1;
		}
		if($card_info){
			$GLOBALS['db']->autoExecute(DB_PREFIX."idcard_validate", $update_data,'UPDATE', " uid=".$user_data['id']);
		}else{
			$update_data['uid']=$user_data['id'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."idcard_validate", $update_data);
		}
		if($is_open_idvalidate==2){
			// 更新user表为审核中
			$GLOBALS['db']->autoExecute(DB_PREFIX."user", array('is_id_validate'=>2), 'UPDATE', " id={$user_data['id']}");
			$info="提交成功";
		}else{
			// 更新user表为已审核
			$GLOBALS['db']->autoExecute(DB_PREFIX."user", array('is_id_validate'=>1), 'UPDATE', " id={$user_data['id']}");
			$info="实名认证成功";
		}
		return output($root, 1, $info);
        
    }
    
    public function delete(){
        $root = array();
        $user_data = $GLOBALS['user_info'];
        
        $user_login_status = check_login();
        $root['user_login_status'] = $user_login_status;
        if (!$root['user_login_status']) {
            return output($root,0,"请先登录");
        }
        
        $type  = $GLOBALS['request']['type'];
        
        $card_info = $GLOBALS['db']->getRow( "select uid,front,bak,status from ".DB_PREFIX."idcard_validate where uid={$user_data['id']}" );
        
        if ( $card_info['status'] == 1 ) {
            return output($root, 0, "已认证成功");
        }elseif( $card_info['status'] == 2 ) {
            return output($root, 0, "审核中");
        }
        
        $GLOBALS['db']->query("UPDATE ".DB_PREFIX."idcard_validate SET status=0 where uid=".$user_data['id']);
        return output($root, 1, "重置成功");
         
    }
}