<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DcThirdDeliveryAction extends CommonAction{
	private function read_modules()
	{
		$directory = APP_ROOT_PATH."system/delivery/";
		$read_modules = true;
		$dir = @opendir($directory);
	    $modules     = array();
	
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            $modules[] = require_once($directory .$file);
	        }
	    }
	    @closedir($dir);
	    unset($read_modules);
	
	    foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);
	
	    return $modules;
	}
	public function index()
	{
		$modules = $this->read_modules();
		$db_modules = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."dc_third_delivery");
		foreach($modules as $k=>$v)
		{
			foreach($db_modules as $kk=>$vv)
			{
				if($v['class_name']==$vv['class_name'])
				{
					//已安装
					$modules[$k]['id'] = $vv['id'];
					$modules[$k]['installed'] = 1;
					$modules[$k]['is_effect'] = $vv['is_effect'];
					$modules[$k]['sort'] = $vv['sort'];
					break;
				}
			}
			
			if($modules[$k]['installed'] != 1)
			$modules[$k]['installed'] = 0;
			$modules[$k]['is_effect'] = intval($modules[$k]['is_effect']);			
			$modules[$k]['sort'] = intval($modules[$k]['sort']);
			$modules[$k]['reg_url'] = $v['reg_url']?$v['reg_url']:'';
		}
		$this->assign("third_delivery",$modules);
		$this->display();
	}
	
	public function install()
	{
		$class_name = sltrim($_REQUEST['class_name']);
		$directory = APP_ROOT_PATH."system/delivery/";
		$read_modules = true;
		
		$file = $directory.$class_name."Delivery.php";
		if(file_exists($file))
		{
			$module = require_once($file);
			$rs = M("DcThirdDelivery")->where("class_name = '".$class_name."'")->count();
			if($rs > 0)
			{
				$this->error(l("DELIVERY_INSTALLED"));
			}
		}
		else
		{
			$this->error(l("INVALID_OPERATION"));
		}
		
		//开始插入数据
		$data['name'] = $module['name'];
		$data['class_name'] = $module['class_name'];
		$data['lang'] = $module['lang'];
		$data['config'] = $module['config'];
		$data['sort'] = (M("DcThirdDelivery")->max("sort") + 1);	

		$this->assign("data",$data);
		
		$this->assign("max_img_size",1);

		$this->display();
		
	}
	
	public function insert()
	{
		$data = M(MODULE_NAME)->create ();

		$data['logo'] = $_REQUEST['img'][0];
		if($data['logo']==''){
		    $this->error('请上传LOGO');
		}
		$data['config'] = serialize($_REQUEST['config']);
		// 更新数据

		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);

		$this->assign("jumpUrl",u(MODULE_NAME."/index"));
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSTALL_SUCCESS"),1);
			$this->success(L("INSTALL_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSTALL_FAILED"),0);
			$this->error(L("INSTALL_FAILED"));
		}
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$directory = APP_ROOT_PATH."system/delivery/";
		$read_modules = true;
		
		$file = $directory.$vo['class_name']."Delivery.php";
		if(file_exists($file))
		{
			$module = require_once($file);
		}
		else
		{
			$this->error(l("INVALID_OPERATION"));
		}
		
		$vo['config'] = unserialize($vo['config']);

		$data['lang'] = $module['lang'];
		$data['config'] = $module['config'];
		if($vo['logo']){
		    $img_list[] = $vo['logo'];
		}
		
		
		$this->assign ( 'img_list', $img_list );
		$this->assign ( 'vo', $vo );
		$this->assign("max_img_size",1);
		$this->assign ( 'data', $data );
		$this->display ();
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		$data['logo'] = $_REQUEST['img'][0];
		$data['config'] = serialize($_REQUEST['config']);
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");

		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));

		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function uninstall()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = intval($_REQUEST ['id']);
		$data = M(MODULE_NAME)->getById($id);
		if($data)
		{
			$info = $data['name'];
			$list = M(MODULE_NAME)->where ( array('id'=>$data['id']) )->delete();	
			if ($list!==false) {
					save_log($info.l("UNINSTALL_SUCCESS"),1);
					$this->success (l("UNINSTALL_SUCCESS"),$ajax);
				} else {
					save_log($info.l("UNINSTALL_FAILED"),0);
					$this->error (l("UNINSTALL_FAILED"),$ajax);
				}
		}
		else
		{
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function setting()
	{
	    $sql="select * from ".DB_PREFIX."conf where name='DELIVERY_MIN_MONEY' or name='DELIVERY_ALARM_MONEY'";
	    $delivery_conf = $GLOBALS['db']->getAll($sql);
	    foreach($delivery_conf as $k=>$v){
	        if($v['name']=='DELIVERY_MIN_MONEY'){
	            $delivery_min_money = $v['value'];
	        }elseif($v['name']=='DELIVERY_ALARM_MONEY'){
	            $delivery_alarm_money = $v['value'];
	        }
	    }
	    $this->assign("delivery_min_money",$delivery_min_money);
	    $this->assign("delivery_alarm_money",$delivery_alarm_money);

	    $this->display();
	}
	
	
	public function setting_update()
	{

	    $log_info = '配送全局设置';	
	    $this->assign("jumpUrl",u(MODULE_NAME."/setting"));
	
	    if($_REQUEST['delivery_min_money'] > $_REQUEST['delivery_alarm_money']){
	        $this->error('最低余额不能大于警戒金额');
	    }
	    // 更新数据
        $sql='update '.DB_PREFIX."conf set value='".$_REQUEST['delivery_min_money']."' where name='DELIVERY_MIN_MONEY'";
        $GLOBALS['db']->query($sql);
        $sql='update '.DB_PREFIX."conf set value='".$_REQUEST['delivery_alarm_money']."' where name='DELIVERY_ALARM_MONEY'";
        $GLOBALS['db']->query($sql);
        $rs=$GLOBALS['db']->affected_rows();
	    if ($rs > 0) {
	        

	       conf('DELIVERY_MIN_MONEY',$_REQUEST['delivery_min_money']);
	       conf('DELIVERY_ALARM_MONEY',$_REQUEST['delivery_alarm_money']);      
	        
	        //开始写入配置文件
	        $sys_configs = M("Conf")->findAll();
	        $config_str = "<?php\n";
	        $config_str .= "return array(\n";
	        foreach($sys_configs as $k=>$v)
	        {
	            $config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
	        }
	        $config_str.=");\n ?>";
	        $filename = get_real_path()."public/sys_config.php";
	        	
	        if (!$handle = fopen($filename, 'w')) {
	            $this->error(l("OPEN_FILE_ERROR").$filename);
	        }
	        	
	         
	        if (fwrite($handle, $config_str) === FALSE) {
	            $this->error(l("WRITE_FILE_ERROR").$filename);
	        }
	        	
	        fclose($handle);
	         
	        
	        //成功提示
	        save_log($log_info.L("UPDATE_SUCCESS"),1);
	        $this->success(L("UPDATE_SUCCESS"));
	    } else {
	        //错误提示
	        save_log($log_info.L("UPDATE_FAILED"),0);
	        $this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
	    }
	}
	
	
}
?>