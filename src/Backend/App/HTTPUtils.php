<?php
namespace University\GymJournal\Backend\App;

class HTTPUtils
{
    public const OK = 200;
    public const CREATED = 201;
    public const ACCEPTED = 202;
    public const NO_CONTENT = 204;

    public const MOVED_PERMANENTLY = 301;
    public const FOUND = 302;
    public const NOT_MODIFIED = 304;

    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const CONFLICT = 409;
    public const UNPROCESSABLE_ENTITY = 422;

    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    public const BAD_GATEWAY = 502;
    public const SERVICE_UNAVAILABLE = 503;
    public const GATEWAY_TIMEOUT = 504;

    public static function sendHTMLAtPublic(int $code, string $publicPath)
    {
        self::sendHTML($code, file_get_contents(__DIR__.'/../../../public/'.$publicPath));
    }
    public static function sendHTML(int $code, string $html)
    {
        http_response_code($code);
        header('Content-Type: text/html');
        echo $html;
    }
    public static function send404HTML()
    {
        self::sendHTMLAtPublic(self::NOT_FOUND, '404.html');
    }
    public static function sendJson(int $code, $value)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($value);
    }
    public static function sendMessage(int $code, string $message)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'message' => $message
        ]);
    }
    public static function redirectAndDie(int $code, string $path)
    {
        http_response_code($code);
        header('Location: '.$path);
        die();
    }
    public static function assertNotNullDie(&$res, $log_info, $message = 'Internal server error!')
    {
        if($res === null)
        {
            self::sendMessage(HTTPUtils::INTERNAL_SERVER_ERROR, $message);
            Logger::Error($log_info);
            die();
        }
    }

    /**
     * @template T
     * @param T|null $res
     * @param string $log_info
     * @param string $message
     * @return T
     */
    public static function assertNotNullDieReturns($res, $log_info, $message = 'Internal server error!')
    {
        if($res === null)
        {
            self::sendMessage(HTTPUtils::INTERNAL_SERVER_ERROR, $message);
            Logger::Error($log_info);
            die();
        }

        return $res;
    }
}
?>