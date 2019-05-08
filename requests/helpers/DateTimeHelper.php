<?php

namespace detalika\requests\helpers;


class DateTimeHelper 
{
    public static function convertToUtc($dateTimeStr, $format)
    { 
        $dateTime = \DateTime::createFromFormat(
            $format,
            $dateTimeStr,  
            self::getApplicationTimeZone()
        );
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime->format($format);
    }
     
    private static function getApplicationTimeZone()
    {
        return (new \DateTimeZone(\Yii::$app->timeZone));
    }
    
    private function __construct() 
    {

    }
}