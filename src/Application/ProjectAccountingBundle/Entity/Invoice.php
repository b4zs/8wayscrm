<?php

namespace Application\ProjectAccountingBundle\Entity;

use Application\CrmBundle\Entity\Address;
use Application\MediaBundle\Entity\Gallery;
use Application\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="accounting__invoice")
 * @ORM\Entity(repositoryClass="Application\ProjectAccountingBundle\Entity\InvoiceRepository")
 */
class Invoice
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
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Application\CrmBundle\Entity\Project")
     */
    private $project;

    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Application\CrmBundle\Entity\Address")
     */
    private $clientBillingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var Work[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\ProjectAccountingBundle\Entity\Work")
     */
    private $works;

    /**
     * @var Price
     *
     * @ORM\Embedded(class="Application\ProjectAccountingBundle\Entity\Price")
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issuedAt", type="datetime", nullable=true)
     */
    private $issuedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime", nullable=true)
     */
    private $dueDate;

    /**
     * @var Gallery
     *
     * @ORM\ManyToOne(targetEntity="Application\MediaBundle\Entity\Gallery")
     */
    private $fileSet;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\UserBundle\Entity\User")
     */
    private $createdBy;

    /**
     * @var Payment[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\ProjectAccountingBundle\Entity\Payment", inversedBy="invoices")
     * @ORM\JoinTable(name="accounting__invoice_payment")
     */
    private $payments;

    public function __construct()
    {
        $this->total = new Price();
        $this->works = new ArrayCollection();
        $this->fileSet = new Gallery();
        $this->createdAt = new \DateTime();
        $this->payments = new ArrayCollection();
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
     * Set project
     *
     * @param integer $project
     *
     * @return Invoice
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return integer
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set clientBillingAddress
     *
     * @param integer $clientBillingAddress
     *
     * @return Invoice
     */
    public function setClientBillingAddress($clientBillingAddress)
    {
        $this->clientBillingAddress = $clientBillingAddress;

        return $this;
    }

    /**
     * Get clientBillingAddress
     *
     * @return integer
     */
    public function getClientBillingAddress()
    {
        return $this->clientBillingAddress;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Invoice
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setWorks($works)
    {
        $this->works = $works;

        return $this;
    }


    public function getWorks()
    {
        return $this->works;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set issuedAt
     *
     * @param \DateTime $issuedAt
     *
     * @return Invoice
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt
     *
     * @return \DateTime
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Invoice
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Invoice
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Invoice
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
     * @return Invoice
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

    /**
     * Set payments
     *
     * @param integer $payments
     *
     * @return Invoice
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;

        return $this;
    }

    /**
     * Get payments
     *
     * @return integer
     */
    public function getPayments()
    {
        return $this->payments;
    }

    public function getFileSet()
    {
        return $this->fileSet;
    }

    public function setFileSet($fileSet)
    {
        $this->fileSet = $fileSet;
    }
}

