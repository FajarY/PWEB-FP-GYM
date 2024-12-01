<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

class ExerciseAPIController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            echo "/api/exercise";
        });
        parent::get('/image', function()
        {
            echo "/api/exercise/image";
        });
    }
}
?>