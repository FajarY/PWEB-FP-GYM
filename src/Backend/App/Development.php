<?php

namespace University\GymJournal\Backend\App;

class Development
{
    public static function isEnableDevelopment() : bool
    {
        return $_SERVER['ALLOW_DEVELOPMENT'];
    }
    public static function validateDevelopmentSecretOrDie404()
    {
        
        if(!isset(Router::$headers['Authorization']))
        {
            HTTPUtils::send404HTML();
            die();
        }
        $parsed = explode(' ', Router::$headers['Authorization']);
        if(count($parsed) < 2)
        {
            HTTPUtils::send404HTML();
            die();
        }

        if($parsed[1] === $_SERVER['DEVELOPMENT_SECRET'])
        {
            return;   
        }
        HTTPUtils::send404HTML();
        die();
    }
}

?>