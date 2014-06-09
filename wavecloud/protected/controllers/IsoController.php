<?php
/**
 * 镜像控制层
 */
class IsoController extends CController
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
     * 镜像页
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * 镜像列表
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
        $sql = "SELECT iso.*,users.username FROM iso
                LEFT JOIN users
                ON iso.add_user=users.id LIMIT $start, $limit";
        $arr = $Common->getSqlList($sql);

        foreach ($arr as $key => $value) {
            $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['iso_id'].'">';
            $arr[$key]['operat'] = '<button onclick="editFunc(\''.Yii::app()->homeUrl.'/iso/edit/id/'.$value['iso_id'].'\')" class="btn btn-info">
                                    <i class="icon-edit icon-white"></i> 编辑 </button> ';
        }
        $count = $Common->getCount('iso');
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
        $arr = $Common->getOneData('iso', '*', 'iso_id', $id);

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
        $id = (int)$data['iso_id'];
        unset($data['iso_id']);
        $data['last_modify_user'] = $this->userid;
        $data['last_modify_date'] = $Common->getDate();
        if($id == 0){
            $data['add_user'] = $this->userid;
            $data['add_date'] = $Common->getDate();
            $Common->getInsert('iso', $data);
            $str = '添加成功！';
        }else{
            $Common->getUpdate('iso', $data, 'iso_id', $id);
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
                $tcount = $Common->getFieldCount('template', 'iso_id', $id);
                if($tcount > 0)
                    $str .= $id.' - 模板在使用';
                else
                    $Common->getDelete('iso', 'iso_id', $id);
            }
        }
        $msg = empty($str) ? '删除成功！' : $str;

        $Common->operationOutput(true, $msg);
    }

}

?>