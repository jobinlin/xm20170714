AJAX 数据返回说明

array(
    'status'=>0,   //返回状态 0 失败，1成功                              [必填]
    'info'='验证成功',  //错误消息或者提示信息，可选弹窗显示                [可选]
    'error_code'=>'1001',  //错误代码                                   [可选]
    'error_msg'=>'登录失败',  //只能是错误消息，传值就一定弹窗显示的         [可选]
    'user_login_status'=>0,  //用户状态 0 未登录，1已经登录，2 临时登录     [可选]
    'biz_user_status'=>0,  //(仅限商户端使用)商户状态 0 未登录，1已经登录，2 临时登录     [可选]
    'data'=>array(                                                      [可选]
        'img'=>'http://baidudud.ff.com/aa.jgp',
        'name'=>'拖里个拖'
    ),	//数据集 或者 HTML
    'jump'=>'http://o2onew.fanwe.net/login.php',    //执行完成后要跳转的连接 [可选]
    //....其他特殊的自定义的必须另外说明
)