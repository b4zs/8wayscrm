<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\AccessType;

/**
 * @AccessType("public_method")
 * QuestionAnswer
 */
class QuestionAnswer
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var Question
     */
    private $question;

    function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set value
     *
     * @param string $value
     * @return QuestionAnswer
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     * @return QuestionAnswer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set question
     *
     * @param \Application\QuotationGeneratorBundle\Entity\Question $question
     * @return QuestionAnswer
     */
    public function setQuestion(\Application\QuotationGeneratorBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Application\QuotationGeneratorBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function getQuestionId()
    {
        return $this->getQuestion() ? $this->getQuestion()->getId() : null;
    }
}
