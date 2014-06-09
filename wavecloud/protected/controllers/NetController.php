<?php
/**
 * 网络控制层
 */
class NetController extends CController
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
     * 网络页
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * 网络列表
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
        $sql = "SELECT net.*,users.username FROM net
                LEFT JOIN users
                ON net.add_user=users.id LIMIT $start, $limit";
        $arr = $Common->getSqlList($sql);

        foreach ($arr as $key => $value) {
            $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['net_id'].'">';
            $arr[$key]['operat'] = '<button onclick="editFunc(\''.Yii::app()->homeUrl.'/net/edit/id/'.$value['net_id'].'\')" class="btn btn-info">
                                    <i class="icon-edit icon-white"></i> 编辑 </button> ';
            $arr[$key]['operat'] .= '<button onclick="lookIp(\''.Yii::app()->homeUrl.'/net/iplist/id/'.$value['net_id'].'\')" class="btn btn-success">
                                     查看 </button> ';
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
     * IP列表
     */
    public function actionIplist($id)
    {
        $id = (int)$id;
        $Common = new Common();
        $netarr = $Common->getOneData('net', '*', 'net_id', $id);
        $arr = $Common->getAllData('net_ips', '*', 'net_id', $id);
        $usearr = array('未使用', '已使用');
        foreach ($arr as $key => $value) {
            $iparr = explode('.', $value['ip']);
            $arr[$key]['net_private'] = $netarr['net_private'].'.'.$iparr[3];
            $arr[$key]['use_status'] = $value['use_status'] == 0 
                                    ? '<font color="green">'.$usearr[$value['use_status']].'</font>' : 
                                    '<font color="red">'.$usearr[$value['use_status']].'</font>';
        }

        $this->layout='index';
        $this->render('iplist', array('arr'=>$arr, 'netarr'=>$netarr));
    }

    /**
     * 编辑
     */
    public function actionEdit($id)
    {
        $id = (int)$id;
        $Common = new Common();
        $arr = $Common->getOneData('net', '*', 'net_id', $id);
        $iplist = array();
        if($arr['check_radio'] == 1){
            $iplist = $Common->getAllData('net_ips', 'ip', 'net_id', $id);
        }
        $ipliststr = '';
        foreach ($iplist as $key => $value) {
            $ipliststr .= $value['ip'].',';
        }
        $ipliststr = rtrim($ipliststr, ',');

        $this->layout='index';
        $this->render('edit', array('arr'=>$arr, 'iplist'=>$iplist, 'ipliststr'=>$ipliststr));
    }
    
    /**
     * 添加、编辑
     */
    public function actionModify()
    {
        $Common = new Common();
        $data = $Common->getFilter($_POST);
        $id = (int)$data['net_id'];
        if(isset($data['ip_list']))
            unset($data['ip_list']);
        
        $iplist = '';
        if($data['check_radio'] == 1){
            $iplist = $data['iplist'];
        }
        unset($data['net_id'], $data['ip'], $data['iplist']);
        $data['last_modify_user'] = $this->userid;
        $data['last_modify_date'] = $Common->getDate();
        if($id == 0){
            $data['add_user'] = $this->userid;
            $data['add_date'] = $Common->getDate();
            $Common->getInsert('net', $data);
            $id = $Common->getLastId();
        }else{
            $Common->getUpdate('net', $data, 'net_id', $id);
        }
        if($data['check_radio'] == 1){
            $iparr = explode(',', $iplist);
        }else{
            $begin = explode('.', $data['ip_begin']);
            $ip_begin = $begin[3];
            $end = explode('.', $data['ip_end']);
            $ip_end = $end[3];
            $iparr = array();
            for ($i = $ip_begin; $i <= $ip_end; $i++) { 
                $iparr[] = $begin[0].'.'.$begin[1].'.'.$begin[2].'.'.$i;
            }
        }
        $newnet_iparr = array();
        $net_iparr = $Common->getAllData('net_ips', 'id,ip,use_status', 'net_id', $id);
        foreach ($net_iparr as $key => $value) {
            if(!in_array($value['ip'], $iparr))
                $Common->getDelete('net_ips', 'id', $value['id']);
            else
                $newnet_iparr[$value['ip']] = $value;
        }
        foreach ($iparr as $key => $value) {
            $ipdata = array();
            $ipdata['net_id'] = $id;
            $ipdata['ip'] = $value;
            if(isset($newnet_iparr[$value])){
                $ipdata['use_status'] = $newnet_iparr[$value]['use_status'];
            }else{
                $Common->getInsert('net_ips', $ipdata);
            }
        }

        header('Location:'.Yii::app()->homeUrl.'/net');
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
                $tcount = $Common->getFieldCount('template_net_mapping', 'net_id', $id);
                if($tcount > 0)
                    $str .= $id.' - 模板在使用';
                else{
                    $Common->getDelete('net', 'net_id', $id);
                    $Common->getDelete('net_ips', 'net_id', $id);
                }
            }
        }
        $Common->operationOutput(true, '删除成功！');
    }

}

?>