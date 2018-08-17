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
        $cur_date = date('Y-m-d',$pBMax);
        $cur_time_min = strtotime($cur_date." 00:00:00");
        $cur_time_max = strtotime($cur_date." 23:59:00");
        if($bMin < $cur_time_min){
            $bMin = $cur_time_min;
        }
        if($eMax > $cur_time_max){
            $eMax = $cur_time_max;
            /*print_r("**Trace : $empId : $bMin (".date('Y-m-d H:i:s',$bMin).") : $cur_date\n");
            print_r("++Trace : $empId : $bMax (".date('Y-m-d H:i:s',$bMax).") : $cur_date\n");
            print_r("@Trace : $empId : $eMax (".date('Y-m-d H:i:s',$eMax).") : $cur_date\n");
            print_r("#Trace : $empId : $eMin (".date('Y-m-d H:i:s',$eMin).") : $cur_date\n");
        */}
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
        $queryBuilder->addOrderBy('c.clockinTime','ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function empAllHistory($empId,$min,$max){
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.clockinTime BETWEEN :min AND :max');
        $queryBuilder->setParameter('min',$min);
        $queryBuilder->setParameter('max',$max);

        $queryBuilder->andWhere('c.employe = :empId')->setParameter('empId',$empId);
        $queryBuilder->addOrderBy('c.clockinTime','ASC');

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

    function ma_fonction($a, $b) {
        if($a == $b){ return 0 ; }
        return ($a < $b) ? -1 : 1;
    }

    public function retard($emp,$date,$interval,$heureNormaleArrive,$bH=null){

        $heureNormaleArrive = $heureNormaleArrive+$date;

        $bHTab = explode(":",$bH);
        $maxDate = $heureNormaleArrive+$interval;
        $minDate = $heureNormaleArrive-$interval;

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);

        $queryBuilder->andWhere('c.clockinTime >= :minDate');
        $queryBuilder->setParameter('minDate',$minDate);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);

        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $new_tab = array();
            foreach($donn as $element){
                $new_tab[] = $element->getClockinTime();
            }
            usort($new_tab, array($this, 'ma_fonction'));
            $ct = $new_tab[0];
            $diff = ($ct-($heureNormaleArrive)); // Timestamp
            /*if($ct<=$heureNormaleArrive ){
                return null;
            }else{
                return array($diff,$ct);
            }*/
            return array($diff,$ct);
        }else{
            return false;
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
        $minDate = $heureNormaleArrivePause-$interval_pause;

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);

        $queryBuilder->andWhere('c.clockinTime >= :minDate');
        $queryBuilder->setParameter('minDate',$minDate);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);
        
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $new_tab = array();
            foreach($donn as $element){
                $new_tab[] = $element->getClockinTime();
            }
            usort($new_tab, array($this, 'ma_fonction'));
            $ct = $new_tab[sizeof($new_tab)-1];
            $diff = $ct- ($heureNormaleArrivePause); // Timestamp
            /*if($ct<=$heureNormaleArrivePause ){
                return null;
            }else{
                return array($diff,$ct);
            }*/
            return array($diff,$ct);
        }else{
            return false;
        }
    }

    public function departPremature($emp,$date,$interval,$heureNormaleDepart){

        $maxDate = $date+$heureNormaleDepart+$interval;
        $minDate = $date+$heureNormaleDepart-$interval;

        /*print_r("Max date : ".date('Y-m-d H:i',$maxDate));
        print_r("Min date : ".date('Y-m-d H:i',$minDate));
        print_r("Date : ".date('Y-m-d H:i',$date));*/

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);

        $queryBuilder->andWhere('c.clockinTime >= :minDate');
        $queryBuilder->setParameter('minDate',$minDate);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);
        
        $donn = $queryBuilder->getQuery()->getResult();
        if($donn != null){
            $new_tab = array();
            foreach($donn as $element){
                $new_tab[] = $element->getClockinTime();
            }
            usort($new_tab, array($this, 'ma_fonction'));
            $ct = $new_tab[sizeof($new_tab)-1];
            //print_r("****".date('Y-m-d H:i',$new_tab[1]));
            $diff = ($date+$heureNormaleDepart)-$ct; // Timestamp
            /*if($ct>=($date+$heureNormaleDepart)){
                return null;
            }else{
                return array($diff,$ct);
            }*/
            return array($diff,$ct);
        }else{
            return false;
        }
    }

    public function departPausePremature($emp,$date,$interval,$heureNormaleDepartPause){

        $maxDate = $date+$heureNormaleDepartPause+$interval;
        $minDate = $date+$heureNormaleDepartPause-$interval;

        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.employe = :emp')->setParameter('emp',$emp);

        $queryBuilder->andWhere('c.clockinTime >= :minDate');
        $queryBuilder->setParameter('minDate',$minDate);
        $queryBuilder->andWhere('c.clockinTime <= (:maxDate)');
        $queryBuilder->setParameter('maxDate',$maxDate);

        $donn = $queryBuilder->getQuery()->getResult();
        
        if($donn != null){
            $new_tab = array();
            foreach($donn as $element){
                $new_tab[] = $element->getClockinTime();
            }
            usort($new_tab, array($this, 'ma_fonction'));
            $ct = $new_tab[sizeof($new_tab)-1];
            $diff = ($date+$heureNormaleDepartPause)-$ct; // Timestamp
            /*if($ct>=($date+$heureNormaleDepartPause)){
                return null;
            }else{
                return array($diff,$ct);
            }*/
            return array($diff,$ct);
        }else{
            return false;
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
    
    public function todaysClockinTimes($date){
        $min_time = strtotime($date." 00:00:00");
        $max_time = strtotime($date." 23:59:59");
        
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->where('c.clockinTime >= :min_time')->setParameter('min_time',$min_time);
        $queryBuilder->andWhere('c.clockinTime <= :max_time')->setParameter('max_time',$max_time);
        $queryBuilder->orderBy('c.clockinTime','DESC');

        $donn = $queryBuilder->getQuery()->getResult();
        return $donn;
    }

}
