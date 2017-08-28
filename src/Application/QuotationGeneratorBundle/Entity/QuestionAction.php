<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Application\ClassificationBundle\Entity\Tag;
use Application\ProjectAccountingBundle\Entity\Price;
use Application\QuotationGeneratorBundle\Enum\ActionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * Action
 */
class QuestionAction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Application\QuotationGeneratorBundle\Entity\Question
     * @Exclude
     */
    private $question;

    /**
     * @var string
     */
    private $criteria;

    /**
     * @var integer
     */
    private $actionType;

    /**
     * @var QuestionOption
     */
    private $questionOption;

    /**
     * @var Question[]|Collection
     */
    private $implyQuestionsBySelection;

    /**
     * @var Tag[]|Collection
     */
    private $implyQuestionsByTags;

    /**
     * @var QuestionGroup[]|Collection
     */
    private $implyQuestionsByGroups;

    /** @var  Price */
    private $quotationItemPrice;

    /** @var  string */
    private $quotationItemName;

    /**
     * @var array
     */
    private $actionParams = array();

    /**
     * @var integer
     */
    private $position;

    /**
     * @var \DateTime
     * @Exclude
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Exclude
     */
    private $deletedAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->implyQuestionsBySelection = new ArrayCollection();
        $this->implyQuestionsByTags = new ArrayCollection();
        $this->implyQuestionsByGroups = new ArrayCollection();
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
     * Set criteria
     *
     * @param string $criteria
     * @return QuestionAction
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Get criteria
     *
     * @return string
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Set actionType
     *
     * @param integer $actionType
     * @return QuestionAction
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * Get actionType
     *
     * @return integer
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Set actionParams
     *
     * @param array $actionParams
     * @return QuestionAction
     */
    public function setActionParams($actionParams)
    {
        $this->actionParams = $actionParams;

        return $this;
    }

    /**
     * Get actionParams
     *
     * @return array
     */
    public function getActionParams()
    {
        return $this->actionParams;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return QuestionAction
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
     * @return QuestionAction
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
     * @return QuestionAction
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

    function __toString()
    {
        $types = ActionType::getChoices();
        return sprintf('#%s(%s)', $this->getId() ? $this->getId() : 'new', isset($types[$this->getActionType()]) ? $types[$this->getActionType()] : '');
    }

    /**
     * @return QuestionOption
     */
    public function getQuestionOption()
    {
        return $this->questionOption;
    }

    /**
     * @param QuestionOption $questionOption
     */
    public function setQuestionOption($questionOption)
    {
        $this->questionOption = $questionOption;
    }

    /**
     * alias for getQuestionOption
     * @return QuestionOption
     */
    public function getOption()
    {
        return $this->getQuestionOption();
    }

    function __clone()
    {
        $this->id = null;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getImplyQuestionsBySelection()
    {
        return $this->implyQuestionsBySelection;
    }

    public function setImplyQuestionsBySelection($implyQuestionsBySelection)
    {
        $this->implyQuestionsBySelection = $implyQuestionsBySelection;
    }

    public function getImplyQuestionsByTags()
    {
        return $this->implyQuestionsByTags;
    }

    public function setImplyQuestionsByTags($implyQuestionsByTags)
    {
        $this->implyQuestionsByTags = $implyQuestionsByTags;
    }

    public function getImplyQuestionsByGroups()
    {
        return $this->implyQuestionsByGroups;
    }

    public function setImplyQuestionsByGroups($implyQuestionsByGroups)
    {
        $this->implyQuestionsByGroups = $implyQuestionsByGroups;
    }

    public function getQuotationItemPrice()
    {
        return $this->quotationItemPrice;
    }

    public function setQuotationItemPrice(Price $quotationItemPrice)
    {
        $this->quotationItemPrice = $quotationItemPrice;
    }

    public function getQuotationItemName()
    {
        return $this->quotationItemName;
    }

    public function setQuotationItemName($quotationItemName)
    {
        $this->quotationItemName = $quotationItemName;
    }
}
