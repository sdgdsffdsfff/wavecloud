#!/bin/bash 
sum=0
function quyun() {
for i in  $1 
  do 
      
    for  j   in ` ls  $i   | grep  disk `
            do
       var=$(qemu-img  info  "$i/$j"   |  sed  -n '3p' | awk  -F"[ :]"  '{print $4 }' |  grep -o '[0-9]\+' )
         let sum=$sum+$var
           done 
        done 
        sum=$sum
}
function qinyun() {
for i in  $1 
  do 
      
    for  j   in ` ls  $i   | grep  qcow2 `
            do
       var=$(qemu-img  info  "$i/$j"   |  sed  -n '3p' | awk  -F"[ :]"  '{print $4 }' |  grep -o '[0-9]\+' )
         let sum=$sum+$var
           done 
        done 
        sum=$sum
}
function kvm() {
for i in  $1 
  do 
      
       var=$(qemu-img  info  /data/vm/$i   |  sed  -n '3p' | awk  -F"[ :]"  '{print $4 }' |  grep -o '[0-9]\+' )
         let sum=$sum+$var
           done 
        sum=$sum
}
IS_BR=`ifconfig | grep "^em" | wc -l `
if [ $IS_BR -eq 0 ];then
    NET="eth"
else
    NET="em"
fi

if [ $NET == "em" ];then
    NET_NUM_1=1
    NET_NUM_2=2
    NET_NUM_3=3
else
    NET_NUM_1=0
    NET_NUM_2=1
    NET_NUM_3=2
fi
IP=`curl http://server.qyops.com/api/getserverinfo.php?output=1`
PUBLIC_MASK=`ifconfig    $NET$NET_NUM_1 |  grep "Mask" | awk -F":" '{print $4}'`
GATEWAY=`route -n | grep "^0.0.0.0"  |   awk '{print $2 }'`

LOCALIP=`ifconfig em1:1 | grep "inet addr:" | awk -F":" '{print $2 }' | grep -o "[0-9\.]\+"`

PRIVATE_IP=`ifconfig private | grep "inet addr:" | awk -F":" '{print $2 }' | grep -o "[0-9\.]\+"`
PRIVATE_MASK=`ifconfig private  |  grep "Mask" | awk -F":" '{print $4}'`

#get quyum  VM disk_total
dir=`ls -d  /data/instances/instance* 2>/dev/null`
  if [ "x"$? = "x0" ];then
        for i in $dir 
       do   
             quyun $i
        done
  fi
# 

#get  qinyun   disk_total
dir=`ls -d  /data/qyinstances/instance*  2>/dev/null`
  if [ "x"$? = "x0" ];then
        for i in $dir 
       do   
             qinyun  $i
        done
  fi

#get  KVM   disk_total
dir=`ls /data/vm  2>/dev/null`
  if [  "x"$? = "x0" ];then
     for   i in  $dir 
            do  kvm $i
        done
    fi 

#get cpu count
cpu_count=`cat /proc/cpuinfo | grep "model name" | wc -l`



#get mem count
mem_count=`cat /proc/meminfo | grep "MemTotal" | awk '{print $2}'`
mem_count=`expr \$mem_count / 1000 / 1000`

#free_mem
free_mem=` free  -g  |  sed -n '3p' |  awk  '{print $4  }'`



#used_memory 
used_mem=` free  -g  |  sed -n '3p' |  awk  '{print $3  }'`


#plan_memory
plan_meory=0
LOCATION=/etc/libvirt/qemu/
for  i in   ` ls $LOCATION | grep xml `
do
     var=`grep currentMemory $LOCATION$i  | awk -F"[<>]" '{print $3}'`
      let  plan_memory=$plan_memory+$var
    done
        plan_memory=`expr \$plan_memory / 1024 / 1024`



#plan_free_mem
plan_free_mem=`expr \$mem_count - \$plan_memory `
#get hd count
disk_list=`cat /proc/partitions | sed -n '/.d.$/p' | awk '{print $3}'`
disk_count=""
for i in `echo $disk_list`
do
    i=`expr \$i / 1000 / 1000`
    if [ $disk_count"x" = "x" ];then
        disk_count=$i
    else
       disk_count=`expr \$disk_count + $i`
    fi
done




tmp=`df | grep -v  '/dev/shm' |  sed  '1d' | awk  '{print $4}'`
total=0
 for i  in $tmp
    do
        let total=$total+$i
done

   avail_disk=`expr \$total / 1024 / 1024 `
# DNS
# DNS=`cat /etc/resolv.conf |awk '{print $2}' | sed -n '1p'`
DNS='8.8.8.8'


#
#echo   "plan_surplus:`let $disk_count-$sum`"
let avariable_disk=$disk_count-$sum
echo   "CPU numbers: $cpu_count"
echo  "VM disk_toal: $sum G"
echo   "Disk       : $disk_count G"
echo   "IP         : $IP"
echo   "public mask: $PUBLIC_MASK"
echo   "gateway    : $GATEWAY"
echo   "private ip : $PRIVATE_IP"
echo   "private mask $PRIVATE_MASK"
echo   "DNS        : $DNS"
echo   "plan_surplus: $avariable_disk G"
echo   "cs_memory  : $mem_count G"
echo   "free_memory: $free_mem G"
#echo   "used_memory: $used_mem G"
echo   "avail_disk : $avail_disk G"
echo   "plan_free_mem: $plan_free_mem G"
curl -k -d "public_mask=$PUBLIC_MASK" \
        -d "public_gateway=$GATEWAY" \
        -d "dns=$DNS" \
        -d "localnet_ip=$LOCALIP" \
        -d "private_ip=$PRIVATE_IP" \
        -d "private_mask=$PRIVATE_MASK" \
        -d "cs_hd=$disk_count" \
        -d "actual_free_hd=$avail_disk" \
        -d "plan_free_hd=$avariable_disk" \
        -d "cpu_cores=$cpu_count" \
        -d "ip=$IP" \
        -d "cs_mem=$mem_count" \
        -d "actual_free_mem=$free_mem" \
        -d "plan_free_mem=$plan_free_mem" \
https://wavephp.com/apis/mergecsinfo

