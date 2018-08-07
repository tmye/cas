<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClockinRecord;
use AppBundle\Entity\Employe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class ClockinReccordController extends EmployeController
{

    static $min_laps = 180;
    static $min_laps_pause = 30;

    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {
        /*
        echo date('d-m-Y H:i:s',1522423928)."<br>";
        echo date('d-m-Y H:i:s',1522428015)."<br>";
        echo date('d-m-Y H:i:s',1522389900)."<br>";
        $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find(26);
        $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
        */
        echo strtotime("07 August 2018 07:30:00")."<br>";
        //$don = $this->findHistoriqueAction($request,$departem = 4,$dat = "2018-03-29",$emplo = 26);
        //print_r($don);
        return new Response("OK");
    }

    /**
     * @Route("/randomClockinRecord", name="randomClockinRecord")
     */
    public function randomClockinRecordAction(Request $request)
    {
        //date_default_timezone_set('Africa/Lome');

        $dateFrom = "2018-07-01";
        $dateTo = "2018-08-05";
        $employees = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
        $timeFrom = strtotime($dateFrom." 00:00:00");
        $timeTo = strtotime($dateTo." 00:00:00");
        $timeDays = $timeTo-$timeFrom;
        $days = $timeDays/(60*60*24);
        $em = $this->getDoctrine()->getManager();
        foreach ($employees as $emp){
            $nowTime = $timeFrom;
            $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
            // Lundi because the are the same
            $type = $empWH["lundi"][0]["type"];
            for ($cpt=0;$cpt<=$days;$cpt++) {
                $randNumber = rand(-3600,3600);
                switch ($type){
                    case "1":
                        $hd = strtotime(date('Y-m-d',$nowTime)." 08:00:00")+$randNumber;
                        $hp = strtotime(date('Y-m-d',$nowTime)." 12:00:00")+$randNumber;
                        $hfp = strtotime(date('Y-m-d',$nowTime)." 14:00:00")+$randNumber;
                        $hf = strtotime(date('Y-m-d',$nowTime)." 17:00:00")+$randNumber;

                        print_r($hd);
                        print_r($hp);
                        print_r($hfp);
                        print_r($hf);

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hd);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hp);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hfp);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hf);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        break;
                    case "2":
                        $hd = strtotime(date('Y-m-d',$nowTime)." 08:00:00")+$randNumber;
                        $hf = strtotime(date('Y-m-d',$nowTime)." 17:00:00")+$randNumber;

                        print_r($hd);
                        print_r($hf);

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hd);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hf);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();
                        break;
                    case "3":
                        $h = strtotime(date('Y-m-d',$nowTime)." 08:00:00")+$randNumber;

                        print_r($h);

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($h);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();
                        break;
                    case "4":
                        $hd = strtotime(date('Y-m-d',$nowTime)." 08:00:00")+$randNumber;
                        $hf = strtotime(date('Y-m-d',$nowTime)." 17:00:00")+$randNumber;

                        print_r($hd);
                        print_r($hf);

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hd);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();

                        $cr = new ClockinRecord();
                        $cr->setEmploye($emp);
                        $cr->setDepartement($emp->getDepartement());
                        $cr->setDeviceId(1);
                        $cr->setClockinTime($hf);
                        $cr->setPic("pic");
                        $cr->setVerify(1);
                        $em->persist($cr);
                        $em->flush();
                        break;
                }
                $nowTime = $nowTime+86400;
            }
        }
        return new Response("Ok");
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

    private function exist($tab,$empId){
        return array_key_exists($empId,$tab);
    }

    /*
     * Fonction booléenne qui renvoi true si un clockinTime est celui d'une heure d'arrivée
     * ou false sinon
    */
    public function arrive(ClockinRecord $cR,$day,$request){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["beginHour"];
        $heureFinNormal = $empWH[$day][0]["endHour"];
        $type = $empWH["lundi"][0]["type"];
        if($type == 2 || $type == "2"){
            $beginHourExploded = explode(":",$heureDebutNormal);
            $endHourExploded = explode(":",$heureFinNormal);
            if(sizeof($beginHourExploded)>1){
                $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);

                $interval = (($endHourInMinutes - $beginHourInMinutes)/2)*60;
            }else{
                $interval = 0;
            }
        }else{
            $interval = ($cR->getEmploye()->getWorkingHour()->getTolerance())*60;
        }

        $_date = date('d-m-Y',$cR->getClockinTime());

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hIInfA = $hSenceA- ($interval);
        $hIInfD = $hSenceD- ($interval);

        $dIInfA = $dSenceA- ($interval);
        $dIInfD = $dSenceD- ($interval);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hISupA = $hSenceA+ ($interval);
        $dISupA = $dSenceA+ ($interval);

        $hISupD = $hSenceD+ ($interval);
        $dISupD = $dSenceD+ ($interval);

        //echo "<br>test : ".strtotime("14 February 2018 6:25:00");

        if($dIInfA <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupA){
            return true;
        }else{
            return false;
        }
    }

    /*
     * Fonction booléenne qui renvoi true si un clockinTime est celui d'une heure de depart
     * ou false sinon
    */
    public function depart(ClockinRecord $cR,$day,$request){

        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["beginHour"];
        $heureFinNormal = $empWH[$day][0]["endHour"];
        $type = $empWH["lundi"][0]["type"];
        if($type == 2 || $type == "2"){
            $beginHourExploded = explode(":",$heureDebutNormal);
            $endHourExploded = explode(":",$heureFinNormal);
            if(sizeof($beginHourExploded)>1){
                $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);

                $interval = (($endHourInMinutes - $beginHourInMinutes)/2)*60;
            }else{
                $interval = 0;
            }
        }else{
            $interval = ($cR->getEmploye()->getWorkingHour()->getTolerance())*60;
        }
        $_date = date('d-m-Y',$cR->getClockinTime());
        $df = strtotime($_date." ".$heureFinNormal);
        // L'heure à laquelle l'employé est sensé partir
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé partir
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé partir
        $hIInfD = $hSenceD- ($interval);
        $dIInfD = $dSenceD- ($interval);

        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé partir
        $hISupD = $hSenceD+ ($interval);
        $dISupD = $dSenceD+ ($interval);

        if($dIInfD <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupD){
            return true;
        }else{
            return false;
        }
    }


    public function pause(ClockinRecord $cR,$day){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["pauseBeginHour"];
        $heureFinNormal = $empWH[$day][0]["pauseEndHour"];

        // Détermination de l'intervalle de pause

        $pauseBeginHourExploded = explode(":",$heureDebutNormal);
        $pauseEndHourExploded = explode(":",$heureFinNormal);

        $pauseEndHourInMinutes =0;
        $pauseBeginHourInMinutes =0;

        if(sizeof($pauseBeginHourExploded)>1){
            $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
            $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);
        }
        //echo "\n test ======== ".$pauseEndHourInMinutes;
        $hour_diff = ($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2;
        //echo "\n Hour diff ======== ".$hour_diff;

        $_date = date('d-m-Y',$cR->getClockinTime());

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $dIInfA = $dSenceA- ($hour_diff * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $dISupA = $dSenceA+ ($hour_diff * 60);

        if($dIInfA <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupA){
            return true;
        }else{
            return false;
        }
    }

    public function finPause(ClockinRecord $cR,$day){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["pauseBeginHour"];
        $heureFinNormal = $empWH[$day][0]["pauseEndHour"];

        // Détermination de l'intervalle de pause

        $pauseBeginHourExploded = explode(":",$heureDebutNormal);
        $pauseEndHourExploded = explode(":",$heureFinNormal);

        $pauseEndHourInMinutes =0;
        $pauseBeginHourInMinutes =0;

        if(sizeof($pauseBeginHourExploded)>1){
            $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
            $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);
        }
        //echo "\n test ======== ".$pauseEndHourInMinutes;
        $hour_diff = ($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2;
        //echo "\n Hour diff ======== ".$hour_diff;

        $_date = date('d-m-Y',$cR->getClockinTime());

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hIInfA = $hSenceA- ($hour_diff * 60);
        $hIInfD = $hSenceD- ($hour_diff * 60);

        $dIInfA = $dSenceA- ($hour_diff * 60);
        $dIInfD = $dSenceD- ($hour_diff * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hISupA = $hSenceA+ ($hour_diff * 60);
        $dISupA = $dSenceA+ ($hour_diff * 60);

        $hISupD = $hSenceD+ ($hour_diff * 60);
        $dISupD = $dSenceD+ ($hour_diff * 60);

        if($dIInfD <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupD){
            return true;
        }else{
            return false;
        }
    }

    /* Fonction qui permet de créer des entrées dans le nouveau tableau */
    public function createEntry(Request $request,$day,$recordTab,ClockinRecord $c){
        if($this->arrive($c,$day,$request)){
            $nom = $c->getEmploye()->getLastName();
            $prenom = $c->getEmploye()->getSurname();

            $function = $c->getEmploye()->getFunction();
            $wH = $c->getEmploye()->getWorkingHour()->getWorkingHour();
            $wH = json_decode($wH,true);

            $_date = $request->request->get('date');
            $day = date('N',strtotime($_date));
            $day = $this->dateDayNameFrench($day);
            $arrive = date('H:i',$c->getClockinTime());

            $type = $wH[$day][0]["type"];
            $quota = $wH[$day][0]["quota"];
            $bH = $wH[$day][0]["beginHour"];
            $pBH = $wH[$day][0]["pauseBeginHour"];
            $pEH = $wH[$day][0]["pauseEndHour"];
            $eH = $wH[$day][0]["endHour"];

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"name"=>$c->getEmploye()->getSurname()." ".$c->getEmploye()->getLastName(),"time_arrive"=>$c->getClockinTime(),"time_depart"=>0,"time_pause"=>0,"time_fin_pause"=>0,"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"quota_en_minuite"=>null,"quota_fait"=>null,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>$arrive,"depart"=>0,"pause"=>0,"finPause"=>0);
        }elseif($this->pause($c,$day,$request)){
            $nom = $c->getEmploye()->getLastName();
            $prenom = $c->getEmploye()->getSurname();

            $function = $c->getEmploye()->getFunction();
            $wH = $c->getEmploye()->getWorkingHour()->getWorkingHour();
            $wH = json_decode($wH,true);
            $pause = date('H:i',$c->getClockinTime());

            $_date = $request->request->get('date');
            $day = date('N',strtotime($_date));
            $day = $this->dateDayNameFrench($day);

            $type = $wH[$day][0]["type"];
            $quota = $wH[$day][0]["quota"];
            $bH = $wH[$day][0]["beginHour"];
            $pBH = $wH[$day][0]["pauseBeginHour"];
            $pEH = $wH[$day][0]["pauseEndHour"];
            $eH = $wH[$day][0]["endHour"];

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"name"=>$c->getEmploye()->getSurname()." ".$c->getEmploye()->getLastName(),"time_arrive"=>0,"time_depart"=>0,"time_pause"=>$c->getClockinTime(),"time_fin_pause"=>0,"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"quota_en_minuite"=>null,"quota_fait"=>null,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>0,"depart"=>0,"pause"=>$pause,"finPause"=>0);
        }elseif($this->finPause($c,$day,$request)){
            $nom = $c->getEmploye()->getLastName();
            $prenom = $c->getEmploye()->getSurname();

            $function = $c->getEmploye()->getFunction();
            $wH = $c->getEmploye()->getWorkingHour()->getWorkingHour();
            $wH = json_decode($wH,true);
            $finPause = date('H:i',$c->getClockinTime());

            $_date = $request->request->get('date');
            $day = date('N',strtotime($_date));
            $day = $this->dateDayNameFrench($day);

            $type = $wH[$day][0]["type"];
            $quota = $wH[$day][0]["quota"];
            $bH = $wH[$day][0]["beginHour"];
            $pBH = $wH[$day][0]["pauseBeginHour"];
            $pEH = $wH[$day][0]["pauseEndHour"];
            $eH = $wH[$day][0]["endHour"];

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"name"=>$c->getEmploye()->getSurname()." ".$c->getEmploye()->getLastName(),"time_arrive"=>0,"time_depart"=>0,"time_pause"=>0,"time_fin_pause"=>$c->getClockinTime(),"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"quota_en_minuite"=>null,"quota_fait"=>null,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>0,"depart"=>0,"pause"=>0,"finPause"=>$finPause);
        }elseif($this->depart($c,$day,$request)){
            $nom = $c->getEmploye()->getLastName();
            $prenom = $c->getEmploye()->getSurname();

            $function = $c->getEmploye()->getFunction();
            $wH = $c->getEmploye()->getWorkingHour()->getWorkingHour();
            $wH = json_decode($wH,true);
            $depart = date('H:i',$c->getClockinTime());

            $_date = $request->request->get('date');
            $day = date('N',strtotime($_date));
            $day = $this->dateDayNameFrench($day);

            $type = $wH[$day][0]["type"];
            $quota = $wH[$day][0]["quota"];
            $bH = $wH[$day][0]["beginHour"];
            $pBH = $wH[$day][0]["pauseBeginHour"];
            $pEH = $wH[$day][0]["pauseEndHour"];
            $eH = $wH[$day][0]["endHour"];

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"name"=>$c->getEmploye()->getSurname()." ".$c->getEmploye()->getLastName(),"time_arrive"=>0,"time_depart"=>$c->getClockinTime(),"time_pause"=>0,"time_fin_pause"=>0,"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"quota_en_minuite"=>null,"quota_fait"=>null,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>0,"depart"=>$depart,"pause"=>0,"finPause"=>0);
        }
        return $recordTab;
    }

    /* Fonction qui permet de tester si un clockinTime est plus récent */
    public function plusRecent($recordTab,ClockinRecord $c){
        if($c->getClockinTime() < $recordTab[$c->getEmploye()->getId()]["time_arrive"] ){
            return true;
        }else{
            return false;
        }
    }
    public function plusAncien($recTab,ClockinRecord $element){
        //if(isset($recTab[$element->getEmploye()->getId()]["time_depart"]) && $recTab[$element->getEmploye()->getId()]["time_depart"] != null){
            if($element->getClockinTime() > $recTab[$element->getEmploye()->getId()]["time_depart"] ){
                return true;
            }else{
                return false;
            }
        //}
    }

    public function miseAJour($recordTab,ClockinRecord $c,$day,$request){
        if($this->arrive($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["arrive"] = date('H:i',$c->getClockinTime());
            $recordTab[$c->getEmploye()->getId()]["time_arrive"] = $c->getClockinTime();
        }elseif($this->pause($c,$day)){
            $recordTab[$c->getEmploye()->getId()]["pause"] = date('H:i',$c->getClockinTime());
            $recordTab[$c->getEmploye()->getId()]["time_pause"] = $c->getClockinTime();
        }elseif($this->finPause($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["finPause"] = date('H:i',$c->getClockinTime());
            $recordTab[$c->getEmploye()->getId()]["time_fin_pause"] = $c->getClockinTime();
        }elseif($this->depart($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["depart"] = date('H:i',$c->getClockinTime());
            $recordTab[$c->getEmploye()->getId()]["time_depart"] = $c->getClockinTime();
        }

        /*
         * Après la mise à jour,il faut que je calcul le nouveau quota*/
        // On fait des tests sur les quotas

        if($recordTab[$c->getEmploye()->getId()]["type"] == 2){
            $quota = $recordTab[$c->getEmploye()->getId()]["quota"];
            $quota_en_minuites = ((int)$recordTab[$c->getEmploye()->getId()]["quota"])*60;
            // On fait d'abord des tests pour voir si les variables ne sont pas vides
            if((($recordTab[$c->getEmploye()->getId()]["arrive"] != null) && $recordTab[$c->getEmploye()->getId()]["arrive"] != "") && (($recordTab[$c->getEmploye()->getId()]["depart"] != null) && $recordTab[$c->getEmploye()->getId()]["depart"] != "")){
                // Pour les débuts et fins
                $heure_arrive = (int)explode(':',$recordTab[$c->getEmploye()->getId()]["arrive"])[0];
                $minuite_arrive = (int)explode(':',$recordTab[$c->getEmploye()->getId()]["arrive"])[1];
                $time_arrive = ($heure_arrive*60)+$minuite_arrive;

                $heure_depart = (int)explode(':',$recordTab[$c->getEmploye()->getId()]["depart"])[0];
                $minuite_depart = (int)explode(':',$recordTab[$c->getEmploye()->getId()]["depart"])[1];
                $time_depart = ($heure_depart*60)+$minuite_depart;
                // Pour les pauses

                // Je calcul le quota
                $quota_fait = $time_depart-$time_arrive; // En minuite
                // Maintenant on fais une mis à jour du quota
                $recordTab[$c->getEmploye()->getId()]["quota_fait"] = $quota_fait;
                $recordTab[$c->getEmploye()->getId()]["quota_en_minuite"] = $quota_en_minuites;
            }else{
                $quota_fait = null;
            }
        }else{
            $quota = null;
        }

        // Avant de renvoyer le tableau on reverifie les bornes
        /*for ($i=0;$i<sizeof($recordTab)-1;$i++){
            for ($j=0;$j<sizeof($recordTab)-1;$j++){
                if($recordTab[$i+1]>$recordTab[$i]){
                    $temp = $recordTab[$i+1];
                    $recordTab[$i+1] = $recordTab[$i];
                    $recordTab[$i] = $temp;
                }
                $j++;
            }
            $i++;
        }*/

        return $recordTab;
    }

    private function elimineDoublon($donnees,$day, Request $request){
        $record = array();
        foreach ($donnees as $element){
            // Si cet identifiant existe déjà dans le tableau on fait des tests,
            // Sinon on crée une nouvelle entrée
            if(!($this->exist($record,$element->getEmploye()->getId()))){
                $record = $this->createEntry($request,$day,$record,$element);
            }

            /*
             * On vérifie quelle intervalle de temps
             * Si c'est une heure d'arrivée on fait un traitement
             * Sinon à ce stade ça ne peut qu'etre une heure de départ
            */
            if($this->arrive($element,$day,$request)){
                //print_r($element->getEmploye()->getSurname()." ### \n");
                /*
                 * On vérifie si ce clockinTime est plus récent
                 * Si c'est le cas on met à jour les données
                 * Sinon on zappe
                */
                if($this->plusRecent($record,$element)){
                    //print_r(" --- Plus recent \n");
                    $record = $this->miseAJour($record,$element,$day,$request);
                }
            }else{
                //print_r($element->getEmploye()->getSurname()." @@@ \n");
                /*
                 * On vérifie si ce clockinTime est plus ancien
                 * Si c'est le cas on met à jour les données
                 * Sinon on zappe
                */
                if($this->plusAncien($record,$element)){
                    //print_r(" ::: Plus ancien \n");
                    $record = $this->miseAJour($record,$element,$day,$request);
                }else{
                    // Il faut quand meme le mettre à jour
                    $record = $this->miseAJour($record,$element,$day,$request);
                }
            }
        }
        return $record;
    }

    /**
     * @Route("/findHistorique", name="findHistorique")
     */
    public function findHistoriqueAction($departem = null,$dat = null,$emplo = null,Request $request = null)
    {

        if(($request->request->get('id') != null) && ($request->request->get('date') != null)){
            $dep = $request->request->get('id');
            $_date = $request->request->get('date');
        }else{
            $dep = $departem;
            $_date = $dat;
        }

        $day = date('N',strtotime($_date));
        $day = $this->dateDayNameFrench(intval($day));


        $empTab = array();
        $empNameTab = array();
        $dataTable = array();
        $don = array();

        // check if one or many employees
        if(isset($emplo) && $emplo != null){
            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findOneBy(array("id"=>$emplo));
        }else{
            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeByDep($dep);
        }

        if(isset($emp) && $emp != null){
            if($emp instanceof Employe) {
                $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);

                $heureDebutNormal = $empWH[$day][0]["beginHour"];
                $heureDebutPauseNormal = $empWH[$day][0]["pauseBeginHour"];
                $heureFinNormal = $empWH[$day][0]["endHour"];
                $heureFinPauseNormal = $empWH[$day][0]["pauseEndHour"];

                $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
                $type = $empWH["lundi"][0]["type"];
                $dayType = $empWH[$day][0]["type"];
                if($type == 2 || $type == "2"){
                    $beginHourExploded = explode(":",$heureDebutNormal);
                    $endHourExploded = explode(":",$heureFinNormal);
                    if(sizeof($beginHourExploded)>1){
                        $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                        $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);

                        $interval = (($endHourInMinutes - $beginHourInMinutes)/2)*60;
                    }else{
                        $interval = 0;
                    }
                }else{
                    $interval = ($emp->getWorkingHour()->getTolerance())*60;
                }

                // Pour le calcul d'un depart prématuré de pause,Calculons l'intervalle

                $pauseBeginHourExploded = explode(":",$heureDebutPauseNormal);
                $pauseEndHourExploded = explode(":",$heureFinPauseNormal);

                if(sizeof($pauseBeginHourExploded)>1){
                    $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                    $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                    $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                }else{
                    $interval_pause = 0;
                }

                $dd = strtotime($_date." ".$heureDebutNormal);
                $dpd = strtotime($_date." ".$heureDebutPauseNormal);
                $dpf = strtotime($_date." ".$heureFinPauseNormal);
                $df = strtotime($_date." ".$heureFinNormal);

                // Timestamp de la dateheure à laquelle l'employé est sensé arriver
                $dSenceA = $dd;
                $dSencePD = $dpd;
                $dSencePF = $dpf;
                $dSenceD = $df;


                $dIInfA = $dSenceA-($interval);
                $dIInfPD = $dSencePD-($interval_pause);
                $dIInfD = $dSenceD-($interval);
                $dIInfPF = $dSencePF-($interval_pause);
                // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
                $dISupA = $dSenceA+($interval);
                $dISupPD = $dSencePD+($interval_pause);
                $dISupPF = $dSencePF+($interval_pause);
                $dISupD = $dSenceD+($interval);

                // On récupère les données appartenant au département sélectionné

                $tempData = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empHistory($emp->getId(),$dep,$dIInfA,$dISupA,$dIInfPD,$dISupPD,$dIInfPF,$dISupPF,$dIInfD,$dISupD);
                $min = strtotime($_date." 00:00:00");
                $max = strtotime($_date." 23:59:59");

                $empAllRecord = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empAllHistory($emp->getId(),$min,$max);
                $empAllRecordFinal = array();
                foreach ($empAllRecord as $clock){
                    $empAllRecordFinal[] = date("H:i:s",$clock->getClockinTime());
                }

                $empTab[]=$emp->getId();
                $empAllHistoryTab[]=array($emp->getId(),$empAllRecordFinal,$dayType);
                $empNameTab[]=$emp->getSurname()." ".$emp->getLastName();
                $empCcidTab[]=$emp->getEmployeeCcid();
                $empTypeTab[]=array($emp->getId(),$type);

                //Maintenant il faut éliminer les doublons
                $don[] = $this->elimineDoublon($tempData,$day,$request);


                $tabLength = sizeof($don);

                $encoders = array(new XmlEncoder(), new JsonEncoder());
                $normalizers = array(new ObjectNormalizer());

                $serializer = new Serializer($normalizers, $encoders);

                $jsonContent = $serializer->serialize(['clockinRecord' => $don],'json');

                $content = array("content"=>$jsonContent,"emp"=>$empTab,"empNames"=>$empNameTab,"empCcid"=>$empCcidTab,"empType"=>$empTypeTab,"allRecord"=>$empAllHistoryTab);
            }else{
                if(sizeof($emp)>0){
                    foreach ($emp as $e){
                        $empWH = json_decode($e->getWorkingHour()->getWorkingHour(),true);
                        $type = $empWH["lundi"][0]["type"];
                        $dayType = $empWH[$day][0]["type"];

                        $heureDebutNormal = $empWH[$day][0]["beginHour"];
                        $heureDebutPauseNormal = $empWH[$day][0]["pauseBeginHour"];
                        $heureFinNormal = $empWH[$day][0]["endHour"];
                        $heureFinPauseNormal = $empWH[$day][0]["pauseEndHour"];

                        if($type == 2 || $type == "2"){
                            $beginHourExploded = explode(":",$heureDebutNormal);
                            $endHourExploded = explode(":",$heureFinNormal);
                            if(sizeof($beginHourExploded)>1){
                                $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                                $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);

                                $interval = (($endHourInMinutes - $beginHourInMinutes)/2)*60;
                            }else{
                                $interval = 0;
                            }
                        }else{
                            $interval = ($e->getWorkingHour()->getTolerance())*60;
                        }

                        $pauseBeginHourExploded = explode(":",$heureDebutPauseNormal);
                        $pauseEndHourExploded = explode(":",$heureFinPauseNormal);

                        if(sizeof($pauseBeginHourExploded)>1){
                            $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                            $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                            $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                        }else{
                            $interval_pause = 0;
                        }

                        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
                        $dSenceA = strtotime($_date." ".$heureDebutNormal);
                        $dSencePD = strtotime($_date." ".$heureDebutPauseNormal);
                        $dSencePF = strtotime($_date." ".$heureFinPauseNormal);
                        $dSenceD = strtotime($_date." ".$heureFinNormal);



                        $dIInfA = $dSenceA-($interval);
                        $dIInfPD = $dSencePD-($interval_pause);
                        $dIInfD = $dSenceD-($interval);
                        $dIInfPF = $dSencePF-($interval_pause);
                        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
                        $dISupA = $dSenceA+($interval);
                        $dISupPD = $dSencePD+($interval_pause);
                        $dISupPF = $dSencePF+($interval_pause);
                        $dISupD = $dSenceD+($interval);

                        // On récupère les données appartenant au département sélectionné

                        $tempData = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empHistory($e->getId(),$dep,$dIInfA,$dISupA,$dIInfPD,$dISupPD,$dIInfPF,$dISupPF,$dIInfD,$dISupD);
                        /*foreach ($tempData as $cr){
                            print_r("----".$cr->getEmploye()->getId()." : ".$cr->getClockinTime()." (".date('Y-m-d H:i:s',$cr->getClockinTime()).")\n");
                        }*/
                        $min = strtotime($_date." 00:00:00");
                        $max = strtotime($_date." 23:59:59");

                        $empAllRecord = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empAllHistory($e->getId(),$min,$max);
                        $empAllRecordFinal = array();
                        foreach ($empAllRecord as $clock){
                            $empAllRecordFinal[] = date("H:i:s",$clock->getClockinTime());
                        }

                        $empTab[]=$e->getId();
                        $empAllHistoryTab[]= array($e->getId(),$empAllRecordFinal,$dayType);
                        $empNameTab[]=$e->getSurname()." ".$e->getLastName();
                        $empCcidTab[]=$e->getEmployeeCcid();
                        $empTypeTab[]=array($e->getId(),$type);

                        // Maintenant il faut éliminer les doublons
                        $don[] = $this->elimineDoublon($tempData,$day,$request);

                        $tabLength = sizeof($don);

                        $encoders = array(new XmlEncoder(), new JsonEncoder());
                        $normalizers = array(new ObjectNormalizer());

                        $serializer = new Serializer($normalizers, $encoders);

                        $jsonContent = $serializer->serialize(['clockinRecord' => $don],'json');

                        $content = array("content"=>$jsonContent,"emp"=>$empTab,"empNames"=>$empNameTab,"empCcid"=>$empCcidTab,"empType"=>$empTypeTab,"allRecords"=>$empAllHistoryTab);
                    }
                }

            }

            if(!empty($emplo) && $emplo != null){
                $type = $empWH[$day][0]["type"];
                $array_of_data = array();
                // Si on est dans le cas où cest un appel depuis un autre controlleur
                if(isset(json_decode($content["content"],true)["clockinRecord"][0][$emplo]) && !empty(json_decode($content["content"],true)["clockinRecord"][0][$emplo])){
                    $quota_en_minuite = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["quota_en_minuite"];
                    $quota_fait_en_minuite = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["quota_fait"];
                    $arrive = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["arrive"];
                    $depart = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["depart"];
                    $pause = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["pause"];
                    $finPause = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["finPause"];

                    $bH = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["bH"];
                    $eH = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["eH"];
                    $pBH = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["pBH"];
                    $pEH = json_decode($content["content"],true)["clockinRecord"][0][$emplo]["pEH"];
                }else{
                    if($type == "2" || $type == 2){
                        $quota_en_minuite = ((int)$empWH[$day][0]["quota"])*60;
                        $quota_fait_en_minuite = 0;
                    }else{
                        $quota_en_minuite = 0;
                        $quota_fait_en_minuite = 0;
                    }
                    $arrive = null;
                    $depart = null;
                    $pause = null;
                    $finPause = null;

                    $bH = 0;
                    $eH = 0;
                    $pBH = 0;
                    $pEH = 0;
                }
                //print_r($quota_en_minuite);
                $array_of_data["date"] = $dat;
                $array_of_data["type"] = $type;
                $array_of_data["quota"] = 480;
                $array_of_data["quota_fait"] = $quota_fait_en_minuite;
                $array_of_data["arrive"] = $arrive;
                $array_of_data["depart"] = $depart;
                $array_of_data["pause"] = $pause;
                $array_of_data["finPause"] = $finPause;

                $array_of_data["bH"] = $bH;
                $array_of_data["eH"] = $eH;
                $array_of_data["pBH"] = $pBH;
                $array_of_data["pEH"] = $pEH;

                return new JsonResponse($array_of_data);
            }else{
                return new JsonResponse($content);
            }
        }else{
            return new Response("null");
        }
    }

    /**
     * @Route("/returnHistorique", name="returnHistorique")
     */
    public function returnHistoriqueAction(Request $request)
    {
        $dep = $request->request->get('id');
        $_date = $request->request->get('date');
        $day = date('N',strtotime($_date));
        $day = $this->dateDayNameFrench(intval($day));

        $heureDebutNormal = "8:00:00";
        $heureDebutPauseNormal = "12:00:00";
        $heureFinNormal = "17:30:00";
        $heureFinPauseNormal = "14:00:00";

        $dd = strtotime($_date." ".$heureDebutNormal);
        $dpd = strtotime($_date." ".$heureDebutPauseNormal);
        $dpf = strtotime($_date." ".$heureFinPauseNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSencePD = strtotime(date("H:i",strtotime($dpd)));
        $hSencePF = strtotime(date("H:i",strtotime($dpf)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSencePD = $dpd;
        $dSencePF = $dpf;
        $dSenceD = $df;


        $dIInfA = $dSenceA- (ClockinReccordController::$min_laps * 60);
        $dIInfPD = $dSencePD- (ClockinReccordController::$min_laps * 60);
        $dIInfD = $dSenceD- (ClockinReccordController::$min_laps * 60);
        $dIInfPF = $dSencePF- (ClockinReccordController::$min_laps * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $dISupA = $dSenceA+ (ClockinReccordController::$min_laps * 60);
        $dISupPD = $dSencePD+ (ClockinReccordController::$min_laps * 60);
        $dISupPF = $dSencePF+ (ClockinReccordController::$min_laps * 60);

        $hISupD = $hSenceD+ (ClockinReccordController::$min_laps * 60);
        $dISupD = $dSenceD+ (ClockinReccordController::$min_laps * 60);


        $empTab = array();
        $clockinRecordTab = array();
        $dataTable = array();
        $empClockinRecordTab = array();

        // On récupère les données appartenant au département sélectionné

        $don = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->history($dep,$dIInfA,$dISupA,$dIInfPD,$dISupPD,$dIInfPF,$dISupPF,$dIInfD,$dISupD);
        $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeByDep($dep);
        foreach ($emp as $e){
            //echo "\nEmployee id : ".$e->getId()."\n";
            $clockinRecordTab = 0;
            $empClockinRecordTab = array();
            //echo "\nPour l'Employee id : ".$e->getId()."\n Le tableau est : ";
            $empTab[] = $e->getSurname();
            $clockinRecordTab = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empHistory($e->getId(),$dep,$dIInfA,$dISupA,$dIInfPD,$dISupPD,$dIInfPF,$dISupPF,$dIInfD,$dISupD);
            //echo "\nLa taille du résultat est : ".sizeof($clockinRecordTab)."\n";
            foreach ($clockinRecordTab as $cr){
                $empClockinRecordTab[] = $cr->getId();
            }
            // Si le tableau n'est pas vide,on peut incrémenter

            $dataTable[] = $empClockinRecordTab;
        }

        return new JsonResponse($dataTable);
    }

    /**
     * @Route("/present", name="present")
     */
    public function employeePresent(Request $request){
        $date = strtotime($request->request->get('date'));
        $empId = $request->request->get('id');
        $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($empId);
        if($emp != null){
            $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->present($emp,$date);
            if($cr != null){
                return new Response("1");
            }else{
                return new Response("0");
            }
        }else{
            return new Response("0");
        }
    }
}
