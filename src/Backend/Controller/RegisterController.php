<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\RegisterView;

class RegisterController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            $view = new RegisterView();
            $view->render();
        });
    }
}
?>