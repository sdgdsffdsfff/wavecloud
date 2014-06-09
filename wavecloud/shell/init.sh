#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

USER=root
KEY=/root/.ssh/id_rsa
PORT=88
SCRIPT=mergecsinfo.sh
VMDIR=/data/wavevms/
SCRIPT2=libvirt_pki.sh
IPLIST=iplist.txt
SENDINFO=sendinfo.sh

if [ a"${IPLIST}" = a ]
then
    echo "IPLIST ERROR."
    exit 1
fi

for i in `cat ${IPLIST}`
do
    ./keys.exp ${i}
done

for i in `cat ${IPLIST}`
do
    scp -i $KEY -P $PORT $SCRIPT ${USER}@${i}:${VMDIR}
    ssh -i $KEY -p $PORT ${USER}@${i} "chmod +x ${VMDIR}${SCRIPT} && sh ${VMDIR}${SCRIPT}"
done

for i in `cat ${IPLIST}`
do
    scp -i $KEY -P $PORT $SENDINFO ${USER}@${i}:${VMDIR}
    ssh -i $KEY -p $PORT ${USER}@${i} "chmod +x ${VMDIR}${SENDINFO}; echo '*/5 * * * *      ${VMDIR}sendinfo.sh' >> /var/spool/cron/root"
    scp -i $KEY -P $PORT $SCRIPT2 ${USER}@${i}:${VMDIR}
    ssh -i $KEY -p $PORT ${USER}@${i} "chmod +x ${VMDIR}${SCRIPT2} && sh ${VMDIR}${SCRIPT2}; sed -i '/wavephp.com/d' /root/.ssh/.keys"
done