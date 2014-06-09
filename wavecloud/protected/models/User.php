<?php

class User extends CActiveRecord
{
    public function commond()
    {  
        return Yii::app()->db->createCommand();
    }
    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string 表名
     */
    public function tableName()
    {
        return '{{users}}';
    }

    /**
     * md5加密
     * @param string password
     */
    public function hashPassword($password)
    {
        return md5($password);
    }

    /**
     * 获得单个数据
     */
    public function getUser($username)
    {
        return $this->commond()
            ->select('*')
            ->from($this->tableName())
            ->where('username=:username', array(':username'=>$username))
            ->queryRow();
    }

    /**
    *  获取所有系统用户
    */
    public function getAllUser()
    {
        return $this->commond()
            ->select('*')
            ->from($this->tableName())
            ->queryAll();
    }

    /**
     *  删除系统用户
     */
    public function deleteUser($uid)
    {
         return $this->commond()
                ->delete($this->tableName(),"id='$uid'");
    }

    /**
     *  添加系统用户
     */
    public function addUser()
    {

    }

}