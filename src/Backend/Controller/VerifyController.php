<?php

namespace University\GymJournal\Backend\Controller;

use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\Models\UsersModel;
use University\GymJournal\Backend\View\VerifyView;

class VerifyController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            JWT::checkAuthJWTOrDieRedirectToAuth();

            if(UsersModel::isVerified(JWT::$id))
            {
                HTTPUtils::redirectAndDie(HTTPUtils::OK, '/home');
            }

            $view = new VerifyView();
            $view->render();
        });
    }
}

?>