<?php
namespace Application\CrmBundle\EventListener;

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


    public function postUpdate(LifecycleEventArgs $args) {
        $object = $args->getObject();

        if($object instanceof Project) {
            $this->manager->updateObjectIndex($object->getClient());
        }

        if($object instanceof Contact) {
            $this->manager->updateObjectIndex($object->getClient());
        }
    }

}