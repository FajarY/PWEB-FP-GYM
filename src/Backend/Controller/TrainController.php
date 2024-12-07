<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\View\TrainView;

class TrainController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            JWT::checkAuthJWTAndUserVerifiedOrDieRedirectToAuthOrVerified();

            $view = new TrainView();
            $view->render();
        });
    }
}
?>