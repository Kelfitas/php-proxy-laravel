<?php
require './request.php';
set_time_limit(0);

$ip = '127.0.0.1'; 
$port = 8086;

$target_ip = '127.0.0.1';
$target_port = 9000;

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
if (!socket_bind($sock, $ip, $port)) {
    exit('Could not bind to address');
}
socket_listen($sock);

while(true) {
    $client = socket_accept($sock); 
    $input = socket_read($client, 1024);

    $request = new Request($input);
    if (!$request->parse()) {
        echo "Error parsing request";
    }
    print_r($request);

    $url = sprintf('%s://%s:%d%s', 
        'http', 
        $target_ip,
        $target_port,
        $request->url
    );
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_BINARYTRANSFER => 1,
        CURLOPT_HTTP_VERSION => $request->protocol,
        CURLOPT_HTTPHEADER => $request->raw_headers,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);

    socket_write($client, $response);

    echo "received: " . $input . "\n";
}

socket_close($client); 
socket_close($sock);