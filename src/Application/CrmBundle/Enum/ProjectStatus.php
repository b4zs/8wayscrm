<?php


namespace Application\CrmBundle\Enum;


class ProjectStatus
{

    const NEW = 'new';
    const ASSESSMENT = 'assessment';
    const QUOTATION = 'quotation';
    const CONTRACT = 'contact';
    const EXECUTION = 'execution';
    const DONE = 'done';

    public static function getChoices()
    {
        $ref = new \ReflectionClass(__CLASS__);
        $statuses = $ref->getConstants();

        return array_combine($statuses, array_map('ucfirst', $statuses));
    }

    public static function getAllData()
    {
        return array(
            self::ASSESSMENT => array('label' => ucfirst(self::ASSESSMENT), 'icon' => 'fa fa-question-circle'),
            self::QUOTATION => array('label' => ucfirst(self::QUOTATION), 'icon' => 'fa fa-calculator'),
            self::EXECUTION => array('label' => ucfirst(self::EXECUTION), 'icon' => 'fa fa-gears'),
            self::NEW => array('label' => ucfirst(self::NEW), 'icon' => 'fa fa-remove'),
            self::DONE => array('label' => ucfirst(self::DONE), 'icon' => 'fa fa-remove'),
            self::CONTRACT => array('label' => ucfirst(self::CONTRACT), 'icon' => 'fa fa-remove'),
        );
    }

}