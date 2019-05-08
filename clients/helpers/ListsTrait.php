<?php

namespace detalika\clients\helpers;

trait ListsTrait 
{
    public function getYesNoList()
    {
        return [
            false => 'Нет',
            true => 'Да'
        ];
    }
}
