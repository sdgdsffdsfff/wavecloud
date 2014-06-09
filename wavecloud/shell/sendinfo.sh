#!/bin/bash 
IP=`curl http://server.qyops.com/api/getserverinfo.php?output=1`
CPU_USE=`cat /proc/stat | grep cpu | head -1 | awk '{sum=$2+$3+$4+$5+$6+$7+$8;use=$2+$3+$4+$6+$7+$8;printf "%.2f\n",(use*100/sum)}'`
MEM_USE=`free | grep Mem: | awk '{use=$3-$6-$7;printf "%.2f\n",(use*100/$2)}'`

curl -k -d "cpu_use=$CPU_USE" -d "mem_use=$MEM_USE"  -d "ip=$IP" 'https://server.qyops.com/apis/mergecsinfo?from=1'
