<?php
namespace Database\University\GymJournal;

use Database\University\GymJournal\Migrations\Migrator;
use Database\University\GymJournal\Seeds\Seeder;

require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

Migrator::run();
Seeder::run();
?>