<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EmployeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeRepository extends EntityRepository
{
    public function employeeByDep($depId){
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.departement = :depId')->setParameter('depId',$depId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function employeeSafe(){
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.roles != :role');
        $queryBuilder->setParameter('role',serialize(array("ROLE_SUPER_ADMIN")));

        return $queryBuilder->getQuery()->getResult();
    }
}
