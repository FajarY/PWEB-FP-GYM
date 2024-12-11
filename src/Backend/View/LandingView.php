<?php

namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\View;

class LandingView extends View
{
    public function render()
    {
        HTTPUtils::sendHTML(200, file_get_contents(__DIR__.'/../../Frontend/Pages/LandingView.html'));
    }
}
