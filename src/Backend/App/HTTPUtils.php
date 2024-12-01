<?php
namespace University\GymJournal\Backend\App;

class HTTPUtils
{
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
        self::sendHTMLAtPublic(404, '404.html');
    }
}
?>