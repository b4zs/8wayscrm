<?php
namespace Application\CrmBundle\Util\Search;

use Application\AdminBundle\Util\Search\Result;
use Application\AdminBundle\Util\Search\ResultProviderInterface;
use Sonata\AdminBundle\Admin\Pool;

class ProjectResultProvider implements ResultProviderInterface
{

    /** @var Pool */
    private $adminPool;

    public function __construct(Pool $adminPool)
    {
        $this->adminPool = $adminPool;
    }

    public function getData($object)
    {
        $result = new Result();
        $result->setObject($object);
        $result->setTitle((string)$object);
        $result->setDescription('');
        $result->setUrl($this->getLink($object));

        return $result;
    }

    private function getLink($object)
    {
        $clientAdmin = $this->adminPool->getAdminByAdminCode('application_crm.admin.project');

        if($clientAdmin->isGranted('VIEW', $object)){
            return $clientAdmin->generateObjectUrl('show', $object);
        }

        return null;
    }

}