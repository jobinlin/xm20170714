<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: jobinlin
// +----------------------------------------------------------------------

class WithdrawConfAction extends CommonAction{
	public function index()
	{
	    $this->assign("title_name","提现设置");
	    $this->assign('fx_auto_withdraw', app_conf('FX_AUTO_WITHDRAW'));
		$this->assign("fx_withdraw_rate",app_conf("FX_WITHDRAW_RATE"));
		$this->assign("fx_withdraw_cycle",app_conf("FX_WITHDRAW_CYCLE"));
		$this->assign("supplier_withdraw_cycle",app_conf("SUPPLIER_WITHDRAW_CYCLE"));
		$this->display ();
	}
	/**
	 * 推荐商家佣金设置保存
	 */
	public function withdraw_conf_save(){
		if(intval($_REQUEST['FX_WITHDRAW_RATE'])>1000){
			$this->error("分销佣金提现手续费不能超过1001‰");
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."conf set value = '".intval($_REQUEST['FX_AUTO_WITHDRAW'])."' where name = 'FX_AUTO_WITHDRAW'");
	    $GLOBALS['db']->query("update ".DB_PREFIX."conf set value = '".$_REQUEST['FX_WITHDRAW_RATE']."' where name = 'FX_WITHDRAW_RATE'");
		$GLOBALS['db']->query("update ".DB_PREFIX."conf set value = '".$_REQUEST['FX_WITHDRAW_CYCLE']."' where name = 'FX_WITHDRAW_CYCLE'");
		$GLOBALS['db']->query("update ".DB_PREFIX."conf set value = '".$_REQUEST['SUPPLIER_WITHDRAW_CYCLE']."' where name = 'SUPPLIER_WITHDRAW_CYCLE'");
	    //开始写入配置文件
	    update_sys_config();
        /*$sys_configs = M("Conf")->findAll();
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

        fclose($handle);*/
        save_log("更新分销佣金配置",1);
		
	    if ($ajax){
	        ajax_return($result);
	    }else{
	        $this->assign("jumpUrl",$result['jump']);
	        $this->success(L("UPDATE_SUCCESS"));
	    }    
	    
	}
}
?>