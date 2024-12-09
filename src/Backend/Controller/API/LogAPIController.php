<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\Models\LogsModel;
use University\GymJournal\Backend\App\Router;

class LogAPIController extends Controller
{
    private function getFull()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, "'id' cannot be empty!");
            return;
        }
        $planId = Router::$queries['id'];
        $exist = HTTPUtils::assertNotNullDieReturns(LogsModel::exist($planId), '/api/log?id={string} Error when checking exist');
        if(!$exist)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Log not found!');
            return;
        }
        $isOwner = HTTPUtils::assertNotNullDieReturns(LogsModel::isOwner($planId, JWT::$id), '/api/log?id={string}');
        if(!$isOwner)
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'User is not the owner!');
            return;
        }

        $data = [];
        $status = HTTPUtils::assertNotNullDieReturns(LogsModel::getFull($planId, $data), '/api/log?id={string} Error when querying');

        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Log not found!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, $data);
    }
    private function getHeaders()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(LogsModel::getHeaders(JWT::$id), '/api/log/headers Error when querying!');

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'logs' => $data
        ]);
    }
    private function create()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $workoutTime = Router::bodyOrNull('workout_time');
        $name = Router::bodyOrNull('name');
        $exercises = Router::bodyOrNull('exercises');

        if($name === null || $exercises === null || $workoutTime === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Incomplete data sent!');
            return;
        }
        if(!is_numeric($workoutTime))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Invalid workout time!');
        }

        $registerId = HTTPUtils::assertNotNullDieReturns(LogsModel::register($name, JWT::$id, $workoutTime), '/api/log Error when registering new log');
        $modify = HTTPUtils::assertNotNullDieReturns(LogsModel::modify($registerId, [
            'name' => $name,
            'exercises' => $exercises,
            'workout_time' => $workoutTime
        ]), '/api/log Error when modyfying data');
        
        if(!$modify)
        {
            HTTPUtils::internalServerErrorDie('/api/log Error is not possible to be false when modifying after create!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::CREATED, [
            'id' => $registerId
        ]);
    }
    public function load()
    {
        parent::get('/', function()
        {
            $this->getFull();
        });
        parent::get('/headers', function()
        {
            $this->getHeaders();
        });
        parent::post('/', function()
        {
            $this->create();
        });
    }
}
?>