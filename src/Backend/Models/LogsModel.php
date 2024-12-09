<?php

namespace University\GymJournal\Backend\Models;

use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Logger;
use PDO;

class LogsModel
{
    public static function exist(string $id) : ?bool
    {
        $res = DB::query(
            'SELECT id FROM workout_logs WHERE id=:id',
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
            'SELECT id FROM workout_logs WHERE id=:id AND users_id=:users_id',
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
            'SELECT id, name, workout_time, complete_at FROM workout_logs WHERE id=:id',
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
            'SELECT workout_logs_id, exercises_id, sets FROM workout_logs_exercises WHERE workout_logs_id=:workout_logs_id',
            [
                'workout_logs_id' => $id
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
                    Logger::Error("LogsModel getFull() Error, sets format is invalid!");
                    error_log("LogsModel getFull() Error, sets format is invalid!");

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
        $output['workout_time'] = $res[0]['workout_time'];
        $output['complete_at'] = $res[0]['complete_at'];
        $output['exercises'] = $exercises;

        return true;
    }
    public static function getHeaders(string $userId) : ?array
    {
        $res = DB::query(
            'SELECT id, name, workout_time, complete_at FROM workout_logs WHERE users_id=:id',
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
    public static function register(string $name, string $userId, int $workout_time) : ?string
    {
        $res = DB::query(
            'INSERT INTO workout_logs(name, users_id, workout_time) VALUES (:name, :users_id, :workout_time) RETURNING id',
            [
                'name' => $name,
                'users_id' => $userId,
                'workout_time' => [ $workout_time, PDO::PARAM_INT ]
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
            'DELETE FROM workout_logs_exercises WHERE workout_logs_id=:id',
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
    public static function modify(string $id, array $value) : ?bool
    {
        if(!self::exist($id))
        {
            return false;
        }

        $name = $value['name'];
        $workoutTime = $value['workout_time'];
        $exercises = PlansModel::sanitizeExercisesInput($value['exercises']);
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
            'UPDATE workout_logs SET name=:name, workout_time=:workout_time WHERE id=:id',
            [
                'name' => $name,
                'id' => $id,
                'workout_time' => [ $workoutTime, PDO::PARAM_INT ]
            ]
        );
        if($res === null)
        {
            Logger::Critical('LogsModel modify() Error when updating workout_logs data');
            return null;
        }

        for($i = 0; $i < count($exercises); $i++)
        {
            $rel = DB::query(
                'INSERT INTO workout_logs_exercises(workout_logs_id, exercises_id, sets) VALUES(:workout_logs_id, :exercises_id, :sets)',
                [
                    'workout_logs_id' => $id,
                    'exercises_id' => $exercises[$i]['id'],
                    'sets' => json_encode($exercises[$i]['sets'])
                ]
            );

            if($rel === null)
            {
                Logger::Critical('LogsModel modify() Error when inserting relation!');
                return null;
            }
        }

        return true;
    }

    //Not tested
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
            'DELETE FROM workout_logs WHERE id=:id',
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
    public static function getHighestPoint(int $count) : ?array
    {
        $res = DB::query(
            '
            SELECT u.username, sc.score_multiplier, sc.users_id, sc.sets
            FROM users u
            JOIN
                (SELECT ex.score_multiplier, sq.users_id, sq.sets
                FROM exercises ex
                JOIN (
                    SELECT wl.users_id, wle.exercises_id, wle.sets
                        FROM workout_logs wl
                        LEFT JOIN workout_logs_exercises wle ON wl.id=wle.workout_logs_id) sq ON ex.id=sq.exercises_id) sc ON u.id = sc.users_id;
            ', []
        );
        if($res === null)
        {
            return null;
        }

        $userScores = [];
        for($i = 0; $i < count($res); $i++)
        {
            $sets = $res[$i]['sets'];
            $sets = json_decode($sets, true);
            $point = 0;
            for($j = 0; $j < count($sets); $j++)
            {
                $point += ($sets[$j]['kg'] * $sets[$j]['reps']);
            }
            $point *= $res[$i]['score_multiplier'];
            $userId = $res[$i]['users_id'];

            if(isset($userScores[$userId]))
            {
                $userScores[$userId]['score'] += $point;
            }
            else
            {
                $userScores[$userId] = [
                    'username' => $res[$i]['username'],
                    'score' => $point
                ];
            }
        }

        $data = [];
        foreach($userScores as $key => $value)
        {
            $data[] = [
                'id' => $key,
                'username' => $value['username'],
                'score' => $value['score']
            ];
        }
        usort($data, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        $ret = [];
        for($i = 0; $i < min(count($data), $count); $i++)
        {
            $ret[] = $data[$i];
        }

        return $ret;
    }
}

?>