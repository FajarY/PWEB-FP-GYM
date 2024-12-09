<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\Image;
use University\GymJournal\Backend\App\JWT;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Models\UsersModel;

class UserAPIController extends Controller
{
    private function image()
    {
        JWT::checkAuthJWTAndUserVerifiedOrDie();

        if(!isset(Router::$queries['id']))
        {
            HTTPUtils::sendJson(HTTPUtils::BAD_REQUEST, "'id' is empty!");
            return;
        }
        $queryId = Router::$queries['id'];

        $data = [];
        $status = HTTPUtils::assertNotNullDieReturns(UsersModel::image($queryId, $data), '/api/user/image Error when trying to get image from DB');
        if(!$status)
        {
            HTTPUtils::sendJson(HTTPUtils::NOT_FOUND, 'Image not found!');
            return;
        }
        if(!isset($data['profile_image']) || !isset($data['profile_image_type']))
        {
            HTTPUtils::internalServerErrorDie('/api/user/image Error data is not completed after successfull query');
        }

        HTTPUtils::sendImageFromResource(HTTPUtils::OK, Image::getImageExtensionFromBinaryType($data['profile_image_type']), $data['profile_image']);
    }
    public function load()
    {
        parent::get('/image', function()
        {
            $this->image();
        });
    }
}
?>