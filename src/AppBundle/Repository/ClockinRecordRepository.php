<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * ClockinRecordRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClockinRecordRepository extends EntityRepository
{
    public function history($depId,$bMin,$bMax,$pBMin,$pBMax,$pEMin,$pEMax,$eMin,$eMax){
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.clockinTime BETWEEN :bMin AND :bMax');
        $queryBuilder->setParameter('bMin',$bMin);
        $queryBuilder->setParameter('bMax',$bMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :eMin AND :eMax');
        $queryBuilder->setParameter('eMin',$eMin);
        $queryBuilder->setParameter('eMax',$eMax);

        // Pour les pauses

        $queryBuilder->orWhere('c.clockinTime BETWEEN :pBMin AND :pBMax');
        $queryBuilder->setParameter('pBMin',$pBMin);
        $queryBuilder->setParameter('pBMax',$pBMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :pEMin AND :pEMax');
        $queryBuilder->setParameter('pEMin',$pEMin);
        $queryBuilder->setParameter('pEMax',$pEMax);

        $queryBuilder->andWhere('c.departement = :id')->setParameter('id',$depId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function empHistory($empId,$depId,$bMin,$bMax,$pBMin,$pBMax,$pEMin,$pEMax,$eMin,$eMax){
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder->where('c.clockinTime BETWEEN :bMin AND :bMax');
        $queryBuilder->setParameter('bMin',$bMin);
        $queryBuilder->setParameter('bMax',$bMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :eMin AND :eMax');
        $queryBuilder->setParameter('eMin',$eMin);
        $queryBuilder->setParameter('eMax',$eMax);

        // Pour les pauses

        $queryBuilder->orWhere('c.clockinTime BETWEEN :pBMin AND :pBMax');
        $queryBuilder->setParameter('pBMin',$pBMin);
        $queryBuilder->setParameter('pBMax',$pBMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :pEMin AND :pEMax');
        $queryBuilder->setParameter('pEMin',$pEMin);
        $queryBuilder->setParameter('pEMax',$pEMax);

        $queryBuilder->andWhere('c.employe = :empId')->setParameter('empId',$empId);
        $queryBuilder->andWhere('c.departement = :depId')->setParameter('depId',$depId);

        return $queryBuilder->getQuery()->getResult();

    }

    public function empAllHistory($empId,$min,$max){
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.clockinTime BETWEEN :min AND :max');
        $queryBuilder->setParameter('min',$min);
        $queryBuilder->setParameter('max',$max);

        $queryBuilder->andWhere('c.employe = :empId')->setParameter('empId',$empId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function empHistorySimple($empId,$depId,$bMin,$bMax,$pBMin,$pBMax,$pEMin,$pEMax,$eMin,$eMax){
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder->where('c.clockinTime BETWEEN :bMin AND :bMax');
        $queryBuilder->setParameter('bMin',$bMin);
        $queryBuilder->setParameter('bMax',$bMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :eMin AND :eMax');
        $queryBuilder->setParameter('eMin',$eMin);
        $queryBuilder->setParameter('eMax',$eMax);

        // Pour les pauses

        $queryBuilder->orWhere('c.clockinTime BETWEEN :pBMin AND :pBMax');
        $queryBuilder->setParameter('pBMin',$pBMin);
        $queryBuilder->setParameter('pBMax',$pBMax);
        $queryBuilder->orWhere('c.clockinTime BETWEEN :pEMin AND :pEMax');
        $queryBuilder->setParameter('pEMin',$pEMin);
        $queryBuilder->setParameter('pEMax',$pEMax);

        $queryBuilder->andWhere('c.employe = :empId')->setParameter('empId',$empId);
        $queryBuilder->andWhere('c.departement = :depId')->setParameter('depId',$depId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function present($emp,$date,$bMin,$bMax,$pBMin,$pBMax,$pEMin,$pEMax,$eMin,$eMax){

        $queryResult = $this->empHistory($emp->getId(),$emp->getDepartement()->getId(),$bMin,$bMax,$pBMin,$pBMax,$pEMin,$pEMax,$eMin,$eMax);
        if($queryResult != null && sizeof($queryResult)>0){
            return true;
        }else{
            return false;
        }
    }

    public function retard($emp,$date,$interval,$heureNormaleArrive,$bH=null){

        $heureNormaleArrive = $heureNormaleArrive+$date;

        $bHTab = explode(":",$bH);
        $maxDate = $heureNormaleArrive+$interval;

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);
        $queryBuilder->andWhere('c.clockinTime > :date');
        $queryBuilder->setParameter('date',$heureNormaleArrive);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $ct = $donn[0]->getClockinTime();
            $diff = ($ct-($heureNormaleArrive)); // Timestamp
            return array($diff,$ct);
        }else{
            return 0;
        }
    }

    public function retardPause($emp,$date,$interval_pause,$heureNormaleArrivePause,$pEH=null){

        $heureNormaleArrivePause = $heureNormaleArrivePause+$date;

        $pEHTab = explode(":",$pEH);
        if(!empty($pEH) && $pEH != null){
            $minutes = ($pEHTab[0]*60*60)+($pEHTab[1]*60);
        }else{
            $minutes = 0;
        }
        $maxDate = $heureNormaleArrivePause+$interval_pause;

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);
        $queryBuilder->andWhere('c.clockinTime > :date');
        $queryBuilder->setParameter('date',$heureNormaleArrivePause);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $ct = $donn[0]->getClockinTime();
            $diff = $ct- ($heureNormaleArrivePause); // Timestamp
            return array($diff,$ct);
        }else{
            return 0;
        }
    }

    public function quota($emp,$date,$interval){

        $heureNormalArrive = (60*60*6)+(60*30); // Timestamp 60sec * 60min * 6heures + 30min = 6h30

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);
        $queryBuilder->andWhere('c.clockinTime > :date');
        $queryBuilder->setParameter('date',$date+$heureNormalArrive);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$date+$heureNormalArrive+$interval);
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $ct = $donn[0]->getClockinTime();
            $diff = $ct- ($date+$heureNormalArrive); // Timestamp
            return $diff;
        }else{
            return 0;
        }
    }

    public function departPremature($emp,$date,$interval,$heureNormaleDepart){

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);
        $queryBuilder->andWhere('c.clockinTime < :date');
        $queryBuilder->setParameter('date',$date+$heureNormaleDepart);
        $queryBuilder->andWhere('c.clockinTime >= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$date+$heureNormaleDepart-$interval);
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $ct = $donn[0]->getClockinTime();
            $diff = ($date+$heureNormaleDepart)-$ct; // Timestamp
            return array($diff,$ct);
        }else{
            return false;
        }
    }

    public function departPausePremature($emp,$date,$interval,$heureNormaleDepartPause){

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);
        $queryBuilder->andWhere('c.clockinTime < :date');
        $queryBuilder->setParameter('date',$date+$heureNormaleDepartPause);
        $queryBuilder->andWhere('c.clockinTime >= (:minDate)');
        $queryBuilder->setParameter('minDate',$date+$heureNormaleDepartPause-$interval);
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $ct = $donn[0]->getClockinTime();
            $diff = ($date+$heureNormaleDepartPause)-$ct; // Timestamp
            return array($diff,$ct);
        }else{
            return false;
        }
    }
}
