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

    public function convertHourInMinutes($time){
        if(!empty($time) && $time != null){
            $time = explode(":",$time);
            $hour = $time[0];
            $min = $time[1];
        }else{
            $hour = 0;
            $min = 0;
        }

        return (($hour*60)+$min);
    }

    /**
     * @Route("/persStat",name="persStat")
     */
    public function persStatAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $em = $this->getDoctrine()->getManager();
            $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/viewPersStat.html.twig',array(
                'listDep'=>$dep,
                'listEmployee'=>$listEmployee
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/rapports",name="rapports")
     */
    public function rapportsAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $em = $this->getDoctrine()->getManager();
            $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/rapports.html.twig',array(
                'listDep'=>$dep,
                'listEmployee'=>$listEmployee
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/rapports_excel",name="rapports_excel")
     */
    public function rapportsExcelAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $em = $this->getDoctrine()->getManager();
            $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/rapports_excel.html.twig',array(
                'listDep'=>$dep,
                'listEmployee'=>$listEmployee
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/userStats",name="userStats")ge
    */
    public function userStatsAction(Request $request,$empId=null,$fromeDate=null,$toDate=null){


        $lost_time_jour = 0;

        // if/else condition because of calling this in the generatePDF function
        if($empId==null && $fromeDate==null && $toDate==null){
            $emp = $request->request->get("empId");
            $dateFrom = $request->request->get("dateFrom");
            $dateTo = $request->request->get("dateTo");
        }else{
            $emp = $empId;
            $dateFrom = $fromeDate;
            $dateTo = $toDate;
        }

        $pR = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");
        $nD = $this->getDoctrine()->getManager()->getRepository("AppBundle:NullDate");

        $timeFrom = strtotime($dateFrom." 00:00:00");
        $timeTo = strtotime($dateTo." 00:00:00");

        $timeDays = $timeTo-$timeFrom;
        $days = $timeDays/(60*60*24);

        $nowTime = $timeFrom;
        $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
        $interval = ($employe->getWorkingHour()->getTolerance())*60;
        $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(),true);
        $taux = (float)$employe->getWorkingHour()->getTaux();
        $salaire = $employe->getSalary();
        $jour_travail = (int)$employe->getWorkingHour()->getJourTravail();
        $salaire_en_minuite = (($salaire/30)/24)/60;
        $salaire_quota_en_minuite = (($salaire/30)/8)/60;
        $cr = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord");

        $absences=0;
        $nbrePermission=0;
        $sommePerduAbsence_3;
        $retards = 0;
        $bonus_retards = 0;

        $totalTempsabsences=0;
        $totalTempsretards = 0;

        $departs = 0;
        $departsPause = 0;

        $retardDiff =0;

        $sommeAbsences =0;
        $sommeRetards =0;
        $bonusSommeRetards = 0;
        $sommeDeparts =0;
        $sommeDepartsPause =0;

        $tempsPerdusAbsences=0;
        $tempsPerdusRetards=0;
        $bonusTempsGagneRetards = 0;
        $tempsPerdusDeparts=0;

        $sommePerduQuota = 0;
        $sommePerduAbsence = 0;
        $sommePerduDepart = 0;
        $sommePerduRetard = 0;
        $bonusSommeGagneRetard = 0;

        $timePP = 0;
        $tempPP = 0;
        $tempsTPP = 0;
        $tempPerduDepartPausePermission = 0;
        $tempsTPerduDepartPausePermission = 0;
        $tempPerduDepartPermission = 0;
        $tempsTPerduDepartPermission = 0;
        $tempPerduRetardPausePermission = 0;
        $tempsTPerduRetardPausePermission = 0;
        $tempPerduRetardPermission = 0;
        $tempsTPerduRetardPermission = 0;

        $tabDepartsPause = array();
        $tabDepartsPausePermission = array();
        $tabRetardsPause = array();
        $tabRetardsPausePermission = array();
        $tabDeparts = array();
        $tabDepartsPermission = array();
        $tabRetards = array();
        $tabRetardsPermission = array();

        $tabAbsencesPermission = array();

        $quota_fait = 0;
        $quota_total = 0;
        $quota_emp_1_4 = 0;
        $inc_auth=0;

        $controlNowTime =0;
        $controlNowTime2 =0;
        $controlNowTimeForOtheType = 0;

        $jourFeries =0; 

        // On boucle sur les jours sélectionnés
        $i=0;$j=0;
        $lost_time = 0;
        $sommePerduAuth = 0;
        $tabType = array();
        for ($cpt=0;$cpt<=$days;$cpt++){
        	$his = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('d-m-Y',$nowTime),$employe->getId(),$request);
            $his = json_decode($his->getContent(),true);
            
            $_arr = $his["arrive"];
            $_dep = $his["depart"];
            $_pau = $his["pause"];
            $_fpa = $his["finPause"];

            $theDay = date('N',$nowTime);
            $theDay = $this->dateDayNameFrench($theDay);
            $type = $empWH[$theDay][0]["type"];

            $tabType[$theDay] = $type;

            $quota = $empWH[$theDay][0]["quota"];
            $quotaUtilisateur = $empWH[$theDay][0]["quota"];

            $hAN = $empWH[$theDay][0]["beginHour"];
            $hDN = $empWH[$theDay][0]["endHour"];
            $hDPN = $empWH[$theDay][0]["pauseBeginHour"];
            $hFPN = $empWH[$theDay][0]["pauseEndHour"];
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

            // Pour le calcul d'un depart prématuré de pause,Calculons l'intervalle
            $heureDebutNormal = $empWH[$theDay][0]["beginHour"];
            $heureFinNormal = $empWH[$theDay][0]["endHour"];
            $heureDebutNormalPause = $empWH[$theDay][0]["pauseBeginHour"];
            $heureFinNormalPause = $empWH[$theDay][0]["pauseEndHour"];

            $beginHourExploded = explode(":",$heureDebutNormal);
            $endHourExploded = explode(":",$heureFinNormal);
            $pauseBeginHourExploded = explode(":",$heureDebutNormalPause);
            $pauseEndHourExploded = explode(":",$heureFinNormalPause);

            if(sizeof($beginHourExploded)>1 && sizeof($endHourExploded)>1){
                $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);
            }else{
                $beginHourInMinutes = 0;
                $endHourInMinutes = 0;
            }
            $heureNormaleArrive = $beginHourInMinutes*60;
            $heureNormaleDepart = $endHourInMinutes*60;

            if(sizeof($pauseBeginHourExploded)>1){
                $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                $heureNormaleArrivePause = $pauseEndHourInMinutes*60;
                $heureNormaleDepartPause = $pauseBeginHourInMinutes*60;
            }else{
                $interval_pause = 0;
                $heureNormaleArrivePause = 0;
                $heureNormaleDepartPause = 0;
            }

            if($type != "null" && $type != null){
                $quota_emp_1_4 += ((($heureNormaleDepartPause - $heureNormaleArrive)+($heureNormaleDepart - $heureNormaleArrivePause))/60)/60;
            }

            if ($type == "1" || $type == "2" || $type == "4"){
                $j++;
                // Si son workingHour est de type 1 ou 2
                //print_r("//// Heure normale d'arrive ".$nowTime." //////\n");
                if(!$cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                    //print_r("Passage ".($cpt+1)." date : ".date('d-m-Y',$nowTime)."\n");
                    $nowDate = date('d/m/Y',$nowTime);
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,"23:59","08:00")){
                        if(!$nD->dayIsNull($permDate)){
                            $absences++;
                            $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                            $timePauseBegin = strtotime($empWH[$theDay][0]["pauseBeginHour"]);
                            $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                            $timePauseEnd = strtotime($empWH[$theDay][0]["pauseEndHour"]);
                            
                            if($type == "1"){
                                $timePerdusAbsences = ($timePauseBegin - $timeDebut)+($timeFin - $timePauseEnd);
                                $timePerdusAbsences /= 3600;
                            }else if($type == "4"){
                                $timePerdusAbsences = $timeFin - $timeDebut;
                                $timePerdusAbsences /= 3600;
                            }else if($type == "2"){
                                $qu = (float)$empWH[$theDay][0]["quota"];
                                $timePerdusAbsences = $qu;
                            }
                            //$tempsPerdusAbsences += $tempPerdu;
                            //$sommeAbsences +=$tempsPerdusAbsences;
                            $sommeAbsences +=$timePerdusAbsences;
                            if($taux > 0){
                                $sommePerduAbsence = ((($salaire*12)/52)/$taux)*$sommeAbsences;
                            }else{
                                $sommePerduAbsence = 0;
                            }
                        }else{
                            $jourFeries ++; 
                        }
                    }
                    $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$empWH[$theDay][0]["endHour"],$empWH[$theDay][0]["beginHour"]);
                    if($p){
                        /*
                         * We need some other variables to avoid conflicts with userStats variables
                         */
                        $nbrePermission++;
                        $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                        $timeDebutPause = strtotime($empWH[$theDay][0]["pauseBeginHour"]);
                        $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                        $timeFinPause = strtotime($empWH[$theDay][0]["pauseEndHour"]);
                        if($type == "1" || $type == 1){
                            $timePP = ($timeDebutPause-$timeDebut)+($timeFin-$timeFinPause);
                        }else if($type == "4" || $type == 4){
                            $timePP = ($timeFin - $timeDebut);
                        }else if($type == "2" || $type == 2){
                            $timePP = (float)$empWH[$theDay][0]["quota"];
                        }
                        $tempPP = $timePP/60/60; // Hour
                        $tempsTPP += $tempPP;
                        $tabAbsencesPermission[]= array("date"=>$nowDate,"heureDepart"=>null,"tempsTotal"=>$tempsTPP,"type"=>"Absence","tempsPerdu"=>$tempPP);
                    }
                }
                $retardDiff = $cr->retard($employe,$nowTime,$interval,$heureNormaleArrive,$empWH[$theDay][0]["beginHour"]);
                if($retardDiff == false){
                	if($cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){

                		//print_r("\n Passage 1 FALSE condition retard diff\n");

                	$ct = date('H:i',$retardDiff[1]);
                        if($type == 1 || $type == "1"){
                        	if( ($_arr == 0 || $_arr == null) || ($_pau == 0 || $_pau == null) || (($_arr == 0 || $_arr == null) && ($_pau == 0 || $_pau == null)) ){
                        		$controlNowTime = $nowTime;
                        		$inc_auth++;
		                         $lost_time_jour = ((int)($this->convertHourInMinutes($heureDebutNormalPause)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
		                        $lost_time += $lost_time_jour;
		                        if($taux > 0){
		                            $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
		                        }else{
		                            $sommePerduAuth = 0;
		                        }
                        	}
                        }elseif (($type == 2 || $type == "2")) {
                        	if( ($_arr == 0 || $_arr == null) || ($_dep == 0 || $_dep == null) || (($_arr == 0 || $_arr == null) && ($_dep == 0 || $_dep == null)) ){
                        		$controlNowTimeForOtheType = $nowTime;
                        		
                        		$inc_auth++;
	                            $lost_time_jour += ((int)($his["quota"]))/60;
	                            $lost_time += $lost_time_jour;
	                            if($taux > 0){
	                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
	                            }else{
	                                $sommePerduAuth = 0;
	                            }
                        	}
                        		# code...
                        }elseif(($type == 4 || $type == "4")){
                        	if( ($_arr == 0 || $_arr == null) || ($_dep == 0 || $_dep == null) || (($_arr == 0 || $_arr == null) && ($_dep == 0 || $_dep == null)) ){
                        		$controlNowTimeForOtheType = $nowTime;
                        		
                        		$inc_auth++;
	                            $lost_time_jour += ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
	                            $lost_time += $lost_time_jour;
	                            if($taux > 0){
	                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
	                            }else{
	                                $sommePerduAuth = 0;
	                            }
                        	}
                        }

                	}
                }elseif($retardDiff[0] > 0){
                	//print_r("\n Passage 1 TRUE condition retard diff\n");

                    $nowDate = date('d/m/Y',$nowTime);
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,date("H:i",$retardDiff[1]),$heureDebutNormal)) {
                        if(!$nD->dayIsNull($permDate)){
		                    $ct = date('H:i',$retardDiff[1]);
                        	if($type == 1 || $type == "1"){
                        		
                        			$retards++;
		                            $sommeRetards +=$retardDiff[0];
		                            $tempsPerdusRetards += (float)($retardDiff[0]/(60))/60;
		                            $perte_temps = (float)($retardDiff[0]/(60))/60;
		                            if($taux > 0){
		                                $sommePerduRetard += ((($salaire*12)/52)/$taux)*$perte_temps;                                
		                            }else{
		                                $sommePerduRetard = 0;
		                            }
		                            $tabRetards[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$perte_temps,"temps_min"=>$perte_temps*60);
                        	}elseif (($type == 2 || $type == "2")){
								$retards++;
                                $sommeRetards +=$retardDiff[0];
                                $tempsPerdusRetards += (float)($retardDiff[0]/(60))/60;
                                $perte_temps = (float)($retardDiff[0]/(60))/60;
                                $ct = date('H:i',$retardDiff[1]);
                                if($taux > 0){
                                    $sommePerduRetard += ((($salaire*12)/52)/$taux)*$perte_temps;                                
                                }else{
                                    $sommePerduRetard = 0;
                                }
                                $tabRetards[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$perte_temps,"temps_min"=>$perte_temps*60);
                        	}elseif(($type == 4 || $type == "4")) {
								$retards++;
                                $sommeRetards +=$retardDiff[0];
                                $tempsPerdusRetards += (float)($retardDiff[0]/(60))/60;
                                $perte_temps = (float)($retardDiff[0]/(60))/60;
                                $ct = date('H:i',$retardDiff[1]);
                                if($taux > 0){
                                    $sommePerduRetard += ((($salaire*12)/52)/$taux)*$perte_temps;
                                }else{
                                    $sommePerduRetard = 0;
                                }
							}
                        }
                    }

                    // Now we deal with the permissions calculations
                    $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$ct,$empWH[$theDay][0]["beginHour"]);
                    if($p){
                        /*
                         * We need some other variables to avoid conflicts with userStats variables
                         */
                        $nbrePermission++;
                        $tempPerduRetardPermission = (($retardDiff[0])/(60))/60;
                        $tempsTPerduRetardPermission += $tempPerduRetardPermission;
                        $tabRetardsPermission[]= array("date"=>$nowDate,"heureRetard"=>null,"tempsTotal"=>$tempsTPerduRetardPermission,"type"=>"Retard","tempsPerdu"=>$tempPerduRetardPermission);
                    }
                }else{
                    /* Must check if the clockinTime isn't null this date (0 in the history)
                    *  Because if it is null that day we should not count it as a bonus
                    */

                    if($type == 1 || $type == "1"){
                        if( (($retardDiff[1] != null) && !empty($retardDiff[1])) && ($_pau != 0 && $_pau != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$retardDiff[0];
                            $bonusTempsGagneRetards += (float)($retardDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($retardDiff[0]/(60))/60;
                            $ct = date('H:i',$retardDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }
    
                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }elseif( ($type == 2 || $type == "2") || ($type == 4 || $type == "4") ){
                        if( (($retardDiff[1] != null) && !empty($retardDiff[1])) && ($_dep != 0 && $_dep != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$retardDiff[0];
                            $bonusTempsGagneRetards += (float)($retardDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($retardDiff[0]/(60))/60;
                            $ct = date('H:i',$retardDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }

                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }
                }
                $retardPauseDiff = $cr->retardPause($employe,$nowTime,$interval_pause,$heureNormaleArrivePause,$empWH[$theDay][0]["pauseEndHour"]);
                if($retardPauseDiff == false){
                	if($cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){

                		$ct = date('H:i',$retardPauseDiff[1]);
                        	if($type == 1 || $type == "1"){
                        		if( ($_fpa == 0 || $_fpa == null) || ($_dep == 0 || $_dep == null) || (($_fpa == 0 || $_fpa == null) && ($_dep == 0 || $_dep == null)) ){
                        			$controlNowTime2 = $nowTime;
                        			$inc_auth++;
		                            $lost_time_jour= ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureFinNormalPause)))/60;
		                            $lost_time += $lost_time_jour;
		                            if($taux > 0){
		                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
		                            }else{
		                                $sommePerduAuth = 0;
		                            }
                        		}
                        	}elseif (($type == 2 || $type == "2") || ($type == 4 || $type == "4")) {
                        		# code...
                        	}


                	}


                }elseif($retardPauseDiff[0] > 0){
                	//print_r("\n Passage 2 one condition retardPause diff\n");

                    $nowDate = date('d/m/Y',$nowTime);
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,date("H:i",$retardPauseDiff[1]),$heureFinNormalPause)){
                        if(!$nD->dayIsNull($permDate)) {
		                    $ct = date('H:i',$retardPauseDiff[1]);
                        	if($type == 1 || $type == "1"){
                        			$retards++;
		                            $sommeRetards +=$retardPauseDiff[0];
		                            $tempsPerdusRetardsPause = ($retardPauseDiff[0]/(60))/60;
		                            $tempsPerdusRetards+= ($retardPauseDiff[0]/(60))/60;
		                            if($taux > 0){
		                                $sommePerduRetard += ((($salaire*12)/52)/$taux)*$tempsPerdusRetardsPause;
		                            }else{
		                                $sommePerduRetard = 0;
		                            }
		                            $tabRetardsPause[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetardsPause,"temps_min"=>$tempsPerdusRetardsPause*60);
                        	}elseif (($type == 2 || $type == "2")){
                        		$retards++;
                                $sommeRetards +=$retardPauseDiff[0];
                                $tempsPerdusRetardsPause = ($retardPauseDiff[0]/(60))/60;
                                $tempsPerdusRetards+= ($retardPauseDiff[0]/(60))/60;
                                $ct = date('H:i',$retardPauseDiff[1]);
                                if($taux > 0){
                                    $sommePerduRetard += ((($salaire*12)/52)/$taux)*$tempsPerdusRetardsPause;
                                }else{
                                    $sommePerduRetard = 0;
                                }
                                $tabRetardsPause[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetardsPause,"temps_min"=>$tempsPerdusRetardsPause*60);
                        	}elseif(($type == 4 || $type == "4")) {
                        		$retards++;
                                $sommeRetards +=$retardPauseDiff[0];
                                $tempsPerdusRetardsPause = ($retardPauseDiff[0]/(60))/60;
                                $tempsPerdusRetards+= ($retardPauseDiff[0]/(60))/60;
                                $ct = date('H:i',$retardPauseDiff[1]);
                                if($taux > 0){
                                    $sommePerduRetard += ((($salaire*12)/52)/$taux)*$tempsPerdusRetardsPause;
                                }else{
                                    $sommePerduRetard = 0;
                                }
                                $tabRetardsPause[]= array("date"=>$nowDate,"heureRetard"=>$ct,"temps"=>$tempsPerdusRetardsPause,"temps_min"=>$tempsPerdusRetardsPause*60);
                        	}
                        }
                    }
                    // Now we deal with the permissions calculations
                    $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$ct,$empWH[$theDay][0]["pauseEndHour"]);
                    if($p){
                        /*
                         * We need some other variables to avoid conflicts with userStats variables
                         */
                        $nbrePermission++;
                        $tempPerduRetardPausePermission = (($retardPauseDiff[0])/(60))/60;
                        $tempsTPerduRetardPausePermission += $tempPerduRetardPausePermission;
                        $tabRetardsPausePermission[]= array("date"=>$nowDate,"heureRetard"=>null,"tempsTotal"=>$tempsTPerduRetardPausePermission,"type"=>"Retard pause","tempsPerdu"=>$tempPerduRetardPausePermission);
                    }
                }else{
                    /* Must check if the clockinTime isn't null this date (0 in the history)
                    *  Because if it is null that day we should not count it as a bonus
                    */

                    if($type == 1 || $type == "1"){
                        if( (($retardPauseDiff[1] != null) && !empty($retardPauseDiff[1])) && ($_dep != 0 && $_dep != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$retardPauseDiff[0];
                            $bonusTempsGagneRetards += (float)($retardPauseDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($retardPauseDiff[0]/(60))/60;
                            $ct = date('H:i',$retardPauseDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }
    
                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }
                }
                $departDiff = $cr->departPremature($employe,$nowTime,$interval,$heureNormaleDepart);
                if($departDiff == false){
                	if($cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){

                		//print_r("\n Passage 3 FALSE condition departPremature diff\n");

                	$ct = date('H:i',$retardDiff[1]);
                        if($type == 1 || $type == "1"){
                        	if( ($_fpa == 0 || $_fpa == null) || ($_dep == 0 || $_dep == null) || (($_fpa == 0 || $_fpa == null) && ($_dep == 0 || $_dep == null)) ){
                        			if($controlNowTime2 != $nowTime){
                        				$inc_auth++;
			                            $lost_time_jour= ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureFinNormalPause)))/60;
			                            $lost_time += $lost_time_jour;
			                            if($taux > 0){
			                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
			                            }else{
			                                $sommePerduAuth = 0;
			                            }
                        			}
                        		}
                        }elseif (($type == 2 || $type == "2")) {
                        	if( ($_arr == 0 || $_arr == null) || ($_dep == 0 || $_dep == null) || (($_arr == 0 || $_arr == null) && ($_dep == 0 || $_dep == null)) ){

                        		if($controlNowTimeForOtheType != $nowTime){
                                    $inc_auth++;
		                            $lost_time_jour = ((int)($his["quota"]))/60;                                    
		                            //$lost_time_jour += ((int)($his["quota"]))/60;
		                            $lost_time += $lost_time_jour;
		                            if($taux > 0){
		                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
		                            }else{
		                                $sommePerduAuth = 0;
		                            }
                        		}
                        	}
                        }elseif(($type == 4 || $type == "4")){
                        	if( ($_arr == 0 || $_arr == null) || ($_dep == 0 || $_dep == null) || (($_arr == 0 || $_arr == null) && ($_dep == 0 || $_dep == null)) ){

                        		if($controlNowTimeForOtheType != $nowTime){
                        			$inc_auth++;
		                            $lost_time_jour = ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
		                            //$lost_time_jour += ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;                                    
                                    $lost_time += $lost_time_jour;
		                            if($taux > 0){
		                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
		                            }else{
		                                $sommePerduAuth = 0;
		                            }
                        		}
                        		
                        	}
                        }
                	}
                }elseif($departDiff[0] > 0){
                	//print_r("\n Passage 3 TRUE condition departPremature diff\n");

                    $nowDate = date('d/m/Y',$nowTime);
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,date("H:i",$departDiff[1]),$heureFinNormal)){
                        if(!$nD->dayIsNull($permDate)) {
		                    $ct = date('H:i',$departDiff[1]);
                        	if($controlNowTime2 != $nowTime){
                        		if($type == 1 || $type == "1"){
                        			$departs++;
		                            $sommeDeparts +=$departDiff[0];
		                            $tempsPerdusDepartsFin = ($departDiff[0])/(60);
		                            $tempsPerdusDepartsFin /=60;
		                            
		                            $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
		                            if($taux > 0){
		                                $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsFin;
		                            }else{
		                                $sommePerduDepart = 0;
		                            }
		                            $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin,"temps_min"=>$tempsPerdusDepartsFin*60);
	                        	}elseif (($type == 2 || $type == "2")){
	                        		$departs++;
                                    $sommeDeparts +=$departDiff[0];
                                    $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                                    $tempsPerdusDepartsFin /=60;
                                    
                                    $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
                                    $ct = date('H:i',$departDiff[1]);
                                    if($taux > 0){
                                        $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsFin;
                                    }else{
                                        $sommePerduDepart = 0;
                                    }
                                    $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin,"temps_min"=>$tempsPerdusDepartsFin*60);
	                        	}elseif(($type == 4 || $type == "4")) {
	                        		$departs++;
                                    $sommeDeparts +=$departDiff[0];
                                    $tempsPerdusDepartsFin = ($departDiff[0])/(60);
                                    $tempsPerdusDepartsFin /=60;
                                    
                                    $tempsPerdusDeparts+=$tempsPerdusDepartsFin;
                                    $ct = date('H:i',$departDiff[1]);
                                    if($taux > 0){
                                        $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsFin;
                                    }else{
                                        $sommePerduDepart = 0;
                                    }
                                    $tabDeparts[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsFin,"temps_min"=>$tempsPerdusDepartsFin*60);
	                        	}
                        	}
                        }
                    }
                    // Now we deal with the permissions calculations
                    $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$ct,$empWH[$theDay][0]["endHour"]);
                    if($p){
                        /*
                         * We need some other variables to avoid conflicts with userStats variables
                         */
                        $nbrePermission++;
                        $tempPerduDepartPermission = (($departDiff[0])/(60))/60;
                        $tempsTPerduDepartPermission += $tempPerduDepartPermission;
                        $tabDepartsPermission[]= array("date"=>$nowDate,"heureDepart"=>null,"tempsTotal"=>$tempsTPerduDepartPermission,"type"=>"Départ prématuré","tempsPerdu"=>$tempPerduDepartPermission);
                    }
                }else{
                    /* Must check if the clockinTime isn't null this date (0 in the history)
                    *  Because if it is null that day we should not count it as a bonus
                    */
                    if($type == 1 || $type == "1"){
                        if( (($departDiff[1] != null) && !empty($departDiff[1])) && ($_fpa != 0 && $_fpa != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$departDiff[0];
                            $bonusTempsGagneRetards += (float)($departDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($departDiff[0]/(60))/60;
                            $ct = date('H:i',$departDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }
    
                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }elseif( ($type == 2 || $type == "2") || ($type == 4 || $type == "4") ){
                        if( (($departDiff[1] != null) && !empty($departDiff[1])) && ($_arr != 0 && $_arr != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$departDiff[0];
                            $bonusTempsGagneRetards += (float)($departDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($departDiff[0]/(60))/60;
                            $ct = date('H:i',$departDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }

                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }
                }
                $departPauseDiff = $cr->departPausePremature($employe,$nowTime,$interval_pause,$heureNormaleDepartPause);
                if($departPauseDiff == false){
                	if($cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){

                		$ct = date('H:i',$departPauseDiff[1]);
                        	if($type == 1 || $type == "1"){
                        		if( ($_arr == 0 || $_arr == null) || ($_pau == 0 || $_pau == null) || (($_arr == 0 || $_arr == null) && ($_pau == 0 || $_pau == null)) ){
                        			// Last verification
                        			if($controlNowTime != $nowTime){
                        				$inc_auth++;
			                            $lost_time_jour = ((int)($this->convertHourInMinutes($heureDebutNormalPause)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
			                            $lost_time += $lost_time_jour;
			                            if($taux > 0){
			                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
			                            }else{
			                                $sommePerduAuth = 0;
			                            }
                        			}
                        		}
                        	}

                	}

                }elseif($departPauseDiff[0] > 0){
                	//print_r("\n Passage 4 one condition departPause diff\n");
                    $nowDate = date('d/m/Y',$nowTime);
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,date("H:i",$departPauseDiff[1]),$heureDebutNormalPause)){
                        if(!$nD->dayIsNull($permDate)) {
		                    $ct = date('H:i',$departPauseDiff[1]);
                        	if($controlNowTime != $nowTime){
                        		if($type == 1 || $type == "1"){
                        			$i++;
		                            $departsPause++;
		                            // Pour prendre en compte les departs de 12 h aussi
		                            $departs++;
		                            $sommeDepartsPause +=$departPauseDiff[0];
		                            $tempsPerdusDepartsPause = ($departPauseDiff[0])/(60);
		                            $tempsPerdusDepartsPause /= 60;
		                            // Pour prendre en compte les departs de 12h aussi
		                            $tempsPerdusDeparts +=$tempsPerdusDepartsPause;
		                            if($taux > 0){
		                                $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsPause;                                
		                            }else{
		                                $sommePerduDepart = 0;
		                            }
		                            $tabDepartsPause[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsPause,"temps_min"=>$tempsPerdusDepartsPause*60);
	                        	}elseif (($type == 2 || $type == "2")){
	                        		$i++;
                                    $departsPause++;
                                    // Pour prendre en compte les departs de 12 h aussi
                                    $departs++;
                                    $sommeDepartsPause +=$departPauseDiff[0];
                                    $tempsPerdusDepartsPause = ($departPauseDiff[0])/(60);
                                    $tempsPerdusDepartsPause /= 60;
                                    // Pour prendre en compte les departs de 12h aussi
                                    $tempsPerdusDeparts +=$tempsPerdusDepartsPause;
                                    $ct = date('H:i',$departPauseDiff[1]);
                                    if($taux > 0){
                                        $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsPause;                                
                                    }else{
                                        $sommePerduDepart = 0;
                                    }
                                    $tabDepartsPause[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsPause,"temps_min"=>$tempsPerdusDepartsPause*60);
	                        	}elseif(($type == 4 || $type == "4")) {
	                        		$i++;
                                    $departsPause++;
                                    // Pour prendre en compte les departs de 12 h aussi
                                    $departs++;
                                    $sommeDepartsPause +=$departPauseDiff[0];
                                    $tempsPerdusDepartsPause = ($departPauseDiff[0])/(60);
                                    $tempsPerdusDepartsPause /= 60;
                                    // Pour prendre en compte les departs de 12h aussi
                                    $tempsPerdusDeparts +=$tempsPerdusDepartsPause;
                                    $ct = date('H:i',$departPauseDiff[1]);
                                    if($taux > 0){
                                        $sommePerduDepart += ((($salaire*12)/52)/$taux)*$tempsPerdusDepartsPause;                                
                                    }else{
                                        $sommePerduDepart = 0;
                                    }
                                    $tabDepartsPause[]= array("date"=>$nowDate,"heureDepart"=>$ct,"temps"=>$tempsPerdusDepartsPause,"temps_min"=>$tempsPerdusDepartsPause*60);
	                        	}
                        	}
                        }
                    }

                    // Now we deal with the permissions calculations
                    $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$ct,$empWH[$theDay][0]["pauseBeginHour"]);
                    if($p){
                        /*
                         * We need some other variables to avoid conflicts with userStats variables
                         */
                        $nbrePermission++;
                        $tempPerduDepartPausePermission = (($departPauseDiff[0])/(60))/60;
                        $tempsTPerduDepartPausePermission += $tempPerduDepartPausePermission;
                        $tabDepartsPausePermission[]= array("date"=>$nowDate,"heureDepart"=>null,"tempsTotal"=>$tempsTPerduDepartPausePermission,"type"=>"Depart pause prématuré","tempsPerdu"=>$tempPerduDepartPausePermission);
                    }
                }else{
                    /* Must check if the clockinTime isn't null this date (0 in the history)
                    *  Because if it is null that day we should not count it as a bonus
                    */
                    if($type == 1 || $type == "1"){
                        if( (($departPauseDiff[1] != null) && !empty($departPauseDiff[1])) && ($_arr != 0 && $_arr != null) ){
                            $bonus_retards++;
                            $bonusSommeRetards +=$departPauseDiff[0];
                            $bonusTempsGagneRetards += (float)($departPauseDiff[0]/(60))/60;
                            $bonus_gain_temps = (float)($departPauseDiff[0]/(60))/60;
                            $ct = date('H:i',$departPauseDiff[1]);
                            if($taux > 0){
                                $bonusSommeGagneRetard += ((($salaire*12)/52)/$taux)*$bonus_gain_temps;
                            }else{
                                $bonusSommeGagneRetard = 0;
                            }
    
                            /*print_r("retard diff : ".$departDiff[0]."\n");
                            print_r("somme totale : ".$bonusSommeRetards."\n");
                            print_r("somme totale en heure : ".$bonusTempsGagneRetards."\n\n");*/
                        }
                    }
                }

                /*// Now we deal with lost time calculations
                // First of all we need the terminals
                $his = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('d-m-Y',$nowTime),$employe->getId(),$request);
                //print_r("(((((((");
                $his = json_decode($his->getContent(),true);
                //print_r(")))))))");
                if((($his["arrive"] != null) && ($his["arrive"] != "")) || (($his["depart"] != null) && ($his["depart"] != "")) || (($his["pause"] != null) && ($his["pause"] != "")) || (($his["finPause"] != null) && ($his["finPause"] != ""))){
                    //print_r($his);
                    $_arr = $his["arrive"];
                    $_dep = $his["depart"];
                    $_pau = $his["pause"];
                    $_fpa = $his["finPause"];

                    // Now that we have terminals, we must check the type of workingHour
                    if($type == "1" || $type == 1){
                        // Un double test a faire
                        if(($_arr == 0 && $_pau != 0) || ($_pau == 0 && $_arr != 0) || ($_pau == 0 && $_arr == 0)){
                            $inc_auth++;
                            $lost_time_jour = ((int)($this->convertHourInMinutes($heureDebutNormalPause)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
                            $lost_time += $lost_time_jour;
                            if($taux > 0){
                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
                            }else{
                                $sommePerduAuth = 0;
                            }
                        }
                        if(($_fpa == 0 && $_dep != 0) || ($_dep == 0 && $_fpa !=0) || ($_dep == 0 && $_fpa ==0)){
                            $inc_auth++;
                            $lost_time_jour= ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureFinNormalPause)))/60;
                            $lost_time += $lost_time_jour;
                            if($taux > 0){
                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
                            }else{
                                $sommePerduAuth = 0;
                            }
                        }
                    }if($type == "4" || $type == 4){
                        if($_arr == 0 || $_dep == 0){
                            $inc_auth++;
                            $lost_time_jour += ((int)($this->convertHourInMinutes($heureFinNormal)) - (int)($this->convertHourInMinutes($heureDebutNormal)))/60;
                            $lost_time += $lost_time_jour;
                            if($taux > 0){
                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
                            }else{
                                $sommePerduAuth = 0;
                            }
                        }
                    }elseif ($type == "2" || $type == 2){
                        // in this case the lost time is his quota because of his terminals
                        //$lost_time += (int)($his["quota"]);
                        if($_arr == 0 || $_dep == 0){
                            $inc_auth++;
                            $lost_time_jour += ((int)($his["quota"]))/60;
                            $lost_time += $lost_time_jour;
                            if($taux > 0){
                                $sommePerduAuth += ((($salaire*12)/52)/$taux)*$lost_time_jour;
                            }else{
                                $sommePerduAuth = 0;
                            }

                        }
                    }
                }
                */

                // SI le type est exclusivement 2,On calcul les quotas horraires
                if($type == "2"){
                    // Après tous on recupère ses quotas en appelant la fonction historique

                    $history = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('d-m-Y',$nowTime),$employe->getId(),$request);
                    if(($history != null) && ($history != "")){
                        $history = json_decode($history->getContent(),true);
                        $quota_total += $history["quota"];
                        $quota_fait += $history["quota_fait"];
                    }
                }
            }else if($type == "3"){
                $j++;
                // Si son workingHour est de type 3
                $his = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('d-m-Y',$nowTime),$employe->getId(),$request);
                $his = json_decode($his->getContent(),true);
                if(!$cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                    $permDate = date('Y-m-d',$nowTime);
                    if(!$pR->enPermission($employe,$permDate,"23:59","08:00")) {
                        if (!$nD->dayIsNull($permDate)) {
                            $nowDate = date('d/m/Y',$nowTime);
                            $absences++;
                            $sommePerduAbsence += (($salaire*12)/52)/$jour_travail;
                            /*$timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                            $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                            $timePerdusAbsences = ($timeFin - $timeDebut);
                            $tempPerdu = $timePerdusAbsences/60;
                            //$tempsPerdusAbsences = $tempPerdu;*/
                            //$sommeAbsences +=$tempsPerdusAbsences;


                            $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission($employe->getId(),date('Y-m-d',$nowTime),$empWH[$theDay][0]["endHour"],$empWH[$theDay][0]["beginHour"]);
                            if($p){
                                /*
                                 * We need some other variables to avoid conflicts with userStats variables
                                 */
                                $timePP = ($timeFin - $timeDebut);
                                $tempPP = $timePP/60;
                                $tempsTPP += $tempPP;
                                $tabAbsencesPermission[]= array("date"=>$nowDate,"heureDepart"=>null,"tempsTotal"=>$tempsTPP,"type"=>"Absence","tempsPerdu"=>$tempPP);
                            }
                        }

                    }
                }
            }else if($type == null || $type == "null"){
                $his = $this->findHistoriqueAction($employe->getDepartement()->getId(),date('d-m-Y',$nowTime),$employe->getId(),$request);
                $his = json_decode($his->getContent(),true);
            }

            $historiques[] = $his;
            $donneesPermission = array("retardStats"=>$tabRetardsPermission,"retardPauseStats"=>$tabRetardsPausePermission,"pauseStats"=>$tabDepartsPausePermission,"finStats"=> $tabDepartsPermission,"absenceStats"=>$tabAbsencesPermission);
            $donnees = array("nbreAbsences"=>$absences,"absences"=>$absences,"retards"=>$retards,"departs"=>$departs,"tpa"=>$sommeAbsences,"tpr"=>$tempsPerdusRetards,"tpd"=>$tempsPerdusDeparts,"type"=>$type,"retardStats"=>$tabRetards,"retardPauseStats"=>$tabRetardsPause,"pauseStats"=>$tabDepartsPause,"finStats"=> $tabDeparts,"quota_total"=>$quota_total,"quota_fait"=>$quota_fait,"tabType"=>$tabType,"permissionData"=>$donneesPermission,"lost_time"=>$lost_time,"inc_auth"=>$inc_auth,"historique"=>$historiques,"sommePerduQuota"=>$sommePerduQuota
            ,"quota_1_4"=>$quota_emp_1_4,"spd"=>$sommePerduDepart,"spr"=>$sommePerduRetard,"spa"=>$sommePerduAbsence,"nbreJourTravail"=>$j,"spAuth"=>$sommePerduAuth,"nbreBonus"=>$bonus_retards,"sommeBonus"=>$bonusSommeRetards,"tempsBonus"=>$bonusTempsGagneRetards,"sommeArgentBonus"=>$bonusSommeGagneRetard,"nbrePermission"=>$nbrePermission,"jourFeries"=>$jourFeries);
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/viewDepStat.html.twig',array(
                'listDep'=>$dep
            ));
        }else{
            return $this->redirectToRoute("login");
        }
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

            // Pour le calcul d'un depart prématuré de pause,Calculons l'intervalle
            $heureDebutNormal = $empWH[$theDay][0]["beginHour"];
            $heureFinNormal = $empWH[$theDay][0]["endHour"];
            $heureDebutNormalPause = $empWH[$theDay][0]["pauseBeginHour"];
            $heureFinNormalPause = $empWH[$theDay][0]["pauseEndHour"];

            $beginHourExploded = explode(":",$heureDebutNormal);
            $endHourExploded = explode(":",$heureFinNormal);
            $pauseBeginHourExploded = explode(":",$heureDebutNormalPause);
            $pauseEndHourExploded = explode(":",$heureFinNormalPause);

            if(sizeof($beginHourExploded)>1 && sizeof($endHourExploded)){
                $beginHourInMinutes = (((int)$beginHourExploded[0])*60)+((int)$beginHourExploded[1]);
                $endHourInMinutes = (((int)$endHourExploded[0])*60)+((int)$endHourExploded[1]);
            }else{
                $beginHourInMinutes = 0;
                $endHourInMinutes = 0;
            }
            $heureNormaleArrive = $beginHourInMinutes*60;
            $heureNormaleDepart = $endHourInMinutes*60;

            if(sizeof($pauseBeginHourExploded)>1){
                $pauseBeginHourInMinutes = (((int)$pauseBeginHourExploded[0])*60)+((int)$pauseBeginHourExploded[1]);
                $pauseEndHourInMinutes = (((int)$pauseEndHourExploded[0])*60)+((int)$pauseEndHourExploded[1]);

                $interval_pause = (($pauseEndHourInMinutes - $pauseBeginHourInMinutes)/2)*60;
                $heureNormaleArrivePause = $pauseEndHourInMinutes*60;
                $heureNormaleDepartPause = $pauseBeginHourInMinutes*60;
            }else{
                $interval_pause = 0;
                $heureNormaleArrivePause = 0;
                $heureNormaleDepartPause = 0;
            }

            if ($type == "1" || $type == "2" || $type == "4") {
                // Si son workingHour est de type 1
                if (!$cr->present($employe, $nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)) {
                    $absences++;
                    $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                    $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                    $timePerdusAbsences = ($timeFin - $timeDebut);
                    $tempPerdu = $timePerdusAbsences / 60;
                    $tempsPerdusAbsences += $tempPerdu;
                    $sommeAbsences += $tempsPerdusAbsences;
                }
                $retardDiff = $cr->retard($employe, $nowTime, $interval, $heureNormaleArrive, $empWH[$theDay][0]["beginHour"]);
                if ($retardDiff != null) {
                    $nowDate = date('d/m/Y', $nowTime);
                    $retards++;
                    $sommeRetards += $retardDiff[0];
                    $tempsPerdusRetards += $retardDiff[0] / (60);
                    $perte_temps = (int)($retardDiff[0] / (60));
                    $ct = date('H:i', $retardDiff[1]);
                    $tabRetards[] = array("date" => $nowDate, "heureRetard" => $ct, "temps" => $perte_temps);
                }
                $retardPauseDiff = $cr->retardPause($employe, $nowTime, $interval_pause, $heureNormaleArrivePause, $empWH[$theDay][0]["pauseEndHour"]);
                if ($retardPauseDiff != null) {
                    $nowDate = date('d/m/Y', $nowTime);
                    $retards++;
                    $sommeRetards += $retardPauseDiff[0];
                    $tempsPerdusRetardsPause = $retardPauseDiff[0] / (60);
                    $tempsPerdusRetards += $retardPauseDiff[0] / (60);
                    $ct = date('H:i', $retardPauseDiff[1]);
                    $tabRetardsPause[] = array("date" => $nowDate, "heureRetard" => $ct, "temps" => $tempsPerdusRetardsPause);
                }
                $departDiff = $cr->departPremature($employe, $nowTime, $interval, $heureNormaleDepart);
                if ($departDiff != null) {
                    $nowDate = date('d/m/Y', $nowTime);
                    $departs++;

                    $sommeDeparts += $departDiff[0];
                    $tempsPerdusDepartsFin = ($departDiff[0]) / (60);
                    // Pour prendre en compte les departs de 17h
                    $tempsPerdusDeparts += $tempsPerdusDepartsFin;
                    $ct = date('H:i', $departDiff[1]);
                    $tabDeparts[] = array("date" => $nowDate, "heureDepart" => $ct, "temps" => $tempsPerdusDepartsFin,"temps_min"=>$tempsPerdusDepartsFin*60);
                }
                $departPauseDiff = $cr->departPausePremature($employe, $nowTime, $interval_pause, $heureNormaleDepartPause);
                if ($departPauseDiff[0] != null) {
                    $i++;
                    $nowDate = date('d/m/Y', $nowTime);
                    $departsPause++;
                    // Pour prendre en compte les departs de 12 h aussi
                    $departs++;
                    $sommeDepartsPause += $departPauseDiff[0];
                    $tempsPerdusDepartsPause = ($departPauseDiff[0]) / (60);
                    // Pour prendre en compte les departs de 12h aussi
                    $tempsPerdusDeparts += $tempsPerdusDepartsPause;
                    $ct = date('H:i', $departPauseDiff[1]);
                    $tabDepartsPause[] = array("date" => $nowDate, "heureDepart" => $ct, "temps" => $tempsPerdusDepartsPause);
                }
            }else if($type == 3){
                // Si son workingHour est de type 3
                if(!$cr->present($employe,$nowTime,$nowTime+$heureNormaleArrive-$interval,$nowTime+$heureNormaleArrive+$interval,$nowTime+$heureNormaleDepartPause-$interval_pause,$nowTime+$heureNormaleDepartPause+$interval_pause,$nowTime+$heureNormaleArrivePause-$interval_pause,$nowTime+$heureNormaleArrivePause+$interval_pause,$nowTime+$heureNormaleDepart-$interval,$nowTime+$heureNormaleDepart+$interval)){
                    $absences++;
                    $timeDebut = strtotime($empWH[$theDay][0]["beginHour"]);
                    $timeFin = strtotime($empWH[$theDay][0]["endHour"]);
                    $timePerdusAbsences = ($timeFin - $timeDebut);
                    $tempsPerdusAbsences = $timePerdusAbsences/60;
                    $sommeAbsences +=$tempsPerdusAbsences;
                }
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
            $perteRetardTemps = 0;
            $perteDepartTemps = 0;
            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeByDep($dep);
            // On parcours aussi tous les employés pour additionner leur stats
            foreach ($emp as $e){

                $empSalary = $e->getSalary();
                $salaireEnMinuite = $empSalary/(30*24*60); // 30 Jours,24 heures, 60 minuites

                $stats = $this->userStatsAction($request,$e->getId(),$dateFrom,$dateTo);

                $stats = json_decode($stats->getContent(),true);

                // Cette variable doit contenir les stats de l'employé courant
                //$stats = $this->_userStatsAction($e, $dateFrom, $dateTo, $interval);

                $sommePerdueRetard = $stats["spr"];
                $sommePerdueDepart = $stats["spd"];

                $perteRetardTemps += $stats ["tpr"];
                $perteDepartTemps += $stats ["tpd"];

                $sommeTotaleRetard += $sommePerdueRetard;
                $sommeTotaleDepart += $sommePerdueDepart;
            }
            $depName = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->find("$dep")->getName();
            $tabStats[]= array("departementId"=>$dep,"departement"=>$depName,"tpr"=>$perteRetardTemps,"tpd"=>$perteDepartTemps,"spr"=>$sommeTotaleRetard,"spd"=>$sommeTotaleDepart);
        }
        return new JsonResponse($tabStats);
    }
}