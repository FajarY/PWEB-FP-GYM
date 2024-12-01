<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
use University\GymJournal\Backend\App\Router;
use University\GymJournal\Backend\Controller\LandingController;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

Router::load();
Router::use(new LandingController());
?>