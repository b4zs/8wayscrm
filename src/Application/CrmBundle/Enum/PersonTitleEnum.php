<?php
namespace Application\CrmBundle\Enum;

class PersonTitleEnum
{
    const TITLE_MR  = 'mr';
    const TITLE_DR  = 'dr'; //doc
    const TITLE_PR  = 'pr'; //prof
    const TITLE_MRS = 'mrs';
    const TITLE_MS  = 'ms';

    public static function getChoices()
    {
        return array(
            ''                => 'Select title',
            static::TITLE_MR  => 'Mr.',
            static::TITLE_MRS => 'Mrs.',
            static::TITLE_MS  => 'Ms.',
        );
    }
}