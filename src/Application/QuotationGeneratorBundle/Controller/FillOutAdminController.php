<?php


namespace Application\QuotationGeneratorBundle\Controller;


use Application\CrmBundle\Entity\Project;
use Application\QuotationGeneratorBundle\Entity\FillOut;
use Application\QuotationGeneratorBundle\Enum\FormType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FillOutAdminController extends CRUDController
{
    public function createAction()
    {
        if ($this->admin->isChild() && $this->admin->getParent()->getSubject() instanceof Project) {
            $newObject = $this->admin->getNewInstance();
            $this->admin->setSubject($newObject);
            $this->admin->create($newObject);

            $url = $this->admin->generateObjectUrl('frontend', $newObject);
            return new RedirectResponse($url);
        } else {
            return parent::createAction();
        }
    }


    protected function redirectTo($object)
    {
        //hack to redirect to "frontend" after CREATE method submitted
        if ($this->getRequest()->getMethod() === 'POST' && !$this->getRequest()->get('id')) {
            $url = $this->admin->generateObjectUrl('frontend', $object);
            return new RedirectResponse($url);
        }

        return parent::redirectTo($object);
    }

    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);

        if ($existingObject instanceof FillOut && $existingObject->getName()) {
            return $this->frontendAction($request);
        } else {
            return parent::editAction($id);
        }
    }

    public function frontendAction(Request $request)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $form = $this->admin->getForm();
        $form->setData($object);

        $this->admin->checkAccess('edit', $object);

        return $this->render('ApplicationQuotationGeneratorBundle:FillOutAdmin:frontend.html.twig', array(
            'action'    => 'edit',
            'object'    => $object,
            'form'      => $form->createView(),
            'admin'     => $this->admin,
        ));
    }
}