<?php

namespace University\GymJournal\Backend\Controller\API;

use PDO;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Development;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\OAuth;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Models\UsersModel;
use University\GymJournal\Backend\View\API\Auth\CompleteView;
use University\GymJournal\Backend\App\Image;
use University\GymJournal\Backend\App\Logger;

class AuthAPIController extends Controller
{
    public function complete()
    {
        if(empty(Router::$queries['code']))
        {
            HTTPUtils::redirectAndDie(HTTPUtils::BAD_REQUEST, "/auth?fail=true&error='code_empty'");
        }

        $status = OAuth::completeGoogleAuth(Router::$queries['code']);
        if($status === null || !$status)
        {
            HTTPUtils::redirectAndDie(HTTPUtils::BAD_REQUEST, "/auth?fail=true&error='code_invalid'");
        }

        $info = OAuth::$googleOAuth->userinfo->get();

        if(empty($info->email))
        {
            HTTPUtils::redirectAndDie(HTTPUtils::BAD_REQUEST, "/auth?fail=true&error='info_invalid'");
        }
        $email = $info->email;

        $emailExist = HTTPUtils::assertNotNullDieReturns(UsersModel::existEmail($email), '/api/auth/complete Error when checking email exist');
        if(!$emailExist)
        {
            $id = HTTPUtils::assertNotNullDieReturns(UsersModel::register($email), '/api/auth/complete Error when inserting new email');
        }
        else
        {
            $id = HTTPUtils::assertNotNullDieReturns(UsersModel::getId($email), '/api/auth/complete Error when getting id by email');
        }

        $userVerified = HTTPUtils::assertNotNullDieReturns(UsersModel::isVerified($id), '/api/auth/complete Error when checking user verified');

        $token = JWT::signJWT([
            'id' => $id,
            'email' => $email
        ], JWT::toHour(6));

        $secure = $_SERVER['ALLOW_NON_SECURE'];
        setcookie('token', $token, time() + JWT::toHour(6), '/', '', $secure, true);
        
        if($userVerified)
        {
            HTTPUtils::redirectAndDie(HTTPUtils::OK, '/home');
        }
        else
        {
            HTTPUtils::redirectAndDie(HTTPUtils::OK, '/verify');
        }
    }
    public function request()
    {
        $url = OAuth::createGoogleAuthURL();
        HTTPUtils::assertNotNullDie($url, '/api/auth/request Error getting auth URL');

        HTTPUtils::redirectAndDie(HTTPUtils::OK, $url);
    }
    public function verify()
    {
        JWT::checkAuthJWTOrDie();

        if(UsersModel::isVerified(JWT::$id))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'User is already verified!');
            return;
        }

        $check = true;
        $value = [];
        Router::addKeyBodyOrNull('username', $value, $check);
        Router::addKeyBodyOrNull('date_of_birth', $value, $check);
        Router::addKeyBodyOrNull('profile_image', $value, $check);

        if(!$check)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Incomplete verification data!');
            return;
        }

        $profile_image_raw = base64_decode($value['profile_image']);

        $image_type = UsersModel::getImageType(Image::getImageExtension($profile_image_raw));

        if($image_type === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Unsupported image format!');
            return;
        }

        $sanitized_image_raw = Image::stripExiff($profile_image_raw);
        if($sanitized_image_raw === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Image broken!');
            return;
        }

        $value['profile_image'] = [$sanitized_image_raw, PDO::PARAM_LOB];
        $value['profile_image_type'] = $image_type;

        $status = HTTPUtils::assertNotNullDieReturns(UsersModel::verify(JWT::$id, $value), '/api/auth/verify Error when updating user data to be verified');
        
        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'User is already verified!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'succeed'=>true
        ]);
    }

    public function load()
    {
        parent::get('/complete', function()
        {
            $this->complete();
        });
        parent::get('/request', function()
        {
            $this->request();
        });
        parent::post('/verify', function()
        {
            $this->verify();
        });
    }
}

?>