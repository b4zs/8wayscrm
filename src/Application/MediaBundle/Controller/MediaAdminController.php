<?php


namespace Application\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController as BaseController;
use Symfony\Component\HttpFoundation\Request;


class MediaAdminController extends BaseController
{
    public function listAction(Request $request = null)
    {
        return parent::listAction($request);

    }

}