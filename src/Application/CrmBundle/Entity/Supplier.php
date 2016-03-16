<?php


namespace Application\CrmBundle\Entity;


class Supplier extends AbstractClient
{
    public function getCanonicalName()
    {
        return $this->getCompany()->getName();
    }
}