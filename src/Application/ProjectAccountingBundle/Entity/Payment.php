<?php

namespace Application\ProjectAccountingBundle\Entity;

use Application\CrmBundle\Entity\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="accounting__payment")
 * @ORM\Entity(repositoryClass="Application\ProjectAccountingBundle\Entity\PaymentRepository")
 */
class Payment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToMany(targetEntity="Application\ProjectAccountingBundle\Entity\Invoice", mappedBy="payments")
     * @ORM\JoinTable(name="accounting__invoice_payment")
     */
    private $invoices;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Application\CrmBundle\Entity\Client")
     */
    private $client;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="settlementDate", type="datetime", nullable=true)
     */
    private $settlementDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="createdBy", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var Price
     *
     * @ORM\Embedded(class="Application\ProjectAccountingBundle\Entity\Price")
     */
    private $amount;

    public function __construct()
    {
        $this->amount = new Price();
        $this->invoices = new ArrayCollection();
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
     * Set client
     *
     * @param integer $client
     *
     * @return Payment
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return integer
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set settlementDate
     *
     * @param \DateTime $settlementDate
     *
     * @return Payment
     */
    public function setSettlementDate($settlementDate)
    {
        $this->settlementDate = $settlementDate;

        return $this;
    }

    /**
     * Get settlementDate
     *
     * @return \DateTime
     */
    public function getSettlementDate()
    {
        return $this->settlementDate;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Payment
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return Payment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}

