<?php

namespace University\GymJournal\Backend\App;

use Exception;
use Google\Service\Oauth2 as GoogleOAuth;
use Google\Client as GoogleClient;
use InvalidArgumentException;

class OAuth
{
    public static ?bool $active = null;

    public static ?GoogleClient $googleClient = null;
    public static ?GoogleOAuth $googleOAuth = null;

    public static function load()
    {
        self::$googleClient = new GoogleClient();

        self::$googleClient->setClientId($_SERVER['GOOGLE_AUTH_CLIENT_ID']);
        self::$googleClient->setClientSecret($_SERVER['GOOGLE_AUTH_CLIENT_SECRET']);
        self::$googleClient->setRedirectUri($_SERVER['GOOGLE_AUTH_REDIRECT_URI']);

        self::$googleClient->addScope('email');

        self::$active = true;
    }
    public static function createGoogleAuthURL() : ?string
    {
        if(self::$active === null)
        {
            self::load();
        }
        if(!self::$active)
        {
            return null;
        }
        
        try
        {
           $url = self::$googleClient->createAuthUrl();

           return $url;
        }
        catch(Exception $err)
        {
            error_log($err);
            Logger::Error($err);
        }

        return null;
    }
    public static function completeGoogleAuth(string $code) : ?bool
    {
        if(self::$active === null)
        {
            self::load();
        }
        if(!self::$active)
        {
            return null;
        }

        if(!isset($code))
        {
            return false;
        }
        try
        {
            $token = self::$googleClient->fetchAccessTokenWithAuthCode($code);
            self::$googleClient->setAccessToken($token);

            self::$googleOAuth = new GoogleOAuth(self::$googleClient);
            return true;
        }
        catch(InvalidArgumentException $args)
        {
            return false;
        }
        catch(Exception $err)
        {
            error_log($err);
            Logger::Error($err);
        }

        return null;
    }
}

?>