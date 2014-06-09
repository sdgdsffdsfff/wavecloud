<?php
/**
 * 模版控制层
 */
class TemplateController extends CController
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
     * 模版页
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * 模板列表
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
        $sql = "SELECT template.*,users.username FROM template
                LEFT JOIN users
                ON template.add_user=users.id 
                WHERE template.add_user='$this->userid'
                LIMIT $start, $limit";
        $arr = $Common->getSqlList($sql);

        foreach ($arr as $key => $value) {
            $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['template_id'].'">';
            $arr[$key]['operat'] = '<button onclick="editFunc(\''.Yii::app()->homeUrl.'/template/edit/id/'.$value['template_id'].'\')" class="btn btn-info">
                                    <i class="icon-edit icon-white"></i> 编辑 </button> ';
        }
        $count = $Common->getFieldCount('template', 'add_user', $this->userid);
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
        $arr = $Common->getOneData('template', '*', 'template_id', $id);
        $infoarr = array();
        $infoarr['iso'] = $Common->getFieldList('iso', 'iso_id,iso_name');
        $infoarr['net'] = $Common->getFieldList('net', 'net_id,net_name');
        $infoarr['pm']  = $Common->getFieldList('pm', 'pm_id,pm_name,pm_ip,public_gateway');
        $infoarr['cpu'] = $Common->cpu();
        $infoarr['mem'] = $Common->mem();
        $infoarr['hd']  = $Common->hd();
        $pmarr = $Common->getAllData('template_pm_mapping', '*', 'template_id', $id);
        if(!empty($pmarr)){
            $pm_idarr = array();
            foreach ($pmarr as $key => $value) {
                $pm_idarr[] = $value['pm_id'];
            }
            foreach ($infoarr['pm'] as $key => $value) {
                if(in_array($value['pm_id'], $pm_idarr))
                    $infoarr['pm'][$key]['checked'] = true;
            }
        }

        $this->layout='index';
        $this->render('edit', array('arr'=>$arr, 'infoarr'=>$infoarr));
    }

    /**
     * 添加、编辑
     */
    public function actionModify()
    {
        $Common = new Common();
        $data = $Common->getFilter($_POST);
        $id = (int)$data['template_id'];
        $pm_ids = isset($data['pm_ids']) ? $data['pm_ids'] : '';
        unset($data['template_id'], $data['pm_ids']);
        $data['last_modify_user'] = $this->userid;
        $data['last_modify_date'] = $Common->getDate();
        if($id == 0){
            $data['add_user'] = $this->userid;
            $data['add_date'] = $Common->getDate();
            $Common->getInsert('template', $data);
            $id = $Common->getLastId();
        }else{
            $Common->getUpdate('template', $data, 'template_id', $id);
        }
        if(!empty($pm_ids)){
            $pmarr = $Common->getAllData('template_pm_mapping', '*', 'template_id', $id);
            $pm_idarr = array();
            foreach ($pmarr as $key => $value) {
                $pm_idarr[] = $value['pm_id'];
            }
            foreach ($pmarr as $key => $value) {
                if(!in_array($value['pm_id'], $pm_ids)){
                    $sql = "DELETE FROM template_pm_mapping 
                            WHERE pm_id='".$value['pm_id']."'
                            AND template_id='$id'";
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }
            foreach ($pm_ids as $key => $pm_id) {
                if(!in_array($pm_id, $pm_idarr)){
                    $mappdata = array();
                    $mappdata['template_id'] = $id;
                    $mappdata['pm_id'] = $pm_id;
                    $Common->getInsert('template_pm_mapping', $mappdata);
                }
            }
        }

        header('Location:'.Yii::app()->homeUrl.'/template');
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
        foreach ($idarr as $key => $id) {
            if(!empty($id)){
                $tcount = $Common->getFieldCount('vm', 'template_id', $id);
                if($tcount > 0)
                    $str .= $id.' - 虚拟机在使用';
                else
                    $Common->getDelete('template', 'template_id', $id);
            }
        }
        $Common->operationOutput(true, '删除成功！');
    }

}

?>