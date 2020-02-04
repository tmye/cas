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
        $time = strtotime($date."00:00:00"); // 2019-09-18 00 00 00
        $j = date('N',$time); //3 -> aujourdhui
        $dayToSubstract = 0;
        if($j > 1){
            $dayToSubstract = $j-1; //2
        }
        $timeToSubstract = 60*60*24*$dayToSubstract; //conversion en timestamp d'aujourdhui
        $timeAfterSubstract = $time - $timeToSubstract; //difference entre la date-param et la date daujourdhui
        $dateAfterSubstract = date('d-m-Y',$timeAfterSubstract); //reconversion en date de la difference
        return array($j,$dayToSubstract,$dateAfterSubstract); //3 2
    }

    /**
     * @Route("/tested",name="tested")
     */
    public function tested(Request $request)
    {
        $retour = $this->jourSemaine("2018-05-02");
        var_dump($retour);
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
        $jour = date("Y").'-'.date("m").'-'.date("d"); //2019-09-18 on prend la date
//        echo "jour ".$jour." \n";
        $tab = $this->jourSemaine($jour);//3

//        echo "<br><br> Jour : $tab[0] <br><br>";
        $timeFrom = strtotime($tab[2]."00:00:00");
//        echo "timeFrom ".$timeFrom." \n";
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
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord"); // les enregistrements dheure au niveau de la machine à empreinte
        $clockinR = $cr->findAll();
        // On boucle sur les jours sélectionnés
        $i=0;
        $interval = 3000; // 30 Minuites

        // On récupère tous les employés
        $listEmp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeSafe();
        // On boucle sur les jours
        for ($cpt=0;$cpt<$tab[0];$cpt++){
            $theDay = date('N',$nowTime);
//            echo "theDay ".$theDay." \n";
            $theDay = $this->dateDayNameFrench($theDay);
            foreach ($listEmp as $emp){

                $tempsPerdusRetards=0;
                $tempsPerdusDeparts=0;

                $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
                $type = $empWH[$theDay][0]["type"];
                $name = $emp->getSurname()." ".$emp->getLastName();
                $picture = $emp->getPicture();
                $dep = $emp->getDepartement()->getName();

                // Pour le calcul d'un depart prématuré de pause,Calculons l'intervalle
                $heureDebutNormal = $empWH[$theDay][0]["beginHour"];
                $heureFinNormal = $empWH[$theDay][0]["endHour"];
                $heureDebutNormalPause = $empWH[$theDay][0]["pauseBeginHour"];
                $heureFinNormalPause = $empWH[$theDay][0]["pauseEndHour"];
//                echo "heureDebutNormal ".$heureDebutNormal." \n";
//                echo "heureFinNormal ".$heureFinNormal." \n";
//                echo "heureDebutNormalPause ".$heureDebutNormalPause." \n";
//                echo "heureFinNormalPause ".$heureFinNormalPause." \n";

                $beginHourExploded = explode(":",$heureDebutNormal);
                $endHourExploded = explode(":",$heureFinNormal);
                $pauseBeginHourExploded = explode(":",$heureDebutNormalPause);
                $pauseEndHourExploded = explode(":",$heureFinNormalPause);
//                echo "beginHourExploded ".$beginHourExploded." \n";
//                echo "endHourExploded ".$endHourExploded." \n";
//                echo "pauseBeginHourExploded ".$pauseBeginHourExploded." \n";
//                echo "pauseEndHourExploded ".$pauseEndHourExploded." \n";

                $interval = ($emp->getWorkingHour()->getTolerance())*60;


                if(sizeof($pauseBeginHourExploded)>1){
                    $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                    $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                    $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                    $heureNormaleArrivePause = $pauseEndHourInMinutes*60;
                    //echo "heureNormaleArrivePause ".$heureNormaleArrivePause." \n";
                    $heureNormaleDepartPause = $pauseBeginHourInMinutes*60;
                   // echo "heureNormaleDepartPause ".$heureNormaleDepartPause." \n";
                }else{
                    $interval_pause = 0;
                    $heureNormaleArrivePause = 0;
                    $heureNormaleDepartPause = 0;
                }

                if(sizeof($beginHourExploded)>1){
                    $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                    $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);
                }else{
                    $heureNormaleArrive = 0;
                    $heureNormaleDepart = 0;
                }
                $heureNormaleArrive = $beginHourInMinutes*60;
                $heureNormaleDepart = $endHourInMinutes*60;
//                echo "heureNormaleArrive ".$heureNormaleArrive." \n";
//                echo "heureNormaleDepartPause ".$heureNormaleDepartPause." \n";
                //print_r(date("d-m-Y H:i:s",$nowTime+$heureNormaleArrive+$interval)."\n");

                if ($type == "1" || $type == 1 || $type == "2" || $type == 2 || $type == "4" || $type == 4) {

                    if(!$cr->present($emp,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                        $absences++;
                    }else{
                        $retardDiff = $cr->retard($emp,$nowTime,$interval,$heureNormaleArrive);
                        if ($retardDiff[0] != null) {
                            $retards++;
                            $sommeRetards += $retardDiff[0];
                            $tempsPerdusRetards += $retardDiff[0] / (60);

                            if ($this->exist($tabClassementRetard, $emp->getId())) {
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementRetard[$emp->getId()]["cumul"];
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusRetards,"picture"=>$picture);
                            } else {
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusRetards,"picture"=>$picture);
                            }
                        }
                        $retardPauseDiff = $cr->retardPause($emp,$nowTime,$interval_pause,$heureNormaleArrivePause);
//                        echo "retardPauseDiff  ".$retardPauseDiff." \n";
                        if ($retardPauseDiff[0] != null) {
                            $retards++;
                            $sommeRetards += $retardPauseDiff[0];
                            $tempsPerdusRetards += $retardPauseDiff[0] / (60);
//                            echo "tempsPerdusRetards  ".$tempsPerdusRetards." \n";

                            if ($this->exist($tabClassementRetard, $emp->getId())) {
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementRetard[$emp->getId()]["cumul"];
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusRetards,"picture"=>$picture);
                            } else {
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusRetards,"picture"=>$picture);
                            }
                        }
                        $departDiff = $cr->departPremature($emp, $nowTime, $interval,$heureNormaleDepart);
                        if ($departDiff[0] != null) {
                            $departs++;
                            $sommeDeparts += $departDiff[0];
                            $tempsPerdusDeparts += ($departDiff[0]) / (60);

                            if ($this->exist($tabClassementDepart, $emp->getId())) {
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementDepart[$emp->getId()]["cumul"];
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusDeparts,"picture"=>$picture);
                            } else {
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusDeparts,"picture"=>$picture);
                            }
                        }
                        $departPauseDiff = $cr->departPausePremature($emp, $nowTime, $interval_pause,$heureNormaleDepartPause);
                        if ($departPauseDiff[0] != null) {
                            $i++;
                            $nowDate = date('d/m/Y', $nowTime);
                            $departsPause++;
                            $departs++;
                            $sommeDepartsPause += $departPauseDiff[0];
                            $tempsPerdusDeparts += ($departPauseDiff[0]) / (60);

                            if ($this->exist($tabClassementDepart, $emp->getId())) {
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementDepart[$emp->getId()]["cumul"];
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusDeparts,"picture"=>$picture);
                            } else {
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusDeparts,"picture"=>$picture);
                            }
                        }
                    }
                }elseif ($type == 3 || $type == "3"){
                    if(!$cr->present($emp,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                        $absences++;
                    }
                }
            }
            // On incrémente la date d'un jour
            $nowTime = $nowTime+86400;
//            echo "now time ".$nowTime;
//            echo "chui ici ";
        }
//        $permissionsData = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findPermEnCours();
//        $nbPermCrs = 0;
//        foreach($permissionsData as $perm ){
//            $nbPermCrs++;
//        }
        $permissionsData = $this->permissionSelect();

        $donnees = array("classementRetard"=>$tabClassementRetard,"classementDepart"=>$tabClassementDepart,"permissions"=>$permissionsData
                        ,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts);
//        $donnees = array("permissions"=>$nbPermCrs);
//        echo "Nombre d'absences : ".$donnees[""]."<br>";
//        echo "<br>classement départ : <br>";
//        print_r($donnees);

        return new JsonResponse($donnees);
    }/**
     * @Route("/homeStatis",name="homeStatis")
     */
    public function homeStatsActionTest(Request $request)
    {
        $jour = date("Y").'-'.date("m").'-'.date("d"); //2019-09-18 on prend la date
//        echo "jour ".$jour." \n";
        $tab = $this->jourSemaine($jour);//3

//        echo "<br><br> Jour : $tab[0] <br><br>";
        $timeFrom = strtotime($tab[2]."00:00:00");
//        echo "timeFrom ".$timeFrom." \n";
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
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord"); // les enregistrements dheure au niveau de la machine à empreinte
        $clockinR = $cr->findAll();
        // On boucle sur les jours sélectionnés
        $i=0;
        $interval = 3000; // 30 Minuites

        // On récupère tous les employés
        $listEmp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeSafe();
        // On boucle sur les jours
        for ($cpt=0;$cpt<$tab[0];$cpt++){
            $theDay = date('N',$nowTime);
//            echo "theDay ".$theDay." \n";
            $theDay = $this->dateDayNameFrench($theDay);
            foreach ($listEmp as $emp){

                $tempsPerdusRetards=0;
                $tempsPerdusDeparts=0;

                $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
                $type = $empWH[$theDay][0]["type"];
                $name = $emp->getSurname()." ".$emp->getLastName();
                $picture = $emp->getPicture();
                $dep = $emp->getDepartement()->getName();

                // Pour le calcul d'un depart prématuré de pause,Calculons l'intervalle
                $heureDebutNormal = $empWH[$theDay][0]["beginHour"];
                $heureFinNormal = $empWH[$theDay][0]["endHour"];
                $heureDebutNormalPause = $empWH[$theDay][0]["pauseBeginHour"];
                $heureFinNormalPause = $empWH[$theDay][0]["pauseEndHour"];
//                echo "heureDebutNormal ".$heureDebutNormal." \n";
//                echo "heureFinNormal ".$heureFinNormal." \n";
//                echo "heureDebutNormalPause ".$heureDebutNormalPause." \n";
//                echo "heureFinNormalPause ".$heureFinNormalPause." \n";

                $beginHourExploded = explode(":",$heureDebutNormal);
                $endHourExploded = explode(":",$heureFinNormal);
                $pauseBeginHourExploded = explode(":",$heureDebutNormalPause);
                $pauseEndHourExploded = explode(":",$heureFinNormalPause);
//                echo "beginHourExploded ".$beginHourExploded." \n";
//                echo "endHourExploded ".$endHourExploded." \n";
//                echo "pauseBeginHourExploded ".$pauseBeginHourExploded." \n";
//                echo "pauseEndHourExploded ".$pauseEndHourExploded." \n";

                $interval = ($emp->getWorkingHour()->getTolerance())*60;


                if(sizeof($pauseBeginHourExploded)>1){
                    $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                    $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                    $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                    $heureNormaleArrivePause = $pauseEndHourInMinutes*60;
                    //echo "heureNormaleArrivePause ".$heureNormaleArrivePause." \n";
                    $heureNormaleDepartPause = $pauseBeginHourInMinutes*60;
                   // echo "heureNormaleDepartPause ".$heureNormaleDepartPause." \n";
                }else{
                    $interval_pause = 0;
                    $heureNormaleArrivePause = 0;
                    $heureNormaleDepartPause = 0;
                }

                if(sizeof($beginHourExploded)>1){
                    $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                    $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);
                }else{
                    $heureNormaleArrive = 0;
                    $heureNormaleDepart = 0;
                }
                $heureNormaleArrive = $beginHourInMinutes*60;
                $heureNormaleDepart = $endHourInMinutes*60;
//                echo "heureNormaleArrive ".$heureNormaleArrive." \n";
//                echo "heureNormaleDepartPause ".$heureNormaleDepartPause." \n";
                //print_r(date("d-m-Y H:i:s",$nowTime+$heureNormaleArrive+$interval)."\n");

                if ($type == "1" || $type == 1 || $type == "2" || $type == 2 || $type == "4" || $type == 4) {

                    if(!$cr->present($emp,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                        $absences++;
                    }else{
                        $retardDiff = $cr->retard($emp,$nowTime,$interval,$heureNormaleArrive);
                        if ($retardDiff[0] != null) {
                            $retards++;
                            $sommeRetards += $retardDiff[0];
                            $tempsPerdusRetards += $retardDiff[0] / (60);

                            if ($this->exist($tabClassementRetard, $emp->getId())) {
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementRetard[$emp->getId()]["cumul"];
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusRetards,"picture"=>$picture);
                            } else {
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusRetards,"picture"=>$picture);
                            }
                        }
                        $retardPauseDiff = $cr->retardPause($emp,$nowTime,$interval_pause,$heureNormaleArrivePause);
//                        echo "retardPauseDiff  ".$retardPauseDiff." \n";
                        if ($retardPauseDiff[0] != null) {
                            $retards++;
                            $sommeRetards += $retardPauseDiff[0];
                            $tempsPerdusRetards += $retardPauseDiff[0] / (60);
//                            echo "tempsPerdusRetards  ".$tempsPerdusRetards." \n";

                            if ($this->exist($tabClassementRetard, $emp->getId())) {
                                $lastNumber = $tabClassementRetard[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementRetard[$emp->getId()]["cumul"];
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusRetards,"picture"=>$picture);
                            } else {
                                $tabClassementRetard[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusRetards,"picture"=>$picture);
                            }
                        }
                        $departDiff = $cr->departPremature($emp, $nowTime, $interval,$heureNormaleDepart);
                        if ($departDiff[0] != null) {
                            $departs++;
                            $sommeDeparts += $departDiff[0];
                            $tempsPerdusDeparts += ($departDiff[0]) / (60);

                            if ($this->exist($tabClassementDepart, $emp->getId())) {
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementDepart[$emp->getId()]["cumul"];
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusDeparts,"picture"=>$picture);
                            } else {
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusDeparts,"picture"=>$picture);
                            }
                        }
                        $departPauseDiff = $cr->departPausePremature($emp, $nowTime, $interval_pause,$heureNormaleDepartPause);
                        if ($departPauseDiff[0] != null) {
                            $i++;
                            $nowDate = date('d/m/Y', $nowTime);
                            $departsPause++;
                            $departs++;
                            $sommeDepartsPause += $departPauseDiff[0];
                            $tempsPerdusDeparts += ($departPauseDiff[0]) / (60);

                            if ($this->exist($tabClassementDepart, $emp->getId())) {
                                $lastNumber = $tabClassementDepart[$emp->getId()]["nombre"];
                                $lastCumul = $tabClassementDepart[$emp->getId()]["cumul"];
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => $lastNumber + 1, "cumul" => $lastCumul+$tempsPerdusDeparts,"picture"=>$picture);
                            } else {
                                $tabClassementDepart[$emp->getId()] = array("name" => $name, "dep" => $dep, "nombre" => 1, "cumul" => $tempsPerdusDeparts,"picture"=>$picture);
                            }
                        }
                    }
                }elseif ($type == 3 || $type == "3"){
                    if(!$cr->present($emp,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                        $absences++;
                    }
                }
            }
            // On incrémente la date d'un jour
            $nowTime = $nowTime+86400;
//            echo "now time ".$nowTime;
//            echo "chui ici ";
        }
        $permissionsData = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findPermEnCours();
        $nbPermCrs = 0;
        foreach($permissionsData as $perm ){
            $nbPermCrs++;
        }
//        $permissionsData = $this->permissionSelect();

        $donnees = array("classementRetard"=>$tabClassementRetard,"classementDepart"=>$tabClassementDepart,"permissions"=>$nbPermCrs
                        ,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts);
//        $donnees = array("permissions"=>$nbPermCrs);
//        echo "Nombre d'absences : ".$donnees[""]."<br>";
//        echo "<br>classement départ : <br>";
//        print_r($donnees);

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
