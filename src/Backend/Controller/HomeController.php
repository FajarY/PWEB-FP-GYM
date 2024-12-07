<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\View\HomeView;

class HomeController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            JWT::checkAuthJWTAndUserVerifiedOrDieRedirectToAuthOrVerified();

            $view = new HomeView();
            $view->render();
        });
    }
}
?>