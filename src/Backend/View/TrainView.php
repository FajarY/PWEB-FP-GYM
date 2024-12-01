<?php
namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\View;
use University\GymJournal\Backend\App\HTTPUtils;

class TrainView extends View
{
    public function render()
    {
        HTTPUtils::sendHTML(200, '/train');
    }
}
?>