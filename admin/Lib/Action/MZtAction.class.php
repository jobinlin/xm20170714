<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class MZtAction extends CommonAction{
	
	private function load_zt_page()
	{
		$directory = APP_ROOT_PATH."mapi/mobile_zt/";
		$files = get_all_files($directory);
		$tmpl_files = array();
		foreach($files as $item)
		{
		    if(substr($item,-6)!="1.html"&&substr($item,-6)!="2.html"){
    			if(substr($item,-5)==".html")
    			{
    				$item = explode($directory,$item);
    				$item = $item[1];
    				if(substr($item,0,4)!="inc/")
    				$tmpl_files[] = $item;
    			}
		    }
		}
		return $tmpl_files;
	}
	
	public function add()
	{
		
		$nav_cfg = $GLOBALS['mobile_cfg'];
		$this->assign("nav_cfg",$nav_cfg);
		
		foreach($nav_cfg as $k=>$v)
		{
			if($v['mobile_type']==0)
			{
				$this->assign("nav_list",$v['nav']);
			}
		}	

		$this->assign("new_sort",intval(M(MODULE_NAME)->max("sort"))+1);
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		foreach($city_list as $k=>$v)
		{
			if($v['pid']==0)$city_list[$k]['id'] = 0;
		}
		$this->assign("city_list",$city_list);
		$zt_id=rand(10000000,90000000);
		$this->assign("zt_id",$zt_id);
		$this->assign("zt_moban",$this->load_zt_page());
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$nav_cfg = $GLOBALS['mobile_cfg'];	
		
		$data = M(MODULE_NAME)->create ();
		
		foreach($nav_cfg as $k=>$v)
		{
			if($v['mobile_type']==$data['mobile_type'])
			{
				$navs = $v['nav'];
			}
		}
		
		foreach($navs as $ctl=>$v)
		{
			if($v['type']==$data['type'])
			{
				$data['ctl'] = $ctl;				
				$cfg = array($v['field']=>$_POST[$v['field']]);				
				$data['data'] = serialize($cfg);
			}
		}
			
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("NAME_EMPTY_TIP"));
		}	
		
		if($_REQUEST['page']){
			$data['page'] = implode(",",$_REQUEST['page']);
		}
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);

			M('MAdv')->where(array('zt_id'=>$_REQUEST['rid']))->save(array('zt_id'=>$list));
			
			$this->success(L("INSERT_SUCCESS"));
			
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}

	
	
	public function edit()
	{
		$nav_cfg = $GLOBALS['mobile_cfg'];
		$this->assign("nav_cfg",$nav_cfg);
		
		$id = intval($_REQUEST['id']);
		$vo = M("MZt")->getById($id);
		$page = explode(",",$vo['page']);
		$page_arr=array();
		if($page){
		    foreach($page as $k=>$v){
		        $page_arr[$v]=$v;
		    }
		}

		$this->assign ('page', $page_arr);
		$vo['data'] = unserialize($vo['data']);
		
		$this->assign ('vo', $vo);
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		foreach($city_list as $k=>$v)
		{
			if($v['pid']==0)$city_list[$k]['id'] = 0;
		}
		$this->assign("city_list",$city_list);
		
		
		foreach($nav_cfg as $k=>$v)
		{
			if($v['mobile_type']==$vo['mobile_type'])
				$this->assign("nav_list",$v['nav']);
		}
		
		$this->assign("zt_moban",$this->load_zt_page());
		$this->display();
	}
	
	
	public function update() {
		B('FilterString');

		$nav_cfg = $GLOBALS['mobile_cfg'];
	
		$data = M(MODULE_NAME)->create ();
		
		foreach($nav_cfg as $k=>$v)
		{
			if($v['mobile_type']==$data['mobile_type'])
			{
				$navs = $v['nav'];
			}
		}
		
		foreach($navs as $ctl=>$v)
		{
			if($v['type']==$data['type'])
			{
				$data['ctl'] = $ctl;
				$cfg = array($v['field']=>$_POST[$v['field']]);
				$data['data'] = serialize($cfg);
			}
		}

		$data['page'] = implode(",",$_REQUEST['page']);
		$log_info = $data['id'];
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("NAME_EMPTY_TIP"));
		}
		
		$log_info = $data['name'];
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
	
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );	
				foreach($rel_data as $data)
				{
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
		
				if ($list!==false) {
					$ztcondition = array ('zt_id' => array ('in', explode ( ',', $id ) ) );
					M("MAdv")->where($ztcondition)->delete();
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}

    public function load_html()
    {
        $zt_moban = strim($_REQUEST['zt_moban']);
        $zt_id = intval($_REQUEST['zt_id']);
        if($zt_id > 0){
            $zt_data = M("MAdv")->where('zt_id='.$zt_id)->findAll();
            foreach($zt_data as $k=>$v){
                $zt_data[$k]['data']=unserialize($v['data']);
            }   
        }


        $file_content = file_get_contents(APP_ROOT_PATH."mapi/mobile_zt/".$zt_moban.".html");
        preg_match_all("/<!--([^-]+)-->/",$file_content,$layout_array);

        $zt_img_data=array();
        foreach($layout_array[1] as $item)
        {
            if($zt_data){
               foreach($zt_data as $k=>$v){
                  if($zt_data[$k]['zt_position']==$item){
                      $zt_img_data[$item]['img']=$zt_data[$k]['img'];
                      $zt_img_data[$item]['type']=$zt_data[$k]['type'];
                      
                      if( $zt_data[$k]['data']){
                          foreach($zt_data[$k]['data'] as $kk=>$vv){
                              $zt_img_data[$item]['ctl_name']=$kk;
                              $zt_img_data[$item]['ctl_value']=$vv;
                          }
                      }
                      break;
                  }
               }
            }
            
            if($zt_img_data[$item]['img']==''){
                $zt_img_data[$item]['img']=APP_TMPL_PATH.'MZt/admin/images/'.$zt_moban.'/'.$item.'.png';
            }
    
        }
        //print_r($zt_img_data);
        $this->assign("zt_moban",$zt_moban);
        $this->assign("zt_img_data",$zt_img_data);
        $this->display('admin/'.$zt_moban);

    }

    public function iframe_box()
    {
        $zt_moban = strim($_REQUEST['zt_moban']);
        $zt_img = strim($_REQUEST['zt_img']);
        $mobile_type = strim($_REQUEST['mobile_type']);
        $type = intval($_REQUEST['type']);
        $ctl_name = strim($_REQUEST['ctl_name']);
        $ctl_value = strim($_REQUEST['ctl_value']);
        $zt_img_pic = strim($_REQUEST['zt_img_pic']);

        $parma=array('zt_moban'=>$zt_moban,'zt_img'=>$zt_img,
            'mobile_type'=>$mobile_type,'type'=>$type,
            'ctl_name'=>$ctl_name,'ctl_value'=>$ctl_value,
            'zt_img_pic'=>$zt_img_pic,
        );
        $url=U('MZt/open_zt_box',$parma);
        $this->assign("url",$url);
        $this->display();

    }

    public function open_zt_box()
    {
        $zt_moban = strim($_REQUEST['zt_moban']);
        $zt_img = strim($_REQUEST['zt_img']);
        $type = intval($_REQUEST['type']);

        $ctl_name = strim($_REQUEST['ctl_name']);
        $ctl_value = strim($_REQUEST['ctl_value']);
        $zt_img_pic = strim($_REQUEST['zt_img_pic']);
        $nav_cfg = $GLOBALS['mobile_cfg'];
        $this->assign("nav_cfg",$nav_cfg);
        $mobile_type = intval($_REQUEST['mobile_type']);
        //$id = intval($_REQUEST['id']);
        foreach($nav_cfg as $k=>$v)
        {
            if($v['mobile_type']==$mobile_type)
                $this->assign("nav_list",$v['nav']);
        }
         
        $this->assign("mobile_type",$mobile_type);
        $this->assign("zt_moban",$zt_moban);
        $this->assign("type",$type);
        $this->assign("ctl_name",$ctl_name);
        $data=array($ctl_name=>$ctl_value);
        $this->assign("ctl_value",$ctl_value);
        $this->assign("zt_img_pic",$zt_img_pic);
        $this->assign("zt_img",$zt_img);
        $this->assign("data",$data);
        $zt_moban_demo=APP_TMPL_PATH.'MZt/admin/images/'.$zt_moban.'/'.$zt_moban.'.png';
        $this->assign("zt_moban_demo",$zt_moban_demo);
        $this->display();

    }
    

    public function zt_img_upload() {
        B('FilterString');
    
        $nav_cfg = $GLOBALS['mobile_cfg'];

        $data = M('MAdv')->create();
        $data['name'] = $data['zt_position'];

        
        
        if($data['img']=="")
        {
            $result['status']=0;
            $result['info']='未上传图片';
            ajax_return($result);
            
        }
        foreach($nav_cfg as $k=>$v)
        {
            if($v['mobile_type']==$data['mobile_type'])
            {
                $navs = $v['nav'];
            }
        }
    
        foreach($navs as $ctl=>$v)
        {
            if($v['type']==$data['type'])
            {
                $data['ctl'] = $ctl;
                $cfg = array($v['field']=>$_REQUEST[$v['field']]);
                $data['data'] = serialize($cfg);
            }
        }
        $data['status']=1;
        $data['position']=2;
        $old_data = M('MAdv')->where(array('zt_id'=>$data['zt_id'],'zt_position'=>$data['zt_position']))->find();

        if($old_data){
            $data['id']=$old_data['id'];
            $list=M('MAdv')->save ($data);
        }else{
            $list=M('MAdv')->add($data);
        }
    
        $log_info = $data['id'];
        $log_info = $data['name'];
       
        if (false !== $list) {
            //成功提示
            save_log($log_info.L("UPDATE_SUCCESS"),1);

        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);

        }
        $result['status']=1;
        $result['info']='上传图片成功';
        ajax_return($result);
    }

}
?>