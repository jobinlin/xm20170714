<?php
/**
* 站内搜索
* @author hhcycj
*/
function set_count_str($search_type = ''){
    $search_type = $search_type ? $search_type : intval($_REQUEST['search_type']);
    switch ($search_type) {
        case 1:
            $count_str = 'count_tuan';
            break;
        case 2:
            $count_str = 'count_youhui';
            break;
        case 3:
            $count_str = 'count_event';
            break;
        case 4:
            $count_str = 'count_supplier_location';
            break;
        case 5:
            $count_str = 'count_shop';
            break;
        default:
            $count_str = '';
            break;
    }
    return $count_str;
}
/**
 * 根据类型对关键字表添加
 * @author hhcycj
 * @param int $type
 * @param int|array $id
 */
function insertKeyWordsApi($id, $search_type){
    $search_type = $search_type ? $search_type : intval($_REQUEST['search_type']);
    if (is_array($id)) {
        $id_str = join(",", $id);
        $where = " id in ({$id_str}) ";
    }else{
        $where = " id={$id} ";
    }
    
    switch ($search_type) {
        case 1: // 商品和团 
            $sql = "select id, is_shop, name_match_row, deal_cate_match_row,  shop_cate_match_row,  locate_match_row, tag_match_row ".
                   "from ".DB_PREFIX."deal where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            dealKewWordsInto($result);
            break;
        case 2: // 优惠
            $sql =  "select id, name_match_row,  deal_cate_match_row, locate_match_row ".
                    "from ".DB_PREFIX."youhui where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            youhuiKewWordsInto($result);
            break;
        case 3: // 活动
            $sql =  "select id, name_match_row, cate_match_row, locate_match_row ".
                    "from ".DB_PREFIX."event where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            eventKewWordsInto($result);
            break;
        case 4: // 门店（商家）
            $sql =  "select id, name_match_row, deal_cate_match_row, locate_match_row, tags_match_row ".
                "from ".DB_PREFIX."supplier_location where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            supplierLocationKewWordsInto($result);
            break;
        default:
            showErr("没有选择需要操作的分类",0);
            break;
    }
}

/**
 * 商品编辑时更新关键字表
 * @author hhcycj
 * @param array $old_data 更新前的数据
 * @param int $search_type
 */
function updateKeyWordsApi($old_data, $search_type){
    // 先删除之前的关键字的数量，再添加现在的关键字进去
    deleteKeyWords($old_data, $search_type, 0);
    insertKeyWordsApi($old_data[0]['id'], $search_type);
}

 

/**
 * 根据类型对关键字表删除
 * @author hhcycj
 * @param array|int $id 删除的id
 * @param int $search_type
 */
function deleteKeyWordsApi($id, $search_type=''){
    $search_type = $search_type ? $search_type : intval($_REQUEST['search_type']);
    if (is_array($id)) {
        $id_str = join(",", $id);
        $where = " id in ({$id_str}) ";
    }else{
        $where = " id={$id} ";
    }
    
    switch ($search_type) {
        case 1: // 商品和团购
            $sql = "select id, is_shop, name_match_row, deal_cate_match_row,  shop_cate_match_row,  locate_match_row, tag_match_row ".
                   "from ".DB_PREFIX."deal where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            deleteKeyWords($result, $search_type);
            break;
        case 2: // 优惠
            $sql =  "select id, name_match_row,  deal_cate_match_row, locate_match_row ".
                    "from ".DB_PREFIX."youhui where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            deleteKeyWords($result, $search_type);
            break;
        case 3: // 活动
            $sql =  "select id, name_match_row, cate_match_row, locate_match_row ".
                    "from ".DB_PREFIX."event where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            deleteKeyWords($result, $search_type);
            break;
        case 4: // 门店（商家）
            $sql =  "select id, name_match_row, deal_cate_match_row, locate_match_row, tags_match_row ".
                    "from ".DB_PREFIX."supplier_location where {$where}";
            $result = $GLOBALS['db']->getAll($sql);
            deleteKeyWords($result, $search_type);
            break;
        default:
            showErr("没有选择需要操作的分类",0);
            break;
    }
}

/**
 * 删除关键字
 * @author hhcycj
 * @param array $result
 * @param int $type
 */
function deleteKeyWords($result, $search_type, $is_delete_all=1){
    if ($search_type == 1) {
        if ($result[0]['is_shop'] == 1) {
            $search_type = 5;
        }else{
            $search_type = 1;
        }
    }
    $count_str = set_count_str($search_type);
    // 需要删除的所有字段
    $fields = array('name_match_row', 'deal_cate_match_row', 'shop_cate_match_row', 'locate_match_row', 'tag_match_row', 'cate_match_row');
    // 合并后最后的关键字
    $array_merge = array();
    
    // 循环组合所有关键字
    foreach ($result as $key=>$value){
       $keys = array_keys($value);
       $array_row = array();
       foreach ($keys as $k=>$v){
           if (in_array($v, $fields)) {
               $array_row =  array_merge($array_row, explode(',', $value[$v]));
           }
       }
       // 去除重复的关键字, 删除空，重新索引
       $array_row = array_values(array_filter(array_unique($array_row)));
       $array_merge = array_merge($array_merge, $array_row);
    }
    $array_merge = array_count_values($array_merge); 
    foreach ($array_merge as $mk=>$mv){
        // 更新关键词数量
        $sql =  "UPDATE `".DB_PREFIX."search_key_words` SET `{$count_str}`= ".
                "if(`{$count_str}` < {$mv}, 0, `{$count_str}`-{$mv}) ".
                "WHERE key_words='{$mk}'";
        $GLOBALS['db']->query($sql);
    }
    
    if($is_delete_all == 1){
        // 删除所有没用的关键字
        $GLOBALS['db']->query("DELETE FROM `".DB_PREFIX."search_key_words` WHERE `count_shop`=0 and `count_tuan`=0 and `count_youhui`=0 and `count_supplier_location`=0 and `count_event`=0");
        
    }
}

/**
 * 关键字入库
 * @author hhcycj
 */
function insertKeyWords(){
    // 先清空所有表数据
    $GLOBALS['db']->query('truncate table '.DB_PREFIX.'search_key_words');
    //商品和团购入库部分
    $all_deal = $GLOBALS['db']->getAll( "select id, name, is_shop, origin_price, brief, img, name_match, name_match_row, deal_cate_match, deal_cate_match_row, shop_cate_match,
                                    shop_cate_match_row, locate_match, locate_match_row, tag_match, tag_match_row, is_hot, is_recommend, sort, dp_count
                                    from ".DB_PREFIX."deal
                                    where is_effect=1 and is_delete = 0  and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) ) ");
    dealKewWordsInto($all_deal);
    
    //门店入库部分
    $all_supplier_location = $GLOBALS['db']->getAll( "select id, name, name_match, name_match_row, deal_cate_match, deal_cate_match_row, locate_match, locate_match_row, tags_match, tags_match_row
                                                 from ".DB_PREFIX."supplier_location where is_effect=1");
    supplierLocationKewWordsInto($all_supplier_location);
    
    
    //优惠入库部分
    $all_youhui = $GLOBALS['db']->getAll( "select id, name, name_match, name_match_row, deal_cate_match, deal_cate_match_row, locate_match_row, locate_match
                                      from ".DB_PREFIX."youhui where is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) )");
    youhuiKewWordsInto($all_youhui);
    
     
    //活动入库部分
    $all_event = $GLOBALS['db']->getAll( "select id, name, name_match, name_match_row, cate_match, cate_match_row, locate_match_row, locate_match
                                     from ".DB_PREFIX."event
                                     where is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= submit_begin_time or submit_begin_time = 0) and (".NOW_TIME."< submit_end_time or submit_end_time = 0) ) )");
    eventKewWordsInto($all_event);
}

 


/**
 * 获取商品搜索的结果
 * @author hhcycj
 * @param array $match  
 * @param unknown $kws_div  分词后的数组
 * @param int $is_shop 是否是商品，或者团购
 */
function getDealSearchResult($match, $kws_div, $is_shop){
    //分页
    require_once(APP_ROOT_PATH."app/Lib/page.php");
    $page_size = 10;
    $page = intval($_REQUEST['p']);
    if($page==0) $page = 1;
    $limit = (($page-1)*$page_size).",".$page_size;
    
    $sql = "SELECT id,name,brief, img, icon, create_time, dp_count, avg_point, MATCH (name_match, deal_cate_match, locate_match, tag_match, shop_cate_match) AGAINST ('".$match."' IN BOOLEAN MODE) as m ".
            "FROM ".DB_PREFIX."deal WHERE MATCH (name_match, deal_cate_match, locate_match, tag_match, shop_cate_match) AGAINST ('".$match."' IN BOOLEAN MODE) ".
            "and is_shop={$is_shop} and is_effect=1 and is_delete = 0 and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) )".
            " order by m desc, is_hot desc, is_best desc, is_new desc limit ".$limit;
    $sql_count = "select count(*) from ".DB_PREFIX."deal WHERE MATCH (name_match, deal_cate_match, locate_match, tag_match, shop_cate_match) AGAINST ('".$match."' IN BOOLEAN MODE) ".
                "and is_shop={$is_shop} and is_effect=1 and is_delete = 0 and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) )";
    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key=>$value){
        // 把出现的字设为红色
        $value['brief'] = strip_tags($value['brief']);
        foreach($kws_div as $k=>$v){
                $v = str_replace(' ', '', $v);
                $value['name'] = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['name']));
                $value['brief'] = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['brief']));
        }
        
        $result[$key]['name'] = $value['name'];
        $result[$key]['brief'] = $value['brief'];
        
        $result[$key]['avg_point'] = round($value['avg_point'], 2);
        $result[$key]['img'] = $result[$key]['img'] ? $result[$key]['img'] : $result[$key]['icon'];
        $result[$key]['url'] = SITE_DOMAIN.url("index","deal#".$value['id']);
        $result[$key]['create_time'] = date('Y-m-d', $value['create_time']);
        $result[$key]['comment_count'] = $value['dp_count'];
        
    }
    
    
    $count = $GLOBALS['db']->getOne($sql_count);
    $page = new Page($count, $page_size);   //初始化分页对象
    $p  =  $page->show();
    
    $GLOBALS['tmpl']->assign('pages',$p);
    $GLOBALS['tmpl']->assign("count",$count);
    $GLOBALS['tmpl']->assign('list',$result);
}

/**
 * 获取优惠的结果
 * @author hhcycj
 * @param array $match
 * @param array $kws_div
 */
function getYouhuiSearchResult($match, $kws_div){
    //分页
    require_once(APP_ROOT_PATH."app/Lib/page.php");
    $page_size = 10;
    $page = intval($_REQUEST['p']);
    if($page==0) $page = 1;
    $limit = (($page-1)*$page_size).",".$page_size;
    
    $sql = "SELECT id,name, brief, image, dp_count,create_time, MATCH (deal_cate_match, locate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE) as m ".
            "FROM ".DB_PREFIX."youhui WHERE MATCH (deal_cate_match, locate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE) ".
            " and is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) )".
            " order by sort desc, is_recommend desc limit ".$limit;
     
    $sql_count = "select count(*) from ".DB_PREFIX."youhui WHERE MATCH (deal_cate_match, locate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE) and is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= begin_time or begin_time = 0) and (".NOW_TIME."< end_time or end_time = 0) ) )";
    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key=>$value){
        // 把出现的字设会红色
        $value['brief'] = strip_tags($value['brief']);
        foreach($kws_div as $k=>$v){
            $v = str_replace(' ', '', $v);
            $value['name']  = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['name']));
            $value['brief'] = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['brief']));
            
        }
        
        $result[$key]['name'] = $value['name'];
        $result[$key]['brief'] = $value['brief'];
        $result[$key]['url'] = SITE_DOMAIN.url("index","youhui#".$value['id']);
        $result[$key]['img'] = $value['image'];
        $result[$key]['create_time'] = date('Y-m-d', $value['create_time']);
        $result[$key]['comment_count'] = $value['dp_count'];
    }
   
    $count = $GLOBALS['db']->getOne($sql_count);
    $page = new Page($count, $page_size);   //初始化分页对象
    $p  =  $page->show();
    
    $GLOBALS['tmpl']->assign('pages',$p);
    $GLOBALS['tmpl']->assign("count",$count);
    $GLOBALS['tmpl']->assign('list',$result);
}


/**
 * 获取活动的结果
 * @author hhcycj
 * @param array $match
 * @param array $kws_div
 */
function getEventSearchResult($match, $kws_div){
    //分页
    require_once(APP_ROOT_PATH."app/Lib/page.php");
    $page_size = 10;
    $page = intval($_REQUEST['p']);
    if($page==0) $page = 1;
    $limit = (($page-1)*$page_size).",".$page_size;

    $sql = "SELECT id,name,brief, icon, dp_count, event_begin_time, event_end_time, submit_begin_time, submit_end_time,  MATCH (locate_match, cate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE) as m ".
            " FROM ".DB_PREFIX."event WHERE MATCH (locate_match, cate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE) ".
            " and is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= submit_begin_time or submit_begin_time = 0) and (".NOW_TIME."< submit_end_time or submit_end_time = 0) ) ) order by m desc, sort desc, is_recommend desc, click_count desc limit ".$limit;
    $sql_count = "select count(*) from ".DB_PREFIX."event WHERE MATCH (locate_match, cate_match, name_match) AGAINST ('".$match."' IN BOOLEAN MODE)   and is_effect=1 and ( 1<>1 or ( (".NOW_TIME.">= submit_begin_time or submit_begin_time = 0) and (".NOW_TIME."< submit_end_time or submit_end_time = 0) ) )";
    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key=>$value){
        // 把出现的字设会红色
        $value['brief'] = strip_tags($value['brief']);
        foreach($kws_div as $k=>$v){
            $v = str_replace(' ', '', $v);
            $value['name']  = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['name']));
            $value['brief'] = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['brief']));
        }

        $result[$key]['name'] = $value['name'];
        $result[$key]['brief'] = $value['brief'];
        $result[$key]['url'] = SITE_DOMAIN.url("index","event#".$value['id']);
        $result[$key]['img'] = $value['icon'];
        $result[$key]['event_begin_time'] = date('Y-m-d', $value['event_begin_time']);
        $result[$key]['event_end_time'] = date('Y-m-d', $value['event_end_time']);
        $result[$key]['submit_begin_time'] = date('Y-m-d', $value['submit_begin_time']);
        $result[$key]['submit_end_time'] = date('Y-m-d', $value['submit_end_time']);
    }

     
    $count = $GLOBALS['db']->getOne($sql_count);
    $page = new Page($count, $page_size);   //初始化分页对象
    $p  =  $page->show();

    $GLOBALS['tmpl']->assign('pages',$p);
    $GLOBALS['tmpl']->assign("count",$count);
    $GLOBALS['tmpl']->assign('list',$result);
}

/**
 * 获取商家（门店）的结果
 * @author hhcycj
 * @param array $match
 * @param array $kws_div
 */
function getSupplierLocationSearchResult($match, $kws_div){
    //分页
    require_once(APP_ROOT_PATH."app/Lib/page.php");
    $page_size = 10;
    $page = intval($_REQUEST['p']);
    if($page==0) $page = 1;
    $limit = (($page-1)*$page_size).",".$page_size;

    $sql = "SELECT id, name,  tel, contact, open_time, good_rate, location_qq, preview, brief, dp_count, address, MATCH (deal_cate_match, locate_match, name_match, tags_match) AGAINST ('".$match."' IN BOOLEAN MODE) as m ".
           " FROM ".DB_PREFIX."supplier_location WHERE MATCH (deal_cate_match, locate_match, name_match, tags_match) AGAINST ('".$match."' IN BOOLEAN MODE) ".
           " and is_effect=1 order by m desc, sort desc, good_rate desc, is_recommend desc limit ".$limit;
    $sql_count = "select count(*) from ".DB_PREFIX."supplier_location WHERE MATCH (deal_cate_match, locate_match, name_match, tags_match) AGAINST ('".$match."' IN BOOLEAN MODE)  and is_effect=1";
    $result = $GLOBALS['db']->getAll($sql);
    foreach ($result as $key=>$value){
        // 把出现的字设会红色
        $value['brief'] = strip_tags($value['brief']);
        foreach($kws_div as $k=>$v){
            $v = str_replace(' ', '', $v);
            $value['name']  = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['name']));
            $value['brief'] = str_replace($v, '<em>'.$v.'</em>', str_replace(' ', '', $value['brief']));
        }

        $result[$key]['name'] = $value['name'];
        $result[$key]['brief'] = $value['brief'];
        $result[$key]['good_rate'] = ($value['good_rate']*100).'%';
        
        $result[$key]['url'] = SITE_DOMAIN.url("index","store#".$value['id']);
        $result[$key]['img'] = $value['preview'];
    }


    $count = $GLOBALS['db']->getOne($sql_count);
    $page = new Page($count, $page_size);   //初始化分页对象
    $p  =  $page->show();

    $GLOBALS['tmpl']->assign('pages',$p);
    $GLOBALS['tmpl']->assign("count",$count);
    $GLOBALS['tmpl']->assign('list',$result);
}
 
 


/**
 * 商品关键词入库
 * @author hhcycj
 */
function dealKewWordsInto($result){
    foreach ($result as $key=>$value){
        // 先把根据逗号，把字符串分割成数组
        $name_match_row         = $value['name_match_row']      ? explode(',', $value['name_match_row']) : array();
        $deal_cate_match_row    = $value['deal_cate_match_row'] ? explode(',', $value['deal_cate_match_row']) : array();
        $shop_cate_match_row    = $value['shop_cate_match_row'] ? explode(',', $value['shop_cate_match_row']) : array();
        $locate_match_row       = $value['locate_match_row']    ? explode(',', $value['locate_match_row']) : array();
        $tag_match_row          = $value['tag_match_row']       ? explode(',', $value['tag_match_row']) : array();
        // 合并每一个商品所有需要入库的关键字
        $merge_array = array_merge($name_match_row, $deal_cate_match_row, $shop_cate_match_row, $locate_match_row, $tag_match_row);
        // 去除重复的关键字
        $merge_array = array_unique($merge_array);
        // 把合并且去重后的关键字 分割
        $merge_array = $merge_array ? ch_substr($merge_array) : '';
        // 连接sql，并执行
        if ($value['is_shop'] == 1) {
            setKeyWords($merge_array, 'count_shop');
        }else{
            setKeyWords($merge_array, 'count_tuan');
        }
        
    }

    
}

/**
 * 商户关键词入库
 * @author hhcycj
 */
function supplierLocationKewWordsInto($result){
    foreach ($result as $key=>$value){
        // 先把根据逗号，把字符串分割成数组
        $name_match_row         = $value['name_match_row']      ? explode(',', $value['name_match_row']) : array();
        $deal_cate_match_row    = $value['deal_cate_match_row'] ? explode(',', $value['deal_cate_match_row']) : array();
        $locate_match_row       = $value['locate_match_row']    ? explode(',', $value['locate_match_row']) : array();
        $tag_match_row          = $value['tag_match_row']       ? explode(',', $value['tag_match_row']) : array();
        // 合并每一个优惠所有需要入库的关键字
        $merge_array = array_merge($name_match_row, $deal_cate_match_row, $locate_match_row, $tag_match_row);
        // 去除重复的关键字
        $merge_array = array_unique($merge_array);
        // 把合并且去重后的关键字 分割
        $merge_array = $merge_array ? ch_substr($merge_array) : '';
        // 连接sql，并执行
        setKeyWords($merge_array, 'count_supplier_location');

    }
}


/**
 * 优惠关键词入库
 * @author hhcycj
 */
function youhuiKewWordsInto($result){
    foreach ($result as $key=>$value){
        // 先把根据逗号，把字符串分割成数组
        $name_match_row         = $value['name_match_row']      ? explode(',', $value['name_match_row']) : array();
        $deal_cate_match_row    = $value['deal_cate_match_row'] ? explode(',', $value['deal_cate_match_row']) : array();
        $locate_match_row       = $value['locate_match_row']    ? explode(',', $value['locate_match_row']) : array();
        
        // 合并每一个优惠所有需要入库的关键字
        $merge_array = array_merge($name_match_row, $deal_cate_match_row, $locate_match_row);
        // 去除重复的关键字
        $merge_array = array_unique($merge_array);
        // 把合并且去重后的关键字 分割
        $merge_array = $merge_array ? ch_substr($merge_array) : '';
        // 连接sql，并执行
        setKeyWords($merge_array, 'count_youhui');
    }


}

/**
 * 活动关键词入库
 * @author hhcycj
 */
function eventKewWordsInto($result){
    foreach ($result as $key=>$value){
        // 先把根据逗号，把字符串分割成数组
        $name_match_row         = $value['name_match_row']      ? explode(',', $value['name_match_row']) : array();
        $cate_match_row         = $value['cate_match_row'] ? explode(',', $value['cate_match_row']) : array();
        $locate_match_row       = $value['locate_match_row']    ? explode(',', $value['locate_match_row']) : array();
        // 合并每一个优惠所有需要入库的关键字
        $merge_array = array_merge($name_match_row, $cate_match_row, $locate_match_row);
        // 去除重复的关键字
        $merge_array = array_unique($merge_array);
        // 把合并且去重后的关键字 分割
        $merge_array = $merge_array ? ch_substr($merge_array) : '';
        // 连接sql，并执行
        setKeyWords($merge_array, 'count_event');
    }


}
 
/**
 * 关键字入库
 * 根据分割的字符串，连接sql语句,并执行sql
 * @author hhcycj
 * @param array $match_str_array
 */
function setKeyWords($match_array, $count_str){
    if (!$match_array) {
        return false;
    }
    
    foreach ($match_array as $key=>$value){
        $key_words = join('', $value);
        $key_words_match = '';
        foreach ($value as $k=>$v){
            $key_words_match[$k] = str_to_unicode_string(strtolower($v));
        }
        $key_words_match = join(',', $key_words_match);
        $value_sql[] = "('{$key_words}', '{$key_words_match}', 1)";
    }
    $value_sql = join(',', $value_sql);
    // 插入的时候判断，key_words是否重复, 如果重复则在相应的$count_str+1
    $sql = 'INSERT INTO `'.DB_PREFIX.'search_key_words`(`key_words`, `key_words_match`, `'.$count_str.'`) VALUES '.$value_sql.' ON DUPLICATE KEY UPDATE `'.$count_str.'`= `'.$count_str.'`+1';
    $GLOBALS['db']->query($sql);
     
}


/**
 * 字符串分割
 * @author hhcycj
 * @param array $key_words_array
 * @param string $charset
 */
function ch_substr($key_words_array, $charset='utf-8'){
    $array = '';  // 分割成数组后的关键字，方便组合入库到关键字表
    foreach ($key_words_array as $key=>$value){
        
        // 关键字太长的，都删除，不用入库
        if(mb_strlen($value) > 8){
            continue;
        }
        
        $str = $value;
        // 去除所有 空白符
        $str =  preg_replace_callback('/([\s]+)/',
            function ($str) {
                return '';
            },
            $str);
        $strlen=mb_strlen($str);
        // 开始分割字符串
        while($strlen){
            $array[$key][]  = mb_substr($str, 0, 1, $charset);
            $str            = mb_substr($str, 1, $strlen, $charset);
            $strlen         = mb_strlen($str);
        }
         
    }
    return $array;
}




$startTime = '';
function set_start_time(){
    global $startTime;
    $startTime = explode(' ',microtime());
}

function set_stop_time(){
    global $startTime;
    $stopTime = explode(' ',microtime());
    $timeSpent = round($stopTime[0]+$stopTime[1]-($startTime[0]+$startTime[1]), 4);
    $GLOBALS['tmpl']->assign('time_spent', $timeSpent);
}

 


 