<?php
namespace Application\CrmBundle\EventListener;

use Application\CrmBundle\Entity\Company;
use Core\ObjectIdentityBundle\Model\ObjectIdentityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Entity\Project;

class PostUpdateEventListener
{

    /**
     * @var ObjectIdentityManager
     */
    protected $manager;

    /**
     * AdminPostUpdateEventListener constructor.
     * @param ObjectIdentityManager $manager
     */
    public function __construct(ObjectIdentityManager $manager)
    {
        $this->manager = $manager;
    }

    public function postPersist(LifecycleEventArgs $args) {
        $this->updateIndex($args->getObject());
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $this->updateIndex($args->getObject());
    }

    protected function updateIndex($object) {

        if($object instanceof Project) {
            $this->manager->updateObjectIndex($object->getClient());
        }

        if($object instanceof Contact) {
            $this->manager->updateObjectIndex($object->getClient());
        }

        if($object instanceof Company) {
            $this->manager->updateObjectIndex($object->getClient());
        }
    }

}