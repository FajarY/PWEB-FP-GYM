<?php

namespace University\GymJournal\Backend\App;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use stdClass;
use University\GymJournal\Backend\Models\UsersModel;

class JWT
{
    public static ?stdClass $data = null;
    public static ?string $email = null;
    public static ?string $id = null;

    public static function toHour(int $hour) : int
    {
        return $hour * 3600;
    }
    public static function signJWT(array $payload, int $expirySeconds) : ?string
    {
        try
        {
            $payload['exp'] = time() + $expirySeconds;
            $val = FirebaseJWT::encode($payload, $_SERVER['JWT_SECRET'], 'HS512');

            return $val;
        }
        catch(Exception $err)
        {
            Logger::Error($err);
            error_log($err);
        }

        return null;
    }

    //Base
    public static function checkAuthJWT() : bool
    {
        try
        {
            if(!isset(Router::$headers['Authorization']) && !isset($_COOKIE['token']))
            {
                return false;
            }

            $token = '';
            if(isset(Router::$headers['Authorization']))
            {
                $parse = explode(' ', Router::$headers['Authorization']);
                if(count($parse) != 2)
                {
                    HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'Unauthorized!');
                    die();
                }

                $token = $parse[1];
            }
            else if(isset($_COOKIE['token']))
            {
                $token = $_COOKIE['token'];
            }
            else
            {
                return false;
            }

            self::$data = FirebaseJWT::decode($token, new Key($_SERVER['JWT_SECRET'], 'HS512'));

            if(empty(self::$data->email) || empty(self::$data->id))
            {
                setcookie('token', '', time() - self::toHour(12), '/', '', false, true);

                return false;
            }
            self::$email = self::$data->email;
            self::$id = self::$data->id;

            $status = UsersModel::existEmailAndId(self::$email, self::$id);
            if($status === null || !$status)
            {
                if($status === null)
                {
                    Logger::Error('checkAuthJWT() error when checking email and id exist');
                }
                return false;
            }

            return true;
        }
        catch(ExpiredException $err)
        {
            setcookie('token', '', time() - self::toHour(12), '/', '', false, true);

            return false;
        }
        catch(Exception $err)
        {
            Logger::Error($err);
            error_log($err);
            return false;
        }
    }
    public static function checkAuthJWTAndUserVerified() : bool
    {
        if(!self::checkAuthJWT())
        {
            return false;
        }
        $status = UsersModel::isVerified(self::$id);

        if($status === null)
        {
            Logger::Error('checkAuthJWTAndVerified() error when checking user verified');
            return false;
        }

        return $status;
    }

    //Check JWT Only
    public static function checkAuthJWTOrDie()
    {
        if(!self::checkAuthJWT())
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'Unauthorized!');
            die();
        }
    }
    public static function checkAuthJWTOrDieRedirectToAuth()
    {
        if(!self::checkAuthJWT())
        {
            HTTPUtils::redirectAndDie(HTTPUtils::UNAUTHORIZED, '/auth?unauthorized=true');
        }
    }

    //Check JWT & User Verified
    public static function checkAuthJWTAndUserVerifiedOrDie()
    {
        if(!self::checkAuthJWTAndUserVerified())
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'Unauthorized!');
            die();
        }
    }
    public static function checkAuthJWTAndUserVerifiedOrDieRedirectToAuthOrVerified()
    {
        if(!self::checkAuthJWT())
        {
            HTTPUtils::redirectAndDie(HTTPUtils::UNAUTHORIZED, '/auth?unauthorized=true');
        }

        $status = UsersModel::isVerified(self::$id);

        if($status === null)
        {
            Logger::Error('checkAuthJWTAndUserVerifiedOrDieRedirectToAuthOrVerified() error when checking user verified');
            HTTPUtils::redirectAndDie(HTTPUtils::UNAUTHORIZED, '/auth?unauthorized=true');
        }
        else if(!$status)
        {
            HTTPUtils::redirectAndDie(HTTPUtils::UNAUTHORIZED, '/verify?unauthorized=true');
        }
    }
    public static function checkAuthFPDFOr404Die()
    {
        if(!isset(Router::$queries['token']) || Router::$queries['token'] != $_SERVER['FPDF_SECRET'])
        {
            HTTPUtils::send404HTML();
            die();
        }
    }
}

?>