<?php
namespace Application\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseCategory;

class Category extends BaseCategory
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