<?php
/**
* 站内搜索
* @author hhcycj
*/

class searchkeywordsModule extends MainBaseModule{
    private $count_str;
    private $search_type;
    
    /**
     * @author hhcycj
     * @param string $search_type
     * 1:团购   2：优惠  3：活动  4：门店  5：商品
     */
    public function __construct($search_type=''){
        require_once(APP_ROOT_PATH."system/model/search_key_words.php");
        parent::__construct();
        $this->search_type = $search_type ? $search_type : intval($_REQUEST['search_type']);
        switch ($this->search_type) {
            case 1:
                $this->count_str = 'count_tuan';
                break;
            case 2:
                $this->count_str = 'count_youhui';
                break;
            case 3:
                $this->count_str = 'count_event';
                break;
            case 4:
                $this->count_str = 'count_supplier_location';
                break;
            case 5:
                $this->count_str = 'count_shop';
                break;
            default:
                $this->count_str = '';
                break;
        }
    }
    
    /**
     * 获取所有相应分类搜索结果
     * @author hhcycj
     */
    public function index(){
        global_run();
        init_app_page();
        set_start_time();
        $keyWords = strim($_REQUEST['search_keyword']);
        // 根据分词系统分词
        $kws_div = array_unique(div_str($keyWords));
     
        // 查找数据库关键字
        $keyWords_array[] = $keyWords;
        $keyWords_array = ch_substr($keyWords_array);
        foreach ($keyWords_array[0] as $key=>$value){
            $keyWordsMatch[] = '+'.str_to_unicode_string(strtolower($value));
        }
        $keyWordsMatch = join(' ', $keyWordsMatch);
        $key_words_sql = "SELECT key_words FROM ".DB_PREFIX."search_key_words WHERE MATCH (key_words_match) AGAINST ('{$keyWordsMatch}' IN BOOLEAN MODE) and `{$this->count_str}` >= 1 limit 10";
        $key_result = $GLOBALS['db']->getAll($key_words_sql);
        // 必须为数组类型，否则合并会出错
        $search_kws_div = array();
        foreach ($key_result as $key=>$value){
            $search_kws_div[] = $value['key_words'];
        }
       
        // 把分词的和数据库查找到的合并为最后需要查询的关键字
        $kws_div = array_change_key_case($kws_div, CASE_LOWER);
        $all_kws_div = array_merge($kws_div, $search_kws_div);
        $all_kws_div = array_unique($all_kws_div);
        
        
        foreach ($all_kws_div as $key=>$value){
            // 给关键词排序，方便关键字替换为红色
            $sort_array[] = strlen($value);
            // 设置搜索的match
            $match[] = str_to_unicode_string($value);
        }
         
        
        
        // 排序的关键字
        array_multisort($sort_array, SORT_DESC, SORT_NUMERIC, $all_kws_div);
       
        $match = join(' ', $match);
        switch ($this->search_type) {
            case 1: // 搜索团购
                getDealSearchResult($match, $all_kws_div, 0);
                break;
            case 2: // 搜索优惠
                getYouhuiSearchResult($match, $all_kws_div);
                break;
            case 3: // 搜活动
                getEventSearchResult($match, $all_kws_div);
                break;
            case 4: // 搜门店（商家）
                getSupplierLocationSearchResult($match, $all_kws_div);
                break;
            case 5: // 搜索商品或团购is_shop=1 是商品，0是团购
                getDealSearchResult($match, $all_kws_div, 1);
                break;
            default:
                getDealSearchResult($match, $all_kws_div, 0);
                break;
        }
        $GLOBALS['tmpl']->assign('search_type', $this->search_type);
        $GLOBALS['tmpl']->assign('keyWords', $_REQUEST['search_keyword']);
        set_stop_time();
        $GLOBALS['tmpl']->display("search_index.html");
    }
    
    /**
     * ajax 请求数据
     * 根据输入的关键字获取数据库已经存在的关键字结果
     * @author hhcycj
     */
    public function keyWordsSearchResult(){
        $keyWords[] = strtolower(strim($_REQUEST['keyWords']));
        $keyWords = ch_substr($keyWords);
    
        foreach ($keyWords[0] as $key=>$value){
            $keyWordsMatch[] = '+'.str_to_unicode_string($value);
        }
    
        $keyWordsMatch = join(' ', $keyWordsMatch);
         
        $sql = "SELECT * FROM ".DB_PREFIX."search_key_words WHERE MATCH (key_words_match) AGAINST ('".$keyWordsMatch."' IN BOOLEAN MODE) and `{$this->count_str}` >= 1 order by `{$this->count_str}` desc limit 10";
         
        $keyWordsResult = $GLOBALS['db']->getAll($sql);
        
        // 组合显示的li
        $li_str = '';
        $data   = '';
        if($keyWordsResult){
            foreach ($keyWordsResult as $key=>$value){
                if (mb_strlen($value['key_words']) > 22) {
                    $value['key_words_display'] = mb_substr($value['key_words'], 0, 22, 'UTF-8').'...';
                }
                $value['key_words_display'] = $value['key_words_display'] ? $value['key_words_display']  : $value['key_words'];
                $li_str .= "<li key_words='{$value['key_words']}'><a href='#'>{$value['key_words_display']}<span></span></a></li>";
            }
            $data['status'] = 1;
            $data['info']   = $li_str;
        }else{
            $data['status'] = 0;
            $data['info']   = '获取信息为空';
        }
        ajax_return($data);
    }
}