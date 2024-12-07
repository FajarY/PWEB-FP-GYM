<?php

namespace University\GymJournal\Backend\Controller;

use University\GymJournal\Backend\App\Controller;
use University\GymJournal\Backend\View\AuthView;

class AuthController extends Controller
{
    public function load()
    {
        parent::get('/', function()
        {
            $view = new AuthView();
            $view->render();
        });
    }
}
?>