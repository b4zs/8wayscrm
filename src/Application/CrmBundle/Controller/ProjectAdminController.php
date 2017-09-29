<?php


namespace Application\CrmBundle\Controller;

use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Repository\ProjectRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Tree\Node;
use JMS\Serializer\Serializer;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
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
            $context = $this->admin->getPersistentParameter('context', $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $dataGrid->setValue('context', null, $context);

//        $data = $dataGrid->getQuery()->execute(array());

        /** @var NestedTreeRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository(Project::class);
        $arrayTree = $repo->childrenHierarchy();
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        $serializedData = $serializer->serialize($arrayTree, 'json');

//
//        // retrieve the main category for the tree view
//        $category = $this->container->get('sonata.classification.manager.category')->getRootCategory($context);
//
//        if (!$filters) {
//            $dataGrid->setValue('category', null, $category);
//        }
//        if ($request->get('category')) {
//            $categoryByContext = $this->container->get('sonata.classification.manager.category')->findOneBy(array(
//                'id' => (int) $request->get('category'),
//                'context' => $context,
//            ));
//
//            if (!empty($categoryByContext)) {
//                $dataGrid->setValue('category', null, $categoryByContext);
//            } else {
//                $dataGrid->setValue('category', null, $category);
//            }
//        }
//
        $formView = $dataGrid->getForm()->createView();

        //get subProjects


//
//        // set the theme for the current Admin Form
//        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('ApplicationCrmBundle:ProjectAdmin:list.html.twig', array(
            'action' => 'list',
            'form' => $formView,
            'datagrid' => json_encode($dataGrid),
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'serializedData' => $serializedData,
        ));
    }
}