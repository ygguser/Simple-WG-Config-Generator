<?php

// default values
$server_public_address = 'myserver.com';
$server_listen_port = '51820';
$server_peer_ip = '10.0.0.1/24';
$server_private_key = '';
$server_public_key = '';
$server_post_up = 'iptables -A FORWARD -i %i -j ACCEPT; iptables -A FORWARD -o %i -j ACCEPT; iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE';
$server_post_down = 'iptables -D FORWARD -i %i -j ACCEPT; iptables -D FORWARD -o %i -j ACCEPT; iptables -t nat -D POSTROUTING -o eth0 -j MASQUERADE';

$client_peer_ip = '10.0.0.2/32';
$client_dns = '94.140.14.15, 94.140.15.16'; //adguard-dns.io/en/public-dns.html
$client_post_up = '';
$client_post_down = '';
$client_allowed_ips = '0.0.0.0/0';
$client_keep_alive = '30';

?>

