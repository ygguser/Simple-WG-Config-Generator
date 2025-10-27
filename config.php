<?php

//checking for accessing this file directly
if ( realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']) ) {
    //exit('Access denied');
    http_response_code(404);
    exit;
}


//The path to the wireguard configuration file on the server.
//For an example: $wg_conf_path = '/etc/wireguard/wg0.conf';
//It is used to get data about the peers that are already in the configuration file (no changes are made to it by these scripts).
//If empty, information about the current peers will simply not be displayed.
//
//Attention! In order for this to work, you also need to give the web server rights to read the /etc/wireguard directory and specifically the configuration file.
//The sequence of commands for this:
//chgrp www-data /etc/wireguard
//chmod g+x /etc/wireguard
//chgrp www-data /etc/wireguard/wg0.conf
//chmod g+r /etc/wireguard/wg0.conf
//
//You also need to allow php to read a file from this directory by changing the value of the open_basedir variable in the php.ini configuration file (if you previously enabled this parameter).
//For an example: open_basedir = /var/www/wgconf/:/etc/wireguard/:/var/log/:/tmp/
$conf_wg_conf_path = '/etc/wireguard/wg0.conf';

// default values
$conf_server_public_address = 'myserver.com';
$conf_server_listen_port = '51820';
$conf_server_peer_ip = '10.0.0.1/24';
$conf_server_private_key = '';
$conf_server_public_key = '';
$conf_server_interface_name = 'ens3';
$conf_server_post_up = "iptables -A FORWARD -i %i -j ACCEPT; iptables -A FORWARD -o %i -j ACCEPT; iptables -t nat -A POSTROUTING -o $conf_server_interface_name -j MASQUERADE";
$conf_server_post_down = "iptables -D FORWARD -i %i -j ACCEPT; iptables -D FORWARD -o %i -j ACCEPT; iptables -t nat -D POSTROUTING -o $conf_server_interface_name -j MASQUERADE";

$conf_client_peer_ip = '10.0.0.2/32';
$conf_client_dns = '94.140.14.14, 94.140.15.15'; //adguard-dns.io/en/public-dns.html
$conf_client_post_up = '';
$conf_client_post_down = '';
$conf_client_allowed_ips = '0.0.0.0/0';
$conf_client_keep_alive = '30';

?>






