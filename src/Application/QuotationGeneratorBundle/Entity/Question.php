<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use Sonata\ClassificationBundle\Model\Tag;

/**
 * Question
 */
class Question
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
    private $formType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Exclude
     */
    private $actions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $answers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $options;

    /**
     * @var QuestionCategory
     */
    private $category;

    /**
     * @var QuestionGroup
     */
    private $group;

    /**
     * @see RequiredUserRole
     * @var string
     */
    private $requiredUserRole;

    /**
     * @var string
     */
    private $alias;

    /**
     * @see Stage
     * @var integer
     */
    private $stage;

    /**
     * @var Tag[]|Collection
     */
    private $tags;


    /**
     * @var \DateTime
     * @Exclude
     */
    private $createdAt;

    /**
     * @var string
     * @Exclude
     */
    private $deletedAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->answres = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->tags = new ArrayCollection();
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
     * @return Question
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
     * Set formType
     *
     * @param string $formType
     * @return Question
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * Get formType
     *
     * @return string 
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Question
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
     * @param string $deletedAt
     * @return Question
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return string 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Add answers
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionAnswer $answers
     * @return Question
     */
    public function addAnswer(\Application\QuotationGeneratorBundle\Entity\QuestionAnswer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionAnswer $answers
     */
    public function removeAnswer(\Application\QuotationGeneratorBundle\Entity\QuestionAnswer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add options
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionOption $options
     * @return Question
     */
    public function addOption(\Application\QuotationGeneratorBundle\Entity\QuestionOption $options)
    {
        $this->options[] = $options;

        return $this;
    }

    /**
     * Remove options
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionOption $options
     */
    public function removeOption(\Application\QuotationGeneratorBundle\Entity\QuestionOption $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add actions
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionAction $actions
     * @return Question
     */
    public function addAction(\Application\QuotationGeneratorBundle\Entity\QuestionAction $actions)
    {
        $this->actions[] = $actions;

        return $this;
    }

    /**
     * Remove actions
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionAction $actions
     */
    public function removeAction(\Application\QuotationGeneratorBundle\Entity\QuestionAction $actions)
    {
        $this->actions->removeElement($actions);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection|QuestionAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return QuestionCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param QuestionCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return QuestionGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param QuestionGroup $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getRequiredUserRole()
    {
        return $this->requiredUserRole;
    }

    /**
     * @param string $requiredUserRole
     */
    public function setRequiredUserRole($requiredUserRole)
    {
        $this->requiredUserRole = $requiredUserRole;
    }

    /**
     * @return int
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param int $stage
     */
    public function setStage($stage)
    {
        $this->stage = $stage;
    }

    function __toString()
    {
        return $this->getText() ?  substr($this->getText(), 0, 64) : 'new';
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }


}
