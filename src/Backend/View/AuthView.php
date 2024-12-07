<?php

namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\View;
use University\GymJournal\Backend\App\HTTPUtils;

class AuthView extends View
{
    public function render()
    {
        HTTPUtils::sendHTML(200, '
        <a href="/api/auth/request">Sign in</a>
        ');
    }
}

?>