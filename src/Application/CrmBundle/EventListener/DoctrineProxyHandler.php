<?php


namespace Application\CrmBundle\EventListener;


use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;

class DoctrineProxyHandler extends DoctrineProxySubscriber
{
    /**
     * @var bool
     */
    private $skipVirtualTypeInit = false;

    /**
     * @var bool
     */
    private $initializeExcluded = true;

    /**
     * @param PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();
        $type = $event->getType();

        // If the set type name is not an actual class, but a faked type for which a custom handler exists, we do not
        // modify it with this subscriber. Also, we forgo autoloading here as an instance of this type is already created,
        // so it must be loaded if its a real class.
        $virtualType = !class_exists($type['name'], false);

        if ($object instanceof PersistentCollection
            || $object instanceof MongoDBPersistentCollection
            || $object instanceof PHPCRPersistentCollection
        ) {
            if (!$virtualType) {
                $event->setType('ArrayCollection');
            }

            return;
        }

        if (($this->skipVirtualTypeInit && $virtualType) ||
            (!$object instanceof Proxy && !$object instanceof ORMProxy)
        ) {
            return;
        }

        // do not initialize the proxy if is going to be excluded by-class by some exclusion strategy
        if ($this->initializeExcluded === false && !$virtualType) {
            $context = $event->getContext();
            $exclusionStrategy = $context->getExclusionStrategy();
            if ($exclusionStrategy !== null && $exclusionStrategy->shouldSkipClass(
                $context->getMetadataFactory()->getMetadataForClass(get_parent_class($object)), $context)) {
                return;
            }
        }

        //$object->__load();

        if (!$virtualType) {
            $event->setType(get_parent_class($object), $type['params']);
        }
    }
}