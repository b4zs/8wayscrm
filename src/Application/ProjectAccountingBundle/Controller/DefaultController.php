<?php

namespace Application\ProjectAccountingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ApplicationProjectAccountingBundle:Default:index.html.twig', array('name' => $name));
    }
}
