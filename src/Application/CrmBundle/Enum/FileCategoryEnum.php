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
            self::INPUT => 'input',
            self::OTHER_DOCUMENT => 'other document',
        );
    }

    public static function getFilterChoices()
    {
        return array(
            '' => 'All medias',
            self::INPUT => 'input',
            self::OTHER_DOCUMENT => 'other document',
        );
    }
}