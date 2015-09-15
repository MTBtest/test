<?php

class ArticleAdvPositionController extends AdminBaseController {

    public function _initialize() {
        parent::_initialize();   
        $this->db= model('Adv_position');
    }


/**
 * 广告位列表
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
    public function lists(){
        $validform = TRUE;
        $list = $this->db->order('status Asc')->select();
        $this->list = $list;
        
        include $this->admin_tpl('article_adv_position_lists');

    }
/**
 *      添加修改页
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
    public function update(){
        $opt=I("opt");
        $id=I("id",0);
        if(IS_POST){
            self::save();
        }else{
            if(isset($opt) && $opt){
                //删除
                if($opt == 'del' && $id>7 ){
                    unset($where);
                    $where['id']=array('in',$id);
                    $this->db->where($where)->delete();
                    //同时删除广告
                    $adv_where['position_id']=array('in',$id);
                    M('adv')->where($adv_where)->delete();
                    showmessage('恭喜你，删除成功！',U('Article_adv_position/lists'),1); 
                    exit(); 
                }else{
                    showmessage('系统广告位,不允许删除!',U('Article_adv_position/lists'),0);
                }
                
            }else{
                showmessage('参数错误,请联系管理员!',U('Article_adv_position/lists'),0);
            }
        }
        
    }
 /**
 *      处理数据
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
    protected function save(){
        $id=I('id');
        //处理
        if (isset($id) && $id) {
            if ($this->db->create()) {
                $this->db->save();
                $nid=$id;
                showmessage("修改广告位成功", U('ArticleAdvPosition/lists'),1);
            } else {
                $this->error($this->db->getError());
            }
        } else {
            if ($this->db->create()) {
                $nid = $this->db->add();
                $this->redirect(U('ArticleAdv/update?opt=add&pid='.$nid.''));
                exit();
                //$this->success("添加广告位成功", );
            } else {
                showmessage($this->db->getError(),U('ArticleAdvPosition/lists'),0);
            }
        }
    }

    
}
