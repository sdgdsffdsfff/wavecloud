<?php
/**
 * 物理机控制层
 */
class PmController extends CController
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
     * 物理页
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * 物理列表
     */
    public function actionList()
    {
        $Common = new Common();
        $start = 0;
        $limit = 10;
        $data = $_GET;
        if (isset( $data['iDisplayStart']) && $data['iDisplayLength'] != '-1' )
        {
            $start = (int)$data['iDisplayStart'];
            $limit = (int)$data['iDisplayLength'];
        }
        $sql = "SELECT pm.*,users.username FROM pm
                LEFT JOIN users
                ON pm.add_user=users.id LIMIT $start, $limit";
        $arr = $Common->getSqlList($sql);

        foreach ($arr as $key => $value) {
            $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['pm_id'].'">';
            $arr[$key]['operat'] = '<button onclick="editFunc(\''.Yii::app()->homeUrl.'/pm/edit/id/'.$value['pm_id'].'\')" class="btn btn-info">
                                    <i class="icon-edit icon-white"></i> 编辑 </button> ';
        }
        $count = $Common->getCount('pm');
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

    /**
     * 编辑
     */
    public function actionEdit($id)
    {
        $id = (int)$id;
        $Common = new Common();
        $arr = $Common->getOneData('pm', '*', 'pm_id', $id);

        $this->layout='index';
        $this->render('edit', array('arr'=>$arr));
    }
    
    /**
     * 添加、编辑
     */
    public function actionModify()
    {
        $Common = new Common();
        $data = $Common->getFilter($_POST);
        $id = (int)$data['pm_id'];
        unset($data['pm_id']);
        $data['last_modify_user'] = $this->userid;
        $data['last_modify_date'] = $Common->getDate();
        if($id == 0){
            $data['add_user'] = $this->userid;
            $data['add_date'] = $Common->getDate();
            $Common->getInsert('pm', $data);
            $str = '添加成功！';
        }else{
            $Common->getUpdate('pm', $data, 'pm_id', $id);
            $str = '更新成功！';
        }

        $Common->operationOutput(true, $str);
    }

    /**
     * 删除
     */
    public function actionDelete()
    {
        $json = array();
        $Common = new Common();
        $ids = $this->request->getParam('ids');
        $idarr = explode(',', $ids);
        $str = '';
        foreach ($idarr as $key => $id) {
            if(!empty($id)){
                $tcount = $Common->getFieldCount('template_pm_mapping', 'pm_id', $id);
                if($tcount > 0)
                    $str .= $id.' - 模板在使用';
                else
                    $Common->getDelete('pm', 'pm_id', $id);
            }
        }
        $msg = empty($str) ? '删除成功！' : $str;

        $Common->operationOutput(true, $msg);
    }

}

?>