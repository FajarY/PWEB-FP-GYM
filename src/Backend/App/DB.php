<?php
namespace University\GymJournal\Backend\App;
use PDO;
use PDOException;

class DB
{
    public static $active = false;
    public static $connection = null;

    public static function connect() : bool
    {
        if(self::$active)
        {
            return true;
        }

        try
        {
            self::$connection = new PDO('pgsql:host='.$_SERVER['DB_HOST'].';dbname='.$_SERVER['POSTGRES_DB'], $_SERVER['POSTGRES_USER'], $_SERVER['POSTGRES_PASSWORD']);

            if(self::$connection)
            {
                self::$active = true;
                return true;
            }
            else
            {
                logger::Error('Database connection failed!');
            }
        }
        catch(PDOException $dbError)
        {
            logger::Error($dbError);
        }
        return false;
    }
}
?>