<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Controller\ClockinReccordController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StatsController extends ClockinReccordController
{
    /**
     * @Route("/t",name="t")
     */
    
    public function tAction(Request $request){
        $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find(26);
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord");
        $je = json_decode($employe->getWorkingHour()->getWorkingHour(),true);
        $theDay = "lundi";
        $interval = 180*60;

        $data = $cr->quota($employe,strtotime("30 March 2018"),$interval);

        foreach ($data as $d){
        }

        return new Response("OK");
    }

    /**
     * @Route("/persStat",name="persStat")
     */
    public function persStatAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

        $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/viewPersStat.html.twig',array(
            'listDep'=>$dep,
            'listEmployee'=>$listEmployee
        ));
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
     * @Route("/userStats",name="userStats")
    */
    public function userStatsAction(Request $request){

        $emp = $request->request->get("empId");
        $dateFrom = $request->request->get("dateFrom");
        $timeFrom = strtotime($request->request->get("dateFrom")." 00:00:00");
        $dateTo = $request->request->get("dateTo");
        $timeTo = strtotime($request->request->get("dateTo")." 00:00:00");
        $interval = $request->request->get("interval");

        $timeDays = $timeTo-$timeFrom;
        $days = $timeDays/(60*60*24);

        $nowTime = $timeFrom;
        $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
        $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(),true);
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord");

        $absences=0;
        $retards = 0;

        $totalTempsabsences=0;
        $totalTempsretards = 0;

        $departs = 0;
        $departsPause = 0;

        $retardDiff =0;

        $sommeAbsences =0;
        $sommeRetards =0;
        $sommeDeparts =0;
        $sommeDepartsPause =0;

        $tempsPerdusAbsences=0;
        $tempsPerdusRetards=0;
        $tempsPerdusDeparts=0;

        $tabDepartsPause = array();
        $tabRetardsPause = array();
        $tabDeparts = array();
        $tabRetards = array();

        $quota_fait = 0;
        $quota_total = 0;

        // On boucle sur les jours sélectionnés
        $i=0;
        for ($cpt=0;$cpt<=$days;$cpt++){
            $theDay = date('N',$nowTime);
            $theDay = $this->dateDayNameFrench($theDay);
            $type = $empWH[$theDay][0]["type"];
            $quota = $empWH[$theDay][0]["quota"];
            $quotaUtilisateur = $empWH[$theDay][0]["quota"];

            $hAN = $empWH[$theDay][0]["beginHour"];
            $_heure_debut = null;
            $_minuites_debut = null;
            $_time_heure_debut = null;
            $_time_minuites_debut = null;

            // Pour éviter les erreurs de "offset"
            if(($hAN != null) && ($hAN !="")){
                $_heure_debut = explode(':',$hAN)[0];
                $_minuites_debut = explode(':',$hAN)[1];

                $_time_heure_debut = ((int)$_heure_debut)*60*60;
                $_time_minuites_debut =((int)$_minuites_debut)*60;

                $_total_time = $_time_heure_debut+$_time_minuites_debut;
            }

            /*echo "\n Heures : ".$_heure_debut;
            echo "\n Minuites : ".$_minuites_debut;
            echo "\n Timestamp Heures : ".$_time_heure_debut;
            echo "\n Timestamp Minuites : ".$_time_minuites_debut;
            */
            $test = null;

            /*
             * NOTE :
             * --------------------------------------------------------------------------
             *
             * Je dois modifier la fonction retard.Pour le moment il ne se base pas sur
             * le clockinHour de l'employé.C'est à dire son heure d'arrivée
             * définie pour lui dans son clockinHour.
             *
             * Ceci est aussi valable pour la fonction departPremature
             *
             * Pour un employé ayant pour type de clockinHour de ce jour = 2
             * S'il valide son quota horraire,on ne doit pas considérer son retard
             * dans la totalisation des heures perdus
            */
            switch ($type){
                case "1":
                    // Si son workingHour est de type 1
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                        $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                        $timePerdusAbsences = ($timeFin - $timeDebut);
                        $tempsPerdusAbsences = $timePerdusAbsences/60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    $retardDiff = $cr->retard($employe,$nowTime,$interval,$_total_time);
                    if($retardDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetards += $retardDiff/(60);
                        $ct = date('H:i',$retardDiff);
                        $tabRetards[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetards);
                    }
                    $retardPauseDiff = $cr->retardPause($employe,$theDay,$nowTime,$_total_time);
                    if($retardPauseDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        //echo "\n J'ai detecte un retard de pause \n";
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetardsPause = $retardPauseDiff/(60);
                        $tempsPerdusRetards+= $retardPauseDiff/(60);
                        $ct = date('H:i',$retardDiff);
                        $tabRetardsPause[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetardsPause);
                    }
                    $departDiff = $cr->departPremature($employe,$nowTime,$interval);
                    if($departDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        $departs++;
                        $sommeDeparts +=$departDiff[0];
                        $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                        // Pour prendre en compte les departs de 17h
                        $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
                        $ct = date('H:i',$departDiff[1]);
                        $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);
                    }
                    $departPauseDiff = $cr->departPausePremature($employe,$nowTime,$interval);
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
                    }else{
                        $test = "trrrrrr";
                    }
                    break;
                case "2":
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $tempsPerdusAbsences = ((int)$quota)*60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    $retardDiff = $cr->retard($employe,$nowTime,$interval,$_total_time);
                    if($retardDiff != null){
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetards = $retardDiff/(60);
                    }
                    $retardPauseDiff = $cr->retardPause($employe,$theDay,$nowTime,$_total_time);
                    if($retardPauseDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        //echo "\n J'ai detecte un retard de pause \n";
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetardsPause = $retardPauseDiff/(60);
                        $tempsPerdusRetards+= $retardPauseDiff/(60);
                        $ct = date('H:i',$retardDiff);
                        $tabRetardsPause[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetardsPause);
                    }
                    $departDiff = $cr->departPremature($employe,$nowTime,$interval);
                    if($departDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        $departs++;
                        $sommeDeparts +=$departDiff[0];
                        $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                        // Pour prendre en compte les departs de 17h
                        $tempsPerdusDeparts +=$tempsPerdusDepartsFin;
                        $ct = date('H:i',$departDiff[1]);
                        $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);
                    }
                    $departPauseDiff = $cr->departPausePremature($employe,$nowTime,$interval);
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
                    }else{
                        $test = "trrrrrr";
                    }

                    // Après tous on recupère ses quotas en appelant la fonction historique

                    $history = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('Y-m-d',$nowTime),$employe->getId(),$request);
                    if(($history != null) && ($history != "")){
                        $history = json_decode($history->getContent(),true);
                        $quota_total += $history["quota"];
                        $quota_fait += $history["quota_fait"];
                    }

                    /*print_r("\n GET CONTENT BEGIN \n");
                    print_r($history);
                    print_r("\n GET CONTENT END \n");*/

                    break;
                case "3":
                    // Si son workingHour est de type 1
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                        $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                        $timePerdusAbsences = ($timeFin - $timeDebut);
                        $tempsPerdusAbsences = $timePerdusAbsences/60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    break;
            }

            $donnees = array("nbreAbsences"=>$absences,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"tpr"=>$tempsPerdusRetards,"tpd"=>$tempsPerdusDeparts,"type"=>$type,"retardStats"=>$tabRetards,"retardPauseStats"=>$tabRetardsPause,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts,"quota_total"=>$quota_total,"quota_fait"=>$quota_fait);
            $nowTime = $nowTime+86400;
        }


        //return new Response($history);
        if($donnees != null){
            return new JsonResponse($donnees);
        }else{
            return new Response("Erreur");
        }
    }




    /*
     * Section des statistiques departementales
     */

    /**
     * @Route("/depStat",name="depStat")
     */
    public function depStatAction(Request $request)
    {
        $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/viewDepStat.html.twig',array(
            'listDep'=>$dep
        ));
    }

    /*
     * Cette version de la fonction s'occupe de retourner les stats des employes
     * En recuperant les données devant etre traitées non pas via $request
     * Mais directement dans l'URL
     * Ceci permettrait de l'utiliser dans la fonction depStats*/
    private function _userStatsAction($emp,$dateFrom,$dateTo,$interval){

        $timeFrom = strtotime($dateFrom." 00:00:00");
        $timeTo = strtotime($dateTo." 00:00:00");

        $timeDays = $timeTo-$timeFrom;
        $days = $timeDays/(60*60*24);

        $nowTime = $timeFrom;
        $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
        $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(),true);
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord");

        $absences=0;
        $retards = 0;

        $totalTempsabsences=0;
        $totalTempsretards = 0;

        $departs = 0;
        $departsPause = 0;

        $retardDiff =0;

        $sommeAbsences =0;
        $sommeRetards =0;
        $sommeDeparts =0;
        $sommeDepartsPause =0;

        $tempsPerdusAbsences=0;
        $tempsPerdusRetards=0;
        $tempsPerdusDeparts=0;

        $tabDepartsPause = array();
        $tabDeparts = array();
        // On boucle sur les jours sélectionnés
        $i=0;
        for ($cpt=0;$cpt<=$days;$cpt++){

            $theDay = date('N',$nowTime);
            $theDay = $this->dateDayNameFrench($theDay);
            $type = $empWH[$theDay][0]["type"];
            $quota = $empWH[$theDay][0]["quota"];
            $quotaUtilisateur = $empWH[$theDay][0]["quota"];
            $test = null;

            /*
             * NOTE :
             * --------------------------------------------------------------------------
             *
             * Je dois modifier la fonction retard.Pour le moment il ne se base pas sur
             * le clockinHour de l'employé.C'est à dire son heure d'arrivée
             * définie pour lui dans son clockinHour.
             *
             * Ceci est aussi valable pour la fonction departPremature
             *
             * Pour un employé ayant pour type de clockinHour de ce jour = 2
             * S'il valide son quota horraire,on ne doit pas considérer son retard
             * dans la totalisation des heures perdus
            */
            switch ($type){
                case "1":
                    // Si son workingHour est de type 1
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                        $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                        $timePerdusAbsences = ($timeFin - $timeDebut);
                        $tempsPerdusAbsences = $timePerdusAbsences/60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    $retardDiff = $cr->retard($employe,$nowTime,$interval);
                    if($retardDiff != null){
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetards = $retardDiff/(60);
                    }
                    $departDiff = $cr->departPremature($employe,$nowTime,$interval);
                    if($departDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        $departs++;
                        $sommeDeparts +=$departDiff[0];
                        $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                        // Pour prendre en compte les departs de 17h
                        $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
                        $ct = date('H:i',$departDiff[1]);
                        $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);
                    }
                    $departPauseDiff = $cr->departPausePremature($employe,$nowTime,$interval);
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
                    }else{
                        $test = "trrrrrr";
                    }
                    break;
                case "2":
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $tempsPerdusAbsences = ((int)$quota)*60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    $retardDiff = $cr->retard($employe,$nowTime,$interval);
                    if($retardDiff != null){
                        $retards++;
                        $sommeRetards +=$retardDiff;
                        $tempsPerdusRetards = $retardDiff/(60);
                    }
                    $departDiff = $cr->departPremature($employe,$nowTime,$interval);
                    if($departDiff != null){
                        $nowDate = date('d/m/Y',$nowTime);
                        $departs++;
                        $sommeDeparts +=$departDiff[0];
                        $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                        // Pour prendre en compte les departs de 17h
                        $tempsPerdusDeparts +=$tempsPerdusDepartsFin;
                        $ct = date('H:i',$departDiff[1]);
                        $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin);
                    }
                    $departPauseDiff = $cr->departPausePremature($employe,$nowTime,$interval);
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
                    }else{
                        $test = "trrrrrr";
                    }
                    break;
                case "3":
                    // Si son workingHour est de type 1
                    if(!$cr->present($employe,$nowTime)){
                        $absences++;
                        $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                        $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                        $timePerdusAbsences = ($timeFin - $timeDebut);
                        $tempsPerdusAbsences = $timePerdusAbsences/60;
                        $sommeAbsences +=$tempsPerdusAbsences;
                    }
                    break;
            }

            $donnees = array("nbreAbsences"=>$absences,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"tpr"=>$tempsPerdusRetards,"tpd"=>$tempsPerdusDeparts,"type"=>$type,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts);
            $nowTime = $nowTime+86400;
        }

        if($donnees != null){
            return $donnees;
        }else{
            return "Erreur";
        }
    }

    /**
     * @Route("/depStats",name="depStats")
     */
    public function depStatsAction(Request $request)
    {

        // On récupère les départements envoyés
        $deps = $request->request->get("deps");

        // On récupère les dates
        $dateFrom = $request->request->get("dateFrom");
        $timeFrom = strtotime($request->request->get("dateFrom") . " 00:00:00");
        $dateTo = $request->request->get("dateTo");
        $timeTo = strtotime($request->request->get("dateTo") . " 00:00:00");

        $interval = $request->request->get("interval");

        $timeDays = $timeTo - $timeFrom;
        $days = $timeDays / (60 * 60 * 24);

        $perteRetardTemps = 0;
        $perteDepartTemps = 0;
        $tabStats = array();

        /*
         * A chaque fois qu'on parcours les départements
         * On doit cumuler toutes les statistiques de tous les employés
         * */
        foreach ($deps as $dep) {
            // A chaque fois qu'on change de département, on réinitialise la somme totale
            $sommeTotaleRetard = 0;
            $sommeTotaleDepart = 0;
            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeByDep($dep);
            // On parcours aussi tous les employés pour additionner leur stats
            foreach ($emp as $e){
                $empSalary = $e->getSalary();
                $salaireEnMinuite = $empSalary/(30*24*60); // 30 Jours,24 heures, 60 minuites

                // Cette variable doit contenir les stats de l'employé courant
                $stats = $this->_userStatsAction($e, $dateFrom, $dateTo, $interval);
                // Somme perdue pour cet employé
                $sommePerdueRetard = ($salaireEnMinuite*$stats["tpr"]);
                $sommePerdueDepart = ($salaireEnMinuite*$stats["tpd"]);
                // On incrémente le total d'argent perdu
                $sommeTotaleRetard += $sommePerdueRetard;
                $sommeTotaleDepart += $sommePerdueDepart;
                /*
                 * Pour chaque département on veut connaitre :
                 * - Pertes retards en temps
                 * - Pertes retards en argent
                 * - Pertes departs en temps
                 * - Pertes departs en argent*/

                // Pour ce département, voici les pertes en temps
                $perteRetardTemps += $stats ["tpr"];
                $perteDepartTemps += $stats ["tpd"];
            }
            // Nom du département courant
            $depName = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->find("$dep")->getName();
            // On met les informations de tous les départements dans un tableau
            $tabStats[]= array("departementId"=>$dep,"departement"=>$depName,"tpr"=>$perteRetardTemps,"tpd"=>$perteDepartTemps,"spr"=>ceil($sommeTotaleRetard),"spd"=>ceil($sommeTotaleDepart));
        }
        return new JsonResponse($tabStats);
    }
}