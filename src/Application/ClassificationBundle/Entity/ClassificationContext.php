<?php
namespace Application\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseContext;

class ClassificationContext extends BaseContext
{

    protected $id;

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