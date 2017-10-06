<?php


namespace Application\CrmBundle\Enum;


class FileCategoryEnum
{
    const QUOTATION = 'quotation';
    const CONTRACT = 'contract';
    const BRIEF = 'brief';
    const INPUT = 'input';
    const OTHER_DOCUMENT = 'other_document';

    public static function getChoices()
    {
        return array(
            self::QUOTATION => 'quotation',
            self::CONTRACT => 'contract',
            self::BRIEF => 'brief',
            self::INPUT => 'input',
            self::OTHER_DOCUMENT => 'other document',
        );
    }
}