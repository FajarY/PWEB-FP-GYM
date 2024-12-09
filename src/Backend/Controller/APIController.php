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
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\Models\LogsModel;
use University\GymJournal\Backend\Models\UsersModel;

class APIController extends Controller
{
    private function me()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(UsersModel::me(JWT::$id), '/api/me Error when getting data from DB');

        HTTPUtils::sendJson(HTTPUtils::OK, $data);
    }
    private function leaderboard()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(LogsModel::getHighestPoint(10), '/api/leaderboard Error when querying');

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'items' => $data
        ]);
    }
    //This Endpoint Is Only Available On Development
    private function reset()
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