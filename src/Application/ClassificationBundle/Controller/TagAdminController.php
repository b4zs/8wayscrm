<?php

namespace Application\ClassificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TagAdminController extends Controller
{
    public function getTagsAction()
    {
        $request = $this->container->get('request');
        $search = $request->get('search');

        $tagsQuery = $this->container
            ->get('doctrine')
            ->getRepository('ApplicationClassificationBundle:Tag')
            ->createQueryBuilder('tag')
            ->andWhere('tag.name LIKE :search')
            ->setParameter('search', sprintf('%%%s%%', $search));

        $pagination = $this->container
            ->get('knp_paginator')
            ->paginate(
                $tagsQuery,
                $request->get('page', 1),
                $request->get('limit', 10)
            );


        $tags = array();
        foreach ($pagination as $skill) {
            $tags[] = $skill->getName();
        }

        $paginationData = $pagination->getPaginationData();

        return new Response(
            json_encode(array(
                'results'   => $tags,
                'total'     => (int)$paginationData['totalCount'],
            )),
            200,
            array(
                'Content-type' => 'application/json',
            )
        );
    }


}