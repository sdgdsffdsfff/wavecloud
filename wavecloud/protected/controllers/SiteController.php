<?php
/**
 * 默认访问控制层
 */
class SiteController extends CController
{
    public $defaultAction = 'Index';
    public $layout;

    /**
     * 首页
     */
    public function actionIndex()
    {
        if(Yii::app()->user->getState('userid')) {
            $group = Yii::app()->user->getState('group');
            $Common = new Common();
            if ($group != 1) {
                $arr = $Common->getOneData('group', 'id,group_name', 'id', $group);
            }else{
                $arr = $Common->getFieldList('group', 'id,group_name', 'id ASC');
                array_shift($arr);
            }
            $this->render('index', array('group'=>$group));
        }else {
            $this->redirect(Yii::app()->user->loginUrl);
        }
    }

    /**
     * 错误页面
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest){
                echo $error['message'];
            }else {
                $this->layout='index';
                $this->render('error', $error);
            }
        }
    }
    
    /**
     * 登录
     */
    public function actionLogin()
    {
        if(Yii::app()->user->getState('userid')) {
            $this->redirect(Yii::app()->user->returnUrl.'/site');
        }else {
            $model = new LoginForm;
            if(isset($_POST['LoginForm']))
            {
                $model->attributes = $_POST['LoginForm'];
                if($model->validate() && $model->login())
                    $this->redirect(Yii::app()->user->returnUrl.'/site');
            }
            $this->layout='login';
            $this->render('login',array('model'=>$model));
        }
    }

    /**
     * 退出
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}