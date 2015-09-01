<?php

namespace Application\CrmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return new RedirectResponse($this->container->get('router')->generate('sonata_admin_dashboard'));
    }
}
