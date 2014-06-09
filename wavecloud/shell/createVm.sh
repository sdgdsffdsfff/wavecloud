#!/bin/bash
# 使用shell命令创建虚拟机
VMNAME=0505-1
VMMEM=4194304
VMCPUCORES=2
VMMAC1="52:54:2d:b0:d3:2a"
VMMAC2="52:54:2d:b0:d3:2b"
ISO=centos6.3.qcow2
HD=500G
SWAPHD=8G
TIME=`date +%Y%m%d_%H%M%S_%N`
SCRIPT=/data/shell/mk_${TIME}.sh
XMLFILE=/data/shell/${VMNAME}.xml
IP=192.168.1.2
NODEIP=192.168.1.2
VMDIR=/data/wavevms
VMPATH=/data/wavevms/${VMNAME}
PYUSER=wavevmadmin
PORT=88

cat > ${SCRIPT} << EOF
#!/bin/bash
mkdir -p ${$VMPATH}
if [ -f ${VMPATH}/disk.qcow2 ]
then
    echo "${VMPATH}/disk.qcow2 is have"
    exit 1
else
    if [ ! -f /data/iso/${ISO} ]
    then
        scp -P ${PORT} -i /home/${PYUSER}/.ssh/id_rsa ${PYUSER}@${NODEIP}:/data/iso/${ISO} /data/iso/${ISO}

    cp /data/iso/${ISO} ${$VMPATH}/disk.qcow2
    qemu-img create -f qcow2 -o preallocation=metadata ${$VMPATH}/disk2.qcow2 ${HD}
    qemu-img create -f qcow2 -o preallocation=metadata ${$VMPATH}/swap.qcow2 ${SWAPHD}
fi
rm -f {$VMDIR}/mk_${TIME}.sh
EOF

chmod +x ${SCRIPT}

scp -P ${PORT} -i /root/.ssh/id_rsa -r ${SCRIPT} ${PYUSER}@${IP}:{$VMDIR}
ssh -p ${PORT} -i /root/.ssh/id_rsa ${PYUSER}@${IP} ${VMDIR}mk_${TIME}.sh

cat > ${XMLFILE} << EOF
<domain type='kvm'>
    <name>${VMNAME}</name>
    <memory>${VMMEM}</memory>
    <currentMemory>${VMMEM}</currentMemory>
    <vcpu>${VMCPUCORES}</vcpu>
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
            <source file='{$VMPATH}/disk.qcow2'/>
            <target dev='hda' bus='ide'/>
        </disk>
        <disk type='file' device='disk'>
            <driver name='qemu' type='qcow2'/>
            <source file='{$VMPATH}${VMNAME}/swap.qcow2'/>
            <target dev='hdb' bus='ide'/>
        </disk>
        <disk type='file' device='disk'>
            <driver name='qemu' type='qcow2'/>
            <source file='{$VMPATH}${VMNAME}/disk2.qcow2'/>
            <target dev='hdc' bus='ide'/>
        </disk>
        <interface type='bridge'>
            <source bridge='public'/>
            <mac address='${VMMAC1}'/>
            <filterref>
                <parameter name='IP' value='192.168.1.10' />
            </filterref>
        </interface>
        <interface type='bridge'>
            <source bridge='private'/>
            <mac address='${VMMAC2}'/>
            <filterref>
                <parameter name='IP' value='10.2.2.2' />
            </filterref>
        </interface>
        <input type='mouse' bus='ps2'/>
        <graphics type='vnc' port='-1' autoport='yes' keymap='en-us' listen='0.0.0.0'/>
    </devices>
</domain>
EOF

virsh -c qemu+tls://${IP}:{$PORT}/system define ${XMLFILE}
virsh -c qemu+tls://${IP}:{$PORT}/system start ${VMNAME}


rm -f ${SCRIPT}
rm -f ${XMLFILE}


