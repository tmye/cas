<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeStatsController extends Controller
{
    // fonction qui renvoi la date du premier jour de la semaine
    private function jourSemaine($date){
        $time = strtotime($date."00:00:00");
        $j = date('N',$time);
        $dayToSubstract = 0;
        if($j > 1){
            $dayToSubstract = $j-1;
        }
        $timeToSubstract = 60*60*24*$dayToSubstract;
        $timeAfterSubstract = $time - $timeToSubstract;
        $dateAfterSubstract = date('d-m-Y',$timeAfterSubstract);
        return array($j,$dayToSubstract,$dateAfterSubstract);
    }

    /**
     * @Route("/tested",name="tested")
     */
    public function tested(Request $request)
    {
        $permissionsData = $this->permissionSelect();
        echo "Number of permissions : ".$permissionsData[1]."<br>";
        echo ":::::::::::::TABLEAU TRIE::::::::::::::::::<br>";
        $tabb = array(3,1,5,4,0,1,6);
        $tabbTrie = $this->trier($tabb);
        var_dump($tabbTrie);
        echo "<br><br>";
        echo $tabbTrie[0];

        return new Response("<br>OK");
    }

    private function dateDayNameFrench($day){
        switch ($day){
            case 1:
                return "lundi";
                break;
            case 2:
                return "mardi";
                break;
            case 3:
                return "mercredi";
                break;
            case 4:
                return "jeudi";
                break;
            case 5:
                return "vendredi";
                break;
            case 6:
                return "samedi";
                break;
            case 7:
                return "dimanche";
                break;
        }
    }

    /**
     * @Route("/homeStats",name="homeStats")
     */
    public function homeStatsAction(Request $request)
    {
        $jour = "2018/03/14";
        $tab = $this->jourSemaine($jour);
        //echo "<br><br> Jour : $tab[0] <br><br>";
        $timeFrom = strtotime($tab[2]."00:00:00");
        $timeTo = strtotime($jour." 00:00:00");

        // On initialise le $nowTime par $timeFrom
        $nowTime = $timeFrom;

        // Les variables
        $absences=0;
        $retards = 0;

        $departs = 0;
        $departsPause = 0;

        $sommeAbsences =0;
        $sommeRetards =0;
        $sommeDeparts =0;
        $sommeDepartsPause =0;

        $tabDepartsPause = array();
        $tabDeparts = array();
        $tabClassementRetard = array();
        $tabClassementDepart = array();
        //$temp = [];
        //$temp["retard"] = [];
        //$temp["depart"] = [];

        // On récupère les clockinRecord pour une fois
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord");
        $clockinR = $cr->findAll();
        // On boucle sur les jours sélectionnés
        $i=0;
        $interval = 1800; // 30 Minuites

        // On récupère tous les employés
        $listEmp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
        // On boucle sur les jours
        for ($cpt=0;$cpt<$tab[0];$cpt++){
            $theDay = date('N',$nowTime);
            $theDay = $this->dateDayNameFrench($theDay);
            foreach ($listEmp as $emp){

                $tempsPerdusRetards=0;
                $tempsPerdusDeparts=0;

                $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
                $type = $empWH[$theDay][0]["type"];
                $name = $emp->getSurname()." ".$emp->getMiddleName()." ".$emp->getLastName();
                $dep = $emp->getDepartement()->getName();
                if(!$cr->present($emp,$nowTime)){
                    $absences++;
                }

                switch ($type){
                    case "1":
                        $retardDiff = $cr->retard($emp,$nowTime,$interval);
                        if($retardDiff != null){
                            $retards++;
                            $sommeRetards +=$retardDiff;
                            $tempsPerdusRetards += $retardDiff/(60);

                            if($this->exist($tabClassementRetard,$emp->getId())){
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $tabClassementRetard[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusRetards);
                            }else{
                                $tabClassementRetard[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusRetards);
                            }
                        }
                        $departDiff = $cr->departPremature($emp,$nowTime,$interval);
                        if($departDiff != null){
                            $nowDate = date('d/m/Y',$nowTime);
                            $departs++;
                            $sommeDeparts +=$departDiff[0];
                            $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                            // Pour prendre en compte les departs de 17h
                            $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
                            $ct = date('H:i',$departDiff[1]);
                            $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);

                            if($this->exist($tabClassementDepart,$emp->getId())){
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusDeparts);
                            }else{
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusDeparts);
                            }
                        }
                        $departPauseDiff = $cr->departPausePremature($emp,$nowTime,$interval);
                        if($departPauseDiff[0] != null){
                            $i++;
                            $nowDate = date('d/m/Y',$nowTime);
                            $departsPause++;
                            // Pour prendre en compte les departs de 12 h aussi
                            $departs++;
                            $sommeDepartsPause +=$departPauseDiff[0];
                            $tempsPerdusDepartsPause = ($departPauseDiff[0])/(60);
                            // Pour prendre en compte les departs de 12h aussi
                            $tempsPerdusDeparts +=$tempsPerdusDepartsPause;
                            $ct = date('H:i',$departPauseDiff[1]);
                            $tabDepartsPause[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsPause);

                            if($this->exist($tabClassementDepart,$emp->getId())){
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusDeparts);
                            }else{
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusDeparts);
                            }
                        }
                        break;
                    case "2":
                        $retardDiff = $cr->retard($emp,$nowTime,$interval);
                        if($retardDiff != null){
                            $retards++;
                            $sommeRetards +=$retardDiff;
                            $tempsPerdusRetards += $retardDiff/(60);

                            if($this->exist($tabClassementRetard,$emp->getId())){
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $tabClassementRetard[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusRetards);
                            }else{
                                $tabClassementRetard[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusRetards);
                            }
                        }
                        $departDiff = $cr->departPremature($emp,$nowTime,$interval);
                        if($departDiff != null){
                            $nowDate = date('d/m/Y',$nowTime);
                            $departs++;
                            $sommeDeparts +=$departDiff[0];
                            $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                            // Pour prendre en compte les departs de 17h
                            $tempsPerdusDeparts +=$tempsPerdusDepartsFin;
                            $ct = date('H:i',$departDiff[1]);
                            $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);

                            if($this->exist($tabClassementDepart,$emp->getId())){
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusDeparts);
                            }else{
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusDeparts);
                            }
                        }
                        $departPauseDiff = $cr->departPausePremature($emp,$nowTime,$interval);
                        if($departPauseDiff[0] != null){
                            $i++;
                            $nowDate = date('d/m/Y',$nowTime);
                            $departsPause++;
                            // Pour prendre en compte les departs de 12 h aussi
                            $departs++;
                            $sommeDepartsPause +=$departPauseDiff[0];
                            $tempsPerdusDepartsPause = ($departPauseDiff[0])/(60);
                            // Pour prendre en compte les departs de 12 h aussi
                            $tempsPerdusDeparts +=$tempsPerdusDepartsPause;
                            $ct = date('H:i',$departPauseDiff[1]);
                            $tabDepartsPause[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsPause);

                            if($this->exist($tabClassementDepart,$emp->getId())){
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>$lastNumber+1,"cumul"=>$tempsPerdusDeparts);
                            }else{
                                $tabClassementDepart[$emp->getId()] = array("name"=>$name,"dep"=>$dep,"nombre"=>1,"cumul"=>$tempsPerdusDeparts);
                            }
                        }
                        break;
                }
            }
            // On incrémente la date d'un jour
            $nowTime = $nowTime+86400;
        }
        $permissionsData = $this->permissionSelect();

        $donnees = array("classementRetard"=>$tabClassementRetard,"classementDepart"=>$tabClassementDepart,"permissions"=>$permissionsData,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts);
        //echo "Nombre d'absences : ".$donnees["departs"]."<br>";
        //echo "<br>classement départ : <br>";
        //print_r($donnees["classementDepart"]);

        return new JsonResponse($donnees);
    }

    private function permissionSelect(){
        $i=0;
        $permissionsTab = array();
        $permissions = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findAll();
        foreach ($permissions as $perm){
            $createTime = $perm->getCreateTime();
            $createTimeString = $createTime->format('Y-m-d H:i:s');
            $exploded_value = explode("-",$createTimeString);
            $state = $perm->getState();
            $year = $exploded_value[0];
            $month = $exploded_value[1];
            if(($year == date('Y')) && ($month == date('m')) && $state == 0) {
                $i++;
                $permissionsTab[] = $perm;
            }
        }
        return $i;
    }

    private function exist($tab,$empId){
        return array_key_exists($empId,$tab);
    }

    private function trier($tableau){
        $taille = 0;
        foreach ($tableau as $element){
            $taille++;
        }
        //echo "Taille : ".$taille."<br>";
        $max=$tableau[0];
        for ($i=0;$i<$taille-1;$i++){
            for($j=0;$j<$taille-1;$j++){
                if($tableau[$j+1]>$tableau[$j]){
                    $max = $tableau[$j+1];
                    $temp = $tableau[$i];
                    $tableau[$i] = $max;
                    $tableau[$j+1] = $temp;
                }
                $j++;
            }
            $i++;
        }
        return $tableau;
    }
}
