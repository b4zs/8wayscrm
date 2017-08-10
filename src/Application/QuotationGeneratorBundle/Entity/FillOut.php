<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Accessor;

/**
 * FillOut
 */
class FillOut
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
     * @var \Doctrine\Common\Collections\Collection|FillOutAnswer[]
     * @SerializedName(value="answers")
     * @Accessor(getter="buildAnswersForApi", setter="setAnswers")
     */
    private $answers;

    /**
     * @var array
     */
    private $state = array();

    /**
     * @var \DateTime
     */
    private $createdAt;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->resetState();
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
     * Set name
     *
     * @param string $name
     * @return FillOut
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add answers
     *
     * @param \Application\QuotationGeneratorBundle\Entity\FillOutAnswer $answer
     * @return FillOut
     */
    public function addAnswer(\Application\QuotationGeneratorBundle\Entity\FillOutAnswer $answer)
    {
        $answer->setFillOut($this);
        $this->answers->add($answer);

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Application\QuotationGeneratorBundle\Entity\FillOutAnswer $answers
     */
    public function removeAnswer(\Application\QuotationGeneratorBundle\Entity\FillOutAnswer $answers)
    {
        $answers->setFillOut(null);
        $this->answers->removeElement($answers);
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return FillOut
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param array $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    public function resetState()
    {
        $this->state = array(
            'questionStack' => array(),
        );
    }

    public function buildAnswersForApi()
    {
        return $this->getAnswers()->filter(function(FillOutAnswer $answer){
            return $answer->getQuestion()->getText() !== '__INIT__';
        });
    }
}
