<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\TrainView;

class TrainController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            $view = new TrainView();
            $view->render();
        });
    }
}
?>