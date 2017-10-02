<?php


namespace Application\CrmBundle\Controller;

use Application\CrmBundle\Entity\Project;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use JMS\Serializer\Serializer;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProjectAdminController extends Controller
{
    public function listAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        if ($listMode = $request->get('_list_mode', 'mosaic')) {
            $this->admin->setListMode($listMode);
        }

        $dataGrid = $this->admin->getDatagrid();

        $filters = $request->get('filter');

        // set the default context
        if (!$filters || !array_key_exists('context', $filters)) {
            $context = $this->admin->getPersistentParameter('context',
                $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $dataGrid->setValue('context', null, $context);
        $formView = $dataGrid->getForm()->createView();

        $count = $this->countResult();
        $result = $this->getResult(0);
        $serializeObject = $this->serializeObject($result);

        return $this->render('ApplicationCrmBundle:ProjectAdmin:list.html.twig', array(
            'action' => 'list',
            'form' => $formView,
            'datagrid' => json_encode($dataGrid),
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'serializedData' => $serializeObject,
            'countResults' => $count,
            'lastResultNumber' => 10,
        ));
    }

    public function loadMoreProjectAction(Request $request)
    {
        $numberOfFirstRow = $request->request->get('numberOfFirstRow');
        $count = $this->countResult();
        $result = $this->getResult($numberOfFirstRow);
        $serializeObject = $this->serializeObject($result);

        return new JsonResponse([
            'serializedData' => $serializeObject,
            'countResults' => $count,
            'lastResultNumber' => ($numberOfFirstRow + 10)
        ]);
    }

    /**
     * @param $numberOfFirstElement
     * @return array
     */
    private function getResult($numberOfFirstElement)
    {
        $limit = 10;
        /** @var NestedTreeRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository(Project::class);
        $result = $repo->createQueryBuilder('node')
            ->select('node')
            ->where('node.lvl = 0')
            ->setFirstResult($numberOfFirstElement)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return integer
     */
    private function countResult()
    {
        /** @var NestedTreeRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository(Project::class);
        $count = $repo->createQueryBuilder('node')
            ->select('count(node.id)')
            ->where('node.lvl = 0')
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    /**
     * @param $data
     * @return mixed|string
     */
    private function serializeObject($data)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        return $serializer->serialize($data, 'json');
    }
}