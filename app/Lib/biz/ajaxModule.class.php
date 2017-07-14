<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class ajaxModule extends BizBaseModule{
	//检查商户帐号唯一
	public function check_field_unique(){
		$field_name = strim($_REQUEST['field_name']);
		$field_data = strim($_REQUEST['field_data']);
		$data = array();
		$data['error'] = 0;
		$data['msg'] = '';
		$account_name = strim($_REQUEST['account_name']);
		$result_data = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."supplier_submit WHERE ".$field_name."='".$field_data."'");
		if($result_data>0){ //已经存在数据
			$data['error'] = 1;
			$data['msg'] = "数据已经存在!";
		}
		ajax_return($data);
	}
	
	public function check_account_name(){
		$account_name = strim($_REQUEST['account_name']);
		$data = array();
		$data['error'] = 0;
		$data['msg'] = '';
		if($GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."supplier_submit WHERE account_name ='".$account_name."'")>0 || $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."supplier_account WHERE account_name ='".$account_name."'")>0 ){
			$data['error'] = 1;
			$data['msg'] = "数据已经存在!";
		}
		ajax_return($data);
	}
	
	

	
	public function check_account_mobile(){
	    $account_mobile = strim($_REQUEST['account_mobile']);
	    if(!check_mobile($account_mobile)){
	        $result['error'] = 1;
	        $result['msg'] = "手机号格式错误";
	        ajax_return($result);
	    }
	    if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_account where mobile='".$account_mobile."'")){
	        $result['error'] = 1;
	        $result['msg'] = "手机号已经存在";
	        ajax_return($result);
	    }
	    $result['error'] = 0;
	    $result['msg'] = "";
	    ajax_return($result);
	}
	
	/**
	 * 发送手机验证码
	 */
	public function send_sms_code()
	{
		$verify_code = strim($_REQUEST['verify_code']);
		$mobile_phone = strim($_REQUEST['mobile']);

		if($mobile_phone=="")
		{
			$data['status'] = false;
			$data['info'] = "请输入手机号";
			$data['field'] = "user_mobile";
			ajax_return($data);
		}
		if(!check_mobile($mobile_phone))
		{
			$data['status'] = false;
			$data['info'] = "手机号格式不正确";
			$data['field'] = "user_mobile";
			ajax_return($data);
		}
	
	
		if(intval($_REQUEST['unique'])==1)
		{
			if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_submit where account_mobile = '".$mobile_phone."'"))>0)
			{
				$data['status'] = false;
				$data['info'] = "手机号已被注册";
				$data['field'] = "account_mobile";
				ajax_return($data);
			}
		}
		
	
		$sms_ipcount = load_sms_ipcount();

		if($sms_ipcount>1)
		{
			//需要图形验证码
			if(es_session::get("verify")!=md5($verify_code))
			{
				$data['status'] = false;
				$data['info'] = "图形验证码错误";
				$data['field'] = "verify_code";
				es_session::delete("verify");
				ajax_return($data);
			}
			es_session::delete("verify");
		}
		
		if(!check_ipop_limit(CLIENT_IP, "send_sms_code",SMS_TIMESPAN))
		{
			showErr("请勿频繁发送短信",1);
		}
	
	
		//删除失效验证码
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
	
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile_phone."'");
		if($mobile_data)
		{
			//重新发送未失效的验证码
			$code = $mobile_data['code'];
			$mobile_data['add_time'] = NOW_TIME;
			$GLOBALS['db']->query("update ".DB_PREFIX."sms_mobile_verify set add_time = '".$mobile_data['add_time']."',send_count = send_count + 1 where mobile_phone = '".$mobile_phone."'");
		}
		else
		{
			$code = rand(100000,999999);
			$mobile_data['mobile_phone'] = $mobile_phone;
			$mobile_data['add_time'] = NOW_TIME;
			$mobile_data['code'] = $code;
			$mobile_data['ip'] = CLIENT_IP;
			$GLOBALS['db']->autoExecute(DB_PREFIX."sms_mobile_verify",$mobile_data,"INSERT","","SILENT");
				
		}
		send_verify_sms($mobile_phone,$code);
		es_session::delete("verify"); //删除图形验证码
		$data['status'] = true;
		$data['info'] = "发送成功";
		$data['lesstime'] = SMS_TIMESPAN -(NOW_TIME - $mobile_data['add_time']);  //剩余时间
		$data['sms_ipcount'] = load_sms_ipcount();
		ajax_return($data);
	
	
	}
	
	
    /**
     * 加载商品分类
     */
    public function load_goods_type(){
        global_run();
        $sql = "select * from ".DB_PREFIX."goods_type";
        if($GLOBALS['account_info']){//登录时候
            $sql.= " where supplier_id=0 or supplier_id=". $GLOBALS['account_info']['supplier_id'];
        }
        $data = $GLOBALS['db']->getAll($sql);
        $html = '<select class="ui-select filter_select medium" name="deal_goods_type" ><option value="0">==请选择类型==</option>';
        foreach ($data as $k=>$v){
            $html.='<option value="'.$v['id'].'">'.$v['name'].'</option>';
        }
        $html .= "</select>";
        echo $html;
    }
    
    /**
     * 加载商品属性
     */
    public function load_attr_html(){
        global_run();
        
        $deal_goods_type = intval($_REQUEST['deal_goods_type']);
        $id = intval($_REQUEST['id']);
        $edit_type = intval($_REQUEST['edit_type']); //1管理员发布 2商户发布 
        
        $is_data = false;
        if($edit_type == 1 && $GLOBALS['db']->getOne("select deal_goods_type from ".DB_PREFIX."deal where id = ".$id)==$deal_goods_type){
            $is_data = true;
        }elseif($edit_type==2 && $GLOBALS['db']->getOne("select deal_goods_type from ".DB_PREFIX."deal_submit where id = ".$id)==$deal_goods_type){
            $is_data = true;
        }
        
        if($id>0 && $is_data)
        {
            $goods_type_attr = null;
            if ($edit_type == 1){
                
                $goods_type_attr = $GLOBALS['db']->getAll("select a.name as attr_name,a.is_checked as is_checked,a.id as deal_attr_id ,b.* from ".DB_PREFIX."deal_attr as a left join ".DB_PREFIX."goods_type_attr as b on a.goods_type_attr_id = b.id where a.deal_id=".$id." order by a.id asc");
            }else{
                //商品分类属性
                $goods_type_attr_data = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_type_attr where goods_type_id = ".$deal_goods_type);
                foreach($goods_type_attr_data as $k=>$v){
                    $f_goods_type_attr[$v['id']] = $v;
                }
                //团购已经选择的分类属性值
                $deal_attr_data = unserialize($GLOBALS['db']->getOne("select cache_deal_attr from ".DB_PREFIX."deal_submit where id=".$id));
                
                
                
                foreach($deal_attr_data as $k=>$v){
                    $temp_data=$v;
		        	$temp_data['name']=$f_goods_type_attr[$v['id']]['name'];
		        	$temp_data['input_type']=$f_goods_type_attr[$v['id']]['input_type'];
		        	$temp_data['preset_value']=$f_goods_type_attr[$v['id']]['preset_value'];
                
                
                    $goods_type_attr[] = $temp_data;
                }
            }
           
            $goods_type_attr_new = array();
            $goods_type_attr_id = 0;
            if($goods_type_attr)
            {
                foreach($goods_type_attr as $k=>$v)
                {
                    $goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
                    if($goods_type_attr_id!=$v['id'])
                    {
                        $goods_type_attr[$k]['is_first'] = 1;
                    }
                    else
                    {
                        $goods_type_attr[$k]['is_first'] = 0;
                    }
                    $goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
                    $goods_type_attr_id = $v['id'];
                }
            }
            else
            {
                $goods_type_attr = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_type_attr where goods_type_id=".$deal_goods_type);
                foreach($goods_type_attr as $k=>$v)
                {
                    $goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
                    $goods_type_attr[$k]['is_first'] = 1;
                    $goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
                }
            }
        }
        else
        {
            $goods_type_attr =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_type_attr where goods_type_id=".$deal_goods_type);
            foreach($goods_type_attr as $k=>$v)
            {
                $goods_type_attr[$k]['attr_list'] = preg_split("/[ ,]/i",$v['preset_value']);
                $goods_type_attr[$k]['is_first'] = 1;
                $goods_type_attr_new[$v['id']][] = $goods_type_attr[$k];
            }
        }
        
        //是否开启自动审核
        $GLOBALS['tmpl']->assign('allow_publish_verify',intval($GLOBALS['db']->getOne("select allow_publish_verify from ".DB_PREFIX."supplier where id=".$GLOBALS['account_info']['supplier_id'])));
        
        $GLOBALS['tmpl']->assign("goods_type_attr",$goods_type_attr_new);
        echo $GLOBALS['tmpl']->fetch("pages/project/load_attr_html.html");
    }
	public function attr_table(){
         $attr_row_arr = $_REQUEST['attr_row_arr'];
         $deal_id = intval($_REQUEST['deal_id']);
         $is_shop = intval($_REQUEST['is_shop']);
         $is_show_attr=intval($_REQUEST['is_show_attr']);
         $edit_type=intval($_REQUEST['edit_type']);
         if($is_show_attr){
             $temp_attr=array();
             $attr_row_arr=$GLOBALS['db']->getAll("select a.name as attr_name,a.is_checked as is_checked,a.id as deal_attr_id ,b.* from ".DB_PREFIX."deal_attr as a left join ".DB_PREFIX."goods_type_attr as b on a.goods_type_attr_id = b.id where a.deal_id=".$deal_id." order by a.id asc");
             foreach($attr_row_arr as $val){
                 $temp_attr[$val['name']][]=array('attr_name'=>$val['attr_name'],"key"=>$val['deal_attr_id']);
             }
             $attr_row_arr=array();
             foreach($temp_attr as $key=>$val){
                 $attr_row_arr[]=array('name'=>$key,'attr'=>$val);
             }
             $this->assign("is_show_attr",$is_show_attr);
         }
         $attr_row_count = count($attr_row_arr);
         if($attr_row_count == 0){
             $html='';
         }else{
         $html='<table class="t3" border="1"><tboty><tr>';

         foreach($attr_row_arr as $k=>$v){
             $html .='<th>'.$v['name'].'</th>';
             $attr_row_arr[$k]['count'] = count($v['attr']);
         }
		 if($is_shop==0){
		 	$html .='<th>递增团购价</th><th>递增结算价</th><th>库存</th><th>销量</th></tr>';
		 }else{
		 	$html .='<th>递增销售价</th><th>递增结算价</th><th>库存</th><th>销量</th></tr>';
		 }
         
         foreach($attr_row_arr as $k=>$v){

             $span = 1;
             for ($i=0;$i < $attr_row_count;$i++){
                 if($k < $i){
                     $span *= $attr_row_arr[$i]['count'];
                 }  
             }
             $attr_row_arr[$k]['span'] = $span;
         }

        // logger::write(print_r($attr_row_arr,1));


         if($edit_type==1){
	         $attr_stock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."attr_stock where deal_id=".intval($deal_id)." order by id asc");       
	         foreach($attr_stock as $k=>$v)
	         {
	             $attr_stock[$k]['attr_cfg'] = unserialize($v['attr_cfg']);
	         }
         }else{
         	$attr_stock =$GLOBALS['db']->getOne("select cache_attr_stock from ".DB_PREFIX."deal_submit where id=".$deal_id);
         	$attr_stock=unserialize($attr_stock);
         }

         require_once(APP_ROOT_PATH."system/model/dc.php");
         $attr_stock = data_format_idkey($attr_stock,$key='attr_key');
       //  logger::write(print_r($attr_stock,1));
         //第一层
         foreach($attr_row_arr[0]['attr'] as $kk=>$vv){
             $html .='<tr><td rowspan="'.$attr_row_arr[0]['span'].'">'.$vv['attr_name'].'</td>';
             
             
             //第二层
             if($attr_row_arr[1]['attr']){
                 

             foreach($attr_row_arr[1]['attr'] as $kkk=>$vvv){
                 if($attr_row_arr[0]['span'] > 1 && $kkk > 0){

                     $html .='<tr><td rowspan="'.$attr_row_arr[1]['span'].'">'.$vvv['attr_name'].'</td>';
                     //第三层
                     if($attr_row_arr[2]['attr']){
                     foreach($attr_row_arr[2]['attr'] as $kkkk=>$vvvv){
                         if($attr_row_arr[1]['span'] > 1 && $kkkk > 0){
                             $html .='<tr><td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                                
                             //第四层
                             if($attr_row_arr[3]['attr']){
                             foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                 if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                     $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                             
                                     $html .='</tr>';
                                 }else{
                                     $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                 }
                                  
                             }
                             }else{

                                 $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                 $key=  $this-> get_data_key($attr_stock,$key_arr);
                                 $html .=$this->attr_table_td($attr_stock,$key);
                             }
                             $html .='</tr>';
                         }else{
                             $html .='<td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                             //第四层
                             if($attr_row_arr[3]['attr']){
                             foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                 if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                     $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                      
                                     $html .='</tr>';
                                 }else{
                                     $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                 }
                             
                             }
                             }else{
                             
                                 $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                 $key=  $this-> get_data_key($attr_stock,$key_arr);
                                 $html .=$this->attr_table_td($attr_stock,$key);
                             }
                         }
                     
                     }
                     }else{

                         $key_arr =array($vv['key'] , $vvv['key']);   
                         $key=  $this-> get_data_key($attr_stock,$key_arr);
                         $html .=$this->attr_table_td($attr_stock,$key);
                     }
                     
                     $html .='</tr>';
                 }else{
                     $html .='<td rowspan="'.$attr_row_arr[1]['span'].'">'.$vvv['attr_name'].'</td>';
                     //第三层
                     if($attr_row_arr[2]['attr']){
                     foreach($attr_row_arr[2]['attr'] as $kkkk=>$vvvv){
                         if($attr_row_arr[1]['span'] > 1 && $kkkk > 0){
                             $html .='<tr><td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                             //第四层
                             if($attr_row_arr[3]['attr']){
                             foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                 if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                     $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                      
                                     $html .='</tr>';
                                 }else{
                                     $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                 }
                             
                             }
                             }else{
                                  
                                 $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                 $key=  $this-> get_data_key($attr_stock,$key_arr);
                                 $html .=$this->attr_table_td($attr_stock,$key);
                             }
                             $html .='</tr>';
                         }else{
                             $html .='<td rowspan="'.$attr_row_arr[2]['span'].'">'.$vvvv['attr_name'].'</td>';
                             
                             //第四层
                             if($attr_row_arr[3]['attr']){
                             foreach($attr_row_arr[3]['attr'] as $kkkkk=>$vvvvv){
                                 if($attr_row_arr[2]['span'] > 1 && $kkkkk > 0){
                                     $html .='<tr><td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                      
                                     $html .='</tr>';
                                 }else{
                                     $html .='<td rowspan="'.$attr_row_arr[3]['span'].'">'.$vvvvv['attr_name'].'</td>';
                                 }
                             
                             }
                             }else{
                             
                                 $key_arr =array($vv['key'] , $vvv['key'] ,$vvvv['key']);
                                 $key=  $this-> get_data_key($attr_stock,$key_arr);
                                 $html .=$this->attr_table_td($attr_stock,$key);
                             }
                            
                         }
                          
                     }
                     }else{
                         
                         $key_arr =array($vv['key'] , $vvv['key']);
                         $key=  $this-> get_data_key($attr_stock,$key_arr);
                         $html .=$this->attr_table_td($attr_stock,$key);
                     }
                 }

             }
             }else{
             	 $html .=$this->attr_table_td($attr_stock,$vv['key']);
             }
             $html .='</tr>';
         }
         
         
         $html .='</tboty></table>';
         }
         echo $html;


          
     }
    public function attr_table_td($attr_stock,$key){
    	$html='<td><input type="text" name="deal_attr_price[]" value="'.$attr_stock[$key]['price'].'" /></td>';
        $html .='<td><input type="hidden" name="deal_add_balance_price[]" value="'.$attr_stock[$key]['add_balance_price'].'" /><span>'.floatval($attr_stock[$key]['add_balance_price']).'</span></td>';
        $html .='<td><input type="text" name="stock_cfg_num[]" value="'.$attr_stock[$key]['stock_cfg'].'" /></td>';
        $html .='<td><input type="hidden" name="stock_buy_count[]" value="'.intval($attr_stock[$key]['buy_count']).'" /><span>'.intval($attr_stock[$key]['buy_count']).'</span></td>';
        return $html;
    }
	public function get_data_key($data,$key_arr){
		if(count($key_arr)==1){
			return $key_arr[0];
		}elseif(count($key_arr)==2){

			if($data[$key_arr[0].'_'.$key_arr[1]]){
				return $key_arr[0].'_'.$key_arr[1];
			}else{
				return $key_arr[1].'_'.$key_arr[0];
			}
		}elseif(count($key_arr)==3){
			if($data[$key_arr[0].'_'.$key_arr[1].'_'.$key_arr[2]]){
				return $key_arr[0].'_'.$key_arr[1].'_'.$key_arr[2];
			}elseif($data[$key_arr[0].'_'.$key_arr[2].'_'.$key_arr[1]]){
				return $key_arr[0].'_'.$key_arr[2].'_'.$key_arr[1];
			}elseif($data[$key_arr[1].'_'.$key_arr[0].'_'.$key_arr[2]]){
				return $key_arr[1].'_'.$key_arr[0].'_'.$key_arr[2];
			}elseif($data[$key_arr[1].'_'.$key_arr[2].'_'.$key_arr[0]]){
				return $key_arr[1].'_'.$key_arr[2].'_'.$key_arr[0];
			}elseif($data[$key_arr[2].'_'.$key_arr[1].'_'.$key_arr[0]]){
				return $key_arr[2].'_'.$key_arr[1].'_'.$key_arr[0];
			}elseif($data[$key_arr[2].'_'.$key_arr[0].'_'.$key_arr[1]]){
				return $key_arr[2].'_'.$key_arr[0].'_'.$key_arr[1];
			}
		}
	}
    public function load_delivery_form_old()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    	
    	if(intval($s_account_info['id'])==0)
    	{
    		$data['status']=1000;
    		ajax_return($data);
    	}
    	
    	if(!check_module_auth("goodso"))
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    	
    	$supplier_id = intval($s_account_info['supplier_id']);
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    	
    	
    	$id = intval($_REQUEST['id']); //发货商品的ID    	
    	$item = $GLOBALS['db']->getRow("select doi.* from ".$order_item_table_name." as doi left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id where doi.id = ".$id." and l.location_id in (".implode(",",$s_account_info['location_ids']).")");

    	if($item)
    	{

    		$location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where id in (".implode(",",$s_account_info['location_ids']).")");
    		$express_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."express where is_effect = 1");

    		$GLOBALS['tmpl']->assign("item",$item);
    		$GLOBALS['tmpl']->assign("is_delivery",$item['is_delivery']);
    		$GLOBALS['tmpl']->assign("express_list",$express_list);
    		$GLOBALS['tmpl']->assign("location_list",$location_list);
    		$data['html'] = $GLOBALS['tmpl']->fetch("inc/delivery_form.html");
    		$data['status'] = 1;
    		ajax_return($data);
    	}
    	else
    	{
    		$data['status'] = 0;
    		$data['info'] = "非法的数据";
    		ajax_return($data);
    	}
    	
    }
    // 发货弹窗重写
    public function load_delivery_form()
    {
        global_run();
        $s_account_info = $GLOBALS['account_info'];
        
        if(intval($s_account_info['id'])==0) {
            $data['status']=1000;
            ajax_return($data);
        }
        
        if(!check_module_auth("goodso")) {
            $data['status'] = 0;
            $data['info'] = "权限不足";
            ajax_return($data);
        }
        
        $supplier_id = intval($s_account_info['supplier_id']);
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
        $order_table_name = get_supplier_order_table_name($supplier_id);
        
        $id = intval($_REQUEST['id']);
        $ordersql = 'SELECT * FROM '.$order_table_name.' WHERE id='.$id;
        $order = $GLOBALS['db']->getRow($ordersql);
        if ($order) {
            $itemsql = 'SELECT * FROM '.$order_item_table_name.' WHERE order_id = '.$order['id'].' AND delivery_status=0 AND refund_status in(0,3)';
            $item = $GLOBALS['db']->getAll($itemsql);
            if ($item) {
                $is_delivery = 1;
                $total_balance = 0;
                foreach ($item as &$val) {
                    if ($val['is_delivery'] == 0) { // 无需配送
                        $is_delivery = 0;
                    }
                    $total_balance += $val['balance_total_price'];
                    $val['balance_unit_price'] = format_price($val['balance_unit_price']);
                    $val['balance_total_price'] = format_price($val['balance_total_price']);
                }
                unset($val);
                $order['create_time'] = to_date($order['create_time']);
                $assign = array(
                    'order' => $order,
                    'is_delivery' => $is_delivery,
                    'item' => $item,
                    'total_balance' => $total_balance,
                );
                if ($is_delivery) { // 需要配送。获取配送地址信息
                    $region_lv = array($order['region_lv1'], $order['region_lv2'], $order['region_lv3'], $order['region_lv4']);
                    $region_lv_sql = 'select name from '.DB_PREFIX.'delivery_region where id in ('.implode(',', $region_lv).') order by id';
                    $region_names = $GLOBALS['db']->getCol($region_lv_sql);

                    $address = $order['address'];
                    $mobile = $order['mobile'];
                    $consignee = $order['consignee'];
                    $street = $order['street'];
                    $doorplate = $order['doorplate'];
                    $zip = $order['zip'];

                    $location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where id in (".implode(",",$s_account_info['location_ids']).")");
                    $express_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."express where is_effect = 1");
                    $assign['express_list'] = $express_list;
                    $assign['location_list'] = $location_list;
                    $assign['address'] = $consignee.'&nbsp;&nbsp;'.$mobile.'&nbsp;&nbsp;'.implode('', $region_names).$address.$street.$doorplate.'&nbsp;&nbsp;'.$zip;
                }
                $GLOBALS['tmpl']->assign($assign);
                $data['html'] = $GLOBALS['tmpl']->fetch("inc/delivery_form_new.html");
                $data['status'] = 1;
                ajax_return($data);
            }
        }
        $data['status'] = 0;
        $data['info'] = "非法的数据";
        ajax_return($data);
    }

    public function load_order_detail()
    {
        global_run();
        $s_account_info = $GLOBALS['account_info'];
        if(intval($s_account_info['id']) ==0 ) {
            $data['status'] = 1000;
            ajax_return($data);
        }
        if(!check_module_auth("goodso")) {
            $data['status'] = 0;
            $data['info'] = "权限不足";
            ajax_return($data);
        }

        $supplier_id = intval($s_account_info['supplier_id']);
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
        $order_table_name = get_supplier_order_table_name($supplier_id);
        
        $id = intval($_REQUEST['id']);
        $ordersql = 'SELECT * FROM '.$order_table_name.' WHERE id='.$id.' AND supplier_id = '.$supplier_id;
        $order = $GLOBALS['db']->getRow($ordersql);
        if ($order) {
            
            $itemsql = 'SELECT oi.*, sl.name AS slname FROM '.$order_item_table_name.' oi LEFT JOIN '.DB_PREFIX.'supplier_location sl ON oi.location_id = sl.id WHERE order_id = '.$order['id'];
            
            $item = $GLOBALS['db']->getAll($itemsql);
            if ($item) {
                $notdev = array(); // 无配送
                $package = array(); // 包裹
                $total_balance = 0;
                $is_delivery = 1;
                $total_refund_money=0;
                $total_balance_price=0;
                foreach ($item as $val) {
                    if($val['refund_status']<>2){
                        $total_balance_price+=$val['balance_total_price'];
                    }
                    
                    $total_balance += $val['balance_total_price'];
                    $val['unit_price'] = format_price($val['unit_price']);
                    $val['total_price'] = format_price($val['total_price']);
                    $val['balance_unit_price'] = format_price($val['balance_unit_price']);
                    $val['balance_total_price'] = format_price($val['balance_total_price']);
                    $val['statusStr'] = '';
                    
                    $total_refund_money+=$val['refund_money'];
                    
                    
                    if ( in_array($val['refund_status'], array(1, 2))) {
                        $val['statusStr'] = $val['refund_status'] == 1 ? '退款审核中' : '已退款';
                    }
                    if ($val['is_delivery'] == 0) { // 无需配送，只有一条
                        if ($val['is_pick'] == 1) { // 如果是自提
                            $val['statusStr'] = $val['statusStr'] ?: ($val['dp_id'] > 0 ? '已评价' : ($val['consume_count'] > 0 ? '待评价' : '待验证'));
                            $order['slname'] = $val['slname'];
                        } else {
                            $val['statusStr'] = $val['statusStr'] ?: ($val['dp_id'] > 0 ? '已评价' : ($val['is_arrival'] == 1 ? '待评价' : ($val['delivery_status'] == 0 ? '待发货' : '待收货')));
                        }
                        $notdev[] = $val;
                        $is_delivery = 0;
                    } elseif ($val['delivery_status'] == 0) {
                        $val['statusStr'] = $val['statusStr'] ?: '待发货';
                        $notdev[] = $val;
                    } else { // 需要配送并且已发货
                        $val['statusStr'] = $val['statusStr'] ?: ($val['dp_id'] > 0 ? '已评价' : ($val['is_arrival'] == 1 ? '待评价' : '待收货'));
                        $package[$val['id']] = $val;
                        $pkid[] = $val['id'];
                    }
                }
                $order['create_time'] = to_date($order['create_time']);
                $assign = array(
                    'order' => $order,
                    'item1' => $notdev,
                    'total_balance' => $total_balance,
                    'is_delivery' => $is_delivery,
                );
                if ($package) {
                    $pksql = 'SELECT distinct(dn.notice_sn), dn.order_item_id,dn.memo, dn.delivery_time , dn.express_id , dn.is_arrival, e.name FROM '.DB_PREFIX.'delivery_notice dn INNER JOIN '.DB_PREFIX.'express e ON e.id = dn.express_id WHERE dn.order_item_id IN ('.implode(',', $pkid).')';
                    $pk = $GLOBALS['db']->getAll($pksql);
                    // $data['sql'] = $pksql;
                    // $data['pk'] = $pk;
                    $sepk = array(); // 分离包裹
                    foreach ($pk as $v) {
                        if (NOW_TIME - $v['delivery_time'] > 3600 * 24 * ORDER_DELIVERY_EXPIRE && $v['is_arrival'] == 0) {
                            $v['force_dev'] = 1;
                        }
                        // $package[$v['order_item_id']]['info'] = $v;
                        $sepk[$v['notice_sn']]['info'] = $v;
                        $sepk[$v['notice_sn']]['list'][] = $package[$v['order_item_id']];
                    }
                    $assign['package'] = $sepk;
                }
                
                
                //支付明细
                $feeinfo=array();
                
                $fee_detail['name']="商品总价";
                $fee_detail['symbol'] = 1;
                $fee_detail['value'] = round($order['deal_total_price'],2);
                $feeinfo[] = $fee_detail;
                
                if($order['discount_price']>0){
                    $fee_detail['name']="等级折扣";
                    $fee_detail['symbol'] = -1;
                    $fee_detail['value'] = round($order['discount_price'],2);
                    $feeinfo[] = $fee_detail;
                }
                
                if($order['youhui_money']>0){
                    $fee_detail['name']="优惠券";
                    $fee_detail['symbol'] = -1;
                    $fee_detail['value'] = round($order['youhui_money'],2);
                    $feeinfo[] = $fee_detail;
                }
                
                if($order['ecv_money']>0){
                    $fee_detail['name']="红包";
                    $fee_detail['symbol'] = -1;
                    $fee_detail['value'] = round($order['ecv_money'],2);
                    $feeinfo[] = $fee_detail;
                }
                
                if($order['exchange_money']>0){
                    $fee_detail['name']="积分抵扣";
                    $fee_detail['symbol'] = -1;
                    $fee_detail['value'] = round($order['exchange_money'],2);
                    $feeinfo[] = $fee_detail;
                }
                
                $fee_detail['name'] = '实际支付金额';
                $fee_detail['symbol'] = 1;
                $fee_detail['value'] = round($order['total_price']-$order['youhui_money']-$order['ecv_money'],2);
                $feeinfo[] = $fee_detail;
                
                if( $total_refund_money > 0){
                    $fee_detail['name'] = '退款金额';
                    $fee_detail['symbol'] = -1;
                    $fee_detail['value'] = round($total_refund_money,2);
                    $feeinfo[] = $fee_detail;
                }
                
                $fee_detail['name'] = '结算金额';
                $fee_detail['symbol'] = 1;
                $fee_detail['value'] = round($total_balance_price+$order['delivery_fee'],2);
                $feeinfo[] = $fee_detail;
                
                $GLOBALS['tmpl']->assign('fee_info',$feeinfo);
                
                // $data['package'] = $sepk;
                // $data['order'] = $order;
                // $data['item'] = $item;
                if ($is_delivery) { // 需要配送。获取配送地址信息
                    $region_lv = array($order['region_lv1'], $order['region_lv2'], $order['region_lv3'], $order['region_lv4']);
                    $region_lv_sql = 'select name from '.DB_PREFIX.'delivery_region where id in ('.implode(',', $region_lv).') order by id';
                    $region_names = $GLOBALS['db']->getCol($region_lv_sql);

                    $address = $order['address'];
                    $mobile = $order['mobile'];
                    $consignee = $order['consignee'];
                    $street = $order['street'];
                    $doorplate = $order['doorplate'];
                    $zip = $order['zip'];

                    $assign['address'] = $consignee.'&nbsp;&nbsp;'.$mobile.'&nbsp;&nbsp;'.implode('', $region_names).$address.$street.$doorplate.'&nbsp;&nbsp;'.$zip;
                }

                $GLOBALS['tmpl']->assign($assign);

                $data['html'] = $GLOBALS['tmpl']->fetch("inc/order_detail.html");
                $data['status'] = 1;
                ajax_return($data);
            }
        }
        $data['status'] = 0;
        $data['info'] = "非法的数据";
        ajax_return($data);
    }

    /*public function load_un_delivery_form()
    {
        global_run();
        $s_account_info = $GLOBALS['account_info'];
         
        if(intval($s_account_info['id'])==0)
        {
            $data['status']=1000;
            ajax_return($data);
        }
         
        if(!check_module_auth("goodso"))
        {
            $data['status'] = 0;
            $data['info'] = "权限不足";
            ajax_return($data);
        }
         
        $supplier_id = intval($s_account_info['supplier_id']);
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table_name = get_supplier_order_item_table_name($supplier_id);
         
         
        $id = intval($_REQUEST['id']); //发货商品的ID
        $item = $GLOBALS['db']->getRow("select doi.* from ".$order_item_table_name." as doi left join ".DB_PREFIX."deal_location_link as l on doi.deal_id = l.deal_id where doi.id = ".$id." and l.location_id in (".implode(",",$s_account_info['location_ids']).")");
    
        if($item)
        {
            
            $location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where id in (".implode(",",$s_account_info['location_ids']).")");
            $GLOBALS['tmpl']->assign("item",$item);
            $GLOBALS['tmpl']->assign("location_list",$location_list);
            $GLOBALS['tmpl']->assign("is_delivery",$item['is_delivery']);
            $data['html'] = $GLOBALS['tmpl']->fetch("inc/delivery_form.html");
            $data['status'] = 1;
            ajax_return($data);
        }
        else
        {
            $data['status'] = 0;
            $data['info'] = "非法的数据";
            ajax_return($data);
        }
         
    }*/
    
    public function do_verify_delivery()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    	 
    	if(intval($s_account_info['id'])==0)
    	{
    		$data['status']=1000;
    		ajax_return($data);
    	}
    	 
    	if(!check_module_auth("goodso"))
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    	
    	$param = $_REQUEST['param'];
        $params = explode('|', $param);
        if (count($params) != 3) {
            $data['status'] = 0;
            $data['info'] = "参数错误";
            ajax_return($data);
        }
        $notice_sn = strim($params[0]);
        $express_id = intval($params[1]);
        $order_id = intval($params[2]);

        $sql = 'SELECT delivery_time FROM '.DB_PREFIX.'delivery_notice WHERE notice_sn = "'.$notice_sn.'" AND express_id = '.$express_id;
    	// $id = intval($_REQUEST['id']);
    	// $supplier_id = intval($s_account_info['supplier_id']);
    	// $delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".DB_PREFIX."deal_location_link as l on l.deal_id = n.deal_id where n.order_item_id = ".$id." and n.is_arrival = 0 and  l.location_id in (".implode(",",$s_account_info['location_ids']).")  order by n.delivery_time desc");
		
        $delivery_notice = $GLOBALS['db']->getRow($sql);
		if($delivery_notice&&NOW_TIME-$delivery_notice['delivery_time']>24*3600*ORDER_DELIVERY_EXPIRE)
    	{
    		require_once(APP_ROOT_PATH."system/model/deal_order.php");
    		// $res = confirm_delivery($delivery_notice['notice_sn'],$id);
            $res = order_confirm_delivery($notice_sn,$express_id,$order_id);
    		if($res)
    		{
    			$data['status'] = true;
    			$data['info'] = "超期收货成功";
    			ajax_return($data);
    		}
    		else
    		{
    			$data['status'] = 0;
    			$data['info'] = "收货失败";
    			ajax_return($data);
    		}
    	}
    	else
    	{
    		$data['status'] = 0;
    		$data['info'] = "订单不符合超期收货的条件";
    		ajax_return($data);
    	}
    }
    
    public function load_filter_box(){
        global_run();
        $edit_type = intval($_REQUEST['edit_type']); //1管理员发布 2商户发布
        
        $shop_cate_id = intval($_REQUEST['shop_cate_id']);
        $id = intval($_REQUEST['id']);
        $ids = $this->get_parent_ids($shop_cate_id);

        $filter_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter_group where cate_id in (".implode(",", $ids).")");

		if($edit_type == 1){ //管理员添加数据
    		
    		foreach($filter_group as $k=>$v)
    		{
    		    
    			$filter_group[$k]['value'] = $GLOBALS['db']->getOne("select filter from ".DB_PREFIX."deal_filter where filter_group_id = ".$v['id']." and deal_id=".$id);
    		}
		
		}elseif ($edit_type == 2){//商户提交数据

		    $cache_deal_filter = $GLOBALS['db']->getOne("select cache_deal_filter from ".DB_PREFIX."deal_submit where id = ".$id);
		    $cache_deal_filter = unserialize($cache_deal_filter);
		    foreach($filter_group as $k=>$v)
		    {
		        $filter_group[$k]['value'] = $cache_deal_filter[$v['id']]['filter'];
		    }  
		}
        $GLOBALS['tmpl']->assign("filter_group",$filter_group);
        echo $GLOBALS['tmpl']->fetch("pages/project/filter_box.html");
    }
    
    //获取当前分类的所有父分类包含本分类的ID
    private $cate_ids = array();
    private function get_parent_ids($shop_cate_id)
    {
        $pid = $shop_cate_id;
        do{
            $pid = $GLOBALS['db']->getOne("select pid from ".DB_PREFIX."shop_cate where id=".$pid);
            if($pid>0)
                $this->cate_ids[] = $pid;
        }while($pid!=0);
    
        $this->cate_ids[] = $shop_cate_id;
    
        return $this->cate_ids;
    }
    
    /**
     * 初始化验证商户是否是否可对订单商品进行退款审核操作
     * @return [type] [description]
     */
    public function refund_init()
    {
        global_run();
        $s_account_info = $GLOBALS['account_info'];
    
        if(intval($s_account_info['id'])==0) {
            $data['status']=1000;
            ajax_return($data);
        }
    
        if(!check_module_auth("goodso")) {
            $data['status'] = 0;
            $data['info'] = "无模块操作权限";
            ajax_return($data);
        }
        $id = intval($_REQUEST['data_id']);
        $supplier_id = intval($s_account_info['supplier_id']);
        $allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
        if(!$allow_refund)
        {
            $data['status'] = 0;
            $data['info'] = "无退款审核权限";
            ajax_return($data);
        }
        require_once(APP_ROOT_PATH."system/model/deal_order.php");
        $order_item_table = get_supplier_order_item_table_name($supplier_id);
    
        $sql = "select * from ".$order_item_table." where refund_status = 1 and id = ".$id." and supplier_id = ".$supplier_id;
        $order_item = $GLOBALS['db']->getRow($sql);
        if($order_item) {
            $reason = $GLOBALS['db']->getOne('SELECT content FROM '.DB_PREFIX.'message WHERE id = '.$order_item['message_id']);
            if ($reason) {
                $GLOBALS['tmpl']->assign('reason', $reason);
            }
            $order_table = get_supplier_order_table_name($supplier_id);
            $osql = 'SELECT * FROM '.$order_table.' WHERE id = '.$order_item['order_id'];
            $order = $GLOBALS['db']->getRow($osql);
            $total_f = $order['total_price'];
            
            // 当前商品价格比例    =  当前商品总价  / （当前订单总价  = 应付总额 + 会员折扣价格  - 运费）
            $scale = $order_item['total_price'] /  $order['deal_total_price'] ;
            // 当前商品折扣价    = 当前商品价格比例  * 总折扣价
            $scale_discount_price = $scale * $order['discount_price'];
            // 当前商品红包价格
            $scale_ecv_money      = $scale * $order['ecv_money'];
            // 当前商品优惠劵价格
            $scale_youhui_money   = $scale * $order['youhui_money'];
            // 当前商品积分抵扣价格
            $scale_exchange_money   = $scale * $order_info['exchange_money'];
            
            // 当前商品实际支付价格
            $real_total_price         = $order_item['total_price'] - $scale_discount_price - $scale_ecv_money - $scale_youhui_money -$scale_exchange_money;        

            // $item_f = $order_item['total_price'] * ($total_f / $order['deal_total_price']);
            $order_item['total_f'] = round($real_total_price, 2);
            $order['total_f'] = round($total_f, 2);
            $order['ecv_money'] = round($order['ecv_money'], 2);
            $order['dfee_f'] = round($order['record_delivery_fee'], 2);
            $order['ref_f'] = round($order['refund_amount']);
            $order['max_r'] = $order['total_price'] - $order['ecv_money'] - $order['refund_amount'];
            $GLOBALS['tmpl']->assign('order', $order);
            $GLOBALS['tmpl']->assign('item', $order_item);
            $data['html'] = $GLOBALS['tmpl']->fetch('inc/refund_detail.html');
            $data['status'] = 1;
            ajax_return($data);
        } else {
            $data['status'] = 0;
            $data['info'] = "商品不能进行退款审核操作";
            ajax_return($data);
        }
    }
    
    public function do_refund_item()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    
    	if(intval($s_account_info['id'])==0) {
    		$data['status']=1000;
    		ajax_return($data);
    	}
    
    	if(!check_module_auth("goodso")) {
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}    	 

    	$supplier_id = intval($s_account_info['supplier_id']);
    	$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
    	if(!$allow_refund)
    	{
    		$data['status'] = 0;
    		$data['info'] = "无退款审核权限";
    		ajax_return($data);
    	}
        $data['info'] = "非法的数据";

    	$id = intval($_REQUEST['id']);
        $money = floatval($_REQUEST['refund_money']); // 退款金额
        $memo = strim($_REQUEST['memo']); // 退款备注
        if ($memo) {
            $memo = '  '.$memo;
        }
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    
    	$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where refund_status = 1 and id = ".$id." and supplier_id = ".$supplier_id);
    	if($order_item) {
    		$status = refund_item_new($order_item['id'], $money, $memo);
            if ($status == 99) {
                $data['status'] = 1;
                $data['info'] = "退款操作成功";
                ajax_return($data);
            } elseif ($status == 1) {
                $data['info'] = '当前商品退款金额超出订单最大退款金额';
            }
    	}
    	$data['status'] = 0;
        ajax_return($data);
    }
    
    
    public function do_refuse_item()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    
    	if(intval($s_account_info['id'])==0)
    	{
    		$data['status']=1000;
    		ajax_return($data);
    	}
    
    	if(!check_module_auth("goodso"))
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    
    	$id = intval($_REQUEST['id']);
    	$supplier_id = intval($s_account_info['supplier_id']);
    	$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
    	if(!$allow_refund)
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    	 
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    
    	$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where refund_status = 1 and id = ".$id." and supplier_id = ".$supplier_id);
    	if($order_item)
    	{
            $memo = strim($_REQUEST['memo']);
    		refuse_item($order_item['id']);
    		$data['status'] = true;
    		$data['info'] = "拒绝退款操作成功";
    		ajax_return($data);
    	}
    	else
    	{
    		$data['status'] = 0;
    		$data['info'] = "非法的数据";
    		ajax_return($data);
    	}
    	 
    }

    public function do_refund_coupon()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    
    	if(intval($s_account_info['id'])==0)
    	{
    		$data['status']=1000;
    		ajax_return($data);
    	}
    
    	if(!check_module_auth("dealo")&&!check_module_auth("goods"))
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    
    	$id = intval($_REQUEST['id']);
    	$supplier_id = intval($s_account_info['supplier_id']);
    	$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
    	if(!$allow_refund)
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    	 
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    
    	$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where refund_status = 1 and id = ".$id." and supplier_id = ".$supplier_id);
    	if($order_item)
    	{
    		$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_deal_id = ".$order_item['id']);
    		foreach($coupon_list as $c)
    		{
    			refund_coupon($c['id']);
    		}
    		$data['status'] = true;
    		$data['info'] = "退款操作成功";
    		ajax_return($data);
    	}
    	else
    	{
    		$data['status'] = 0;
    		$data['info'] = "非法的数据";
    		ajax_return($data);
    	}
    	 
    }
    
    public function do_refuse_coupon()
    {
    	global_run();
    	$s_account_info = $GLOBALS['account_info'];
    
    	if(intval($s_account_info['id'])==0)
    	{
    		$data['status']=1000;
    		ajax_return($data);
    	}
    
    	if(!check_module_auth("dealo")&&!check_module_auth("goods"))
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    
    	$id = intval($_REQUEST['id']);
    	$supplier_id = intval($s_account_info['supplier_id']);
    	$allow_refund = $GLOBALS['db']->getOne("select allow_refund from ".DB_PREFIX."supplier where id = ".$supplier_id);
    	if(!$allow_refund)
    	{
    		$data['status'] = 0;
    		$data['info'] = "权限不足";
    		ajax_return($data);
    	}
    
    	require_once(APP_ROOT_PATH."system/model/deal_order.php");
    	$order_item_table_name = get_supplier_order_item_table_name($supplier_id);
    
    	$order_item = $GLOBALS['db']->getRow("select * from ".$order_item_table_name." where refund_status = 1 and id = ".$id." and supplier_id = ".$supplier_id);
    	if($order_item)
    	{
    		$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_deal_id = ".$order_item['id']);
    		foreach($coupon_list as $c)
    		{
    			refuse_coupon($c['id']);
    		}
    		$data['status'] = true;
    		$data['info'] = "拒绝退款操作成功";
    		ajax_return($data);
    	}
    	else
    	{
    		$data['status'] = 0;
    		$data['info'] = "非法的数据";
    		ajax_return($data);
    	}
    
    }

    /**
     * 设置关联商品
     */
    public function add_related_deal()
    {

//    	$deal_id=intval($_REQUEST['deal_id']);
//    	$edit_type=intval($_REQUEST['edit_type']);
//        if($deal_id>0){
//        	$related_deal_id=$GLOBALS['db']->getOne("select relate_ids from ".DB_PREFIX."relate_goods where good_id = ".$deal_id);
//        	if($edit_type==2){
//        		$related_deal_id=$GLOBALS['db']->getOne("select cache_relate from ".DB_PREFIX."deal_submit where id = ".$deal_id);
//        		$related_deal_id= unserialize($related_deal_id);
//        		$related_deal_id= $related_deal_id['relate_ids'];
//        		
//        	}
//        	$related_deal=$GLOBALS['db']->getAll("select id,name,icon from ".DB_PREFIX."deal where id in(".$related_deal_id.")");
//        	$GLOBALS['tmpl']->assign("related_deal",$related_deal);
//        	$GLOBALS['tmpl']->assign("related_deal_id",$related_deal_id);
//        }
        
    	$data['html'] = $GLOBALS['tmpl']->fetch("pages/project/add_related_deal.html");
        ajax_return($data);
    }    

      /**
     * 加载可关联商品
     */
	public function load_relate()
	{
		global_run();
		$account_info = $GLOBALS['account_info'];
        $supplier_id = $account_info['supplier_id'];
        
		require_once(APP_ROOT_PATH . 'app/Lib/page.php');
		$deal_id = intval($_REQUEST['deal_id']);
		$is_shop = intval($_REQUEST['is_shop']);
		$related_deal = strim($_REQUEST['related_deal']);
		if($related_deal=='')$related_deal=0;
		$page = intval($_REQUEST['p']);
		$keyword = strim($_REQUEST['keyword']);
        //不关联无需配送
		$condition=' 1=1 and is_delivery=1 ';
		if($keyword!='')$condition= " name like '%".$keyword."%' ";
		
		//print_r($_REQUEST);exit;
		$page = intval($_REQUEST['p']);
		$page_size = 8;

		if($page<=0)$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
		//echo "select id,name,icon from ".DB_PREFIX."deal where id <> ".$deal_id." and ".$condition." and is_shop=".$is_shop." and id not in(".$related_deal.") and  supplier_id=".$supplier_id." and is_effect = 1 and is_delete=0  and buy_type <> 1 order by sort desc limit ".$limit;
		$list = $GLOBALS['db']->getAll("select id,name,icon from ".DB_PREFIX."deal where id <> ".$deal_id." and ".$condition." and is_shop=".$is_shop." and id not in(".$related_deal.") and  supplier_id=".$supplier_id." and is_effect = 1 and is_delete=0  and buy_type <> 1 order by sort desc limit ".$limit);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where id <> ".$deal_id." and ".$condition." and is_shop=".$is_shop." and id not in(".$related_deal.") and supplier_id=".$supplier_id." and is_effect = 1 and is_delete=0  and buy_type <> 1 ");
				
		$GLOBALS['tmpl']->assign("list",$list);
		$page = new Page($count,$page_size);   //初始化分页对象
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
    	$data['html'] = $GLOBALS['tmpl']->fetch("pages/project/load_relate.html");
        ajax_return($data);
	}
    /**
     * @desc      根据分类id获得品牌
     * @author    郑雄
     */
    public function cate_brand(){
        $shop_cate_id=strim($_REQUEST['shop_cate_id']);
        $brand_id=intval($_REQUEST['brand_id']);
        $name1=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."shop_cate where id in(".$shop_cate_id.")");
        /*foreach ($name1 as $k=>$v){
            if($v['pid'] > 0){
                $first_cate = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where id =".$v['pid']);
                $name1[$k]['first_cate'] = $first_cate['name'];
            }else{
                $name1[$k]['first_cate']='';
            }
        }
        $brand_all = array();
        foreach ($name1 as $k=>$v){
            $brand_name=$v['name'];
            $brand_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."brand where FIND_IN_SET('".$brand_name."',tag_match_row)");
            foreach($brand_list as $kk=>$brand){
                $brand_all[$kk] = $brand;
            }
        }*/
		$cate_id_key=array();
		foreach ($name1 as $k=>$v){
            if($v['pid'] > 0){
				if($cate_id_key[$v['pid']]!=1){
					$cate_id_key[$v['pid']]=1;
				}
			}
			$cate_id_key[$v['id']]=1;
        }
		$brand_all = array();
        foreach ($cate_id_key as $k=>$v){
			if($v==1){
				$brand_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."brand where shop_cate_id=".$k);
				foreach($brand_list as $kk=>$brand){
					$brand_all[$brand['id']] = $brand;
				}
			}
            
        }
        $html='<select class="ui-select filter_select medium" name="brand_id" >';
        $html.='<option value="0">==请选择品牌==</option>';
        foreach ($brand_all as $k =>$v){
            $html.='<option value="'.$v['id'].'"';
            if($v['id']==$brand_id){
                $html.='selected=selected';
            }
            $html.='>'.$v['name'].'</option>';
        }
        $html.='</select>';
        //ajax_return($html);
        echo $html;

    }
    
}