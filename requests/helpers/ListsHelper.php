<?php

namespace detalika\requests\helpers;

class ListsHelper 
{
    public static function getYesNoList()
    {
        return [
            false => 'Нет',
            true => 'Да'
        ];
    }
}