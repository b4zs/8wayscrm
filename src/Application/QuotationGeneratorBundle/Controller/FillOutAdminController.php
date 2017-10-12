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
        $fillOut = $this->admin->getObject($id);

        if (!$fillOut) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('edit', $fillOut);

        return $this->render('ApplicationQuotationGeneratorBundle:FillOutAdmin:frontend.html.twig', array(
            'fillOut' => $fillOut,
        ));
    }
}