<?php
/**
 * 用户控制层
 */
class UserController extends CController
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
     * 用户中心
     */
    public function actionIndex()
    {
        $Common = new Common();
        $garr = $Common->getFieldList('group', 'id,group_name');
        $grouparr = array();
        foreach ($garr as $key => $value) {
            $grouparr[$value['id']] = $value['group_name'];
        }
        $groupname = $grouparr[$this->group];

        $this->layout='inner';
        $this->render('index', array('username' =>$this->username, 'groupname'=>$groupname, 'group'=>$this->group));
    }
    
    /**
     * 修改密码
     */
    public function actionPwdModify()
    {
        $oldpwd = isset($_POST['oldpwd']) ? $_POST['oldpwd'] : '';
        $newpwd = isset($_POST['newpwd']) ? $_POST['newpwd'] : '';
        $return_json = array();
        if (empty($oldpwd)) 
            $Common->operationOutput(false, '请输入旧密码！');
        
        if (empty($newpwd)) 
            $Common->operationOutput(false, '请输入新密码！');

        $json = array();
        $Common = new Common();
        $User = new User();
        $arr = $Common->getOneData('users', '*', 'username', $this->username);
        if($arr['password'] == $User->hashPassword($oldpwd)){
            $res = $Common -> getUpdate('users', array('password' => $User->hashPassword($newpwd)), 'username', $this->username);
            if ($res) {
                $Common->operationOutput(true, '修改成功！');
            }else{
                $Common->operationOutput(false, '未做任何修改！');
            }
        }else{
            $Common->operationOutput(false, '密码错误！');
        }
    }


    /**
     * 用户列表
     */
    public function actionList()
    {
        if ($this->group === '1') {
            $this->layout='inner';
            $this->render('list', array('group'=>$this->group));
        }
    }

    /**
     * 用户列表JSON
     */
    public function actionListJson()
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
            $garr = $Common->getFieldList('group', 'id,group_name');
            $grouparr = array();
            foreach ($garr as $key => $value) {
                $grouparr[$value['id']] = $value['group_name'];
            }
            $arr = $Common->getDataList('users', '*', $start, $limit);
            foreach ($arr as $key => $value) {
                $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['id'].'">';
                $arr[$key]['group'] = $grouparr[$value['group']];
                $arr[$key]['operat'] = '<button class="btn btn-info" onclick="editFunc(\''.Yii::app()->homeUrl.'/user/edit/id/'.$value['id'].'\')">
                                        <i class="icon-edit icon-white"></i> 编辑 </button> ';
            }
            $count = $Common->getCount('users');
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
            $garr = $Common->getFieldList('group', 'id,group_name');
            $arr = $Common->getOneData('users', 'id,username,email,group', 'id', $id);

            $this->layout='index';
            $this->render('edit', array('userinfo' =>$arr, 'garr'=>$garr));
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
            $id = (int)$_POST['userid'];
            $data = $_POST['user'];
            foreach ($data as $key => $value) {
                $data[$key] = trim(strip_tags($value));
            }
            unset($data['userid']);
            $return_json = array();
            if (empty($data['username'])) 
                $Common->operationOutput(false, '请输入用户名！');

            if(empty($id)){
                if (empty($data['password'])) 
                    $Common->operationOutput(false, '请输入密码！');

                $count = $Common->getFieldCount('users', 'username', $data['username']);
                if ($count >= 1){
                    $Common->operationOutput(false, '用户名已存在！');
                }else{
                    $data['create_date'] = date('Y-m-d H:i:s');
                    $data['password'] = $User->hashPassword($data['password']);
                    $result = $Common->getInsert('users', $data);
                }
                $Common->operationOutput(true, '添加用户成功！');

            }else{
                if (empty($data['password'])) {
                    unset($data['password']);
                }else{
                    $data['password'] = $User->hashPassword($data['password']);
                }
                $row = $Common->getOneData('users', 'username', 'id', $id);
                if($row['username'] !== $data['username']){
                    $count = $Common->getFieldCount('users', 'username', $data['username']);
                    if ($count >= 1)
                        $Common->operationOutput(false, '用户名已存在！');
                }
                $result = $Common->getUpdate('users', $data, 'id', $id);

                $Common->operationOutput(true, '修改用户成功！');
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
            $ids = $_POST['ids'];
            $idarr = explode(',', $ids);
            foreach ($idarr as $key => $id) {
                if(!empty($id))
                    $Common->getDelete('users', 'id', $id);
            }
            $Common->operationOutput(true, '删除成功！');
            
        }else{
            $Common->operationOutput(false, '对不起，你没有权限！');
        }
    }


}