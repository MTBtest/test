<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class Page {
    
    // 分页栏每页显示的页数
    public $rollPage = 2;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    public $totalPages  ;
    // 总行数
    public $totalRows  ;
    // 当前页数
    public $nowPage    ;
    // 分页显示定制
    // protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %prePage%  %linkPage%');
    protected $config = array('header'=>'条记录','prev'=>'','next'=>'','first'=>'第一页','last'=>'最后一页','theme'=>'%upPage% %linkPage% %downPage%');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'page' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->rollPages     =   ($this->rollPage * 2) + 1;

        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
		//统一处理后台样式
		if (getconfig('page_config')){
			$this->config = getconfig('page_config');
		}
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                if(empty($_GET)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $_GET;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
            $upPage     =   "<a class='uppage' href='".str_replace('__PAGE__',$upRow,$url)."'>"."</a>";
        }else{
            $upPage     =   "<span class='nouppage'></span>";
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<a class='downpage' href='".str_replace('__PAGE__',$downRow,$url)."'>"."</a>";
        }else{
            $downPage   =   "<span class='nodownpage'></span>";
        }
        // << < > >>
        // if($this->nowPage <= 1){
        //     $frist    =   '';
        // } else {
        //     $frist    =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
        // }

        // if($this->nowPage >= $this->totalPages) {
        //     $end = '';
        // } else {
        //     $end =   "<a href='".str_replace('__PAGE__', $this->totalPages, $url)."' >".$this->config['last']."</a>";
        // }
        $linkPage = "";
        /* 每页显示多少个页码 */      
        $StartPage = $this->nowPage - $this->rollPage;
        $EndPage = $this->nowPage + $this->rollPage;
        if($StartPage < 1) {
            $StartPage = 1;
        }
        if($EndPage < $this->rollPages) {
            $EndPage = $this->rollPages;
        }
        if($EndPage > $this->totalPages) {
            $EndPage = $this->totalPages;
        }
        for ($page = $StartPage; $page <= $EndPage; $page++) { 
            if($page==$this->nowPage) {
                $linkPage.= "<a href='javascript:;' class=\"current\">$page</a>\r\n";
            } else {
                $linkPage.= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>\r\n";
            }
        }
        if ($this->totalPages - $EndPage > 1) {
            $linkPage .= "<a href='javascript:;'>...</a>";
            $linkPage .= "<a href='".str_replace('__PAGE__', $this->totalPages,$url)."'>".$this->totalPages."</a>";
        }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%frist%', '%end%', '%totalRow%','%totalPage%','%upPage%','%downPage%','%prePage%','%linkPage%','%listRows%'),
            array($this->config['header'],$this->nowPage, $frist, $end, $this->totalRows,$this->totalPages,$upPage,$downPage,$prePage,$linkPage,$this->listRows),$this->config['theme']);
        return $pageStr;
    }

}