<?php

class Vm extends CActiveRecord
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
        return '{{vm}}';
    }

    /**
     * 生成mac地址
     */
    public function generateMac($ip)
    {
        $iparr = explode('.', $ip);
        $marr = array();
        foreach ($iparr as $key => $value) {
            $chex = dechex($value);
            if(strlen($chex) > 1)
                $marr[] = $chex;
            else
                $marr[] = '0'.$chex;
        }
        $mac = implode(':', $marr);
        unset($marr, $iparr);

        return '52:54:'.$mac;        
    }

    /**
     * 生成xml
     */
    public function generateXml($xmlarr, $publicArr, $diskarr = array())
    {
        $xml = "<domain type='kvm'>
        <name>".$xmlarr['vm_name']."</name>
        <memory>".$xmlarr['mem']."</memory>
        <currentMemory>".$xmlarr['mem']."</currentMemory>
        <vcpu>".$xmlarr['cpu']."</vcpu>
        <os>
            <type arch='x86_64' machine='pc'>hvm</type>
            <boot dev='hd'/>
        </os>
        <features>
            <acpi/>
            <apic/>
            <pae/>
        </features>
        <clock offset='localtime'/>
        <on_poweroff>destroy</on_poweroff>
        <on_reboot>restart</on_reboot>
        <on_crash>destroy</on_crash>
        <devices>
            <emulator>/usr/libexec/qemu-kvm</emulator>
            <disk type='file' device='disk'>
                <driver name='qemu' type='qcow2'/>
                <source file='".$publicArr['wavevms'].$xmlarr['vm_instanse']."/disk.qcow2'/>
                <target dev='sda' bus='virtio'/>
            </disk>
            <disk type='file' device='disk'>
                <driver name='qemu' type='qcow2'/>
                <source file='".$publicArr['wavevms'].$xmlarr['vm_instanse']."/swap.qcow2'/>
                <target dev='sdb' bus='virtio'/>
            </disk>
            <disk type='file' device='disk'>
                <driver name='qemu' type='qcow2'/>
                <source file='".$publicArr['wavevms'].$xmlarr['vm_instanse']."/disk2.qcow2'/>
                <target dev='sdc' bus='virtio'/>
            </disk>
            ";

        // 添加的其他磁盘
        $sdarr = range('d', 'z');
        if(!empty($diskarr)){
            foreach ($diskarr as $key => $value) {
                $xml .= "<disk type='file' device='disk'>
                    <driver name='qemu' type='qcow2'/>
                    <source file='".$publicArr['wavevms'].$xmlarr['vm_instanse']."/disk-".$value['disk_id'].".qcow2'/>
                    <target dev='sd".$sdarr[$key]."' bus='virtio'/>
                </disk>
                ";
            }
        }

        $xml .= "<disk type='file' device='cdrom'>
                <driver name='qemu' type='raw'/>
                <source file='".$publicArr['wavevms'].$xmlarr['vm_instanse']."/meta.iso'/>
                <target dev='hda' bus='ide'/>
                <readonly/>
            </disk>
            ";
        $xml .= "<interface type='bridge'>
                <source bridge='public'/>
                <model type='virtio'/>
                <mac address='".$xmlarr['mac_ip']."'/>
                <filterref>
                    <parameter name='IP' value='".$xmlarr['vm_ip']."' />
                </filterref>
            </interface>
            ";
        if(!empty($xmlarr['vm_ip_internel'])){
            $xml .= "<interface type='bridge'>
                    <source bridge='private'/>
                    <model type='virtio'/>
                    <mac address='".$xmlarr['mac_ip_internal']."'/>
                    <filterref>
                        <parameter name='IP' value='".$xmlarr['vm_ip_internel']."' />
                    </filterref>
                </interface>
                ";
        }
        $xml .= "<input type='mouse' bus='ps2'/>";
        if(isset($xmlarr['proxy_ip'])){
            $xml .= "
            <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='".$xmlarr['proxy_ip']."'/>";
        }else{
            $xml .= "
            <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0' passwd='gamewave'/>";
        }
        $xml .= "
            </devices>
        </domain>";

        return $xml;
    }

    /**
     * 创建磁盘
     */
    public function createHd($Common, $pm_ip, $node_ip, $vmarr, $publicArr)
    {
        // 检测镜像节点有没有镜像
        $isopath = $vmarr['iso_path'];
        $ssh_command = 'if [ -f '.$isopath.' ] ;then echo "yes";else echo ""; fi';
        $ls = $Common->runCmd($node_ip, $publicArr['sshuser'], $publicArr['keyfilename'], $ssh_command, $publicArr['port']);
        if(!$ls){
            $Common->record('镜像节点没有镜像!');
            return '镜像节点没有镜像!';
        }

        $hd = $vmarr['hd'].'G';
        $swap_hd = $vmarr['mem'].'G';
        // 写到某个文件里
        $vm_instanse = $vmarr['vm_instanse'];
        $wavevms = $publicArr['wavevms'];
        $vmpath = $wavevms.$vm_instanse;
        $shell  = "#!/bin/bash\n";
        $shell .= 'mkdir -p '.$vmpath."\n";
        $shell .= 'if [ -f '.$vmpath.'/disk.qcow2 ]'."\n";
        $shell .= "then\n";
        $shell .= "    exit 1\n";
        $shell .= "else\n";
        $shell .= "    if [ ! -f ".$isopath." ]\n";
        $shell .= "    then\n";
        $shell .= "        scp -P ".$publicArr['port']." -i /home/".$publicArr['pyuser']."/.ssh/id_rsa ".$publicArr['pyuser']."@".$node_ip.":".$isopath." $isopath\n";
        $shell .= "    fi\n";
        $shell .= "\n";
        $shell .= '    cp '.$isopath.' '.$vmpath.'/disk.qcow2'."\n";
        $shell .= '    qemu-img create -f qcow2 -o preallocation=metadata '.$vmpath.'/disk2.qcow2 '.$hd."\n";
        $shell .= '    qemu-img create -f qcow2 -o preallocation=metadata '.$vmpath.'/swap.qcow2 '.$swap_hd."\n";
        $shell .= '    mv '.$wavevms.'meta_'.$vm_instanse.'.js '.$vmpath.'/meta.js'."\n";
        $shell .= '    mkisofs -r -o '.$vmpath.'/meta.iso '.$vmpath.'/meta.js'."\n";
        $shell .= "fi\n";
        $shell .= 'if [ -f '.$vmpath.'/disk.qcow2 ]'."\n";
        $shell .= "then\n";
        $shell .= '    mv '.$wavevms.'vm_'.$vm_instanse.'.sh '.$vmpath.'/vm.sh'."\n";
        // $shell .= "    curl 'https://wavephp.com/index.php/apis/receivingstate?hd_status=1'\n";
        $shell .= "fi";

        $filepath = dirname(__FILE__).'/../../vm/';
        if(!is_dir($filepath)){
            mkdir($filepath, 0777);
        }
        $filepath .= $vm_instanse;
        if(!is_dir($filepath)){
            mkdir($filepath, 0777);
        }
        file_put_contents($filepath."/vm_$vm_instanse.sh", $shell);
        $Common->record($vmarr['vm_name'].'生成shell脚本!');

        $ssh_command = "scp -P ".$publicArr['port']." -i ".$publicArr['keyfilename']." -r ".$filepath."/meta_$vm_instanse.js ".$publicArr['pyuser']."@".$pm_ip.":".$wavevms;
        $Common->runCmd($publicArr['ops_server_ip'], $publicArr['ops_ssh_user'], $publicArr['keyfilename'], $ssh_command, $publicArr['port']);
        $Common->record('传递'.$vmarr['vm_name'].'的meta.js!');

        $ssh_command = "scp -P ".$publicArr['port']." -i ".$publicArr['keyfilename']." -r ".$filepath."/vm_$vm_instanse.sh ".$publicArr['pyuser']."@".$pm_ip.":".$wavevms;
        $Common->runCmd($publicArr['ops_server_ip'], $publicArr['ops_ssh_user'], $publicArr['keyfilename'], $ssh_command, $publicArr['port']);
        $Common->record('传递'.$vmarr['vm_name'].'的shell脚本!');

        $ssh_command = "sh ".$wavevms."vm_".$vm_instanse.".sh > /dev/null 2>&1 &";
        $Common->runCmd($pm_ip, $publicArr['pyuser'], $publicArr['keyfilename'], $ssh_command, $publicArr['port']);
        $Common->record('执行'.$vmarr['vm_name'].'的shell脚本!');

        return true;
    }

    /**
     * 创建虚拟机导入文件
     */
    public function VMImportFile($Common, $vmarr, $pmarr, $template)
    {
        $meta = array();
        $set = array();
        $set['netmask'] = $pmarr['public_mask'];
        $set['gateway_v6'] = null;
        $set['name'] = 'eth0';
        $set['dns'] = $pmarr['dns'];
        $set['address'] = $vmarr['vm_ip'];
        $set['gateway'] = $pmarr['public_gateway'];
        $set['netmask_v6'] = null;
        $set['address_v6'] = null;
        $meta['nets'][0] = $set;
        $set = array();
        $set['netmask'] = $pmarr['private_mask'];
        $set['gateway_v6'] = null;
        $set['name'] = 'eth1';
        $set['dns'] = $pmarr['dns'];
        $set['address'] = $vmarr['vm_internel_ip'];
        $set['netmask_v6'] = null;
        $set['address_v6'] = null;
        $meta['nets'][1] = $set;

        if((int)$template['partition'] === 1){
            $meta['script']['home'] = (int)($template['hd']) * (((int)$template['percentage'])/100);
            $meta['script']['data'] = (int)($template['hd']) - $meta['script']['home'];
        }else{
            $meta['script']['home'] = 0;
            $meta['script']['data'] = $template['hd'];
        }
        $metajs = json_encode($meta);

        $metapath = dirname(__FILE__).'/../../vm/';
        if(!is_dir($metapath)){
            mkdir($metapath, 0777);
        }

        $vm_instanse = $vmarr['vm_instanse'];
        $metapath .= $vm_instanse;
        if(!is_dir($metapath)){
            mkdir($metapath, 0777);
        }
        file_put_contents($metapath."/meta_$vm_instanse.js", $metajs);

        $Common->record('写meta文件');

        return true;
    }

    /**
     * 生成虚拟机
     */
    public function generateVM($Common, $pmarr, $vmarr, $templatearr, $isoarr, $publicArr)
    {
        $vmarr['mac_ip'] = $this->generateMac($vmarr['vm_ip']);
        $vmarr['mac_ip_internal'] = $this->generateMac($vmarr['vm_ip_internel']);
        $arr = array_merge($vmarr, $templatearr);
        $xml = $this->generateXml($arr, $publicArr);
        $node_arr = $Common->getOneData('pm', 'pm_ip', 'pm_id', $templatearr['iso_node_id']);
        if(!empty($node_arr)){
            $swap_hd = $Common->swap($templatearr['mem']);
            $vmarr['hd'] = (((int)$templatearr['hd']) + 35 + $swap_hd);
            $vmarr['mem'] = $templatearr['mem'];
            $vmarr['iso_path'] = $isoarr['iso_path'];
            $node_ip = $node_arr['pm_ip'];
            $Common->recode($vmarr['vm_name'].'开始写meta.js文件');
            $this->VMImportFile($Common, $vmarr, $pmarr, $templatearr);
            $Common->recode($vmarr['vm_name'].'开始创建磁盘');
            $res = $this->createHd($Common, $pmarr['pm_ip'], $node_ip, $vmarr, $publicArr);
            if($res == true){
                $Libvirt = new Libvirt($publicArr['qemu'].$pmarr['pm_ip'].$publicArr['libvirt_port']);
                $Libvirt->domain_define($xml);
            }
            return $res;

        }else{

            return '此模板没有选择镜像节点！';
        }
    }


}