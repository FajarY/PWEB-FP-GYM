<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Models\PlansModel;

class PlanAPIController extends Controller
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
        $exist = HTTPUtils::assertNotNullDieReturns(PlansModel::exist($planId), '/api/plan?id={string} Error when checking exist');
        if(!$exist)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Plan not found!');
            return;
        }
        $isOwner = HTTPUtils::assertNotNullDieReturns(PlansModel::isOwner($planId, JWT::$id), '/api/plan?id={string}');
        if(!$isOwner)
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'User is not the owner!');
            return;
        }

        $data = [];
        $status = HTTPUtils::assertNotNullDieReturns(PlansModel::getFull($planId, $data), '/api/plan?id={string} Error when querying');

        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Plan not found!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, $data);
    }
    private function headersAll()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(PlansModel::getHeaders(JWT::$id), '/api/plan/headers Error when querying!');

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'plans' => $data
        ]);
    }
    private function create()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $name = Router::bodyOrNull('name');
        $exercises = Router::bodyOrNull('exercises');

        if($name === null || $exercises === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Incomplete data sent!');
            return;
        }

        $registerId = HTTPUtils::assertNotNullDieReturns(PlansModel::register($name, JWT::$id), '/api/plan Error when registering new plan');
        $modify = HTTPUtils::assertNotNullDieReturns(PlansModel::modify($registerId, [
            'name' => $name,
            'exercises' => $exercises
        ]), '/api/plan Error when modyfying data');
        
        if(!$modify)
        {
            HTTPUtils::internalServerErrorDie('/api/plan Error is not possible to be false when modifying after create!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::CREATED, [
            'id' => $registerId
        ]);
    }
    private function update()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, "'id' cannot be empty!");
            return;
        }
        $planId = Router::$queries['id'];
        $exist = HTTPUtils::assertNotNullDieReturns(PlansModel::exist($planId), '/api/plan Error when updating, when checking exist');
        if(!$exist)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Plan to update not found!');
            return;
        }
        $isOwner = HTTPUtils::assertNotNullDieReturns(PlansModel::isOwner($planId, JWT::$id), '/api/plan Error when updating. Cannot query ownership!');
        if(!$isOwner)
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'User is not the owner!');
            return;
        }

        $name = Router::bodyOrNull('name');
        $exercises = Router::bodyOrNull('exercises');

        if($name === null || $exercises === null)
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, 'Incomplete data sent!');
            return;
        }

        $status = HTTPUtils::assertNotNullDieReturns(PlansModel::modify($planId, [
            'name' => $name,
            'exercises' => $exercises
        ]), '/api/plan Error when trying to update data!');

        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Cannot find plan to update!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'id' => $planId,
            'success' => $status
        ]);
    }
    private function delete()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, "'id' cannot be empty!");
            return;
        }

        $planId = Router::$queries['id'];
        $exist = HTTPUtils::assertNotNullDieReturns(PlansModel::exist($planId), '/api/plan Error when deleting, when checking exist');
        if(!$exist)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Plan to delete not found!');
            return;
        }
        $isOwner = HTTPUtils::assertNotNullDieReturns(PlansModel::isOwner($planId, JWT::$id), '/api/plan Error when deleting. Cannot query ownership!');
        if(!$isOwner)
        {
            HTTPUtils::sendMessage(HTTPUtils::UNAUTHORIZED, 'User is not the owner!');
            return;
        }

        $status = HTTPUtils::assertNotNullDieReturns(PlansModel::del($planId), '/api/plan Error when deleting. Error when querying delete!');
        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Plan to delete not found!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, [
            'success' => $status
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
            $this->headersAll();

        });
        parent::post('/', function()
        {
            $this->create();
        });
        parent::put('/', function()
        {
            $this->update();
        });
        parent::del('/', function()
        {
            $this->delete();
        });
    }
}
?>