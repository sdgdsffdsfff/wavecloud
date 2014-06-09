#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

USER=wavevmadmin
KEY=/root/.ssh/id_rsa
PORT=88
SCRIPT=/data/iso/centos6.3.qcow2
#IPLIST=$1
IPLIST=iplist.txt

if [ a"${IPLIST}" = a ]
then
    echo "IPLIST ERROR."
    exit 1
fi

for i in `cat ${IPLIST}`
do
    scp -i $KEY -P $PORT $SCRIPT ${USER}@${i}:/data/iso/
done
