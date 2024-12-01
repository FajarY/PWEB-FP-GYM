<?php
namespace University\GymJournal\Backend\Controller;
use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\LoginView;

class LoginController extends Controller
{
    public function load()
    {
        parent::get('/', function(){
            $view = new LoginView();
            $view->render();
        });
    }
}
?>