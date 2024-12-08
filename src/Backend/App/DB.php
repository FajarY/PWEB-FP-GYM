<?php
namespace University\GymJournal\Backend\App;
use PDO;
use PDOException;
use University\GymJournal\Backend\App\Logger;

class DB
{
    public static ?bool $active = null;
    public static ?PDO $connection = null;

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
    public static function query(string $sql, array $params, array $options = []) : ?array
    {
        if(self::$active == null)
        {
            self::connect();
        }
        if(!self::$active)
        {
            return null;
        }

        try
        {
            $statement = self::$connection->prepare($sql, $options);

            foreach($params as $key => $value)
            {
                if(is_array($value) && count($value) == 2)
                {
                    $statement->bindValue(':'.$key, $value[0], $value[1]);
                }
                else
                {
                    $statement->bindValue(':'.$key, $value);
                }
            }

            $res = $statement->execute();

            if($res)
            {
                $arr = $statement->fetchAll(PDO::FETCH_ASSOC);
                return $arr;
            }
        }
        catch(PDOException $error)
        {
            error_log($error);
            Logger::Error($error->__toString());
        }

        return null;
    }

    //Unsafe Be Sure To Escape String
    public static function rawQuery(string $sql) : bool
    {
        if(self::$active == null)
        {
            self::connect();
        }
        if(!self::$active)
        {
            return null;
        }

        try
        {
            $status = self::$connection->exec($sql);
            if(!$status)
            {
                return false;
            }

            return true;
        }
        catch(PDOException $error)
        {
            error_log($error);
            Logger::Error($error->__toString());
        }

        return false;
    }
}
?>