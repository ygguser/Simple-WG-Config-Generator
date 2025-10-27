<?php 
if (!extension_loaded('sodium')) { 
    die('Error: Sodium extension is required. Please install php-sodium package.'); 
} 
require_once('config.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <link rel="shortcut icon" type="image/png" href="wg-ico.png" sizes="256x256">
    <link rel="apple-touch-icon" type="image/png" href="wg-ico.png" sizes="256x256">
    <title>WG Config Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-sections {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
        }
        .form-section {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .form-section h2 {
            margin-top: 0;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
            min-height: 60px;
        }
        button {
            background-color: #007cba;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 20px auto;
        }
        button:hover {
            background-color: #005a87;
        }
        #result {
            margin-top: 30px;
            display: none;
        }
        /* Styles for results */
        .result-section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .result-section h3 {
            margin-top: 0;
            color: #333;
        }
        .config-textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
            background-color: #f8f8f8;
        }
        .client-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .client-config {
            flex: 1;
        }
        .qr-code {
            flex: 0 0 auto;
            text-align: center;
        }
        .qr-code img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .qr-code p {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        .error {
            color: red;
            padding: 10px;
            background-color: #ffe6e6;
            border: 1px solid red;
            border-radius: 4px;
        }
        /* optional fields style */
        .optional-field {
            opacity: 0.8;
            /* background-color: #f8f8f8; */
        }
        /* Styles for the button to show current peers */
        .peers-toggle-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
            height: auto;
            margin: 0;
            transform: translateY(-1px);
        }
        .peers-toggle-btn:hover {
            background-color: #5a6268;
        }
        .current-peers-container {
            display: none;
            padding: 8px;
            margin-top: 6px; /* немного отступа снизу от label */
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
        .current-peers-container.visible {
            display: block;
        }
        .label-with-button {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin-bottom: 0px;
        }
        /* Adaptability for mobile devices */
        @media (max-width: 768px) {
            .form-sections {
                flex-direction: column;
            }
            .client-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WG Config Generator</h1>
        <form id="configForm">
            <div class="form-sections">
                <div class="form-section">
                    <h2>Server</h2>
                    <div class="form-group">
                        <label for="server_public_address">Public Address:</label>
                        <input type="text" id="server_public_address" name="server_public_address" value="<?php echo "$conf_server_public_address"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="server_listen_port">Listen Port:</label>
                        <input type="text" id="server_listen_port" name="server_listen_port" value="<?php echo "$conf_server_listen_port"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="server_peer_ip">Peer IP:</label>
                        <input type="text" id="server_peer_ip" name="server_peer_ip" value="<?php echo "$conf_server_peer_ip"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="server_post_up">Post-Up Rule:</label>
                        <textarea id="server_post_up" name="server_post_up"><?php echo "$conf_server_post_up"; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="server_post_down">Post-Down Rule:</label>
                        <textarea id="server_post_down" name="server_post_down"><?php echo "$conf_server_post_down"; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="server_private_key">Private Key:</label>
                        <input type="text" id="server_private_key" name="server_private_key" class="optional-field" placeholder="Leave empty to generate" value="<?php echo "$conf_server_private_key"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="server_public_key">Public Key:</label>
                        <input type="text" id="server_public_key" name="server_public_key" class="optional-field" placeholder="Leave empty to generate" value="<?php echo "$conf_server_public_key"; ?>">
                    </div>
                </div>
                <div class="form-section">
                    <h2>Client</h2>
                    <div class="form-group">
                        <div class="label-with-button">
                            <label for="client_peer_ip">Peer IP:</label>
                            <?php if(!empty($conf_wg_conf_path)) echo "<button type=\"button\" id=\"toggleCurrentPeers\" class=\"peers-toggle-btn\" title=\"Show the ip addresses that are already in use\">Show current peers</button>"; ?>
                        </div>
                        <div id="currentPeersContainer" class="current-peers-container"></div>
                        <input type="text" id="client_peer_ip" name="client_peer_ip" value="<?php echo "$conf_client_peer_ip"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="client_dns">DNS:</label>
                        <input type="text" id="client_dns" name="client_dns" value="<?php echo "$conf_client_dns"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="client_allowed_ips">Allowed IPs:</label>
                        <input type="text" id="client_allowed_ips" name="client_allowed_ips" value="<?php echo "$conf_client_allowed_ips"; ?>">
                    </div>
                    <div class="form-group">
                        <label for="client_post_up">Post-Up Rule:</label>
                        <textarea id="client_post_up" name="client_post_up"><?php echo "$conf_client_post_up"; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="client_post_down">Post-Down Rule:</label>
                        <textarea id="client_post_down" name="client_post_down"><?php echo "$conf_client_post_down"; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="client_keep_alive">Keep Alive:</label>
                        <input type="text" id="client_keep_alive" name="client_keep_alive" value="<?php echo "$conf_client_keep_alive"; ?>">
                    </div>
                </div>
            </div>
            <button type="submit">Generate</button>
        </form>
        <div id="result"></div>
    </div>

    <script>
        // A function for switching the display of current peers
        document.getElementById('toggleCurrentPeers').addEventListener('click', function() {
            const container = document.getElementById('currentPeersContainer');
            const button = this;
            
            if (container.classList.contains('visible')) {
                // hide container
                container.classList.remove('visible');
                button.textContent = 'Show current peers';
                button.title = 'Show current peers';
            } else {
                // show container and load data
                if (container.innerHTML === '') {
                    // load data only if the container is empty
                    fetch('getPeers.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(data => {
                            if (data.trim() === '') {
                                container.innerHTML = 'No peers found.';
                            } else {
                                const curr_peers = data.trim().split('\n');
                                container.innerHTML = '<strong>Current peers:</strong><br>' + curr_peers.map(peer => `${peer}`).join('<br>');
                            }
                            container.classList.add('visible');
                            button.textContent = 'Hide current peers';
                            button.title = 'Hide current peers';
                        })
                        .catch(error => {
                            container.innerHTML = '<span style="color: red;">Error loading peers: ' + error + '</span>';
                            container.classList.add('visible');
                            button.textContent = 'Hide current peers';
                            button.title = 'Hide current peers';
                        });
                } else {
                    // If the data has already been loaded, just show the container
                    container.classList.add('visible');
                    button.textContent = 'Hide current peers';
                    button.title = 'Hide current peers';
                }
            }
        });

         document.getElementById('configForm').addEventListener('submit', function(e) {
             e.preventDefault();
             const formData = new FormData(this);
             const resultDiv = document.getElementById('result');
            
             fetch('generate_config.php', {
                 method: 'POST',
                 body: formData
             })
             .then(response => {
                 if (!response.ok) {
                     throw new Error('Network response was not ok');
                 }
                 return response.text();
             })
             .then(html => {
                 resultDiv.innerHTML = html;
                 resultDiv.style.display = 'block';
                 (function() {
                     var qr = new QRious({
                         element: document.getElementById('qr'),
                         value: document.getElementById("clientconf").value,
                         size: 195
                     });
                 })();
                 document.getElementById('result').scrollIntoView({
                     behavior: 'smooth',
                     block: 'start'
                 });
             })
             .catch(error => {
                 resultDiv.innerHTML = '<div class="error">Error: ' + error + '</div>';
                 resultDiv.style.display = 'block';
                 document.getElementById('result').scrollIntoView({
                     behavior: 'smooth',
                     block: 'start'
                 });
             });
         });
    </script>
</body>
</html>
