<?php

//checking for accessing this file directly
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) ) {
    //exit('Access denied');
    http_response_code(404);
    exit;
}

// default values
$server_public_address = 'myserver.com';
$server_listen_port = '51820';
$server_peer_ip = '10.0.0.1/24';
$server_private_key = '';
$server_public_key = '';
$server_interface_name = 'ens3';
$server_post_up = "iptables -A FORWARD -i %i -j ACCEPT; iptables -A FORWARD -o %i -j ACCEPT; iptables -t nat -A POSTROUTING -o $server_interface_name -j MASQUERADE";
$server_post_down = "iptables -D FORWARD -i %i -j ACCEPT; iptables -D FORWARD -o %i -j ACCEPT; iptables -t nat -D POSTROUTING -o $server_interface_name -j MASQUERADE";

$client_peer_ip = '10.0.0.2/32';
$client_dns = '94.140.14.14, 94.140.15.15'; //adguard-dns.io/en/public-dns.html
$client_post_up = '';
$client_post_down = '';
$client_allowed_ips = '0.0.0.0/0';
$client_keep_alive = '30';

?>




