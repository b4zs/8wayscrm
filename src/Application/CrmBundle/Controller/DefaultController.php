<?php

namespace Application\CrmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ApplicationCrmBundle:Default:index.html.twig');
    }
}
