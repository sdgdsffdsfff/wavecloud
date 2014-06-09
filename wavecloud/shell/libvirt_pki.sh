#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
IP=192.168.1.2

mkdir -p /etc/pki/CA/
mkdir -p /etc/pki/libvirt/private/

cat > /etc/pki/server.info << EOF
organization = wavecloud
cn = ${IP}
tls_www_server
encryption_key
signing_key
EOF

cat > /etc/pki/CA/cacert.pem << EOF
-----BEGIN CERTIFICATE-----
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
-----END CERTIFICATE-----
EOF

cat > /etc/pki/CA/cakey.pem << EOF
-----BEGIN RSA PRIVATE KEY-----
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
-----END RSA PRIVATE KEY-----
EOF

certtool --generate-privkey > /etc/pki/libvirt/private/serverkey.pem
certtool --generate-certificate --load-privkey /etc/pki/libvirt/private/serverkey.pem \
         --load-ca-certificate /etc/pki/CA/cacert.pem --load-ca-privkey /etc/pki/CA/cakey.pem \
         --template /etc/pki/server.info --outfile /etc/pki/libvirt/servercert.pem

#sed -i '/^listen_tls/ {s/.*/listen_tls = 1/}' /etc/libvirt/libvirtd.conf
#sed -i '/^tls_port/ {s/.*/tls_port = "59879"/}' /etc/libvirt/libvirtd.conf
mv /etc/libvirt/libvirtd.conf /etc/libvirt/libvirtd.conf.bak_`date +"%F_%T"`
cat > /etc/libvirt/libvirtd.conf << EOF
listen_tls = 1
tls_port = "59879"
EOF

#sed -i '/^LIBVIRTD_ARGS/ {s/.*/LIBVIRTD_ARGS="--listen"/}' /etc/sysconfig/libvirtd
mv /etc/sysconfig/libvirtd /etc/sysconfig/libvirtd.bak_`date +"%F_%T"`
cat > /etc/sysconfig/libvirtd << EOF
LIBVIRTD_ARGS="--listen"
EOF
/etc/init.d/libvirtd restart
