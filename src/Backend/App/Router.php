<?php
namespace University\GymJournal\Backend\App;
use University\GymJournal\Backend\App\Controller;

class Router
{
    public static array $rawUriParsed;
    public static string $rawPath;
    public static string $method;
    public static array $paths;
    public static array $queries;
    public static array $body;
    public static array $headers;

    private static ?Controller $now = null;
    
    public static function load()
    {
        self::$rawUriParsed = parse_url($_SERVER['REQUEST_URI']);
        self::$rawPath = self::$rawUriParsed['path'];
        self::$method = $_SERVER['REQUEST_METHOD'];

        self::$paths = explode('/', self::$rawPath);

        self::$queries = [];
        if(isset(self::$rawUriParsed['query']))
        {
            parse_str(self::$rawUriParsed['query'], self::$queries);
        }

        $trimed = [];
        for($i = 0; $i < count(self::$paths); $i++)
        {
            $current = trim(self::$paths[$i]);
            if($current === '')
            {
                continue;
            }

            $trimed[] = $current;
        }
        self::$paths = $trimed;
        self::$headers = getallheaders();

        self::$body = [];
        $bodyStream = file_get_contents('php://input');
        if($bodyStream)
        {
            self::$body = json_decode($bodyStream);
        }
    }
    public static function use(Controller $controller)
    {
        $call = '/';
        self::$now = $controller;

        $controller->load();

        for($i = 0; $i < count(self::$paths); $i++)
        {
            $currentPath = '/'.self::$paths[$i];
            $routing = self::$now->routes[self::$method];

            $item = $routing[$currentPath];

            if($item instanceof Controller)
            {
                $item->load();
                self::$now = $item;
                $call = '/';
            }
            else if($i == (count(self::$paths) - 1))
            {
                if(is_callable($item))
                {
                    $call = $currentPath;
                    break;
                }
                else
                {
                    self::$now = null;
                    break;
                }
            }
            else
            {
                self::$now = null;
                break;
            }
        }

        if(self::$now === null)
        {
            HTTPUtils::send404HTML();
            return;
        }

        $function = self::$now->routes[self::$method][$call];

        if(is_callable($function))
        {
            call_user_func($function);
        }
        else
        {
            HTTPUtils::send404HTML();
        }
    }
}
?>