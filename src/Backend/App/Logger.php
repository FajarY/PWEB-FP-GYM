<?php
namespace University\GymJournal\Backend\App;

class Logger
{
    public static function Info(string $message)
    {
        self::WriteToLog('[INFO] '.$message);
    }
    public static function Warn(string $message)
    {
        self::WriteToLog('[WARN] '.$message);
    }
    public static function Error(string $message)
    {
        error_log($message);
        self::WriteToLog('[ERROR] '.$message);
    }
    public static function Critical(string $message)
    {
        self::WriteToLog('[CRITICAL] '.$message);
    }
    public static function WriteToLog(string $message)
    {
        file_put_contents(__DIR__.'/../../../app.log', $message.PHP_EOL, FILE_APPEND);
    }
}
?>