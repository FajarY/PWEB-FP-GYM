<?php

namespace University\GymJournal\Backend\Models;
use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Image;

class ExercisesModel
{
    public static function headers() : ?array
    {
        $res = DB::query(
            'SELECT id, name FROM exercises',
            [], []
        );

        if($res === null)
        {
            return null;
        }

        return $res;
    }
    public static function exist(string $id) : ?bool
    {
        $res = DB::query(
            'SELECT id FROM exercises WHERE id=:id',
            [
                'id' => $id
            ], []
        );

        if($res === null)
        {
            return null;
        }
        
        return count($res) >= 1;
    }
    public static function select(string $id, array &$output) : ?bool
    {
        $res = DB::query(
            'SELECT id, name, score_multiplier FROM exercises WHERE id=:id',
            [
                'id' => $id
            ], []
        );

        if($res === null)
        {
            return null;
        }
        if(count($res) <= 0)
        {
            return false;
        }

        $output['id'] = $res[0]['id'];
        $output['name'] = $res[0]['name'];
        $output['score_multiplier'] = $res[0]['score_multiplier'];

        return true;
    }
    public static function image(string $id, array &$output) : ?bool
    {
        $res = DB::query(
            'SELECT display_image, display_image_type FROM exercises WHERE id=:id',
            [
                'id' => $id
            ],
            []
        );

        if($res === null)
        {
            return null;
        }
        if(count($res) <= 0)
        {
            return false;
        }

        $output['display_image'] = $res[0]['display_image'];
        $output['display_image_type'] = $res[0]['display_image_type'];

        return true;
    }
    public static function getExercisesImagesTypeAcociative() : ?array
    {
        $res = DB::query(
            'SELECT id, display_image_type FROM exercises', []
        );

        if($res === null)
        {
            return null;
        }

        $arr = [];
        for($i = 0; $i < count($res); $i++)
        {
            $arr[$res[$i]['id']] = Image::getImageExtensionFromBinaryType($res[$i]['display_image_type']);
        }

        return $arr;
    }
}

?>