<?php
class fxModule extends HizBaseModule{
    function __construct(){
        parent::__construct();
        global_run();
    }
    public function fx_log(){
        init_app_page();
        //获取参数
        $page=$_REQUEST['p'];
        $user_id=intval($_REQUEST['user_id']);
        $limit=formatLimit($page);
        //开始取数据
        $list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."fx_user_money_log where user_id=".$user_id." order by id desc ".$limit);
        $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."fx_user_money_log where user_id=".$user_id);
        foreach($list as $key=>$value){
            $list[$key]['create_time']=to_date($value['create_time']);
            $list[$key]['money']=$value['money']<0?"-&yen;".number_format(abs($value['money']),2):"&yen;".number_format($value['money'],2);
        }
        formatPage($count);
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->display("pages/fx/fx_log.html");
    }
    public function index(){
        init_app_page();
        //获取参数
        $name=$_REQUEST['name'];
        $p_name=$_REQUEST['p_name'];
        $u_id=intval($_REQUEST['u_id']);//查找会员的所属下线
        $hiz_info=$GLOBALS['hiz_account_info'];
        $level=intval($_REQUEST['level']);
        if($level<4){
            $limit=formatLimit(intval($_REQUEST['p']));
            $where=" ";
            if($name){
                $where=" and (u.user_name='".$name."' or u.mobile='".$name."') ";
            }
            if($p_name){
                $where=" and (p.user_name='".$p_name."' or p.mobile='".$p_name."') ";
            }
            if($u_id){
                $where=" and u.pid=".$u_id." ";
            }
            //只有不是下级分销查找的，才是寻找该代理商的
            if($level==0){
                $where.=" and u.agency_id=".$hiz_info['id']." and u.is_fx=1 ";
            }
            $list=$GLOBALS['db']->getAll("select u.id,u.user_name,u.mobile,u.fx_money,u.fx_total_balance,u.fx_total_money,p.user_name p_user_name,p.mobile p_mobile from ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user p on u.pid=p.id where u.is_effect=1 ".$where."order by u.id desc".$limit);
            $count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user u where u.is_effect ".$where);
            foreach($list as $key=>$value){
                $list[$key]['fx_money']=number_format($value['fx_money'],2);
                $list[$key]['fx_total_balance']=number_format($value['fx_total_balance'],2);
                $list[$key]['fx_total_money']=number_format($value['fx_total_money'],2);
            }
            formatPage($count);
        }
        $level++;
        $GLOBALS['tmpl']->assign("level",$level);
        $GLOBALS['tmpl']->assign("list",$list);
        $GLOBALS['tmpl']->display("pages/fx/index.html");
    }
}