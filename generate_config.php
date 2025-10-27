<?php

// This function is for clearing input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

// WireGuard key generation using libsodium
function generate_wireguard_keys() {
    // Generate private key (32 random bytes)
    $privateKey = random_bytes(SODIUM_CRYPTO_BOX_SECRETKEYBYTES);
    
    // Derive public key from private key using Curve25519
    $publicKey = sodium_crypto_box_publickey_from_secretkey($privateKey);
    
    // Convert to base64 (WireGuard format)
    return [
        'private' => base64_encode($privateKey),
        'public' => base64_encode($publicKey)
    ];
}

// Validate WireGuard key format
function validate_wireguard_key($key) {
    if (empty($key)) return false;
    
    // WireGuard keys are base64 encoded 32-byte values
    $decoded = base64_decode($key, true);
    if ($decoded === false) return false;
    
    return strlen($decoded) === 32;
}

// Clearing all input parameters
$server_public_address = sanitize_input($_POST['server_public_address'] ?? '');
$server_listen_port = sanitize_input($_POST['server_listen_port'] ?? '');
$server_peer_ip = sanitize_input($_POST['server_peer_ip'] ?? '');
$server_private_key = sanitize_input($_POST['server_private_key'] ?? '');
$server_public_key = sanitize_input($_POST['server_public_key'] ?? '');
$server_post_up = sanitize_input($_POST['server_post_up'] ?? '');
$server_post_down = sanitize_input($_POST['server_post_down'] ?? '');
$client_peer_ip = sanitize_input($_POST['client_peer_ip'] ?? '');
$client_dns = sanitize_input($_POST['client_dns'] ?? '');
$client_post_up = sanitize_input($_POST['client_post_up'] ?? '');
$client_post_down = sanitize_input($_POST['client_post_down'] ?? '');
$client_allowed_ips = sanitize_input($_POST['client_allowed_ips'] ?? '');
$client_keep_alive = sanitize_input($_POST['client_keep_alive'] ?? '');

// Generating keys for the server (only if not received and valid)
if (!empty($server_private_key) && validate_wireguard_key($server_private_key)) {
    $serverPrivateKey = $server_private_key;
    
    // If private key is provided but public key is not, derive it
    if (empty($server_public_key) || !validate_wireguard_key($server_public_key)) {
        $privateKeyBinary = base64_decode($server_private_key);
        $publicKeyBinary = sodium_crypto_box_publickey_from_secretkey($privateKeyBinary);
        $serverPublicKey = base64_encode($publicKeyBinary);
    } else {
        $serverPublicKey = $server_public_key;
    }
} else {
    // Generate new server keys
    $serverKeys = generate_wireguard_keys();
    $serverPrivateKey = $serverKeys['private'];
    $serverPublicKey = $serverKeys['public'];
}

// Generating keys for the client
$clientKeys = generate_wireguard_keys();
$clientPrivateKey = $clientKeys['private'];
$clientPublicKey = $clientKeys['public'];


// Creating the server configuration
$serverConf = "[Interface]\n";
$serverConf .= "Address = " . $server_peer_ip . "\n";
$serverConf .= "PrivateKey = " . $serverPrivateKey . "\n";
$serverConf .= "ListenPort = " . $server_listen_port . "\n";

if (!empty($server_post_up)) {
    $serverConf .= "PostUp = " . $server_post_up . "\n";
}

if (!empty($server_post_down)) {
    $serverConf .= "PostDown = " . $server_post_down . "\n";
}

$serverConf .= "\n[Peer]\n";
$serverConf .= "PublicKey = " . $clientPublicKey . "\n";
$serverConf .= "AllowedIPs = " . $client_peer_ip . "\n";

// Creating the client configuration
$clientConf = "[Interface]\n";
$clientConf .= "Address = " . $client_peer_ip . "\n";
$clientConf .= "PrivateKey = " . $clientPrivateKey . "\n";
$clientConf .= "DNS = " . $client_dns . "\n";

if (!empty($client_post_up)) {
    $clientConf .= "PostUp = " . $client_post_up . "\n";
}

if (!empty($client_post_down)) {
    $clientConf .= "PostDown = " . $client_post_down . "\n";
}

$clientConf .= "\n[Peer]\n";
$clientConf .= "PublicKey = " . $serverPublicKey . "\n";
$clientConf .= "Endpoint = " . $server_public_address . ":" . $server_listen_port . "\n";
$clientConf .= "AllowedIPs = " . $client_allowed_ips . "\n";
$clientConf .= "PersistentKeepAlive = " . $client_keep_alive . "\n";

// Return the HTML with the results 
?>
<div class="result-section">
    <h3>Server config</h3>
    <textarea class="config-textarea" readonly><?php echo $serverConf; ?></textarea>
</div>

<div class="result-section">
    <h3>Client config</h3>
    <div class="client-container">
        <div class="client-config">
            <textarea id="clientconf" class="config-textarea" readonly><?php echo $clientConf; ?></textarea>
        </div>
        <div class="qr-code">
            <canvas id="qr"></canvas>
            <p>Scan to import client config</p>
        </div>
    </div>
</div>
