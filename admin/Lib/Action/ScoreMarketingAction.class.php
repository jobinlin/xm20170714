<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: jobinlin
// +----------------------------------------------------------------------

class ScoreMarketingAction extends CommonAction{
	public function score_purchase()
	{
		$vo = M("Conf")->where('name = "SCORE_PURCHASE_SWITCH" or name = "SCORE_PURCHASE_EXCHANGE_MONEY" or name = "SCORE_PURCHASE_MAX_MONEY" or name = "SCORE_PURCHASE_MAX_PROPORTION_MONEY"')->findAll ();
		$arr=array();
		foreach($vo as $k=>$v){
			$arr[$v['name']]=$v['value'];
			if($v['name']=="SCORE_PURCHASE_MAX_PROPORTION_MONEY"){
				$arr[$v['name']]=$arr[$v['name']]*100;
			}
		}
	    $this->assign ( 'vo', $arr );
	    $this->assign("title_name","积分抵现");
		$this->display();
	}
    public function score_recharge(){
        $vo = M("Conf")->where("instr(name,'SCORE_RECHARGE')>0")->findAll ();
        $arr=array();
        foreach($vo as $k=>$v){
            $arr[$v['name']]=$v['value'];
        }
        $this->assign ( 'vo', $arr );
        $this->assign("title_name","积分购买");
        $this->display();
    }
    public function score_recharge_save(){
        $conf_res=array();
        $conf_res['SCORE_RECHARGE_SWITCH']=$_REQUEST['SCORE_RECHARGE_SWITCH'];
        $conf_res['SCORE_RECHARGE_USABLE_SCORE']=$_REQUEST['SCORE_RECHARGE_USABLE_SCORE'];
        $conf_res['SCORE_RECHARGE_FROZEN_SCORE']=$_REQUEST['SCORE_RECHARGE_FROZEN_SCORE'];
        $conf_res['SCORE_RECHARGE_SCORE_NUMBER_SET']=$_REQUEST['SCORE_RECHARGE_SCORE_NUMBER_SET'];
        foreach($conf_res as $k=>$v)
        {
            conf($k,$v);
        }
        $this->_refreshConfFile();
        $this->success(L("UPDATE_SUCCESS"));
    }
	public function score_purchase_save(){
		
	    $conf_res=array();
		$conf_res['SCORE_PURCHASE_SWITCH']=intval($_REQUEST['SCORE_PURCHASE_SWITCH']);
		$conf_res['SCORE_PURCHASE_EXCHANGE_MONEY']=intval(floatval($_REQUEST['SCORE_PURCHASE_EXCHANGE_MONEY'])*100)/100;
		$conf_res['SCORE_PURCHASE_MAX_MONEY']=intval(floatval($_REQUEST['SCORE_PURCHASE_MAX_MONEY'])*100)/100;
		$conf_res['SCORE_PURCHASE_MAX_PROPORTION_MONEY']=intval($_REQUEST['SCORE_PURCHASE_MAX_PROPORTION_MONEY'])/100;
		foreach($conf_res as $k=>$v)
		{
			conf($k,$v);
		}
        $this->_refreshConfFile();
		$this->success(L("UPDATE_SUCCESS"));

	}

    /**
     * @desc  根据conf表刷新配置文件
     * @author    吴庆祥
     */
    private function _refreshConfFile(){
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
        save_log("更新积分抵现配置",1);
    }
	
}