<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

/**
 * 刷新会员安全登录状态
 */
function refresh_user_info()
{
	global $user_info;
	global $user_logined;
	//实时刷新会员数据
	if($user_info)
	{
		$user_info = load_user($user_info['id']);
		$user_level = load_auto_cache("cache_user_level");
		$user_info['level'] = $user_level[$user_info['level_id']]['level'];
		$user_info['level_name'] = $user_level[$user_info['level_id']]['name'];
		es_session::set('user_info',$user_info);

		$user_logined_time = intval(es_session::get("user_logined_time"));
		$user_logined = es_session::get("user_logined");
		if(NOW_TIME-$user_logined_time>=MAX_LOGIN_TIME)
		{
			es_session::set("user_logined_time",0);
			es_session::set("user_logined", false);
			$user_logined = false;
		}
		else
		{
			if($user_logined)
				es_session::set("user_logined_time",NOW_TIME);
		}
	}
}

/**
 * 前端全运行函数，生成系统前台使用的全局变量
 * 1. 定位城市 GLOBALS['city'];
 * 2. 加载会员 GLOBALS['user_info'];
 * 3. 定位经纬度 GLOBALS['geo'];
 * 4. 加载推荐人与来路
 * 5. 更新购物车
 */
function global_run()
{
	global $global_is_run;
	$global_is_run = true;
	if(app_conf("SHOP_OPEN")==0)  //网站关闭时跳转到站点关闭页
	{
		app_redirect(wap_url("index","close"));
	}


	//处理城市
	global $city; 
	require_once(APP_ROOT_PATH."system/model/city.php");
	$city = City::locate_city(intval($_REQUEST['city_id']));

	//处理经纬度
	global $geo;
	$geo = City::locate_geo($_REQUEST['m_longitude'],$_REQUEST['m_latitude'],$_REQUEST['m_type']);
		
	global $ref_uid;
	
	//保存返利的cookie
	if($_REQUEST['r'])
	{
		$rid = intval(base64_decode($_REQUEST['r']));
		$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval($rid)));
		es_cookie::set("REFERRAL_USER",intval($ref_uid));
	}
	else
	{
		//获取存在的推荐人ID
		if(intval(es_cookie::get("REFERRAL_USER"))>0)
			$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval(es_cookie::get("REFERRAL_USER"))));
	}
	
	//会员自动登录及输出
	global $cookie_uname;
	global $cookie_upwd;
	global $user_info;
	global $user_logined;
	global $wx_info;
	global $is_weixin; //是否为微信访问
	global $supplier_info;  //商家信息
	//处理商户信息
	global $spid;
	$spid = intval($_REQUEST['spid']);
	if($spid>0)
	{
		$supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$spid);
	}
	
	$is_weixin=isWeixin();
	$user_info = es_session::get('user_info');
	$wx_info = es_session::get("wx_info"); //微信在平台的信息记录，openid为平台公众号的openid，wx_openid为商家公众号的openid
	$spid = $account_id = intval($supplier_info['id']);
	 //log
    if($user_info){
        es_cookie::set("user_login_id",$user_info['id']);
    }
	
	$weixin_login = intval($_REQUEST['weixin_login']);
	if($weixin_login==1)
	{
		es_cookie::delete("deny_weixin_".intval($GLOBALS['supplier_info']['id']));
		$deny_weixin = 0;
	}
	else
	{
		$deny_weixin = intval(es_cookie::get("deny_weixin_".intval($GLOBALS['supplier_info']['id'])));
	}
	
	//$account_id = 23;
	if(empty($user_info))
	{
		//关于微信登录		
		$m_config = getMConfig();//初始化手机端配置		
		if($is_weixin&&$deny_weixin==0)			
		{
			require_once(APP_ROOT_PATH.'system/utils/weixin.php');
			$weixin_conf = load_auto_cache("weixin_conf");
			if($weixin_conf['platform_status']==1&&WEIXIN_TYPE!="platform")
			{				
				$weixin_account_platform = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = 0");
				$weixin_account = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_account where user_id = '".$account_id."'");
				if(empty($weixin_account))$weixin_account=$weixin_account_platform;
				if($weixin_conf['platform_component_verify_ticket']!=""&&$weixin_account&&$weixin_account_platform)
				{		
					if(!$wx_info['openid'])//如未获取平台公众的openid先获取openid
					{
						$wx_code = strim($_REQUEST['code']);
						$wx_status = intval($_REQUEST['state']);
						if($wx_code&&$wx_status)
						{
							//微信端回跳回wap
							$url =  get_current_url();
							$weixin=new weixin($weixin_account_platform['authorizer_appid'],"",SITE_DOMAIN.$url,"snsapi_base");
							$wx_info['openid'] = $weixin->scope_get_openid($wx_code);
							es_session::set("wx_info", $wx_info);		
							app_redirect($url);
						}
						else
						{
							//跳转至微信的授权页
							$url =  get_current_url();
							$weixin = new weixin($weixin_account_platform['authorizer_appid'],"",SITE_DOMAIN.$url,"snsapi_base");
							$wx_url=$weixin->scope_get_code_platform();
							app_redirect($wx_url);
						}
					}
					else
					{
						//平台openid已授权，获取商家的openid
						$openid = $wx_info['openid'];
						$wx_code = strim($_REQUEST['code']);
						$wx_status = intval($_REQUEST['state']);
						if($wx_code&&$wx_status&&intval($_REQUEST['redirect'])==0)
						{
							//微信端回跳回wap							
							$url =  get_current_url();
							$weixin=new weixin($weixin_account['authorizer_appid'],"",SITE_DOMAIN.$url,"snsapi_base");								
							$wx_info['openid'] = $openid;	
							$wx_info['wx_openid'] = $weixin->scope_get_openid($wx_code);
							es_session::set("wx_info", $wx_info);
						}
							
						
						if($wx_info&&$wx_info['wx_openid']&&$wx_info['openid'])
						{
							$platform_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user  where wx_openid='".$wx_info['openid']."'"); //平台用户
							$supplier_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_user where account_id = '".$account_id."' and user_id = '".$platform_user_info['id']."'"); //获取当前商户的用户
							
							if($platform_user_info&&$supplier_user) //有平台用户，并且该用户已在商户生成过用户
							{
								auto_do_login_user($platform_user_info['user_name'],$platform_user_info['user_pwd'],false);
								$user_info = es_session::get('user_info');
							}
							elseif($platform_user_info&&!$supplier_user)//有平台用户但未成商家生成过用户
							{
								$GLOBALS['db']->query("delete from ".DB_PREFIX."weixin_user where openid = '".$wx_info['wx_openid']."'");
								$supplier_user = array();
								$supplier_user['user_id'] = $platform_user_info['id'];
								$supplier_user['account_id'] = $account_id;
								$supplier_user['openid'] = $wx_info['wx_openid']; //商户openid
								$supplier_user['nickname'] = $platform_user_info['user_name'];
								$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$supplier_user);
								$supplier_user['id'] = $GLOBALS['db']->insert_id();
								auto_do_login_user($platform_user_info['user_name'],$platform_user_info['user_pwd'],false);
								$user_info = es_session::get('user_info');
							}
							else
							{
								if(intval($_REQUEST['redirect'])==0) //未通知重定向
								{									
									//再次跳转
									$url =  get_current_url()."&redirect=1";	
									app_redirect($url);
								}
								else
								{
									
									//先再次授权下拉用户信息
									//第三次
									$wx_code = strim($_REQUEST['code']);
									$wx_status = intval($_REQUEST['state']);
									if($wx_code&&$wx_status)
									{
										//微信端回跳回wap
										$url =  get_current_url();
										$weixin=new weixin($weixin_account['authorizer_appid'],"",SITE_DOMAIN.$url);
										$weixin_user_info = $weixin->scope_get_userinfo_platform($wx_code); //通过授权获取到用户信息
										
										if(!$platform_user_info&&$weixin_user_info['unionid'])
										$platform_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user  where union_id='".$weixin_user_info['unionid']."'"); //平台用户
										if($platform_user_info)
										{
											$GLOBALS['db']->query("update ".DB_PREFIX."user set union_id = '".$weixin_user_info['unionid']."',wx_openid='".$weixin_user_info['openid']."' where id = ".$platform_user_info['id']);
											$supplier_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_user where account_id = '".$account_id."' and user_id = '".$platform_user_info['id']."'");
											if($platform_user_info&&$supplier_user) //有平台用户，并且该用户已在商户生成过用户
											{
												auto_do_login_user($platform_user_info['user_name'],$platform_user_info['user_pwd'],false);
												$user_info = es_session::get('user_info');
											}
											elseif($platform_user_info&&!$supplier_user)//有平台用户但未成商家生成过用户
											{
												$GLOBALS['db']->query("delete from ".DB_PREFIX."weixin_user where openid = '".$wx_info['wx_openid']."'");
												$supplier_user = array();
												$supplier_user['user_id'] = $platform_user_info['id'];
												$supplier_user['account_id'] = $account_id;
												$supplier_user['openid'] = $wx_info['wx_openid']; //商户openid
												$supplier_user['nickname'] = $platform_user_info['user_name'];
												$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$supplier_user);
												$supplier_user['id'] = $GLOBALS['db']->insert_id();
												auto_do_login_user($platform_user_info['user_name'],$platform_user_info['user_pwd'],false);
												$user_info = es_session::get('user_info');
											}										
										}
										else
										{
											if($weixin_user_info['errcode'])
											{
												die($weixin_user_info['errmsg']);
											}
											//获取到用户详细信息
											if(!$platform_user_info)//平台用户未生成
											{
												$user_data = array();
												$user_data['user_name'] = $weixin_user_info['nickname'];
												$user_data['wx_openid'] = $wx_info['openid']; //平台openid
												$user_data['union_id'] = $weixin_user_info['unionid'];
												$rs = auto_create($user_data);
												$platform_user_info = $rs['user_data'];
											}
											
											if(!$supplier_user)//商户会员未生成
											{
												$supplier_user = array();
												$supplier_user['user_id'] = $platform_user_info['id'];
												$supplier_user['account_id'] = $account_id;
												$supplier_user['openid'] = $wx_info['wx_openid']; //商户openid
												$supplier_user['nickname'] = $platform_user_info['user_name'];
												$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$supplier_user);
												$supplier_user['id'] = $GLOBALS['db']->insert_id();
											}
											
											if($supplier_user['openid']!=$wx_info['wx_openid'])
											{
												$supplier_user['user_id'] = $platform_user_info['id'];
												$supplier_user['account_id'] = $account_id;
												$supplier_user['openid'] = $wx_info['wx_openid']; //商户openid
												$supplier_user['nickname'] = $weixin_user_info['nickname'];
												$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$supplier_user,"UPDATE","id='".$supplier_user['id']."'");
											}
											auto_do_login_user($platform_user_info['user_name'],$platform_user_info['user_pwd'],false);
											$user_info = es_session::get('user_info');
										}
										
									}
									else
									{
										//跳转至微信的授权页
										$url =  get_current_url();
										$weixin = new weixin($weixin_account['authorizer_appid'],"",SITE_DOMAIN.$url);
										$wx_url=$weixin->scope_get_code_platform();
										app_redirect($wx_url);
									}
								}//第三次授权成功
							}							
							
						}
						else
						{
							//跳转至微信的授权页
							$url =  get_current_url();
							$weixin = new weixin($weixin_account['authorizer_appid'],"",SITE_DOMAIN.$url,"snsapi_base");
							$wx_url=$weixin->scope_get_code_platform();
							app_redirect($wx_url);
						}
					}
					
				}
			}
			else
			{		

				if(WEIXIN_TYPE=="platform")
				{
					//方维云平台saas模式接入
					$appid = FANWE_APP_ID;
					$appsecret = FANWE_AES_KEY;
					$server = new SAASAPIServer($appid, $appsecret);
					$ret = $server->takeSecurityParams($_SERVER['QUERY_STRING']);

				
					if($ret['openid'])
					{
						$wx_info = $ret;
						es_session::set("wx_info", $wx_info);
						wx_info_login($wx_info);
							
					}else
					{
						//加密
						$client = new SAASAPIClient($appid, $appsecret);
						$widthAppid = true;  // 生成的安全地址是否附带appid参数
						$timeoutMinutes = 10; // 安全参数过期时间（单位：分钟），小于等于0表示永不过期
						if($_REQUEST['ctl']=='store_pay'||$_REQUEST['ctl']=='shop'||$_REQUEST['ctl']=='main'||($_REQUEST['ctl']=='uc_fx'&&$_REQUEST['ctl']=='mall')){//到店买单页，团购首页，商城首页，我的小店通过扫描二维码进入，登录结束返回对应界面
							$params['from'] ='//'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
						}else{
							$params['from'] = SITE_DOMAIN.wap_url("index");
						}
						$params['appsys_name'] = $GLOBALS['_FANWE_SAAS_ENV']['APPSYS_ID'];
				
						$url = 'http://service.yun.fanwe.com/weixin/create_url';
						$wx_url = $client->makeSecurityUrl($url, $params, $widthAppid, $timeoutMinutes);
						//var_dump($wx_url);exit;
						//$wx_url = 'http://service.yun.fanwe.com/weixin/create_url?from='.urlencode($back_url);
						app_redirect($wx_url);
					}
				
				}
				else
				{
                    if($m_config['wx_appid']&&$m_config['wx_secrit'])
                    {
                        $wx_code = strim($_REQUEST['code']);
                        if($wx_code&&strim($_REQUEST['state']))
                        {

                            //微信端回跳回wap
                            $url =  get_current_url();
                            $weixin=new weixin($m_config['wx_appid'],$m_config['wx_secrit']);
                            $wx_info=$weixin->scope_get_userinfo($wx_code);

                        }

                        if($wx_info)//避免重复跳转微信授权页面
                        {
                            if($wx_info['openid']){
                                wx_info_login($wx_info);
                            }else{
                                echo print_r($wx_info,1);exit;
                            }

                        }
                        else
                        {
                            //跳转至微信的授权页
                            $url =  get_current_url();
                            $weixin = new weixin($m_config['wx_appid'],$m_config['wx_secrit'],SITE_DOMAIN.$url."&state=STATE");
                            $wx_url=$weixin->scope_get_code();
                            app_redirect($wx_url);
                        }
                    }
                    else
                    {
                        //showErr("微信功能未开通");
                    }
				}//end account
				
				
			}//end platform
			
		}
		else
		{
			$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
			$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';
			if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
			{
				$cookie_uname = strim($cookie_uname);
				$cookie_upwd = strim($cookie_upwd);
				auto_do_login_user($cookie_uname,$cookie_upwd);
				$user_info = es_session::get('user_info');
			}
		}		
	}
	else
	{
//		//已登录
//		if($is_weixin&&$deny_weixin==0)
//		{
//			$weixin_conf = load_auto_cache("weixin_conf");
//			if($weixin_conf['platform_status']==1&&WEIXIN_TYPE!="platform")
//			{
//				$supplier_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_user where account_id = '".$account_id."' and user_id = '".$user_info['id']."'"); //获取当前商户的用户
//				if(empty($supplier_user))
//				{
//					$data = call_api_core("user","loginout");
//					es_cookie::delete("user_name");
//					es_cookie::delete("user_pwd");
//					es_session::delete("wx_info");
//					$url = get_gopreview();
//					$url = preg_replace("/&redirect=[^&]*/i", "", $url);
//					app_redirect($url);
//				}
//			}
//		}
	}

	//关于微信绑定
	if($user_info&&intval($_REQUEST['weixin_bind'])){
        require_once APP_ROOT_PATH.'system/utils/weixin.php';
        //关于微信登录
        $m_config = getMConfig();//初始化手机端配置
        if($is_weixin) {

            if($m_config['wx_appid']&&$m_config['wx_secrit'])
            {
                if(WEIXIN_TYPE=="platform"){
					//方维云平台saas模式接入
					$appid = FANWE_APP_ID;
					$appsecret = FANWE_AES_KEY;
					$server = new SAASAPIServer($appid, $appsecret);
					$ret = $server->takeSecurityParams($_SERVER['QUERY_STRING']);
				
				
					if($ret['openid'])
					{
						$wx_info = $ret;
						es_session::set("user_wx_info", $wx_info);
						//$wx_info_bind_result=wx_info_bind($wx_info);
							
					}else
					{
						//加密
						$client = new SAASAPIClient($appid, $appsecret);
						$widthAppid = true;  // 生成的安全地址是否附带appid参数
						$timeoutMinutes = 10; // 安全参数过期时间（单位：分钟），小于等于0表示永不过期
						$params['from'] = SITE_DOMAIN.get_current_url();
						$params['appsys_name'] = $GLOBALS['_FANWE_SAAS_ENV']['APPSYS_ID'];
				
						$url = 'http://service.yun.fanwe.com/weixin/create_url';
						$wx_url = $client->makeSecurityUrl($url, $params, $widthAppid, $timeoutMinutes);
						//var_dump($wx_url);exit;
						//$wx_url = 'http://service.yun.fanwe.com/weixin/create_url?from='.urlencode($back_url);
						app_redirect($wx_url);
					}
				}else{
					$wx_code = strim($_REQUEST['code']);
					$wx_status = intval($_REQUEST['state']);
					if($wx_code&&$wx_status)
					{
						//微信端回跳回wap
						$url =  get_current_url();
						$weixin=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],SITE_DOMAIN.$url);
						$wx_info=$weixin->scope_get_userinfo($wx_code);
					}
					if($wx_info&&$wx_info['openid'])
					{
						es_session::set("user_wx_info", $wx_info);
						//$wx_info_bind_result=wx_info_bind($wx_info);
					}
					else
					{
						//跳转至微信的授权页
						$url =  get_current_url();
						$weixin = new weixin($m_config['wx_appid'],$m_config['wx_secrit'],SITE_DOMAIN.$url);
						$wx_url=$weixin->scope_get_code();
						app_redirect($wx_url);
					}
				}
            }
            else
            {
                //showErr("微信功能未开通");
            }
        }
    }


	refresh_user_info();
	
	//此处是会员（商家登录状态的初始化）
	require_once APP_ROOT_PATH."system/libs/biz_user.php";
	global $cookie_biz_uname;
	global $cookie_biz_upwd;
	global $account_info;
	$account_info = es_session::get('account_info');
	
	if(empty($account_info))
	{
		$cookie_biz_uname = es_cookie::get("biz_uname")?es_cookie::get("biz_uname"):'';
		$cookie_biz_upwd = es_cookie::get("biz_upwd")?es_cookie::get("biz_upwd"):'';
		if($cookie_biz_uname!=''&&$cookie_biz_upwd!=''&&!es_session::get("account_info"))
		{
			$cookie_biz_uname = strim($cookie_biz_uname);
			$cookie_biz_upwd = strim($cookie_biz_upwd);
			do_login_biz($cookie_biz_uname, $cookie_biz_upwd);
			$account_info = es_session::get('account_info');
		}
	}
	//实时刷新会员数据
	if($account_info)
	{
        $account_info = $GLOBALS['db']->getRow("select sa.*,s.is_open_dada_delivery,s.delivery_money,s.money,s.publish_verify_balance*100 as publish_verify_balance,s.platform_status from ".DB_PREFIX."supplier_account as sa left join ".DB_PREFIX."supplier as s on sa.supplier_id=s.id where sa.is_delete = 0 and sa.is_effect = 1 and sa.id = ".intval($account_info['id']));
	    if($account_info['is_main'] == 1){ //主账户取所有门店
			$account_locations = $GLOBALS['db']->getAll("select id as location_id from ".DB_PREFIX."supplier_location where supplier_id = ".$account_info['supplier_id']);
		}else
			$account_locations = $GLOBALS['db']->getAll("select location_id from ".DB_PREFIX."supplier_account_location_link where account_id = ".$account_info['id']);
	
		$account_location_ids = array();
		foreach($account_locations as $row)
		{
			$account_location_ids[] = $row['location_id'];
		}
		$account_info['location_ids'] =  $account_location_ids;
		$GLOBALS['account_info']['location_ids'] =  $account_location_ids;
	
		es_session::set('account_info',$account_info);
	}
	//end 商家端处理
	
	//刷新购物车
	require_once(APP_ROOT_PATH."system/model/cart.php");
	refresh_cart_list();

	

	global $referer;
	//保存来路
	// 	es_cookie::delete("referer_url");
	if(!es_cookie::get("referer_url"))
	{
		if(!preg_match("/".urlencode(SITE_DOMAIN.APP_ROOT)."/",urlencode($_SERVER["HTTP_REFERER"])))
		{
			$ref_url = $_SERVER["HTTP_REFERER"];
			if(substr($ref_url, 0,7)=="http://"||substr($ref_url, 0,8)=="https://")
			{
				preg_match("/http[s]*:\/\/[^\/]+/", $ref_url,$ref_url);
				$referer = $ref_url[0];
				if($referer)
					es_cookie::set("referer_url",$referer);
			}
		}
	}
	else
	{
		$referer = es_cookie::get("referer_url");
	}
	$referer = strim($referer);

	es_cookie::delete("is_pc");
}
/**
 * 初始化页面信息，如会员登录状态的显示输出
 */
function init_app_page()
{
	global $is_app;
	if($GLOBALS['user_info'])
	{
		$GLOBALS['tmpl']->assign("is_login",1);
	}
	else
	{
		$GLOBALS['tmpl']->assign("is_login",0);
	}
	// MODULE_NAME;
	// ACTION_NAME;
	

	$user_id = intval($GLOBALS['user_info']['id']);
	$is_weixin = isWeixin();
	if ($is_weixin && $user_id) {
	    // 微信分享验证
	    require_once APP_ROOT_PATH."system/model/weixin_jssdk.php";
	    
	    $signPackage = getSignPackage();

	    $GLOBALS['tmpl']->assign("signPackage",$signPackage);
	    // print_r($signPackage);exit;
	    //分享url
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    // 不使用uri避免首页有的时候获取不到分享的url

	    unset($_GET['r']);
	    unset($_GET['_saas_params']);
	    unset($_GET['_saas_appid']);
	    $get = http_build_query($_GET);
	
	    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
	    $rd = rand(999, 10000);
	    $share_url = $url."?rand_num={$rd}".'&r='.base64_encode($GLOBALS['user_info']['id']).'&'.$get;
	    $share_img = format_image_path(app_conf("SHARE_ICON"));
	    $share_title = app_conf("SHARE_TITLE");
	    $share_content = app_conf("SHARE_CONTENT");
	    $GLOBALS['tmpl']->assign("share_img", $share_img);
	    $GLOBALS['tmpl']->assign("share_title", $share_title);
	    $GLOBALS['tmpl']->assign("share_content", $share_content);
	    $GLOBALS['tmpl']->assign("wx_share_url", $share_url);
	}
	
	user_center_back();
	if ($GLOBALS['account_info']){
	    $GLOBALS['tmpl']->assign("biz_is_login",1);
	}else{
	    $GLOBALS['tmpl']->assign("biz_is_login",0);
	}
	$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);
	
	$GLOBALS['tmpl']->assign("account_info",$GLOBALS['account_info']);
	
	if($GLOBALS['geo']['address'])
	$GLOBALS['tmpl']->assign("geo",$GLOBALS['geo']);

    $weixin_conf = load_auto_cache("weixin_conf");
    $GLOBALS['tmpl']->assign("weixin_conf", $weixin_conf);
    $tmpl_path = $GLOBALS['tmpl']->_var['TMPL'].'/style5.2/';
    $GLOBALS['tmpl']->assign("tmpl_path",$tmpl_path);
	$GLOBALS['tmpl']->assign("is_weixin",$GLOBALS['is_weixin']);
	$GLOBALS['tmpl']->assign("is_app",$is_app);
    $GLOBALS['tmpl']->assign("app_index",APP_INDEX);
    $GLOBALS['tmpl']->assign("page_finsh",PAGE_FINSH);
    $GLOBALS['tmpl']->assign("SCORE_RECHARGE_SWITCH", app_conf("SCORE_RECHARGE_SWITCH"));
	$GLOBALS['tmpl']->assign("sitename",SITE_DOMAIN.APP_ROOT);
	$GLOBALS['tmpl']->assign("pc_url",url("index","index",array("is_pc"=>1)));
}

//编译生成css文件
function parse_css($urls,$cssgroup=array() ,$include=array())
{
	$color_cfg = require_once APP_ROOT_PATH.FOLDER_NAME."/Tpl/".APP_TYPE."/".TMPL_NAME."/color_cfg.php";
	$showurl = $url = md5(implode(',',$urls).SITE_DOMAIN);
	$css_url = 'public/runtime/statics/'.CACHE_SUBDIR.'/'.$url.'.css';
	$pathwithoupublic = 'runtime/statics/'.CACHE_SUBDIR.'/';
	$url_path = APP_ROOT_PATH.$css_url;

	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'.CACHE_SUBDIR.'/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/'.CACHE_SUBDIR.'/',0777);
		$tmpl_path = $GLOBALS['tmpl']->_var['TMPL'];

		$css_content = '';
		foreach($urls as $url)
		{
			$css_content .= @file_get_contents($url);
		}
		
		$tmpl_root_path="Tpl/".APP_TYPE."/".TMPL_NAME."/";
		$include_content='';
		if($include){
    		foreach($include as $k=>$v){
    		    $include_file = $tmpl_root_path.$v['file'];
    		    $include_content.=@file_get_contents($include_file);
    		}
		}
		$include_css_arr=array();

		$include_content = str_replace("\$this->_var['TMPL_REAL']",$tmpl_path,$include_content);

		if($cssgroup){
		    foreach($cssgroup as $css)
		    {
	            preg_match_all("/".$css.".*?;/i",$include_content,$include_css_arr);   
		    }
		    $include_css_arr_new=array();
		    foreach($include_css_arr[0] as $k=>$include_css){
		        $match=array();
		        $include_css = str_replace('"',"",$include_css);
		        $include_css = str_replace('./',"/",$include_css);
		        preg_match("/=.*?;/",$include_css,$match);
		        $include_css = $match[0];
		        $include_css = str_replace(';',"",$include_css);
		        $include_css = str_replace('=',"",$include_css);
		        $include_css_arr_new[]=trim($include_css);
		    }  
		    $include_css_arr_new = array_unique($include_css_arr_new);
		    foreach($include_css_arr_new as $css){
		        $css_content .= @file_get_contents($css);
		    
		    }
		}

		
		$css_content = preg_replace("/[\r\n]/",'',$css_content);
		$css_content = str_replace("../images/",$tmpl_path."/images/",$css_content);
		$css_content = str_replace("@url",$tmpl_path."/style5.2/images",$css_content);
		if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
		    /*
			curl_download($GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/iconfont.css", APP_ROOT_PATH."public/iconfont/iconfont.css");
			curl_download($GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/iconfont.eot", APP_ROOT_PATH."public/iconfont/iconfont.eot");
			curl_download($GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/iconfont.svg", APP_ROOT_PATH."public/iconfont/iconfont.svg");
			curl_download($GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/iconfont.ttf", APP_ROOT_PATH."public/iconfont/iconfont.ttf");
			curl_download($GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/iconfont.woff", APP_ROOT_PATH."public/iconfont/iconfont.woff");
			*/
			//	$css_content = str_replace("./public/iconfont/",$GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN']."/public/iconfont/",$css_content);
		}
	
		$css_content = str_replace("./public/",SITE_DOMAIN.APP_ROOT."/public/",$css_content);
		$css_content = str_replace("@rand",time(),$css_content);
		foreach($color_cfg as $k=>$v)
		{
			$css_content = str_replace($k,$v,$css_content);
		}
		//		@file_put_contents($url_path, unicode_encode($css_content));
		@file_put_contents($url_path, $css_content);
		if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
			syn_to_remote_file_server($css_url);
			$GLOBALS['refresh_page'] = true;
		}
	}
	if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		$domain = $GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN'];
	}
	else
	{
		$domain = SITE_DOMAIN.APP_ROOT;
	}
	return $domain."/".$css_url."?v=".app_conf("DB_VERSION").".".app_conf("APP_SUB_VER");
}

/**
 *
 * @param $urls 载入的脚本
 * @param $encode_url 需加密的脚本
 */
function parse_script($urls,$encode_url=array())
{
	$showurl = $url = md5(implode(',',$urls));
	$js_url = 'public/runtime/statics/'.CACHE_SUBDIR.'/'.$url.'.js';
	$pathwithoupublic = 'runtime/statics/'.CACHE_SUBDIR.'/';
	$url_path = APP_ROOT_PATH.$js_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/',0777);
		
		if(!file_exists(APP_ROOT_PATH.'public/runtime/statics/'.CACHE_SUBDIR.'/'))
			mkdir(APP_ROOT_PATH.'public/runtime/statics/'.CACHE_SUBDIR.'/',0777);

		if(count($encode_url)>0)
		{
			require_once APP_ROOT_PATH."system/libs/javascriptpacker.php";
		}

		$js_content = '';
		foreach($urls as $url)
		{
			$append_content = @file_get_contents($url)."\r\n";
			if(in_array($url,$encode_url))
			{
				$packer = new JavaScriptPacker($append_content);
				$append_content = $packer->pack();
			}
			$js_content .= $append_content;
		}
		//		require_once APP_ROOT_PATH."system/libs/javascriptpacker.php";
		//	    $packer = new JavaScriptPacker($js_content);
		//		$js_content = $packer->pack();
		@file_put_contents($url_path,$js_content);
		if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
		{
			syn_to_remote_file_server($js_url);
			$GLOBALS['refresh_page'] = true;
		}
	}
	if($GLOBALS['distribution_cfg']['CSS_JS_OSS']&&$GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
	{
		$domain = $GLOBALS['distribution_cfg']['OSS_FILE_DOMAIN'];
	}
	else
	{
		$domain = SITE_DOMAIN.APP_ROOT;
	}

	return $domain."/".$js_url."?v=".app_conf("DB_VERSION").".".app_conf("APP_SUB_VER");
}



if(!function_exists('getWebAdsUrl')){
function getWebAdsUrl($data){
	//2:URL广告;9:团购列表;10:商品列表;11:活动列表;12:优惠列表;14:团购明细;15:商品明细;17:优惠明细;22:商家列表;23：商家明细; 24:门店自主下单

	if($data['ctl']=="url")
	{
		$url = $data['data']['url'];
		if(empty($url))
		{
			$url = "javascript:void(0);";
		}
		else
		{
			$url = "javascript:open_url('".$data['data']['url']."');";
		}
	}
	else
	$url = wap_url("index",$data['ctl'],$data['data']);

	return $url;

}
}



/**
 * 获取前次停留的页面地址
 * @return string url
 */
function get_gopreview()
{
	$gopreview = es_session::get("wap_gopreview");
	
	if($gopreview==get_current_url())
	{
		$gopreview = wap_url("index");
	}
	
	if(empty($gopreview))
		$gopreview = wap_url("index");
	return $gopreview;
}


/**
 * 获取当前的url地址，包含分页
 * @return string
 */
function get_current_url()
{
	$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
	$parse = parse_url($url);
	if(isset($parse['query'])) {
		parse_str($parse['query'],$params);
		$url   =  $parse['path'].'?'.http_build_query($params);
	}

	$url = preg_replace("/&code=[^&]*/i", "", $url);
	$url = preg_replace("/&state=[^&]*/i", "", $url);
	$url = preg_replace("/&appid=[^&]*/i", "", $url);
	return $url;
}

/**
 * 将当前页设为回跳的上一页地址
 */
function set_gopreview()
{
	$url =  get_current_url();
	es_session::set("wap_gopreview",$url);
}




/**
 * 微信登录(仅作用于云平台接入)
 * @param unknown_type $wx_info
 * @param unknown_type $type 0 wap端（公众号登录） 1 app登录
 */
function wx_info_login($wx_info,$type=0)
{
	if(!$wx_info['openid'])
	{
		return false;
	}
	//用户未登陆
	
	if($wx_info['unionid'])
	{
		if($type==0)
			$wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where wx_openid='".$wx_info['openid']."' or union_id = '".$wx_info['unionid']."' order by id desc limit 1");
		else
			$wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where m_openid='".$wx_info['openid']."' or union_id = '".$wx_info['unionid']."' order by id desc limit 1");
	}
	else
	{
		if($type==0)
			$wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where wx_openid='".$wx_info['openid']."' order by id desc limit 1");
		else
			$wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where m_openid='".$wx_info['openid']."' order by id desc limit 1");
	}

	if($wx_user_info){

		if($type==0)
			$GLOBALS['db']->query("update ".DB_PREFIX."user set wx_openid = '".$wx_info['openid']."',union_id='".$wx_info['unionid']."' where id = ".$wx_user_info['id']);
		else
			$GLOBALS['db']->query("update ".DB_PREFIX."user set m_openid = '".$wx_info['openid']."',union_id='".$wx_info['unionid']."' where id = ".$wx_user_info['id']);
		//如果会员存在，直接登录
		auto_do_login_user($wx_user_info['user_name'],$wx_user_info['user_pwd'],false);
	}else{
		//会员不存在进入自动创建流程
		$user_data = array();
		$user_data['user_name'] = $wx_info['nickname'];
		if($type==0)
			$user_data['wx_openid'] = $wx_info['openid'];
		else
			$user_data['m_openid'] = $wx_info['openid'];
		$user_data['union_id'] = $wx_info['unionid'];
        
		$rs = auto_create($user_data);
		$user_data = $rs['user_data'];
		save_url_avatar($wx_info['headimgurl'],$user_data['id']);
		auto_do_login_user($user_data['user_name'],$user_data['user_pwd'],false);
	}
	global $user_info;
	$user_info = es_session::get('user_info');
	return true;
}


function user_center_back(){
    
    if(
    MODULE_NAME=="uc_order"||
    MODULE_NAME=="uc_store_pay_order"||
    MODULE_NAME=="dc_dcorder"||
    MODULE_NAME=="dc_rsorder"||
    MODULE_NAME=="uc_coupon"||
    MODULE_NAME=="uc_youhui"||
    MODULE_NAME=="uc_event"||
    MODULE_NAME=="uc_lottery"||
    MODULE_NAME=="uc_review"||
    MODULE_NAME=="uc_ecv"&&ACTION_NAME=="index"||
    MODULE_NAME=="uc_collect"||
    MODULE_NAME=="uc_invite"||
    MODULE_NAME=="uc_fx"&&ACTION_NAME=="index"
    ){
         $GLOBALS['tmpl']->assign("back_user_url",wap_url("index","user_center"));
    }
     
}
/**
 * 微信绑定(仅作用于云平台接入，以及app接入)
 * @param unknown_type $wx_info
 * @param unknown_type $type 0 wap端（公众号登录） 1 app登录
 */
function wx_info_bind($wx_info,$type=0)
{
    if(!$wx_info['openid'])
    {
        return array("status"=>0,"info"=>"微信的唯一ID未传递");
    }
    //用户未登陆

    if($wx_info['unionid'])
    {
        if($type==0)
            $wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete=0 and  wx_openid='".$wx_info['openid']."' or union_id = '".$wx_info['unionid']."' order by id desc limit 1");
        else
            $wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete=0 and  m_openid='".$wx_info['openid']."' or union_id = '".$wx_info['unionid']."' order by id desc limit 1");
    }
    else
    {
        if($type==0)
            $wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete=0 and  wx_openid='".$wx_info['openid']."' order by id desc limit 1");
        else
            $wx_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete=0 and  m_openid='".$wx_info['openid']."' order by id desc limit 1");
    }

    if($wx_user_info){
		if($GLOBALS['user_info']['bind_count']>10){
			return array("status"=>0,"info"=>"合并微信会员次数到达上限");
		}
        if($wx_user_info['mobile']==''){
			$update = array();
			if($GLOBALS['user_info']['pid']==0&&$wx_user_info['pid']>0&&$wx_user_info['pid']!=$GLOBALS['user_info']['id']){
				//保留上级推荐人关系
				$update['pid']=$wx_user_info['pid'];
			}
			$update['bind_count']=$GLOBALS['user_info']['bind_count']+1;
			if($type==0){
				$update['wx_openid']=$wx_info['openid'];
			}else{
				$update['m_openid']=$wx_info['openid'];
			}
			$update['union_id']=$wx_info['unionid'];
			$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'id='.$GLOBALS['user_info']['id']);
			//保留下级推荐人
			$update = array();
			$update['pid']=$GLOBALS['user_info']['id'];
			$GLOBALS['db']->autoExecute(DB_PREFIX.'user', $update, 'UPDATE', 'pid='.$wx_user_info['id'].' and id!='.$GLOBALS['user_info']['id']);
			if($wx_user_info['money']>0||$wx_user_info['score']>0||$wx_user_info['point']>0){
				require_once(APP_ROOT_PATH."system/model/user.php");
				$data = array("money"=>$wx_user_info['money'],"score"=>$wx_user_info['score'],"point"=>$wx_user_info['point']);
				modify_account($data,$GLOBALS['user_info']['id'],"在".to_date(NOW_TIME)."合并微信账号".$wx_user_info['user_name']);
			}
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user where id = ".$wx_user_info['id']);

        }else{
			return array("status"=>0,"info"=>"微信会员已绑定过手机号");
		}
    }else{
		$GLOBALS['db']->query("update ".DB_PREFIX."user set  wx_openid = '".$wx_info['openid']."',union_id='".$wx_info['unionid']."' where id = ".$GLOBALS['user_info']['id']);
    }
    $user_data = load_user($GLOBALS['user_info']['id'],true);
    es_session::set("user_info",$user_data);
    global $user_info;
    $user_info = es_session::get('user_info');
    return array("status"=>1,"info"=>"绑定成功");
}

/**
 * 关于驿站部分
 */

function dist_global_run () {
    global $global_is_run;
    $global_is_run = true;
    if(app_conf("SHOP_OPEN")==0)  //网站关闭时跳转到站点关闭页
    {
        app_redirect(wap_url("index","close"));
    }
    if(!defined("PAGE_SIZE")){
        $page_size=app_conf("PAGE_SIZE");
        if($page_size){
            define("PAGE_SIZE",$page_size);
        }else{
            define("PAGE_SIZE",25);
        }

    };
    //处理城市
    global $city;
    require_once APP_ROOT_PATH."system/model/city.php";
    $city = City::locate_city(intval($_REQUEST['city_id']));

    //处理经纬度
    global $geo;
    $geo = City::locate_geo($_REQUEST['m_longitude'],$_REQUEST['m_latitude']);

    //社区驿站登录状态的初始化
    require_once APP_ROOT_PATH."system/model/dist_user.php";
    global $cookie_dist_uname;
    global $cookie_dist_upwd;
    global $dist_info;
    $dist_info = es_session::get('dist_info');
    //$dist_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."distribution where id=".$dist_info['id']);
    if(empty($dist_info))
    {
        $cookie_dist_uname = es_cookie::get("dist_uname")?es_cookie::get("dist_uname"):'';
        $cookie_dist_upwd = es_cookie::get("dist_upwd")?es_cookie::get("dist_upwd"):'';

        if($cookie_dist_uname!=''&&$cookie_dist_upwd!=''&&!es_session::get("dist_info"))
        {
            $cookie_dist_uname = strim($cookie_dist_uname);
            $cookie_dist_upwd = strim($cookie_dist_upwd);
            //do_login_dist($cookie_dist_uname, $cookie_dist_upwd);
            auto_do_login_dist($cookie_dist_uname, $cookie_dist_upwd, true);

            $dist_info = es_session::get('dist_info');
        }
    }

    global $dist_referer;
    //保存来路
    // 	es_cookie::delete("referer_url");
    if(!es_cookie::get("dist_referer_url"))
    {
        if(!preg_match("/".urlencode(SITE_DOMAIN.APP_ROOT)."/",urlencode($_SERVER["HTTP_REFERER"])))
        {
            $ref_url = $_SERVER["HTTP_REFERER"];
            if(substr($ref_url, 0,7)=="http://"||substr($ref_url, 0,8)=="https://")
            {
                preg_match("/http[s]*:\/\/[^\/]+/", $ref_url,$ref_url);
                $dist_referer = $ref_url[0];
                if($dist_referer)
                    es_cookie::set("dist_referer_url",$dist_referer);
            }
        }
    }
    else
    {
        $dist_referer = es_cookie::get("dist_referer_url");
    }
    $dist_referer = strim($dist_referer);

    if($dist_info)
    {
        //手机端菜单
        global $m_dist_nav_list;
        //$m_agent_nav_list = load_mobile_biz_nav();
        $m_dist_nav_list = require APP_ROOT_PATH."system/dist_cfg/".APP_TYPE."/m_distnav_cfg.php";
        foreach($m_dist_nav_list as $k=>$v)
        {
            $module_name = $k;
            foreach($v['node'] as $kk=>$vv)
            {
                $module_name = $vv['module'];
                $action_name = $vv['action'];
                $m_dist_nav_list[$k]['node'][$kk]['url'] = wap_url("dist",$module_name."#".$action_name);

            }
        }
    }
    es_cookie::delete("is_pc");
}

/**
 * 初始化页面信息，加载配送点信息
 */
function dist_init_app_page () {
    if ($GLOBALS['dist_info']){
        $GLOBALS['tmpl']->assign("dist_is_login",1);
    }else{
        $GLOBALS['tmpl']->assign("dist_is_login",0);
    }

    $GLOBALS['tmpl']->assign("m_dist_nav_list",$GLOBALS['m_dist_nav_list']);

    $GLOBALS['tmpl']->assign("is_app",isApp());//输出是否为APP

    $GLOBALS['tmpl']->assign("app_title",app_conf("SHOP_TITLE")."配送点中心");
}


?>