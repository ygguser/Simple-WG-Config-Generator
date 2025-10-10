# Simple WG Config Generator

A very simple web-based generator of [Wireguard](https://www.wireguard.com/) configuration files.

How it looks at work:

![how](https://github.com/user-attachments/assets/1eed7484-f2ec-4588-95f7-ff005d415e6b)

## Dependencies

- php-sodium

## Installation

Checking if sodium is installed:

```
php -m | grep sodium
```

If you see `sodium` in the output, it means that it is already installed.

If the output is empty, install it:

Debian/Ubuntu: `sudo apt update && sudo apt install php-sodium -y`

Place the files of this project in any directory of your php-enabled web server.

[Optional] Make the web server the owner of the files:

```
chown -R www-data:www-data /var/www/wgconf # /var/www/wgconf is a directory with project files
```

## Using

Navigate `index.php`, fill in the fields on the form and click **Generate**.

You will see the generated server and client configuration files, as well as a QR code for client.

You can specify your default values in `config.php` for fields and then quickly generate new configs for new clients. The `[Peer]` section on the server will have to be added manually, there is no such functionality here. The existing Wireguard configuration on the server is not used here in any way. However, you can specify the public and private keys of the existing server in the fields of the form (or in config.php) so that they can be used when creating configs.

## Recommendations

This project can be used as a means of helping the Wireguard server administrator. I do not recommend opening public access to this service due to the fact that relatively sensitive data appears here: the server address and its keys. Use this on a private network or secure access with at least basic web server authentication.

An example of a simple nginx configuration:
```
server {
    listen 10.0.0.1:80; # The site will only work on the internal network

    auth_basic "Enter password!"; # Basic authentication
    auth_basic_user_file /etc/nginx/conf.d/.htpasswd; # generate it with apache2-utils: sudo htpasswd -c /etc/nginx/conf.d/.htpasswd admin

    autoindex off;
    root /var/www/wgconf;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        include snippets/fastcgi-php.conf;
        fastcgi_read_timeout 300;
    }
}
```
