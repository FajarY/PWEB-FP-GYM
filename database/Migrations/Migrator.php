<?php

namespace Database\University\GymJournal\Migrations;
use University\GymJournal\Backend\App\DB;

class Migrator
{
    public static function run() : bool
    {
        $res = DB::rawQuery(
            file_get_contents(__DIR__.'/query.sql')
        );

        if($res === null)
        {
            error_log('Failed running migrations!');

            return false;
        }

        error_log('Migration completed!');
        return false;
    }
}

?>