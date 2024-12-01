<?php
namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\View;

class HomeView extends View
{
    public function render()
    {
        HTTPUtils::sendHTML(200, '/home');
    }
}
?>