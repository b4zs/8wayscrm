<?php


namespace Application\ProjectAccountingBundle\Enum;

class WorkTracker extends AbstractEnum
{
    const
        DESIGN = 0,
        SITEBUILD = 1,
        DEVELOPMENT = 2,
        SUPPORT = 3,
        CONTENT = 4,
        PM = 5,
        MIXED = 6;

}