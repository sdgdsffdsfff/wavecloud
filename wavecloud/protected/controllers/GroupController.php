<?php
/**
 * 用户组控制层
 */
class GroupController extends CController
{
    public $defaultAction = 'Index';
    public $request = null;  
    public $layout;
    public $username;
    public $userid;
    public $group;

    /**
     * 初始化
     *
     */
    public function init()
    {  
        parent::init();
        
        $this->request = Yii::app()->request;  
        if(Yii::app()->user->getState('userid')) {
            $this->userid = Yii::app()->user->getState('userid');
            $this->username = Yii::app()->user->getState('username');
            $this->group = Yii::app()->user->getState('group');
        }else{
            $this->redirect(Yii::app()->homeUrl);
        }
    }

    /**
     * 用户组首页
     */
    public function actionIndex()
    {
        if ($this->group === '1') {

            $this->layout='inner';
            $this->render('index', array('group'=>$this->group));
        }
    }

    /**
     * 用户组列表
     */
    public function actionList()
    {
        if ($this->group === '1') {
            $Common = new Common();
            $start = 0;
            $limit = 10;
            $data = $_GET;
            if (isset( $data['iDisplayStart']) && $data['iDisplayLength'] != '-1' )
            {
                $start = (int)$data['iDisplayStart'];
                $limit = (int)$data['iDisplayLength'];
            }
            $arr = $Common->getDataList('group', '*', $start, $limit);
            foreach ($arr as $key => $value) {
                $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['id'].'">';
                $arr[$key]['operat'] = '<button onclick="editFunc(\''.Yii::app()->homeUrl.'/group/edit/id/'.$value['id'].'\')" class="btn btn-info">
                                        <i class="icon-edit icon-white"></i> 编辑 </button> ';
            }
            $count = $Common->getCount('group');
            $output = array(
                "sEcho" => $data['sEcho'],
                "iTotalRecords" => $count,
                "iTotalDisplayRecords" => $count,
                "aaData" => array()
            );
            $output['aaData'] = $arr;
            echo json_encode($output);
            unset($arr,$output);
        }
    }

    /**
     * 编辑
     */
    public function actionEdit($id)
    {
        if ($this->group === '1') {
            $id = (int)$id;
            $Common = new Common();
            $garr = $Common->getOneData('group', '*', 'id', $id);

            $this->layout='index';
            $this->render('edit', array('garr'=>$garr));
        }
    }

    /**
     * 添加、编辑
     */
    public function actionModify()
    {
        if ($this->group === '1') {
            $Common = new Common();
            $User = new User();
            $id = (int)$this->request->getParam('groupid');
            $data = $Common->getFilter($_POST);
            unset($data['groupid']);
            $return_json = array();
            if (empty($data['group_name'])) 
                $Common->operationOutput(false, '请输入用户组名！');

            if(empty($id)){
                $count = $Common->getFieldCount('group', 'group_name', $data['group_name']);
                if ($count >= 1)
                    $Common->operationOutput(false, '用户组名已存在！');
                else{
                    $data['create_date'] = date('Y-m-d H:i:s');
                    $result = $Common->getInsert('group', $data);
                    $Common->operationOutput(true, '添加用户组成功！');
                }
            }else{
                $row = $Common->getOneData('group', 'group_name', 'id', $id);
                if($row['group_name'] !== $data['group_name']){
                    $count = $Common->getFieldCount('group', 'group_name', $data['group_name']);
                    if ($count >= 1) 
                        $Common->operationOutput(false, '用户组名已存在！');
                }
                $result = $Common->getUpdate('group', $data, 'id', $id);
                $Common->operationOutput(true, '修改用户组成功！');
            }
        }
    }

    /**
     * 删除
     */
    public function actionDelete()
    {
        $json = array();
        if ($this->group === '1') {
            $Common = new Common();
            $ids = $this->request->getParam('ids');
            $idarr = explode(',', $ids);
            foreach ($idarr as $key => $id) {
                if($id != 1){
                    if(!empty($id))
                        $res = $Common->getDelete('group', 'id', $id);
                }
            }
            $Common->operationOutput(true, '删除成功！');

        }else{
            $Common->operationOutput(false, '对不起，你没有权限！');
        }
    }
}
?>