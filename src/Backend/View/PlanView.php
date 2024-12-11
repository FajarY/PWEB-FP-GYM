<?php
namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\View;
use University\GymJournal\Backend\App\HTTPUtils;

class PlanView extends View
{
    public function render()
    {
        HTTPUtils::sendHTMLAtPublic(200, 'Plan.html');
    }
}
?>