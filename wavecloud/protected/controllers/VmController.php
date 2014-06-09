<?php
/**
 * 虚拟机控制层
 */
class VmController extends CController
{
    public $defaultAction = 'Index';
    public $request = null;  
    public $layout;
    public $username;
    public $userid;
    public $group;
    public $ops_server_ip = '192.168.1.1';      // 网站服务器ip 即 客户端ip
    public $qemu = 'qemu+tls://';               // virsh 命令连接类型
    public $port = '88';                        // 客户端 服务端 端口
    public $libvirt_port = '';                  // libvirt端口
    public $ops_ssh_user = 'root';              // 网站服务器用户权限
    public $keyfilename = '/root/.ssh/id_rsa';  // 网站服务器私钥地址
    public $pyuser = 'wavevmadmin';             // 物理机用户名
    public $wavevms = '/data/wavevms/';         // 物理机放虚拟机的位置
    public $publicArr = array();

    /**
     * 初始化
     *
     */
    public function init()
    {  
        parent::init();
        $this->libvirt_port = ':'.$this->port.'/system';

        $this->publicArr = array('qemu'             => $this->qemu, 
                                 'libvirt_port'     => $this->libvirt_port,
                                 'ops_server_ip'    => $this->ops_server_ip,
                                 'port'             => $this->port,
                                 'ops_ssh_user'     => $this->ops_ssh_user,
                                 'keyfilename'      => $this->keyfilename,
                                 'pyuser'           => $this->pyuser,
                                 'wavevms'          => $this->wavevms);
        
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
     * 虚拟机页
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * 虚拟机列表
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
        $sql = "SELECT users.username,vm.* FROM vm
                LEFT JOIN users
                ON vm.add_user=users.id
                WHERE vm.add_user='$this->userid'
                LIMIT $start, $limit";
        $arr = $Common->getSqlList($sql);

        foreach ($arr as $key => $value) {
            $arr[$key]['checkbox'] = '<input type="checkbox" onclick="checkBox(this)" name="checkbox" value="'.$value['vm_id'].'">';
            $arr[$key]['ips'] = $value['vm_ip'].'<br>'.$value['vm_internel_ip'];
            $arr[$key]['vnc'] = '<button class="btn btn-small btn-success">
                                    <i class="icon-list icon-white"></i> VNC 
                                 </button>';
            $arr[$key]['status'] = $Common->getVmStatus($value['vm_status']);
            $arr[$key]['operating'] = '<button class="btn btn-small btn-primary">查看</button>';
        }
        $count = $Common->getFieldCount('vm', 'add_user', $this->userid);
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
        $arr = $Common->getOneData('vm', '*', 'vm_id', $id);
        $templatearr = $Common->getFieldList('template', 'template_id,template_name');

        $this->layout='index';
        $this->render('edit', array('arr'=>$arr, 'templatearr'=>$templatearr));
    }

    /**
     * 添加
     */
    public function actionAdd()
    {
        $Common = new Common();
        $Vm = new Vm();
        $data = $Common->getFilter($_POST);
        $vm_num = (int)$data['vm_num'];
        unset($data['vm_num']);
        $error = '';
        $count = 0;
        if($vm_num > 0){
            // 虚拟机创建
            $templatearr = $Common->getOneData('template', '*', 'template_id', $data['template_id']);
            $isoarr = $Common->getOneData('iso', 'iso_path', 'iso_id', $templatearr['iso_id']);
            $pmidarr = $Common->getAllData('template_pm_mapping', '*', 'template_id', $data['template_id']);
            $pmidstr = '';
            foreach ($pmidarr as $key => $value) {
                $pmidstr .= $value['pm_id'].',';
            }
            $pmidstr = trim($pmidstr, ',');
            for ($i = 1; $i <= $vm_num; $i++) {
                $netsql = "SELECT net_ips.*,net.net_private FROM net_ips 
                           LEFT JOIN net
                           ON net_ips.net_id=net.net_id 
                           WHERE net_ips.net_id='".$templatearr['net_id']."' 
                           AND net_ips.use_status='0'";
                $netarr = $Common->getSqlOne($netsql);
                if(!empty($netarr)){
                    $sql = 'SELECT * FROM pm 
                            WHERE actual_free_hd > '.$templatearr['hd'].' 
                            AND actual_free_mem > '.$templatearr['mem'].'
                            AND pm_id IN('.$pmidstr.') 
                            ORDER BY cpu_use ASC';
                    $pmarr = $Common->getSqlOne($sql);
                    if(!empty($pmarr)){
                        $data['pm_id'] = $pmarr['pm_id'];
                        $data['vm_ip'] = $netarr['ip'];
                        $iparr = explode('.', $netarr['ip']);
                        $data['vm_internel_ip'] = $netarr['net_private'].'.'.$iparr[3];
                        $data['vm_status'] = 'loading';
                        $data['add_user'] = $data['last_modify_user'] = $this->userid;
                        $data['add_date'] = $data['last_modify_date'] = $Common->getDate();
                        $data['pm_ip'] = $pmarr['pm_ip'];
                        $Common->getInsert('vm', $data);
                        $data['vm_id'] = $Common->getLastId();
                        $data['vm_instanse'] = 'instanse-'.$data['vm_id'];
                        $Common->getUpdate('vm', array('vm_instanse'=>$data['vm_instanse']), 'vm_id', $data['vm_id']);
                        if(!empty($pmarr)){
                            $res = $Vm->generateVM($Common, $pmarr, $data, $templatearr, $isoarr, $this->publicArr);
                            if($res == true){
                                $Common->getUpdate('net_ips', array('use_status'=>1), 'id', $netarr['id']);
                                $count += 1;
                            }else{
                                $error .= $res;
                            }
                        }
                    }else{
                        $error .= '没有合适的物理机！<br>';
                    }
                }else{
                    $error .= 'IP不够<br>！';
                }
            }
        }

        $Common->operationOutput(true, '创建'.$count.'台虚拟机！<br>'.$error);
    }

    /**
     * 开机、普通关机、强制关机、重启
     */
    public function actionOperating($type)
    {
        
        $ip = $iparr[0];
        $Libvirt = @new Libvirt($this->qemu.$ip.$this->libvirt_port);
        $hn = $Libvirt->get_hostname();
        if ($hn == false)
            $ret .= 'Cannot open connection'.$ip;

        $res = $Libvirt->get_domain_by_name($name);
        $vmdata = array();
        if($type == 'boot'){
            if(!$Libvirt->domain_is_running($res, $name)){
                if(!$Libvirt->domain_start($name)){
                    $ret .= 'Error while starting '.$name.': '.$Libvirt->get_last_error().'<br>';
                    $vmdata['vm_status'] = 'shutoff';
                }else{
                    $ret .= 'Domain '.$name.' started'.'<br>';
                    $vmdata['vm_status'] = 'running';
                }
            }
        }else{
            if($Libvirt->domain_is_running($res, $name)){
                if($type == 'shutdown'){
                    if(!$Libvirt->domain_shutdown($name)){
                        $ret .= 'Error while shutdown '.$name.': '.$Libvirt->get_last_error().'<br>';
                        $vmdata['vm_status'] = 'shutoff';
                    }else{
                        $ret .= 'Domain '.$name.' shutdown'.'<br>';
                        $vmdata['vm_status'] = 'shutoff';
                    }
                    $Libvirt->domain_destroy($name);
                }elseif($type == 'destroy'){
                    if(!$Libvirt->domain_destroy($name)){
                        $ret .= 'Error while destroy '.$name.': '.$Libvirt->get_last_error().'<br>';
                        $vmdata['vm_status'] = 'shutoff';
                    }else{
                        $ret .= 'Domain '.$name.' destroy'.'<br>';
                        $vmdata['vm_status'] = 'shutoff';
                    }
                }elseif($type == 'reboot'){
                    if(!$Libvirt->domain_reboot($name)){
                        $ret .= 'Error while reboot '.$name.': '.$Libvirt->get_last_error().'<br>';
                        $vmdata['vm_status'] = 'shutoff';
                    }else{
                        $ret .= 'Domain '.$name.' reboot'.'<br>';
                        $vmdata['vm_status'] = 'running';
                    }
                }
            }else{
                $ret .= $name.'虚拟机，未启动！' ;
                $vmdata['vm_status'] = 'shutoff';
            }

        }
        
    }

}

?>