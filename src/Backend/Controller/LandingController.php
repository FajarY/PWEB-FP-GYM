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

        parent::use('/auth', new AuthController());
        parent::use('/verify', new VerifyController());
        parent::use('/home', new HomeController());
        parent::use('/plan', new PlanController());
        parent::use('/train', new TrainController());
        parent::use('/leaderboard', new LeaderboardController());
        parent::use('/api', new APIController());
    }
}
?>