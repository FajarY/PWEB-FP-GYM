<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

class UserAPIController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            echo "/api/user";
        });
        parent::get('/image', function()
        {
            echo "/api/user/image";
        });
    }
}
?>