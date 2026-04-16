<?php
header('Content-Type: application/json');

$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    echo json_encode(['online' => false, 'msg' => 'Invalid IP address']);
    exit;
}

@fsockopen('tcp://' . $ip, 10703, $errno, $errstr, 2.0);

if ($errno === 0) {
    echo json_encode(['online' => true, 'msg' => 'Online — ready to receive a game']);
} elseif ($errno === 111) {
    echo json_encode(['online' => true, 'msg' => 'Online — game running (port 10703 busy)']);
} else {
    echo json_encode(['online' => false, 'msg' => 'Offline — ' . htmlspecialchars($errstr, ENT_QUOTES, 'UTF-8') . ' (errno ' . $errno . ')']);
}
