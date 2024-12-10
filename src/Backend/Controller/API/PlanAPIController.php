<?php
namespace University\GymJournal\Backend\Controller\API;

use Fpdf\Fpdf;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Models\ExercisesModel;
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
    private function pdf()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(PlansModel::getAll(JWT::$id), '/api/plan/pdf Error when querying data');
        $pdf = new Fpdf('P', 'mm', 'A3');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(277, 40, 'Workout Plans', 0, 1, 'C');

        $imageTypes = ExercisesModel::getExercisesImagesTypeAcociative();

        foreach($data as $key => $val)
        {
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(277, 7, 'Plan name : '.$val['name'], 0, 1);
            $pdf->SetFont('Arial', '', 16);
            $pdf->Cell(277, 7, 'Created at : '.$val['created_at'], 0, 1);
            $pdf->Cell(277, 7, 'Last modified at : '.$val['modified_at'], 0, 1);

            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetFillColor(220, 220, 255);
            $pdf->Cell(30, 13, 'No.', 1, 0, 'C', true);
            $pdf->Cell(100, 13, 'Exercise Name', 1, 0, 'C', true);
            $pdf->Cell(50, 13, 'Image', 1, 0, 'C', true);
            $pdf->Cell(48.5, 13, 'Weight', 1, 0, 'C', true);
            $pdf->Cell(48.5, 13, 'Reps', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 16);

            for($i = 0; $i < count($val['exercises']); $i++)
            {
                $exercise = $val['exercises'][$i];
                $setsCount = count($exercise['sets']);
                $sets = $exercise['sets'];

                $defaultHeight = 50;
                $targetCellSize = max($defaultHeight, $setsCount * 14);
                $pdf->Cell(30, $targetCellSize, $i + 1, 1, 0, 'C');
                $pdf->Cell(100, $targetCellSize, $exercise['name'], 1, 0, 'C');
                $imageCursor = $pdf->GetX();
                $pdf->Cell(50, $targetCellSize, '', 1, 0, 'C');
                $startCursor = $pdf->GetX();

                $imageType = $imageTypes[$exercise['id']];
                $beforeImageY = $pdf->GetY();
                
                $pdf->Image('http://localhost/api/exercise/imageinternal?id='.$exercise['id'].'&token='.$_SERVER['FPDF_SECRET'], $imageCursor + 2.5, $pdf->GetY() + 2.5, 45, 45, $imageType);

                $pdf->SetX($startCursor);
                $pdf->SetY($beforeImageY, false);

                for($j = 0; $j < $setsCount; $j++)
                {
                    $height = 14;
                    if($j == $setsCount - 1 && $targetCellSize <= $defaultHeight)
                    {
                        $height = $defaultHeight - ($setsCount - 1) * 14;
                    }
                    $pdf->Cell(48.5, $height, $sets[$j]['kg'], '1', 0, 'C');
                    $pdf->Cell(48.5, $height, $sets[$j]['reps'], '1', 1, 'C');

                    if($j != $setsCount - 1)
                    {
                        $pdf->SetX($startCursor);
                    }
                }
            }

            $pdf->Cell(277, 20, '', 0, 1);
        }

        $pdf->Output();
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
        parent::get('/pdf', function()
        {
            $this->pdf();
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