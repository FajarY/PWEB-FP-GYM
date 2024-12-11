<?php
namespace University\GymJournal\Backend\View;

use University\GymJournal\Backend\App\HTTPUtils;
use University\GymJournal\Backend\App\View;
use University\GymJournal\Frontend\Component\Navbar;

class HomeView extends View
{
    public function render()
    {
        HTTPUtils::sendHTMLAtPublic(200, 'HomeView.html');
    }
}
?>