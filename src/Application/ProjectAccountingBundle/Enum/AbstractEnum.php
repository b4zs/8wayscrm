<?php

namespace Application\ProjectAccountingBundle\Enum;

class AbstractEnum
{

    public static function getChoices()
    {
        $constants = (new \ReflectionClass(get_called_class()))->getConstants();

        return array_map(function($v){ return strtolower(str_replace('_',' ', $v)); }, array_flip($constants));
    }
}