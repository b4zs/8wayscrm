<?php
namespace Application\CrmBundle\Entity;

use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityInterface;

class CustomProperty implements ObjectIdentityAware
{
    use ObjectIdentityAwareTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var Client
     */
    protected $client;

    /**
     * CustomProperty constructor.
     */
    public function __construct()
    {
        $this->initObjectIdentity();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param id $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param AbstractClient $client
     */
    public function setClient(AbstractClient $client = null)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getCanonicalName()
    {
        $this->getName();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id === null ? 'NA' : sprintf('%s: %s', $this->name, $this->value);
    }


}