<?php

namespace University\GymJournal\Backend\Models;

use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Logger;

class UsersModel
{
    public static function existEmail(string $email) : ?bool
    {
        $res = DB::query('SELECT email FROM users WHERE email=:email',
        [
            'email'=>$email
        ]);
        if($res === null)
        {
            return null;
        }

        return count($res) > 0;
    }
    public static function existEmailAndId(string $email, string $id) : ?bool
    {
        $res = DB::query('SELECT email FROM users WHERE email=:email AND id=:id',
        [
            'email'=>$email,
            'id'=>$id
        ]);
        if($res === null)
        {
            return null;
        }

        return count($res) > 0;
    }
    public static function getId(string $email) : ?string
    {
        $res = DB::query('SELECT id FROM users WHERE email=:email',
        [
            'email' => $email
        ]);

        if($res === null || count($res) <= 0)
        {
            return null;
        }

        return $res[0]['id'];
    }
    public static function existId(string $id) : ?bool
    {
        $res = DB::query('SELECT id FROM users WHERE id=:id',
        [
            'id'=>$id
        ]);
        if($res === null)
        {
            return null;
        }

        return count($res) > 0;
    }
    public static function isVerified(string $id) : ?bool
    {
        $res = DB::query('SELECT verified FROM users WHERE id=:id',
        [
            'id'=>$id
        ]);
        if($res === null)
        {
            return null;
        }

        if(count($res) <= 0 || !isset($res[0]['verified']))
        {
            return null;
        }

        return $res[0]['verified'];
    }
    public static function register(string $email) : ?string
    {
        $res = DB::query(
        'INSERT INTO users(email) VALUES (:email) RETURNING id',
        [
            'email' => $email
        ]);

        if($res === null || count($res) <= 0)
        {
            return null;
        }

        return $res[0]['id'];
    }
    public static function verify(string $id, array $params) : ?bool
    {
        $res = DB::query('UPDATE users SET username=:username, date_of_birth=:date_of_birth, profile_image=:profile_image, profile_image_type=:profile_image_type, verified=1 WHERE id=:id AND verified=0 RETURNING id',
        array_merge($params, ['id' => $id]), []);

        if($res === null)
        {
            return null;
        }

        if(count($res) <= 0)
        {
            return false;
        }

        return true;
    }
    public static function me(string $id) : ?array
    {
        $res = DB::query(
            'SELECT id, email, username, date_of_birth, created_at FROM users WHERE id=:id',
            [
                'id' => $id
            ], []
        );
        if($res === null || count($res) <= 0)
        {
            return null;
        }

        return $res[0];
    }
    public static function get(string $id) : ?array
    {
        $res = DB::query(
            'SELECT id, email, username, date_of_birth, created_at, profile_image_type, verified FROM users WHERE id=:id',
            [
                'id' => $id
            ], []
        );
        if($res === null || count($res) <= 0)
        {
            return null;
        }

        return $res[0];
    }
    public static function image(string $id, array &$output) : ?bool
    {
        $res = DB::query(
            'SELECT profile_image, profile_image_type FROM users WHERE id=:id',
            [
                'id' => $id
            ],[]
        );

        if($res === null)
        {
            return null;
        }
        if(count($res) <= 0)
        {
            return false;
        }

        $output['profile_image'] = $res[0]['profile_image'];
        $output['profile_image_type'] = $res[0]['profile_image_type'];
        return true;
    }
}

?>