<?php

namespace Jensovic\DbMySyncAddin;

class Response
{
    /**
     * Send JSON success response
     */
    public static function success($data, int $code = 200): void
    {
        self::send([
            'success' => true,
            'data' => $data
        ], $code);
    }

    /**
     * Send JSON error response
     */
    public static function error(string $message, int $code = 400): void
    {
        self::send([
            'success' => false,
            'error' => $message
        ], $code);
    }

    private static function send(array $payload, int $code): void
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            error_log('DbMySync: json_encode failed: ' . json_last_error_msg());
            $code = 500;
            $json = '{"success":false,"error":"Internal server error"}';
        }

        http_response_code($code);
        header('Content-Type: application/json');
        echo $json;
        exit;
    }
}

