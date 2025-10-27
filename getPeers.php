<?php

//todo: 
//кнопку воровнять с надписью
//
//обновить репку

require_once('config.php');

$content = file_get_contents($conf_wg_conf_path);
if ($content === false) {
    die("Couldn't read the $filename\n");
}

// Bring newlines to the Unix style
$content = str_replace(["\r\n", "\r"], "\n", $content);

// Splitting the text into lines
$lines = explode("\n", $content);

$inPeer = false;
$peerSection = [];

foreach ($lines as $line) {
    if (preg_match('/^\s*\[Peer\]\s*$/', $line)) {
        // If it is already in the Peer section, output the previous one
        if ($inPeer && !empty($peerSection)) {
            echo implode("\n", $peerSection) . "\n";
            //echo str_repeat('-', 40) . "\n";
            $peerSection = [];
        }
        $inPeer = true;
    }

    if ($inPeer) {
        $peerSection[] = $line;
    }
}

// Output of the last section
if (!empty($peerSection)) {
    echo implode("\n", $peerSection) . "\n";
    //echo str_repeat('-', 40) . "\n";
}
