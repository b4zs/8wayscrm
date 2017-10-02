<?php
namespace Application\CrmBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ClientRepository extends EntityRepository
{
    /**
     * @param $status
     * @return int
     */
    public function getClientCountByStatus($status) {
        $qb = $this->createQueryBuilder('client');

        $qb->select('count(client.id) as c');
        $qb->where('client.status = :status');
        $qb->groupBy('client.status');
        $qb->setParameter('status', $status);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return empty($result) ? 0 : $result[0]['c'];
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    public function getNewClientsCountSince(\DateTime $dateTime) {
        $qb = $this->createQueryBuilder('client');

        $qb->select('count(1) as c');
        $qb->where('client.createdAt >= :createdAt');
        $qb->setParameter('createdAt', $dateTime);

        return $qb->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }

}