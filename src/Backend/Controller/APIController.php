<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\Controller\API\ExerciseAPIController;
use University\GymJournal\Backend\Controller\API\LogAPIController;
use University\GymJournal\Backend\Controller\API\PlanAPIController;
use University\GymJournal\Backend\Controller\API\UserAPIController;

class APIController extends Controller
{
    private function register()
    {
        
    }
    private function login()
    {

    }
    private function me()
    {

    }
    private function leaderboard()
    {

    }
    public function load()
    {
        parent::post('/register', function()
        {
            $this->register();
        });
        parent::post('/login', function()
        {
            $this->login();
        });
        parent::get('/me', function()
        {
            $this->me();
        });
        parent::use('/plan', new PlanAPIController());
        parent::use('/log', new LogAPIController());
        parent::use('/exercise', new ExerciseAPIController());
        parent::use('/user', new UserAPIController());
        parent::get('/leaderboard', function()
        {
            $this->leaderboard();
        });
        
    }
}
?>