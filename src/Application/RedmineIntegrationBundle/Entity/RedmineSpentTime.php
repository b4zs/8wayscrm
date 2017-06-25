<?php

namespace Application\RedmineIntegrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RedmineSpentTime
 *
 * @ORM\Table(name="redmine_spent_time")
 * @ORM\Entity
 */
class RedmineSpentTime
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
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data;

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
     * Set data
     *
     * @param array $data
     *
     * @return RedmineSpentTime
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
     * Set lastSyncAt
     *
     * @param \DateTime $lastSyncAt
     *
     * @return RedmineSpentTime
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

