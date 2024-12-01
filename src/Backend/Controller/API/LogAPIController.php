<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

class LogAPIController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            echo "/api/log";
        });
        parent::get('/all', function()
        {
            echo "/api/log/all";
        });
        parent::post('/', function()
        {
            echo "/api/log";
        });
    }
}
?>