<?php
namespace Application\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseTag;

class Tag extends BaseTag
{

    protected $id;

    public function __construct()
    {
        $this->enabled = true;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

}