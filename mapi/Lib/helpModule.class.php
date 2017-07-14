<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class helpApiModule extends MainBaseApiModule
{
    public function index()
    {
    	$cateSql = 'SELECT * FROM '.DB_PREFIX.'help_cate WHERE is_effect=1 AND is_delete=0 ORDER BY sort';
    	$db_cateList = $GLOBALS['db']->getAll($cateSql);

    	$cateIds = array();
    	$cateList = array();
    	foreach ($db_cateList as $cate) {
    		$cateIds[] = $cate['id'];
    		$cateList[$cate['id']] = $cate;
    	}
    	$articleSql = 'SELECT * FROM '.DB_PREFIX.'help_article WHERE cate_id in ('.implode(',', $cateIds).') AND is_effect=1 AND is_delete=0 ORDER BY sort';
    	$articleList = $GLOBALS['db']->getAll($articleSql);
    	foreach ($articleList as $art) {
            $art['url'] = wap_url('index', 'help#detail', array('id' => $art['id']));
    		$cateList[$art['cate_id']]['list'][] = $art;
    	}
        foreach ($cateList as $key => $value) {
            if (empty($value['list'])) {
                unset($cateList[$key]);
            }
        }
    	$root['data'] = $cateList;
        $root['shop_tel'] = app_conf('SHOP_TEL');

        $settingid = '';
        if (APP_INDEX == 'app' && isOpenXN()) {
            $settingid = app_conf('XN_SETTING_ID');
        }
        $root['settingid'] = $settingid;

        $root['page_title'] = '客服中心';
    	return output($root);
    }

    public function load($value='')
    {
        # code...
    }

    public function do_search()
    {
    	do {
    		$root = array();
    		$status = 0;
   			$info = '';
    		$keyword = strim($GLOBALS['request']['keyword']);
	    	if (empty($keyword)) {
	    		$info = '关键字不能为空';
	    		break;
	    	}
	    	$articleSql = 'SELECT id, title FROM '.DB_PREFIX.'help_article WHERE title like "%'.$keyword.'%" AND is_effect=1 AND is_delete=0 ORDER BY sort';
	    	$articleList = $GLOBALS['db']->getAll($articleSql);
            if (empty($articleList)) {
                $info = '没有找到帮助文章';
                break;
            }
            foreach ($articleList as &$art) {
                $art['wap_url'] = wap_url('index', 'help#detail', array('id' => $art['id']));
            }
            unset($art);
	    	$root['list'] = $articleList;
	    	$status = 1;
    	} while (0);

    	return output($root, $status, $info);
    	
    }

    public function detail()
    {
    	do {
    		$root = array();
    		$status = 0;
   			$info = '';
    		$id = intval($GLOBALS['request']['id']);
	    	if (empty($id)) {
	    		$info = '参数错误';
	    		break;
	    	}
	    	$articleSql = 'SELECT * FROM '.DB_PREFIX.'help_article WHERE id='.$id.' AND is_effect=1 AND is_delete=0';
	    	$article = $GLOBALS['db']->getRow($articleSql);
	    	if (empty($article)) {
	    		$info = '文章不存在';
	    		break;
	    	}

            /*if (check_ipop_limit(get_client_ip(), 'article', 60, $article['id'])) {
                //每一分钟访问更新一次点击数
                $GLOBALS['db']->query("update ".DB_PREFIX."help_article set click_count = click_count + 1 where id =".$article['id']);
            }*/
            $root['page_title'] = $article['title'];
	    	$root['article'] = $article;
	    	$status = 1;
    	} while (0);

    	return output($root, $status, $info);
    }
}