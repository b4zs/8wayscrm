<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\UserBundle\Entity;

use Application\CrmBundle\Entity\Person;
use Application\CrmBundle\Entity\ProjectMembership;
use Application\CrmBundle\Model\OwnerGroupAware;
use Application\MediaBundle\Entity\Gallery;
use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Application\ObjectIdentityBundle\Model\ObjectIdentityAwareTrait;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Core\ObjectIdentityBundle\Model\ObjectIdentityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class User extends BaseUser implements
    OwnerGroupAware,
    \Octet\Ticketing\Lib\Model\UserInterface,
    ObjectIdentityAware
{
    use ObjectIdentityAwareTrait;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Group
     */
    private $primaryGroup;

    protected $title;

    /**
     * @var integer
     */
    protected $redmineUserId;

    /** @var  string */
    protected $nationality;

    /**
     * @var Collection|ProjectMembership[]
     */
    protected $projectMemberships;

    private $workPermit;

    private $privateEmail;

    private $workLine;

    private $workMobileLine;

    private $privateHomeLine;

    private $privateMobileLine;

    private $privateAddress;

    private $holidaysRemaining;

    private $fileset;

    public function __construct()
    {
        parent::__construct();
        $this->projectMemberships = new ArrayCollection();
        $this->enabled = true;
        $this->setRoles(array('ROLE_SONATA_ADMIN', 'ROLE_SALES', 'ROLE_ADMIN'));
        $this->fileset = new Gallery();
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function addProjectMembership(ProjectMembership $projectMembership)
    {
        $this->projectMemberships->add($projectMembership);
    }

    public function removeProjectMembership(ProjectMembership $projectMembership)
    {
        $this->projectMemberships->remove($projectMembership);
    }

    /**
     * @return \Application\CrmBundle\Entity\ProjectMembership[]|Collection
     */
    public function getProjectMemberships()
    {
        return $this->projectMemberships;
    }

    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFirstname() || $this->getLastname()
            ? $this->getFirstname() . ' ' . $this->getLastname()
            : $this->getUsername();
    }

    /**
     * @return Group
     */
    public function getPrimaryGroup()
    {
        return $this->primaryGroup;
    }

    /**
     * @param Group $primaryGroup
     */
    public function setPrimaryGroup($primaryGroup)
    {
        $this->primaryGroup = $primaryGroup;
    }


    public function buildGroups()
    {
        $g = $this->getGroups()->toArray();
        $g[] = $this->getPrimaryGroup();
        $g = array_filter($g);
        $g = array_unique($g);
        $g = array_map(function($g){ return $g->getName();}, $g);

        return implode(', ', $g);
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getRedmineUserId()
    {
        return $this->redmineUserId;
    }

    /**
     * @param int $redmineUserId
     */
    public function setRedmineUserId($redmineUserId)
    {
        $this->redmineUserId = $redmineUserId;
    }

    /**
     * @return mixed
     */
    public function getPrivateEmail()
    {
        return $this->privateEmail;
    }

    /**
     * @param mixed $privateEmail
     */
    public function setPrivateEmail($privateEmail)
    {
        $this->privateEmail = $privateEmail;
    }

    /**
     * @return mixed
     */
    public function getWorkLine()
    {
        return $this->workLine;
    }

    /**
     * @param mixed $workLine
     */
    public function setWorkLine($workLine)
    {
        $this->workLine = $workLine;
    }

    /**
     * @return mixed
     */
    public function getWorkMobileLine()
    {
        return $this->workMobileLine;
    }

    /**
     * @param mixed $workMobileLine
     */
    public function setWorkMobileLine($workMobileLine)
    {
        $this->workMobileLine = $workMobileLine;
    }

    /**
     * @return mixed
     */
    public function getPrivateHomeLine()
    {
        return $this->privateHomeLine;
    }

    /**
     * @param mixed $privateHomeLine
     */
    public function setPrivateHomeLine($privateHomeLine)
    {
        $this->privateHomeLine = $privateHomeLine;
    }

    /**
     * @return mixed
     */
    public function getPrivateMobileLine()
    {
        return $this->privateMobileLine;
    }

    /**
     * @param mixed $privateMobileLine
     */
    public function setPrivateMobileLine($privateMobileLine)
    {
        $this->privateMobileLine = $privateMobileLine;
    }

    /**
     * @return mixed
     */
    public function getPrivateAddress()
    {
        return $this->privateAddress;
    }

    /**
     * @param mixed $privateAddress
     */
    public function setPrivateAddress($privateAddress)
    {
        $this->privateAddress = $privateAddress;
    }

    /**
     * @return mixed
     */
    public function getHolidaysRemaining()
    {
        return $this->holidaysRemaining;
    }

    /**
     * @param mixed $holidaysRemaining
     */
    public function setHolidaysRemaining($holidaysRemaining)
    {
        $this->holidaysRemaining = $holidaysRemaining;
    }

    /**
     * @return Gallery
     */
    public function getFileset()
    {
        return $this->fileset;
    }

    /**
     * @param Gallery $fileset
     */
    public function setFileset($fileset)
    {
        $this->fileset = $fileset;
    }

    public function updateFilesetName(LifecycleEventArgs $eventArgs)
    {
        $this->getFileset()->setName($this->__toString());
    }

    /**
     * @return mixed
     */
    public function getWorkPermit()
    {
        return $this->workPermit;
    }

    /**
     * @param mixed $workPermit
     */
    public function setWorkPermit($workPermit)
    {
        $this->workPermit = $workPermit;
    }

    public function addGalleryHasMedias(GalleryHasMediaInterface $galleryHasMedia)
    {
        $this->getFileset()->addGalleryHasMedias($galleryHasMedia);
    }


    public function getReminders()
    {
        // TODO: Implement getReminders() method.
    }

    public function setReminders()
    {
        // TODO: Implement setReminders() method.
    }

    public function addReminder()
    {
        // TODO: Implement addReminder() method.
    }

    public function removeReminder()
    {
        // TODO: Implement removeReminder() method.
    }

    public function getNotes()
    {
        // TODO: Implement getNotes() method.
    }

    public function setNotes()
    {
        // TODO: Implement setNotes() method.
    }

    public function addNote()
    {
        // TODO: Implement addNote() method.
    }

    public function removeNote()
    {
        // TODO: Implement removeNote() method.
    }

    /**
     * @return string
     */
    public function getCanonicalName()
    {
        return $this->getUsernameCanonical();
    }

}