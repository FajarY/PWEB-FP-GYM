<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

var_dump(parse_url($_SERVER['REQUEST_URI']));

$db_connection = null;
try
{
    $db_connection = new PDO('pgsql:host='.$_SERVER['DB_HOST'].';dbname='.$_SERVER['POSTGRES_DB'], $_SERVER['POSTGRES_USER'], $_SERVER['POSTGRES_PASSWORD']);
    if($db_connection)
    {
        echo "Database connection successfull!";
    }
    else
    {
        echo "Database connection unsuccessfull!";
    }
}
catch(PDOException $error)
{
    echo "Error connecting database!";
}
?>