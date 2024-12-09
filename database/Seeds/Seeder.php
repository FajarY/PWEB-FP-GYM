<?php

namespace Database\University\GymJournal\Seeds;

use OCILob;
use PDO;
use PgSql\Lob;
use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Image;

class Seeder
{
    private static function exercises() : bool
    {
        $data = json_decode(file_get_contents(__DIR__.'/exercises.json'), true);
        $seeds = $data['seeds'];

        for($i = 0; $i < count($seeds); $i++)
        {
            $val = $seeds[$i];
            $val['display_image'] = file_get_contents(__DIR__.$val['display_image']);
            $val['display_image_type'] = Image::getImageBinaryType(Image::getImageExtension($val['display_image']));
            
            $val['display_image'] = [$val['display_image'], PDO::PARAM_LOB];

            $res = DB::query(
                'INSERT INTO exercises(id, name, score_multiplier, display_image, display_image_type) VALUES (:id, :name, :score_multiplier, :display_image, :display_image_type) RETURNING id',
                $val, []
            );
            if($res === null)
            {
                return false;
            }
        }

        return true;
    }

    public static function run() : bool
    {
        if(!self::exercises())
        {
            error_log("Seeding exercises failed!");
            return false;
        }

        error_log("Seeding successfull!");
        return true;
    }
}

?>