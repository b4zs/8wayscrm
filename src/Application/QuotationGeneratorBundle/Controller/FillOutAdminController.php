<?php


namespace Application\QuotationGeneratorBundle\Controller;


use Application\QuotationGeneratorBundle\Enum\FormType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class FillOutAdminController extends CRUDController
{
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