<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PermissionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PermissionRepository extends EntityRepository
{
    public function findByOrder(){
        $qb = $this->createQueryBuilder('p');
        $qb->orderBy('p.createTime','DESC');

        return $qb->getQuery()->getResult();
    }

    public function grantedPermissions($date){
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.state = :state');
        $qb->setParameter('state',1);

        return $qb->getQuery()->getResult();
    }

    /*public function enPermission($emp,$date,$savedHour,$normalHour){

        // The hours are on the H:i format
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.state = :state');
        $qb->setParameter('state',1);
        $qb->andWhere('p.employee = :e');
        $qb->setParameter('e',$emp);

        $permissionHourB = null;
        $permissionHourA = null;
        $result = $qb->getQuery()->getResult();
        $recycledResult = array();

        foreach ($result as $r){
            if($r->getDateFrom()->format("Y-m-d") == $date){
                $recycledResult[]=$r;
            }
        }

        if(sizeof($recycledResult)>0){

            $firstResult = $recycledResult[0];
            $permissionHourA = strtotime($date.' '.$firstResult->getTimeFrom());
            $permissionHourB = strtotime($date.' '.$firstResult->getTimeTo());

            $empHourA = strtotime($date.' '.$normalHour);
            $empHourB = strtotime($date.' '.$savedHour);

            if($empHourB <= $permissionHourB && $empHourA >= $permissionHourA){
                //print_r("\n OUI DATE : ".$date);
                return true;
            }else{
                //print_r("\n NON DATE : ".$date);
                return true;
            }
        } else{
            //print_r("\n NON DEUXIEME CAS ".$date);
            return false;
        }
    }*/

    public function enPermission($emp,$date){


        // The hours are on the H:i format
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.state = :state');
        $qb->setParameter('state',1);
        $qb->andWhere('p.employee = :e');
        $qb->setParameter('e',$emp);

        $permissionHourB = null;
        $permissionHourA = null;
        $result = $qb->getQuery()->getResult();
        $recycledResult = array();

        foreach ($result as $r){
            if($r->getDateFrom()->format("Y-m-d") == $date){
                $recycledResult[]=$r;
            }
        }

        if(sizeof($recycledResult)>0){

            $firstResult = $recycledResult[0];

            if((strtotime($date) <= strtotime($firstResult->getDateTo()->format("Y-m-d"))) && (strtotime($date) >= strtotime($firstResult->getDateFrom()->format("Y-m-d")))){
                //print_r("\n OUI DATE : ".$date);
                return true;
            }else{
                //print_r("\n NON DATE : ".$date);
                return true;
            }
        } else{
            //print_r("\n NON DEUXIEME CAS ".$date);
            return false;
        }
    }

    public function countPermission($state){
        $queryBuilder = $this->createQueryBuilder("p");
        $queryBuilder->select($queryBuilder->expr()->count("p"));
        $queryBuilder->where("p.state =:state")->setParameter("state", $state);

        $query = $queryBuilder->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }
}
