<?php
namespace University\GymJournal\Backend\Controller\API;
use University\GymJournal\Backend\App\Controller;

class PlanAPIController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            echo "/api/plan";
        });
        parent::get('/all', function()
        {
            echo "/api/plan/all";
        });
        parent::post('/', function()
        {
            echo "/api/plan";
        });
        parent::put('/', function()
        {
            echo "/api/plan";
        });
        parent::del('/', function(){
            echo "/api/plan";
        });
    }
}
?>