<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class InvoiceConfAction extends CommonAction{
	public function index()
	{
		$condition['supplier_id'] = 0;
		$this->assign("default_map",$condition);
		$vo = M(MODULE_NAME)->where($condition)->find();
		$contents = $vo['invoice_content'];
		if ($contents) {
			$vo['invoice_content'] = explode(' ', $contents);
		}
		$vo['description'] = app_conf('INVOICE_NOTICE');
		$this->assign('vo', $vo);
		$this->display();
	}

	public function update()
	{
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$saveData = array(
			'invoice_type' => $data['invoice_type'],
		);

		$new_content = array();
		if ($data['invoice_type'] != 0 && !empty($data['invoice_content'])) { 
			$new_content = array();
			foreach ($data['invoice_content'] as $val) {
				// 去除字符串中间的空格
				$val = str_replace(' ', '', $val);
				$val = strim($val);
				if (empty($val) || mb_strlen($val, 'utf-8') > 6) {
					continue;
				}
				$new_content[] = $val;
			}
						
		}
		if ($new_content) {
			$saveData['invoice_content'] = implode(' ', $new_content);
		} else {
			$saveData['invoice_content'] = '';
		}

		$exist = M(MODULE_NAME)->where('supplier_id=0')->find();

		if (!$exist) {
			$saveData['supplier_id'] = 0;
			$res = M(MODULE_NAME)->add($saveData);
		} else {
			$res = M(MODULE_NAME)->where('supplier_id=0')->save($saveData);
		}
		$description = $_REQUEST['description'];
		M('Conf')->where('name="INVOICE_NOTICE"')->save(array('value' => $description));
		update_sys_config();

		if (false !== $res) {
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}		
	}
	
}
?>