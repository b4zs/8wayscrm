<?php

namespace Application\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Office
 */
class Office
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Application\CrmBundle\Entity\Company
     */
    private $company;

    /**
     * @var \Application\CrmBundle\Entity\Address
     */
    private $address;

    function __construct()
    {
        $this->address = new Address();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set company
     *
     * @param \Application\CrmBundle\Entity\Office $company
     * @return Office
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\CrmBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set address
     *
     * @param \Application\CrmBundle\Entity\Address $address
     * @return Office
     */
    public function setAddress(\Application\CrmBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Application\CrmBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getId()
            ? ($this->getCompany() ? $this->getCompany() .' - ' : '') . $this->getName()
            : 'new';
    }


}
