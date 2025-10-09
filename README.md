# Simple WG Config Generator

A very simple web-based generator of [Wireguard](https://www.wireguard.com/) configuration files.

Just two files: [index.html](/index.html) and [generate_config.php](/generate_config.php).

How it looks at work:

![how](https://github.com/user-attachments/assets/1eed7484-f2ec-4588-95f7-ff005d415e6b)

## Dependencies

- php
- wireguard-tools
- qrencode

## Installation

Install the wireguard-tools and qrencode.

An example for Arch:

```
sudo pacman -S wireguard-tools wireguard-tools qrencode
```

Place the files of this project in any directory of your php-enabled web server.

## Using

Navigate index.html, fill in the fields on the form and click **Generate**.

You will see the generated server and client configuration files, as well as a QR code for client.

If you are familiar with html markup, you can specify your default values for fields and then quickly generate new configs for new clients. The `[Peer]` section on the server will have to be added manually, there is no such functionality here. The existing Wireguard configuration on the server is not used here in any way. However, you can specify the public and private keys of the existing server in the fields of the form so that they can be used when creating configs.
