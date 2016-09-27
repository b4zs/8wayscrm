<?php

namespace Application\AdminBundle\Controller;


use Application\ClientBundle\Entity\Client;
use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{

    public function searchAction(Request $request) {

        $request = $this->get('request');

        $query = $request->get('query');

        $results = $queryBuilder = $this
            ->container
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('ApplicationObjectIdentityBundle:ObjectIdentity')
            ->createFulltextSearchQueryBuilder($query)
            ->andWhere('oid.type IN (:types)')
            ->setParameter('types', $this->getParameter('global_search.types'))
            ->getQuery()
            ->getResult()
        ;

        $resultList = $this->getResultList($results);
        $resultList = $this->paginate($resultList, $request);

        return $this->render('ApplicationAdminBundle:Search:search.html.twig', array(
            'results' => $resultList,
            'admin_pool' => $this->container->get('sonata.admin.pool')
        ));
    }

    protected function getResultList($results)
    {
        $resultProviderPool = $this->get('application_admin.util.search.result_provider_pool');
        $resultList = array();

        foreach ($results as $result) {
            /** @var ObjectIdentity $objectIdentity */
            $objectIdentity = $result['objectIdentity'];
            $reference = $objectIdentity->getReference();

            $resultProvider = $resultProviderPool->getProvider(get_class($reference));
            $resultList[] = $resultProvider->getData($reference);
        }
        return $resultList;
    }

    protected function paginate($target, Request $request, $defaultPage = 1, $limit = 10)
    {
        $pagination = $this->container->get('knp_paginator')
            ->paginate(
                $target,
                $request->get('page', $defaultPage),
                $request->get('limit', $limit)
            )
        ;

        return $pagination;

    }


}