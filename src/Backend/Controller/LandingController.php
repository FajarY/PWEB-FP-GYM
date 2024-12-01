<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\LandingView;

class LandingController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            $view = new LandingView();
            $view->render();
        });

        parent::use('/register', new RegisterController());
        parent::use('/login', new LoginController());
        parent::use('/home', new HomeController());
        parent::use('/plan', new PlanController());
        parent::use('/train', new TrainController());
        parent::use('/leaderboard', new LeaderboardController());
        parent::use('/api', new APIController());
    }
}
?>