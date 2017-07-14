<?php

/**
 * 计划任务
 * Created by PhpStorm.
 * User: jobinlin
 * Date: 2017/5/3
 * Time: 16:03
 */
class Schedule
{

    /**
     * 发送计划任务
     * @param unknown_type $type  计划任务执行的相关接口
     * @param unknown_type $name 后台识别的名称
     * @param unknown_type $schedule_data  执行接口所需的参数内容
     * @param unknown_type $schedule_time  计划任务执行时间(0表示立即执行)
     * @param unknown_type $dest 发送目标(针对短信，邮件，推送等，方便查询，站内业务为空)
     *
     * 关于定时器与计划任务的说明
     * 定时器负责定时执行相关类型的计划任务请求入口
     * 请求入口按类型查询需要执行的计划任务，调用exec_schedule_plan，返回 array("status"=>0/1, "attemp"=>0/1,  "info"=>string);
     * attemp:1: 表示程序将会继续对该任务进行调度
     * 0:则更新计划任务状态与结束时间，表示任务处理完成，不再对任务进行调度
     *
     * status表示当前业务是否执行成功
     *
     */
    function send_schedule_plan($type,$name,$schedule_data,$schedule_time,$dest="")
    {
        $data['type'] = $type;
        $data['name'] = $name;
        if($dest)
            $data['dest'] = $dest;
        $data['data'] = serialize($schedule_data);

        if($schedule_time>0)
            $schedule_exec_time = $schedule_time;
        else
            $schedule_exec_time = NOW_TIME;
        $data['schedule_date'] = to_date($schedule_exec_time,"Y-m-d");
        $data['schedule_time'] = $schedule_exec_time;

        $GLOBALS['db']->autoExecute(DB_PREFIX."schedule_list",$data);
        $data['id'] = $GLOBALS['db']->insert_id();
        if($schedule_time==0&&$data['id'])
        {
            //立即执行处理
            exec_schedule_plan($data);
        }
    }

    /**
     * 立即执行计划任务(不考虑计划时间)
     * @param unknown_type $schedule_data 数据库中计划任务的整个数据集
     * 返回 array("status"=>0/1, "attemp"=>0/1,  "info"=>string);
     */
    function exec_schedule_plan($schedule_data)
    {
        $type = $schedule_data['type'];
        $cname = $type."_schedule";
        require_once APP_ROOT_PATH."system/schedule/".$cname.".php";
        $c = new $cname;
        $item_data = unserialize($schedule_data['data']);
        $result = $c->exec($item_data);

        if($schedule_data['exec_status']==0) //第一次开始
            $schedule_data['exec_begin_time'] = NOW_TIME;

        if($result['info'])
        {
            $schedule_data['exec_info'] = $result['info'];
        }
        else
        {
            unset($schedule_data['exec_info']);
        }

        if($result['attemp'])
        {
            $schedule_data['exec_status'] = 1; //进行中
        }
        else
        {
            $schedule_data['exec_status'] = 2; //结束
            $schedule_data['exec_end_time'] = NOW_TIME;
        }
        $GLOBALS['db']->autoExecute(DB_PREFIX."schedule_list",$schedule_data,"UPDATE","id='".$schedule_data['id']."'","SILENT");

        return $result;
    }


    function make_app_js()
    {
        $content = @file_get_contents(APP_ROOT_PATH."system/app.js");
        $content = str_replace("__HOST__", get_host(), $content);
        $content = str_replace("__APP_ROOT__", APP_ROOT, $content);

        require_once APP_ROOT_PATH.'/system/libs/crypt_aes.php';
        $aes = new CryptAES();
        $aes->set_key(FANWE_AES_KEY);
        $aes->require_pkcs5();

        $json = json_encode(array("type"=>"mass","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_MASS__", $encText, $content);

        $json = json_encode(array("type"=>"mail","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_MAIL__", $encText, $content);

        $json = json_encode(array("type"=>"sms","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_SMS__", $encText, $content);

        $json = json_encode(array("type"=>"weixin","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_WEIXIN__", $encText, $content);

        $json = json_encode(array("type"=>"android","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_ANDROID__", $encText, $content);

        $json = json_encode(array("type"=>"ios","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_IOS__", $encText, $content);

        $json = json_encode(array("type"=>"gc","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_GC__", $encText, $content);

        $json = json_encode(array("type"=>"order","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_ORDER__", $encText, $content);
        
        $json = json_encode(array("type"=>"dc_order","key"=>FANWE_APP_ID));
        $encText = $aes->encrypt($json);
        $content = str_replace("__TYPE_DC_ORDER__", $encText, $content);
        

        $gc_schedule_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."schedule_list where type = 'gc' and exec_status = 0");
        if(intval($gc_schedule_count)==0)
        {
            $this->send_schedule_plan("gc", "定时任务", array(), NOW_TIME);
        }
        $order_schedule_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."schedule_list where type = 'order' and exec_status = 0");
        if(intval($order_schedule_count)==0)
        {
            $this->send_schedule_plan("order", "订单定时任务", array(), NOW_TIME);
        }
        
        $dc_order_schedule_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."schedule_list where type = 'dc_order' and exec_status = 0");
        if(intval($dc_order_schedule_count)==0)
        {
            $this->send_schedule_plan("dc_order", "订单定时任务", array(), NOW_TIME);
        }
        
         @file_put_contents(APP_ROOT_PATH."public/app.js", $content);
    }

}