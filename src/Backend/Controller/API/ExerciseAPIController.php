<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\Image;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\Router;

use University\GymJournal\Backend\Models\ExercisesModel;

class ExerciseAPIController extends Controller
{
    private function headers()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $data = HTTPUtils::assertNotNullDieReturns(ExercisesModel::headers(), '/api/exercise/headers Error when querying');
        $payload = [
            'exercises' => $data
        ];

        HTTPUtils::sendJson(HTTPUtils::OK, $payload);
    }
    private function data()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, "'id' cannot be empty!");
            return;
        }

        $data = [];
        $status = HTTPUtils::assertNotNullDieReturns(ExercisesModel::select(Router::$queries['id'], $data), '/api/exercise?id={string} Error when querying');

        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Exercise not found!');
            return;
        }

        HTTPUtils::sendJson(HTTPUtils::OK, $data);
    }
    private function sendImage()
    {
        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendMessage(HTTPUtils::BAD_REQUEST, "'id' cannot be empty!");
            return;
        }

        $data = [];
        $status = HTTPUtils::assertNotNullDieReturns(ExercisesModel::image(Router::$queries['id'], $data), '/api/exercise/image?id={string} Error when querying');

        if(!$status)
        {
            HTTPUtils::sendMessage(HTTPUtils::NOT_FOUND, 'Exercise image not found!');
            return;
        }

        HTTPUtils::sendImageFromResource(HTTPUtils::OK, Image::getImageExtensionFromBinaryType($data['display_image_type']), $data['display_image']);
    }
    private function image()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        $this->sendImage();
    }
    private function imageInternal()
    {
        JWT::checkAuthFPDFOr404Die();

        $this->sendImage();
    }

    public function load()
    {
        parent::get('/headers', function()
        {
            $this->headers();
        });
        parent::get('/', function()
        {
            $this->data();
        });
        parent::get('/image', function()
        {
            $this->image();
        });
        parent::get('/imageinternal', function()
        {
            $this->imageInternal();
        });
    }
}
?>