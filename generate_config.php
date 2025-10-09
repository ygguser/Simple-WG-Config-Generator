<?php
// This function is for clearing input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
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

// Generating keys for the server (only if not received)
if (!empty($server_private_key)) {
    $serverPrivateKey = $server_private_key;
} else {
    $serverPrivateKey = shell_exec('wg genkey');
    $serverPrivateKey = trim($serverPrivateKey);
}

if (!empty($server_public_key)) {
    $serverPublicKey = $server_public_key;
} else {
    $serverPublicKey = shell_exec("echo " . escapeshellarg($serverPrivateKey) . " | wg pubkey");
    $serverPublicKey = trim($serverPublicKey);
}

// Generating keys for the client
$clientPrivateKey = shell_exec('wg genkey');
$clientPrivateKey = trim($clientPrivateKey);
$clientPublicKey = shell_exec("echo " . escapeshellarg($clientPrivateKey) . " | wg pubkey");
$clientPublicKey = trim($clientPublicKey);

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
$serverConf .= "AllowedIPs = " . $client_allowed_ips . "\n";

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

// Generating a QR code
$tempFilename = '/tmp/wg_' . uniqid() . '.png';
$command = "echo " . escapeshellarg($clientConf) . " | qrencode -t png -o " . escapeshellarg($tempFilename);
shell_exec($command);

// Read the QR code and encode it in base64
$qrCodeBase64 = '';
if (file_exists($tempFilename)) {
    $qrImage = file_get_contents($tempFilename);
    $qrCodeBase64 = base64_encode($qrImage);
    // Удаляем временный файл
    unlink($tempFilename);
}

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
            <textarea class="config-textarea" readonly><?php echo $clientConf; ?></textarea>
        </div>
        <?php if (!empty($qrCodeBase64)): ?>
        <div class="qr-code">
            <img src="data:image/png;base64,<?php echo $qrCodeBase64; ?>" alt="QR Code for Client Config">
            <p>Scan to import client config</p>
        </div>
        <?php endif; ?>
    </div>
</div>