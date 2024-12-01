<?php
namespace University\GymJournal\Backend\App;

use Error;

class Controller
{
    public array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    public final function get($route, $item)
    {
        if(!($item instanceof Controller || is_callable($item)) || !isset($route))
        {
            throw new Error('Invalid item!');
        }

        $this->routes['GET'][$route] = $item;
    }
    public final function post($route, $item)
    {
        if(!($item instanceof Controller || is_callable($item)) || !isset($route))
        {
            throw new Error('Invalid item!');
        }

        $this->routes['POST'][$route] = $item;
    }
    public final function put($route, $item)
    {
        if(!($item instanceof Controller || is_callable($item)) || !isset($route))
        {
            throw new Error('Invalid item!');
        }

        $this->routes['PUT'][$route] = $item;
    }
    public final function del($route, $item)
    {
        if(!($item instanceof Controller || is_callable($item)) || !isset($route))
        {
            throw new Error('Invalid item!');
        }

        $this->routes['DELETE'][$route] = $item;
    }
    public final function use($route, $item)
    {
        if(!($item instanceof Controller || is_callable($item)) || !isset($route))
        {
            throw new Error('Invalid item!');
        }

        $this->routes['GET'][$route] = $item;
        $this->routes['POST'][$route] = $item;
        $this->routes['PUT'][$route] = $item;
        $this->routes['DELETE'][$route] = $item;
    }
    public function load()
    {
        
    }
}
?>