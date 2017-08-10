<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionOption
 */
class QuestionOption
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $value;

    private $question;

    /**
     * @var Collection
     */
    private $actions;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->actions = new ArrayCollection();
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
     * Set text
     *
     * @param string $text
     * @return QuestionOption
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return QuestionOption
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
     * @param \DateTime $createdAt
     * @return QuestionOption
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return QuestionOption
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set question
     *
     * @param \Application\QuotationGeneratorBundle\Entity\Question $question
     * @return QuestionOption
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

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getValue() ? $this->getValue() : 'new';
    }

    /**
     * @return Collection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Collection $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    public function addAction(QuestionAction $action)
    {
        $action->setQuestion($this);
        $this->actions->add($action);
    }

    public function removeAction(QuestionAction $action)
    {
        $this->actions->removeElement($action);
    }

}
