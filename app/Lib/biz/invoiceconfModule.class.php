<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class invoiceconfModule extends BizBaseModule
{
    function __construct(){
        parent::__construct();
        global_run();
        $this->check_auth();
    }
    /**
     * 活动点评
     * @see BizBaseModule::index()
     */
	public function index()
	{		
		init_app_page();
		
        $supplier_id = $GLOBALS['account_info']['supplier_id'];

        $sql = 'SELECT * FROM '.DB_PREFIX.'invoice_conf WHERE supplier_id='.$supplier_id;
        $invoice = $GLOBALS['db']->getRow($sql);
        
        if (!empty($invoice['invoice_content'])) {
            $invoice['invoice_content'] = explode(' ', $invoice['invoice_content']);
        }

        $GLOBALS['tmpl']->assign('invoice', $invoice);

		$GLOBALS['tmpl']->assign("head_title","开票设置");
		$GLOBALS['tmpl']->display("pages/invoiceConf/index.html");
	}
	
    public function update()
    {
        $supplier_id = $GLOBALS['account_info']['supplier_id'];

        $data = array();

        $io_type = intval($_REQUEST['invoice_type']);
        if ($io_type !== 0 && $io_type !== 1) {
            $io_type = 0;
        }
        $data['invoice_type'] = $io_type;
        $new_content = array();
        if ($io_type !== 0 && !empty($_REQUEST['invoice_content'])) {
            $io_content = $_REQUEST['invoice_content'];
            if (!is_array($io_content)) {
                $io_content = array($io_content);
            }
            foreach ($io_content as $key => $value) {
                $value = str_replace(' ', '', $value);
                $value = strim($value);
                if (empty($value) || mb_strlen($value, 'utf-8') > 6) { // 如果多于6个字去除
                    continue;
                }
                $new_content[] = strim($value);
            }
            $new_content = array_unique($new_content);
        }
        if ($new_content) {
            $data['invoice_content'] = implode(' ', $new_content);
        } else {
            $data['invoice_content'] = '';
        }
        

        $exist = $GLOBALS['db']->getRow('SELECT * FROM '.DB_PREFIX.'invoice_conf WHERE supplier_id='.$supplier_id);
        if (!$exist) {
            $data['supplier_id'] = $supplier_id;
            $GLOBALS['db']->autoExecute(DB_PREFIX.'invoice_conf', $data);
        } else {
            // 判断一下信息是否发生变化
            if($data['invoice_type'] == $exist['invoice_type'] && $data['invoice_content'] == $exist['invoice_content']) {
                $res = 1;
                goto res;
            }
            $GLOBALS['db']->autoExecute(DB_PREFIX.'invoice_conf', $data, 'UPDATE', 'supplier_id='.$supplier_id);
        }
        $res = $GLOBALS['db']->affected_rows();
        $debug['sql'] = $GLOBALS['db']->getLastSql();
        res:
        if ($res > 0) {
            $status = 1;
            $info = '保存成功';
        } else {
            $status = 0;
            $info = '保存失败';
        }
        ajax_return(array('status' => $status, 'info' => $info, 'debug' => $debug));
    }
	
}
?>