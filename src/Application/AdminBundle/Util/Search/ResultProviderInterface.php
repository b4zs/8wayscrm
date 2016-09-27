<?php

namespace Application\AdminBundle\Util\Search;


interface ResultProviderInterface
{
    /**
     * @param object $object
     * @return Result
     */
    public function getData($object);

}