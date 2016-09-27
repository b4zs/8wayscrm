<?php
namespace Application\ObjectIdentityBundle\Entity;

use Core\ObjectIdentityBundle\Entity\ObjectIdentityRepository as BaseRepository;

class ObjectIdentityRepository extends BaseRepository
{

    public function createTypeQueryBuilder(array $types, $alias = 'oid')
    {
        $queryBuilder = $this->createQueryBuilder($alias);
        $queryBuilder
            ->andWhere(sprintf('%s.type IN (:types)', $alias))
            ->setParameter('types', $types)
        ;

        return $queryBuilder;
    }

    public function findByTypes(array $types)
    {
        $queryBuilder = $this->createTypeQueryBuilder($types);

        return $queryBuilder->getQuery()->execute();
    }
}
