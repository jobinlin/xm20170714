<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: jobinlin
// +----------------------------------------------------------------------

class FxQualificationAction extends CommonAction{
	public function index()
	{
	    $vo = M(MODULE_NAME)->find();
	    $vo['pay_fee'] = round($vo['pay_fee'],2);
	    $vo['tz_award'] = $vo['tz_award']*100;
        $vo['fx_award']=$vo['fx_award']*100;
	    $this->assign ( 'vo', $vo );
	    $this->assign("title_name","分销资质设置");
		$this->display();
	}

	public function edit(){
	    $id = intval($_REQUEST['id']);
	    $name = strim($_REQUEST['name']);
	    $pay_fee = strim($_REQUEST['pay_fee']);
	    $tz_award = strim($_REQUEST['tz_award']);
        $fx_award = strim($_REQUEST['fx_award']);
	    $pay_agreement = $_REQUEST['pay_agreement'];
	    $pc_privilege = $_REQUEST['pc_privilege'];
	    $phone_description = $_REQUEST['phone_description'];
	    if($name == "")
	    {
	       $this->error(l("请输入分销员名称！"));
	    }
	    elseif ($pay_fee == ""){
	       $this->error(l("请输入缴费金额！"));
	    }
	    elseif ($tz_award == ""){
	        $this->error(l("请输入奖励金额！"));
	    }
        elseif ($fx_award == ""){
            $this->error(l("请输入分销奖励百分比！"));
        }
	    elseif ($pay_agreement == ""){
	        $this->error(l("请填写购买协议！"));
	    }
	    elseif ($pc_privilege == ""){
	        $this->error(l("请填写PC端特权！"));
	    }
	    elseif (   !preg_match_all('/false\">[^\/\s\t(\<BR\>)]+<\/div>/i', $phone_description, $match) && !preg_match('/img/i', $phone_description)) {
	    	$this->error(l("请填写手机端特权！"));
	    }
	    else{
    	    $data = M(MODULE_NAME)->create();
    	    $data['tz_award']=round($data['tz_award']/100,2);
            $data['fx_award']=round($data['fx_award']/100,2);
    	    if(!$id){
    	        $data['id']=1;
    	        M("FxQualification")->add($data);
    	        $this->success(L("UPDATE_SUCCESS"));
    	    }
    	    else{
    	        M("FxQualification")->save($data);
    	        $this->success(L("UPDATE_SUCCESS"));
    	    }

	    }

	}
	
	
}
?>