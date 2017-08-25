<?php


namespace Application\QuotationGeneratorBundle\Controller;


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
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('edit', $existingObject);

        return $this->render('ApplicationQuotationGeneratorBundle:FillOutAdmin:frontend.html.twig', array(

        ));
    }
}