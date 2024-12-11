<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\Models\LogsModel;
use University\GymJournal\Backend\App\Router;
use Fpdf\Fpdf;
use University\GymJournal\Backend\App\Image;
use University\GymJournal\Backend\Models\ExercisesModel;
use University\GymJournal\Backend\Models\UsersModel;

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
    private function tes()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = LogsModel::getAll(JWT::$id);

        HTTPUtils::sendJson(HTTPUtils::OK, $data);
    }
    private function pdf()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(LogsModel::getAll(JWT::$id), '/api/plan/pdf Error when querying data');
        $pdf = new Fpdf('P', 'mm', 'A3');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 20);
        $userData = UsersModel::get(JWT::$id);
        $pdf->Cell(277, 30, $userData['username']."'s Logs", 0, 1, 'C');
        $userImageVertical = $pdf->GetY();
        $pdf->Cell(277, 80, '', 0, 1, 'C');
        $pdf->Cell(277, 20, '', 0, 1, 'C');
        $totalSets = 0;
        $totalWeight = 0;
        $totalReps = 0;
        foreach($data as $key => $val)
        {
            for($i = 0; $i < count($val['exercises']); $i++)
            {
                $exercise = $val['exercises'][$i];
                $sets = $exercise['sets'];
                for($j = 0; $j < count($sets); $j++)
                {
                    $totalSets++;
                    $totalReps += $sets[$j]['reps'];
                    $totalWeight += $sets[$j]['kg'];
                }
            }
        }
        $pdf->Cell(277, 10, 'Completed Workouts : '.(count($data)), 0, 1, 'C');
        $pdf->Cell(277, 10, 'Total Sets : '.($totalSets), 0, 1, 'C');
        $pdf->Cell(277, 10, 'Total Weight : '.($totalWeight), 0, 1, 'C');
        $pdf->Cell(277, 10, 'Total Reps : '.($totalReps), 0, 1, 'C');
        $pdf->Cell(277, 20, '', 0, 1, 'C');
        $baseCursor = $pdf->GetY();
        $pdf->Image('http://localhost/api/user/imageinternal?id='.$userData['id'].'&token='.$_SERVER['FPDF_SECRET'], 108.5, $userImageVertical, 80, 80, Image::getImageExtensionFromBinaryType($userData['profile_image_type']));
        $pdf->SetY($baseCursor);

        $imageTypes = ExercisesModel::getExercisesImagesTypeAcociative();

        foreach($data as $key => $val)
        {
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(277, 7, 'Plan name : '.$val['name'], 0, 1);
            $pdf->SetFont('Arial', '', 16);
            $pdf->Cell(277, 7, 'Complete at : '.$val['complete_at'], 0, 1);
            $pdf->Cell(277, 7, 'Workout time : '.$val['workout_time'], 0, 1);

            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetFillColor(220, 255, 220);
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
                
                if($setsCount == 0)
                {
                    $pdf->Cell(97, $defaultHeight, '-', 1, 1, 'C');
                }
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
        parent::get('/tes', function()
        {
            $this->tes();
        });
        parent::get('/pdf', function()
        {
            $this->pdf();
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