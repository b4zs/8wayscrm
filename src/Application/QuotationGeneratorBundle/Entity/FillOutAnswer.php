<?php

namespace Application\QuotationGeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * FillOutAnswer
 */
class FillOutAnswer
{
    /**
     * @var integer
     */
    private $id;


    /**
     * @var integer
     */
    private $step;

    /**
     * @var array
     * @Serializer\SerializedName(value="param")
     */
    private $data = array();

    /**
     * @var string
     * @Serializer\SerializedName(value="value")
     */
    private $value;

    /**
     * @var \Application\QuotationGeneratorBundle\Entity\Question
     * @Serializer\Accessor(getter="getQuestionId")
     * @Serializer\SerializedName(value="question_id")
     * @Serializer\Type(name="integer")
     */
    private $question;

    /**
     * @var \Application\QuotationGeneratorBundle\Entity\QuestionOption
     * @Serializer\Exclude()
     */
    private $option;

    /**
     * @var \Application\QuotationGeneratorBundle\Entity\FillOut
     * @Serializer\Exclude()
     */
    private $fillOut;

    /**
     * @var \DateTime
     */
    private $createdAt;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return FillOutAnswer
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
     * Set step
     *
     * @param integer $step
     * @return FillOutAnswer
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return FillOutAnswer
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return FillOutAnswer
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
     * Set question
     *
     * @param \Application\QuotationGeneratorBundle\Entity\Question $question
     * @return FillOutAnswer
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
     * Set option
     *
     * @param \Application\QuotationGeneratorBundle\Entity\QuestionOption $option
     * @return FillOutAnswer
     */
    public function setOption(\Application\QuotationGeneratorBundle\Entity\QuestionOption $option = null)
    {
        $this->option = $option;
        if (null !== $option) {
            $this->setValue($option->getValue());
        }

        return $this;
    }

    /**
     * Get option
     *
     * @return \Application\QuotationGeneratorBundle\Entity\QuestionOption 
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set fillOut
     *
     * @param \Application\QuotationGeneratorBundle\Entity\FillOut $fillOut
     * @return FillOutAnswer
     */
    public function setFillOut(\Application\QuotationGeneratorBundle\Entity\FillOut $fillOut = null)
    {
        $this->fillOut = $fillOut;

        return $this;
    }

    /**
     * Get fillOut
     *
     * @return \Application\QuotationGeneratorBundle\Entity\FillOut 
     */
    public function getFillOut()
    {
        return $this->fillOut;
    }

    public function getQuestionId()
    {
        return $this->getQuestion() ? $this->getQuestion()->getId() : null;
    }
}
