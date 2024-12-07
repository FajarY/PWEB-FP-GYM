<?php

//Uses Imagick Extension
namespace University\GymJournal\Backend\App;

use Imagick;
use ImagickException;

class Image
{
    public const array extensionCheks = [
        'png' => "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a",
        'jpg' => "\xFF\xD8\xFF"
    ];

    public static function getImageExtension(string &$binary) : ?string
    {
        foreach(self::extensionCheks as $ext => $check)
        {
            if(strpos($binary, $check) === 0)
            {
                return $ext;
            }
        }

        return null;
    }
    public static function stripExiff(string &$binary) : ?string
    {
        try
        {
            $image = new Imagick();
            $image->readImageBlob($binary);

            $profiles = $image->getImageProfiles('icc', true);

            $image->stripImage();

            if(!empty($profiles))
            {
                $image->profileImage('icc', $profiles['icc']);
            }

            $sanitized = $image->getImageBlob();
            $image->clear();
            $image->destroy();

            return $sanitized;
        }
        catch(ImagickException $err)
        {
            error_log($err);
            Logger::Error($err);
        }
        return null;
    }
}

?>