<?php
/**
 * API控制层
 */
class ApisController extends CController
{
    public $defaultAction = 'Index';
    public $request = null;  
    public $layout;

    /**
     * 初始化
     *
     */
    public function init()
    {  
        parent::init();
        
        $this->request = Yii::app()->request;
    }


    /**
     * 接收状态
     */
    public function actionReceivingState($vmid)
    {
        $Common = new Common();

        $hd_status = (int)$_GET['hd_status'];
        if ($hd_status == 1) {
            $Vm = new Vm();
            $vmarr = $Common->getOneData('vm', 'vm_id,vm_name,pm_id', 'vm_id', $vmid);
            $pmarr = $Common->getOneData('pm', 'pm_ip', 'pm_id', $vmarr['pm_id']);
            $ip = $pmarr['pm_ip'];
            $qemu = 'qemu+tls://';
            $libvirt_port = ':59879/system';
            $Libvirt = @new Libvirt($qemu.$ip.$libvirt_port);
            $hn = $Libvirt->get_hostname();
            if ($hn == false)
                echo 'Cannot open connection'.$ip;

            $Libvirt->domain_start($vmarr['vm_name']);

            // 更改虚拟机状态
            $vmdata = array();
            $vmdata['vm_status'] = 'running';
            $Common->getUpdate('vm', $vmdata, 'vm_id', $vmid);   
        }
    }

}