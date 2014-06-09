<?php

class Common
{
    public function commond() 
    {  
        return Yii::app()->db->createCommand();
    }

    /**
     * 获得当前日期时间
     */
    public function getDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 过滤
     * @param array $data   需过滤的数组
     * @return array        过滤数组
     */
    public function getFilter($data)
    {
        foreach ($data as $key => $value) {
            if(!empty($value)){
                if(is_array($value)){
                    foreach ($value as $k => $v) {
                        $data[$key][$k] = addslashes($v);
                    }
                }else{
                    $data[$key] = addslashes($value);
                }
            }
        }

        return $data;
    }

    /**
     * 虚拟机状态
     */
    public function getVmStatus($s)
    {
        $arr = array('loading'=>'部署中',
                     'running'=>'运行中',
                     'stoping'=>'关机中');
        
        return $arr[$s];
    }

    /**
     * 创建虚拟机的相关信息日志
     */
    public function record($txt)
    {
        $filepath = dirname(__FILE__).'/../../record/';
        if(!is_dir($filepath)){
            mkdir($filepath, 0777);
        }
        return file_put_contents($filepath.date('Y-m-d').'.txt', $txt."\n", FILE_APPEND);
    }

    /**
     * ssh 连接 并 执行命令
     */
    public function runCmd($ssh_host, $user_name, $keyfilename, $ssh_command, $port)
    {
        $connection = @ssh2_connect($ssh_host, $port); 
        if (!$connection) $this->operationOutput(false, $ssh_host.' SSH Connection failed!'); 

        if (@ssh2_auth_pubkey_file($connection, $user_name, $keyfilename.".pub", $keyfilename, 'wave')) { 
            $stream = ssh2_exec($connection, $ssh_command);
            stream_set_blocking($stream, true); 
            $line = stream_get_line($stream, 1024, "\n");
            unset($stream);
            return $line;
        } else { 
            
            $this->operationOutput(false, $ssh_host.' SSH Public Key Authentication Failed!'); 
        }
    }

    /**
     * CPU核心数
     */
    public function cpu()
    {
        return array('1'    => '1核',
                     '2'    => '2核',
                     '4'    => '4核',
                     '6'    => '6核',
                     '8'    => '8核',
                     '16'    => '16核');
    }

    /**
     * 内存
     */
    public function mem()
    {
        return array('1'    => '1G',
                     '2'    => '2G',
                     '4'    => '4G',
                     '8'    => '8G',
                     '16'   => '16G',
                     '32'   => '32G');
    }

    /**
     * 硬盘
     */
    public function hd()
    {
        return array('100'  => '100G',
                     '160'  => '160G',
                     '320'  => '320G',
                     '420'  => '460G',
                     '500'  => '500G');
    }

    /**
     * 内存缓存
     * @param string $result 内存
     */
    public function swap($mem)
    {
        $arr = array('1'    => '1',
                     '2'    => '2',
                     '4'    => '4',
                     '8'    => '8',
                     '16'   => '8',
                     '32'   => '16');
        return $arr[$mem];
    }

    /**
     * 操作错误输出JSON
     * @param boolean $result       操作结果 true or false
     * @param string $str           操作信息
     */
    public function operationOutput($result, $str, $id = null)
    {
        $result_array = array();
        $result_array['success'] = $result;
        $result_array['msg'] = $str;
        if(!empty($id)) $result_array['id'] = $id;
        echo json_encode($result_array);
        unset($result_array);die;
    }

    /*
     * 二维数组排序
     */
    public function sortBy($array, $keyname = null, $sortby){
        $myarray = $inarray = array();
        foreach($array as $i=>$befree){
          $myarray[$i] = $array[$i][$keyname];
        }
        switch($sortby){
           case 'asc':
           asort($myarray);
           break;
           case 'arsort':
           arsort($myarray);
           break;
           case 'natcasesor':
           natcasesor($myarray);
           break;
        }
        foreach($myarray as $key=>$befree){
            $inarray[] = $array[$key];
        }
        return $inarray;
    }

    /**
     * 验证邮箱
     */
    public function checkEmail($email)
    {
        if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",$email)) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 验证ip地址
     */
    public function regIp($ip)
    {
        if(preg_match("/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])(\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])){3}$/", $ip)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获得所有数据数量
     */
    public function getCount($table)
    {
        $count = $this->commond()
                    ->select('count(*) count')
                    ->from($table)
                    ->queryRow();
        return $count['count'];
    }

    /**
     * 根据字段统计数量
     */
    public function getFieldCount($table, $field, $id)
    {
        $count = $this->commond()
            ->select('count(*) count')
            ->from($table)
            ->where($field.'=:id', array(':id'=>$id))
            ->queryRow();
        return $count['count'];
    }

    /**
     * 有条件的获得单个数据
     */
    public function getOneData($table, $allfield, $field, $id)
    {
        return $this->commond()
                ->select("$allfield")
                ->from($table)
                ->where($field.'=:id', array(':id'=>$id))
                ->queryRow();
    }

    /**
     * 根据字段获得所有数据
     */
    public function getFieldList($table, $allfield, $order = null)
    {
        if($order == null) {
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->queryAll();
        }else{
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->order("$order")
                    ->queryAll();
        }
    }

    /**
     * 有条件的获得所有数据
     */
    public function getAllData($table, $allfield, $field, $id, $order = null)
    {
        if($order == null) {
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->where($field.'=:id', array(':id'=>$id))
                    ->queryAll();
        }else{
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->where($field.'=:id', array(':id'=>$id))
                    ->order("$order")
                    ->queryAll();
        }
    }

    /**
     * 数据列表
     */
    public function getDataList($table, $allfield, $start, $limit, $order = null)
    {
        if($order == null) {
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->limit($limit) 
                    ->offset($start)
                    ->queryAll();
        }else{
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->order("$order")
                    ->limit($limit) 
                    ->offset($start)
                    ->queryAll();
        }
    }

    /**
     * 有条件获得分页数据列表
     */
    public function getFieldDataList($table, $allfield, $fieldarr, $wherearr, $start, $limit, $order = null)
    {
        if($order == null) {
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->where($fieldarr, $wherearr)
                    ->limit($limit) 
                    ->offset($start)
                    ->queryAll();
        }else{
            return $this->commond()
                    ->select("$allfield")
                    ->from($table)
                    ->where($fieldarr, $wherearr)
                    ->order("$order")
                    ->limit($limit) 
                    ->offset($start)
                    ->queryAll();
        }
    }

    /**
     * 执行sql获得数据
     */
    public function getSqlList($sql)
    {
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    /**
     * 执行sql获得单个数据
     */
    public function getSqlOne($sql)
    {
        return Yii::app()->db->createCommand($sql)->queryRow();
    }

    /**
     * 插入数据
     */
    public function getInsert($table, $data)
    {
        return $this->commond()
            ->insert($table,$data);
    }
    
    /**
     * 获得刚插入的id
     */
    public function getLastId()
    {
        return Yii::app()->db->lastInsertID;
    }

    /**
     * 更新数据
     */
    public function getUpdate($table, $data, $field, $id, $in = null)
    {
        if($in == null) {
            return $this->commond()
                ->update($table,$data,"$field='$id'");
        }else{
            return $this->commond()
                ->update($table,$data,"$field in $id");
        }
    }

    /**
     * 删除数据
     */
    public function getDelete($table, $field, $id, $in = null)
    {
        if($in == null) {
            return $this->commond()
                ->delete($table,"$field='$id'");
        }else{
            return $this->commond()
                ->delete($table,"$field in $id");
        }
    }

    /**
     * 发送邮件
     */
    public function sendEmail($to,$from_name,$subject,$message,$attachment = NULL, $ishtml = true, $relay_to = NULL)
    {
        $phpmailer_con = Yii::app()->params['phpmailer_con'];
        if (empty($to)){
            return false;
        }
        $mail = new phpmailer();
        $mail->IsSMTP();
        $mail->Host = $phpmailer_con['Host'];
        $mail->SMTPAuth = true;
        $mail->Username = $phpmailer_con['Username'];
        $mail->Password = $phpmailer_con['Password'];
        $date = date("Y年m月d日 G:i");
        $mail->From = $phpmailer_con['From'];
        $mail->FromName = $from_name;
        if ($relay_to != NULL){
            $mail->AddReplyTo($relay_to);
        }
        $to = explode(',',$to);
        foreach ($to as $address){
            $mail->AddAddress($address,"");
        }
        if ($attachment != NULL){
            $attachment = explode(',',$attachment);
            foreach ($attachment as $attachment){
                $mail->AddAttachment($attachment);
            }
        }
        $mail->IsHTML($ishtml);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->Port = 25; 
        if ($mail->Send()){
            return true;
        } else {
            return false;
        }
    }

}