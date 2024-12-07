<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\Logger;
use University\GymJournal\Backend\App\Mailer;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Controller\API\AuthAPIController;
use University\GymJournal\Backend\Controller\API\ExerciseAPIController;
use University\GymJournal\Backend\Controller\API\LogAPIController;
use University\GymJournal\Backend\Controller\API\PlanAPIController;
use University\GymJournal\Backend\Controller\API\UserAPIController;
use University\GymJournal\Backend\App\Development;
use University\GymJournal\Backend\App\DB;

class APIController extends Controller
{
    private function me()
    {

    }
    private function leaderboard()
    {

    }
    //This Endpoint Is Only Available On Development
    public function reset()
    {
        if(!Development::isEnableDevelopment())
        {
            return;
        }
        Development::validateDevelopmentSecretOrDie404();

        $res = DB::query('TRUNCATE workout_logs_exercises, workout_plans_exercises, workout_plans, workout_logs, users, exercises;', []);

        if($res === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::INTERNAL_SERVER_ERROR, 'Error erasing!');
        }
        else
        {
            HTTPUtils::sendMessage(HTTPUtils::OK, 'Erased all data!');
        }
    }
    public function load()
    {
        parent::use('/auth', new AuthAPIController());
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
        if(Development::isEnableDevelopment())
        {
            parent::get('/reset', function()
            {
                $this->reset();
            });
        }
    }
}
?>