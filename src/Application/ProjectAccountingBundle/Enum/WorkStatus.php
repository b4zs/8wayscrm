<?php


namespace Application\ProjectAccountingBundle\Enum;

class WorkStatus extends AbstractEnum
{
    const
        QUOTED = 1,
        SCHEDULED = 2,
        IN_PROGRESS = 3,
        INVOICED = 4,
        DONE = 5;

}