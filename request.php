<?php

class Request {
    private $_raw_input;
    public $method;
    public $url;
    public $protocol;
    public $headers;
    public $body = '';

    public function __construct($raw_input) {
        $this->_raw_input = $raw_input;
    }

    public function parse(): bool {
        // GET /robots.txt HTTP/1.1
        // Host: 127.0.0.1:8086
        // Connection: keep-alive
        // User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36
        // Accept: */*
        // DNT: 1
        // Accept-Encoding: gzip, deflate, br
        // Accept-Language: en-US,en;q=0.8
        $lines = explode("\r\n", $this->_raw_input);
        if (count($lines) === 0) {
            return false;
        }

        list($this->method, $this->url, $this->protocol) = explode(" ", $lines[0]);

        for ($i = 1; $i < count($lines); $i++) { // ignore first line, we handled that
            if (strpos($lines[$i], ':')) {
                $this->raw_headers[] = $lines[$i];
            } else {
                $this->body .= $lines[$i];
                if ($i < count($lines) - 1) {
                    $this->body .= "\r\n";
                }
            }
        }

        return true;
    }
}