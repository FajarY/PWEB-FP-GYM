<?php

namespace University\GymJournal\Backend\Models;

use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Logger;

class PlansModel
{
    public static function exist(string $id) : ?bool
    {
        $res = DB::query(
            'SELECT id FROM workout_plans WHERE id=:id',
            [
                'id' => $id
            ]
        );
        if($res === null)
        {
            return null;
        }
        return count($res) > 0;
    }
    public static function isOwner(string $id, string $userId) : ?bool
    {
        $res = DB::query(
            'SELECT id FROM workout_plans WHERE id=:id AND users_id=:users_id',
            [
                'id' => $id,
                'users_id' => $userId
            ]
        );

        if($res === null)
        {
            return null;
        }

        return count($res) > 0;
    }
    public static function getFull(string $id, array &$output) : ?bool
    {
        $res = DB::query(
            'SELECT id, name, created_at, modified_at FROM workout_plans WHERE id=:id',
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

        $rel = DB::query(
            'SELECT workout_plans_id, exercises_id, sets FROM workout_plans_exercises WHERE workout_plans_id=:workout_plans_id',
            [
                'workout_plans_id' => $id
            ]
        );

        if($rel === null)
        {
            return null;
        }

        //Validate sets
        $exercises = [];
        for($i = 0; $i < count($rel); $i++)
        {
            $sets = json_decode($rel[$i]['sets'], true);

            for($j = 0; $j < count($sets); $j++)
            {
                if(!isset($sets[$j]['reps']) || !isset($sets[$j]['kg']))
                {
                    Logger::Error("PlansModel getFull() Error, sets format is invalid!");
                    error_log("PlansModel getFull() Error, sets format is invalid!");

                    return null;
                }
            }

            $data = [
                'id' => $rel[$i]['exercises_id'],
                'sets' => $sets
            ];
            $exercises[] = $data;
        }

        $output['id'] = $res[0]['id'];
        $output['name'] = $res[0]['name'];
        $output['created_at'] = $res[0]['created_at'];
        $output['modified_at'] = $res[0]['modified_at'];
        $output['exercises'] = $exercises;

        return true;
    }
    public static function getHeaders(string $userId) : ?array
    {
        $res = DB::query(
            'SELECT id, name, created_at, modified_at FROM workout_plans WHERE users_id=:id',
            [
                'id' => $userId
            ]
        );

        if($res === null)
        {
            return null;
        }

        return $res;
    }
    public static function register(string $name, string $userId) : ?string
    {
        $res = DB::query(
            'INSERT INTO workout_plans(name, users_id) VALUES (:name, :users_id) RETURNING id',
            [
                'name' => $name,
                'users_id' => $userId
            ]
        );
        if($res === null)
        {
            return null;
        }

        return $res[0]['id'];
    }
    public static function clearRelation(string $id) : ?bool
    {
        $res = DB::query(
            'DELETE FROM workout_plans_exercises WHERE workout_plans_id=:id',
            [
                'id' => $id
            ]
        );

        if($res === null)
        {
            return null;
        }

        return true;
    }
    public static function sanitizeExercisesInput(array $value) : ?array
    {
        $exercises = [];
        for($i = 0; $i < count($value); $i++)
        {
            if(!isset($value[$i]['id']) || !isset($value[$i]['sets']))
            {
                return null;
            }
            if(!ExercisesModel::exist($value[$i]['id']))
            {
                return null;
            }

            $item = [
                'id' => $value[$i]['id'],
                'sets' => []
            ];

            $sets = $value[$i]['sets'];
            for($j = 0; $j < count($sets); $j++)
            {
                if(!isset($sets[$j]['reps']) || !isset($sets[$j]['kg']))
                {
                    return null;
                }
                $sanitized = [
                    'reps' => $sets[$j]['reps'],
                    'kg' => $sets[$j]['kg']
                ];

                if(!is_numeric($sanitized['reps']) || !is_numeric($sanitized['kg']))
                {
                    return null;
                }
                $item['sets'][] = $sanitized;
            }

            $exercises[] = $item;
        }

        return $exercises;
    }
    public static function modify(string $id, array $value) : ?bool
    {
        if(!self::exist($id))
        {
            return false;
        }

        $name = $value['name'];
        $exercises = self::sanitizeExercisesInput($value['exercises']);
        if($exercises === null)
        {
            return null;
        }

        $clearStatus = self::clearRelation($id);
        if($clearStatus === null)
        {
            return null;
        }

        $res = DB::query(
            'UPDATE workout_plans SET name=:name, modified_at=DEFAULT WHERE id=:id',
            [
                'name' => $name,
                'id' => $id
            ]
        );
        if($res === null)
        {
            Logger::Critical('PlansModel modify() Error when updating workout_plans data');
            return null;
        }

        for($i = 0; $i < count($exercises); $i++)
        {
            $rel = DB::query(
                'INSERT INTO workout_plans_exercises(workout_plans_id, exercises_id, sets) VALUES(:workout_plans_id, :exercises_id, :sets)',
                [
                    'workout_plans_id' => $id,
                    'exercises_id' => $exercises[$i]['id'],
                    'sets' => json_encode($exercises[$i]['sets'])
                ]
            );

            if($rel === null)
            {
                Logger::Critical('PlansModel modify() Error when inserting relation!');
                return null;
            }
        }

        return true;
    }
    public static function del(string $id) : ?bool
    {
        if(!self::exist($id))
        {
            return false;
        }

        $status = self::clearRelation($id);
        if($status === null)
        {
            return null;
        }

        $res = DB::query(
            'DELETE FROM workout_plans WHERE id=:id',
            [
                'id' => $id
            ]
        );
        if($res === null)
        {
            return null;
        }

        return true;
    }
    public static function getAll(string $userId) : ?array
    {
        $res = DB::query(
            'SELECT wp.id, wp.name, wp.created_at, wp.modified_at, wp.exercises_id, wp.sets, ex.name as exercises_name
                FROM
                (SELECT wp.id, wp.name, wp.created_at, wp.modified_at, wpe.exercises_id, wpe.sets
                FROM
                    (SELECT wp.id, wp.name, wp.created_at, wp.modified_at
                    FROM workout_plans wp
                        WHERE users_id=:users_id) wp
                    LEFT JOIN workout_plans_exercises wpe
                        ON wpe.workout_plans_id=wp.id) wp
                JOIN exercises ex
                    ON ex.id=wp.exercises_id',
            [
                'users_id' => $userId
            ], []
        );

        if($res === null)
        {
            return null;
        }

        $mapped = [];
        for($i = 0; $i < count($res); $i++)
        {
            $item = $res[$i];
            if(!isset($mapped[$item['id']]))
            {
                $mapped[$item['id']] = [
                    'name' => $item['name'],
                    'created_at' => $item['created_at'],
                    'modified_at' => $item['modified_at'],
                    'exercises' => [],
                    'count' => 0
                ];
            }
            $sets = json_decode($item['sets'], true);

            $mapped[$item['id']]['exercises'][] = [
                'id' => $item['exercises_id'],
                'name' => $item['exercises_name'],
                'sets' => $sets
            ];

            $mapped[$item['id']]['count'] += 1;
        }

        return $mapped;
    }
}

?>