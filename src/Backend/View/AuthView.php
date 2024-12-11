<?php

namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\View;
use University\GymJournal\Backend\App\HTTPUtils;

class AuthView extends View
{
    public function render()
    {
        HTTPUtils::sendHTMLAtPublic(200, 'AuthView.html');
    }
}

?>