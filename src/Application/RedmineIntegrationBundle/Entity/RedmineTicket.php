<?php

namespace Application\RedmineIntegrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RedmineTicket
 *
 * @ORM\Table(name="redmine_ticket")
 * @ORM\Entity
 */
class RedmineTicket
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
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastSyncAt", type="datetime")
     */
    private $lastSyncAt;


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
     * Set subject
     *
     * @param string $subject
     *
     * @return RedmineTicket
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set lastSyncAt
     *
     * @param \DateTime $lastSyncAt
     *
     * @return RedmineTicket
     */
    public function setLastSyncAt($lastSyncAt)
    {
        $this->lastSyncAt = $lastSyncAt;

        return $this;
    }

    /**
     * Get lastSyncAt
     *
     * @return \DateTime
     */
    public function getLastSyncAt()
    {
        return $this->lastSyncAt;
    }
}

