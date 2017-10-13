<?php


namespace Application\CrmBundle\Controller;

use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Enum\ProjectStatus;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use JMS\Serializer\Serializer;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProjectAdminController extends Controller
{
    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('show', $object);

        $preResponse = $this->preShow($request, $object);

        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        return $this->render('ApplicationCrmBundle:ProjectAdmin:show.html.twig', array(
            'action' => 'show',
            'object' => $object,
            'elements' => $this->admin->getShow(),
        ), null);
    }

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

        if (!$filters || !array_key_exists('context', $filters)) {
            $context = $this->admin->getPersistentParameter('context',
                $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $dataGrid->setValue('context', null, $context);
        $formView = $dataGrid->getForm()->createView();
        $count = $this->countResult();
        $isFilterSet = false;

        if ($filters !== null) {
            $isFilterSet = true;
        }

        if ($isFilterSet) {
            return $this->render($this->admin->getTemplate('list'), array(
                'action' => 'list',
                'form' => $formView,
                'datagrid' => $dataGrid,
                'csrf_token' => $this->getCsrfToken('sonata.batch'),
                'isFilterSet' => $isFilterSet,
            ));
        }

        $result = $this->getResult(0);

        $serializeObject = $this->serializeObject($result);

        return $this->render('ApplicationCrmBundle:ProjectAdmin:list.html.twig', array(
            'action' => 'list',
            'form' => $formView,
            'datagrid' => $dataGrid,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'serializedData' => $serializeObject,
            'countResults' => $count,
            'lastResultNumber' => 10,
            'isFilterSet' => $isFilterSet,
            'projectStatusArray' => json_encode(ProjectStatus::getChoices()),
        ));
    }

    public function loadMoreProjectAction(Request $request)
    {
        $numberOfFirstRow = $request->request->get('numberOfFirstRow');
        $count = $this->countResult();
        $result = $this->getResult($numberOfFirstRow);
        $serializeObject = $this->serializeObject($result, 'array');

        return new JsonResponse([
            'serializedData' => $serializeObject,
            'countResults' => $count,
            'lastResultNumber' => ($numberOfFirstRow + 10)
        ]);
    }

    public function loadChildrenAction(Request $request)
    {
        $parentId = $request->request->get('id');
        /** @var NestedTreeRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository(Project::class);
        /** @var Project $parent */
        $parent = $repo->findOneBy([
            'id' => $parentId,
        ]);

        $children = $parent->getChildren(false);

        $object = $this->serializeObject($children, 'array');

        return new JsonResponse($object);
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
        $qb = $repo->createQueryBuilder('node')
            ->select('node')
            ->where('node.lvl = 0')
            ->setFirstResult($numberOfFirstElement)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $qb;
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
     * @param string $format
     * @return array|mixed|string
     */
    private function serializeObject($data, $format = 'json')
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');

        if ($format == 'array') {
            return $serializer->toArray($data);
        }

        return $serializer->serialize($data, $format);
    }
}