<?php
//过期、即将过期优惠券和红包消息发送
function expire_msg(){
    require_once(APP_ROOT_PATH.'system/msg/msg.php');
    $msg = new Msg;
    $now=NOW_TIME;
    $more_day=$now+3600*24;
    
    //即将过期红包
    $sql="select e.id,e.user_id,e.expire_msg,et.name from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id=et.id ";
    $sql.=" where (e.end_time BETWEEN ".$now." AND ".$more_day.") and (e.use_count<e.use_limit or e.use_limit=0) and e.expire_msg=0";
    $soon_ecv=$GLOBALS['db']->getAll($sql);
    if($soon_ecv){
        foreach ($soon_ecv as $t => $v){
            $content="您的".$v['name']."即将过期，快去使用吧~";
            $msg->send_msg($v['user_id'],$content,"account",array("type"=>12));
            $GLOBALS['db']->query('UPDATE '.DB_PREFIX.'ecv SET expire_msg=1 WHERE id='.$v['id']);
        }
    }
    
    //过期红包
    $sql1="select e.id,e.user_id,e.expire_msg,et.name from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id=et.id ";
    $sql1.=" where e.end_time<".$now." and e.end_time<>0 and (e.use_count<e.use_limit or e.use_limit=0) and e.expire_msg<>2";
    $expire_ecv=$GLOBALS['db']->getAll($sql1);
    if($expire_ecv){
        foreach ($expire_ecv as $t => $v){
            $content="您的".$v['name']."已过期";
            $msg->send_msg($v['user_id'],$content,"account",array("type"=>7));
            $GLOBALS['db']->query('UPDATE '.DB_PREFIX.'ecv SET expire_msg=2 WHERE id='.$v['id']);
        }
    }
    
    //即将过期优惠券
    $sql2="select id,youhui_sn,user_id,expire_msg from ".DB_PREFIX."youhui_log where confirm_time=0 and expire_msg=0 and (expire_time BETWEEN ".$now." AND ".$more_day.")";
    $soon_youhui=$GLOBALS['db']->getAll($sql2);
    if($soon_youhui){
        foreach ($soon_youhui as $t => $v){
            $content="您的优惠券<".$v['youhui_sn'].">即将过期，快去使用吧~";
            $msg->send_msg($v['user_id'],$content,"notify",array("type"=>8));
            $GLOBALS['db']->query('UPDATE '.DB_PREFIX.'youhui_log SET expire_msg=1 WHERE id='.$v['id']);
        }
    }
    
    //过期优惠券
    $sql3="select id,youhui_sn,user_id,expire_msg from ".DB_PREFIX."youhui_log where confirm_time=0 and expire_msg<>2 and expire_time<".$now." and expire_time<>0";
    $expire_youhui=$GLOBALS['db']->getAll($sql3);
    if($expire_youhui){
        foreach ($expire_youhui as $t => $v){
            $content="您的优惠券<".$v['youhui_sn'].">已过期";
            $msg->send_msg($v['user_id'],$content,"notify",array("type"=>9));
            $GLOBALS['db']->query('UPDATE '.DB_PREFIX.'youhui_log SET expire_msg=2 WHERE id='.$v['id']);
        }
    }
    
}
?>