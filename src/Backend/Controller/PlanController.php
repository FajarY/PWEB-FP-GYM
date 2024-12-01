<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\PlanView;

class PlanController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            $view = new PlanView();
            $view->render();
        });
    }
}
?>