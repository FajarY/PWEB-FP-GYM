<?php

namespace University\GymJournal\Backend\Models;

use University\GymJournal\Backend\App\DB;
use University\GymJournal\Backend\App\Logger;

class UsersModel
{
    public const array supportedImageTypes = [
        'jpg' => 0,
        'jpeg' => 0,
        'png' => 1
    ];

    public static function getImageType(?string $type) : ?int
    {
        if($type === null)
        {
            return null;
        }
        if(isset(self::supportedImageTypes[$type]))
        {
            return self::supportedImageTypes[$type];
        }

        return null;
    }
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
}

?>