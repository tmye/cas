<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClockinRecord;
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


class ClockinReccordController extends Controller
{

    static $min_laps = 180;

    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {
        /*$date = strtotime("14 February 2018");
        echo "La date simple : ".$date."<br>";
        $heure = (60*60*12);
        echo "L'heure : ".$heure."<br>";
        echo "Type d'heure : ".gettype($heure)."<br>";
        echo "Type de la date : ".gettype($date)."<br>";
        echo "La date & l'heure : ".($date+$heure)."<br><br>";
        echo "La date & l'heure - l'intervalle : ".($date+$heure-1800)."<br><br>";
        echo "<br>Test<br>";*/

        /*$tab = array();
        $tab[] = array("date"=>"la_date","temps"=>"le_temps");
        $tab[] = array("date2"=>"la_date2","temps2"=>"le_temps2");
        print_r($tab);
        echo "<br>";
        echo $tab[0]["date"];
        echo "<br>";
        echo $this->dateDayNameFrench(date('N',strtotime("21 March 2018 17:15:00")))."<br>";
        */
        //return new Response(strtotime("17 March 2018 17:29:00"));
        echo date('d-m-Y H:i:s',1522423928)."<br>";
        echo date('d-m-Y H:i:s',1522428015)."<br>";
        echo date('d-m-Y H:i:s',1522389900)."<br>";
        $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find(26);
        $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);
        return new Response(strtotime("30 March 2018 08:05:00"));
    }


    /**
     * @Route("/randomClockinRecord", name="randomClockinRecord")
     */
    public function randomClockinRecordAction(Request $request)
    {

        for($cpt = 0;$cpt<100;$cpt++){

            $rand_employe_id = random_int(1,2);
            $rand_departement_id = random_int(1,2);
            $rand_employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($rand_employe_id);
            $rand_departement = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->find($rand_departement_id);
            $rand_device = random_int(1,15);
            $rand_timestamp = random_int(-432000,432000);

            $a = strtotime("14 February 2018");
            $b = $a-$rand_timestamp;

            print_r($b);
            echo "<br>";

            $cr = new ClockinRecord();
            $cr->setEmploye($rand_employe);
            $cr->setDepartement($rand_departement);
            $cr->setDeviceId($rand_device);
            $cr->setClockinTime($b);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cr);
            $em->flush();
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
<<<<<<< HEAD
    public function arrive(ClockinRecord $cR,$day,$request){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["beginHour"];
        $heureFinNormal = $empWH[$day][0]["endHour"];
=======
    public function arrive(ClockinRecord $cR,$request){

         // 30min
        $heureDebutNormal = "6:30:00";
        $heureFinNormal = "17:30:00";
>>>>>>> f8b2e072d889d77b7c1118ddd1c2847bdb2cb91a
        $dep = $request->request->get('id');
        $_date = $request->request->get('date');

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hIInfA = $hSenceA- (ClockinReccordController::$min_laps * 60);
        $hIInfD = $hSenceD- (ClockinReccordController::$min_laps * 60);

        $dIInfA = $dSenceA- (ClockinReccordController::$min_laps * 60);
        $dIInfD = $dSenceD- (ClockinReccordController::$min_laps * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hISupA = $hSenceA+ (ClockinReccordController::$min_laps * 60);
        $dISupA = $dSenceA+ (ClockinReccordController::$min_laps * 60);

        $hISupD = $hSenceD+ (ClockinReccordController::$min_laps * 60);
        $dISupD = $dSenceD+ (ClockinReccordController::$min_laps * 60);

        //echo "<br>test : ".strtotime("14 February 2018 6:25:00");

        if($dIInfA <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupA){
            return true;
        }else{
            return false;
        }
    }

<<<<<<< HEAD
    public function pause(ClockinRecord $cR,$day,$request){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["pauseBeginHour"];
        $heureFinNormal = $empWH[$day][0]["pauseEndHour"];
=======
    public function pause(ClockinRecord $cR,$request){

         // 30min
        $heureDebutNormal = "12:00:00";
        $heureFinNormal = "14:00:00";
>>>>>>> f8b2e072d889d77b7c1118ddd1c2847bdb2cb91a
        $dep = $request->request->get('id');
        $_date = $request->request->get('date');

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hIInfA = $hSenceA- (ClockinReccordController::$min_laps * 60);
        $hIInfD = $hSenceD- (ClockinReccordController::$min_laps * 60);

        $dIInfA = $dSenceA- (ClockinReccordController::$min_laps * 60);
        $dIInfD = $dSenceD- (ClockinReccordController::$min_laps * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hISupA = $hSenceA+ (ClockinReccordController::$min_laps * 60);
        $dISupA = $dSenceA+ (ClockinReccordController::$min_laps * 60);

        $hISupD = $hSenceD+ (ClockinReccordController::$min_laps * 60);
        $dISupD = $dSenceD+ (ClockinReccordController::$min_laps * 60);

        if($dIInfA <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupA){
            return true;
        }else{
            return false;
        }
    }
<<<<<<< HEAD
    public function finPause(ClockinRecord $cR,$day,$request){
        $empWH = json_decode($cR->getEmploye()->getWorkingHour()->getWorkingHour(),true);
        $heureDebutNormal = $empWH[$day][0]["pauseBeginHour"];
        $heureFinNormal = $empWH[$day][0]["pauseEndHour"];
=======
    public function finPause(ClockinRecord $cR,$request){

         // 30min
        $heureDebutNormal = "12:00:00";
        $heureFinNormal = "14:00:00";
>>>>>>> f8b2e072d889d77b7c1118ddd1c2847bdb2cb91a
        $dep = $request->request->get('id');
        $_date = $request->request->get('date');

        $dd = strtotime($_date." ".$heureDebutNormal);
        $df = strtotime($_date." ".$heureFinNormal);

        // L'heure à laquelle l'employé est sensé arriver
        $hSenceA = strtotime(date("H:i",strtotime($dd)));
        $hSenceD = strtotime(date("H:i",strtotime($df)));
        // Timestamp de la dateheure à laquelle l'employé est sensé arriver
        $dSenceA = $dd;
        $dSenceD = $df;
        // Borne inférieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hIInfA = $hSenceA- (ClockinReccordController::$min_laps * 60);
        $hIInfD = $hSenceD- (ClockinReccordController::$min_laps * 60);

        $dIInfA = $dSenceA- (ClockinReccordController::$min_laps * 60);
        $dIInfD = $dSenceD- (ClockinReccordController::$min_laps * 60);
        // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
        $hISupA = $hSenceA+ (ClockinReccordController::$min_laps * 60);
        $dISupA = $dSenceA+ (ClockinReccordController::$min_laps * 60);

        $hISupD = $hSenceD+ (ClockinReccordController::$min_laps * 60);
        $dISupD = $dSenceD+ (ClockinReccordController::$min_laps * 60);

        if($dIInfD <= $cR->getClockinTime() && $cR->getClockinTime() <= $dISupD){
            return true;
        }else{
            return false;
        }
    }

    /* Fonction qui permet de créer des entrées dans le nouveau tableau */
<<<<<<< HEAD
    public function createEntry(Request $request,$day,$recordTab,ClockinRecord $c){
        if($this->arrive($c,$day,$request)){
=======
    public function createEntry(Request $request,$recordTab,ClockinRecord $c){

         // 30min
        if($this->arrive($c,$request)){
>>>>>>> f8b2e072d889d77b7c1118ddd1c2847bdb2cb91a
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

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>$arrive,"depart"=>0,"pause"=>0,"finPause"=>0);
        }else{
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

            $recordTab[$c->getEmploye()->getId()] = array("id"=>$c->getEmploye()->getId(),"nom"=>$nom,"prenom"=>$prenom,"function"=>$function,"type"=>$type,"quota"=>$quota,"bH"=>$bH,"pBH"=>$pBH,"pEH"=>$pEH,"eH"=>$eH,"arrive"=>0,"depart"=>$depart,"pause"=>0,"finPause"=>0);
        }
        return $recordTab;
    }

    /* Fonction qui permet de tester si un clockinTime est plus récent */
    public function plusRecent($recordTab,ClockinRecord $c){

         // 30min
        if($c->getClockinTime() < $recordTab[$c->getEmploye()->getId()]["arrive"] ){
            return true;
        }else{
            return false;
        }
    }
    public function plusAncien($recTab,ClockinRecord $element){
        if($element->getClockinTime() > $recTab[$element->getEmploye()->getId()]["depart"] ){
            return true;
        }else{
            return false;
        }
    }

    public function miseAJour($recordTab,ClockinRecord $c,$day,$request){
        if($this->arrive($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["arrive"] =date('H:i',$c->getClockinTime());
        }elseif($this->pause($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["pause"] =date('H:i',$c->getClockinTime());
        }elseif($this->finPause($c,$day,$request)){
            $recordTab[$c->getEmploye()->getId()]["finPause"] =date('H:i',$c->getClockinTime());
        }else{
            $recordTab[$c->getEmploye()->getId()]["depart"] =date('H:i',$c->getClockinTime());
        }
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
                /*
                 * On vérifie si ce clockinTime est plus récent
                 * Si c'est le cas on met à jour les données
                 * Sinon on zappe
                */
                if($this->plusRecent($record,$element)){
                    $record = $this->miseAJour($record,$element,$day,$request);
                }
            }else{
                /*
                 * On vérifie si ce clockinTime est plus ancien
                 * Si c'est le cas on met à jour les données
                 * Sinon on zappe
                */
                if($this->plusAncien($record,$element)){
                    $record = $this->miseAJour($record,$element,$day,$request);
                }
            }
        }
        return $record;
    }

    /**
     * @Route("/findHistorique", name="findHistorique")
     */
    public function findHistoriqueAction(Request $request)
    {
<<<<<<< HEAD
=======

         // 30min

        $heureDebutNormal = "6:30:00";
        $heureDebutPauseNormal = "12:00:00";
        $heureFinNormal = "17:30:00";
        $heureFinPauseNormal = "14:00:00";
>>>>>>> f8b2e072d889d77b7c1118ddd1c2847bdb2cb91a
        $dep = $request->request->get('id');
        $_date = $request->request->get('date');
        $day = date('N',strtotime($_date));
        $day = $this->dateDayNameFrench(intval($day));


        $dataTable = array();
        $don = array();

        $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeByDep($dep);


        if(sizeof($emp)>0){
            foreach ($emp as $e){
                $empTab[]=$e->getId();
                $empWH = json_decode($e->getWorkingHour()->getWorkingHour(),true);

                $heureDebutNormal = $empWH[$day][0]["beginHour"];
                $heureDebutPauseNormal = $empWH[$day][0]["pauseBeginHour"];
                $heureFinNormal = $empWH[$day][0]["endHour"];
                $heureFinPauseNormal = $empWH[$day][0]["pauseEndHour"];

                //echo "\nBeginHour : ".$heureDebutNormal."\n";

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


                $dIInfA = $dSenceA-1800;
                $dIInfPD = $dSencePD-1800;
                $dIInfD = $dSenceD-1800;
                $dIInfPF = $dSencePF-1800;
                // Borne superieur de l'intervalle d'heure à laquelle l'employé est sensé se présenter
                $dISupA = $dSenceA+1800;
                $dISupPD = $dSencePD+1800;
                $dISupPF = $dSencePF+1800;

                $hISupD = $hSenceD+1800;
                $dISupD = $dSenceD+1800;

                // On récupère les données appartenant au département sélectionné

                $tempData = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->empHistory($e->getId(),$dep,$dIInfA,$dISupA,$dIInfPD,$dISupPD,$dIInfPF,$dISupPF,$dIInfD,$dISupD);

                //Maintenant il faut éliminer les doublons
                $don[] = $this->elimineDoublon($tempData,$day,$request);
            }

            /*
            $d = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->findAll();
            echo "\n Avant l'elimination de doublons :";
            foreach ($d as $res){
                echo "\n el :".$res->getId();
            }

            echo "\n Après l'elimination de doublons :";
            $after = $this->elimineDoublon($d,$day,$request);
            foreach ($after as $result){
                print_r($result);
            }


            foreach ($don as $data){
                echo "\n Passage de niveau 1 :";
                print_r($data);
            }
            */

            $tabLength = sizeof($don);



            // Toujours des echo de débogage
            //print('Au total après le filtre de date : '.$j.'<br>');
            //print('Au total dans le département: '.$i);

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize(['clockinRecord' => $don],'json');

            $content = array("content"=>$jsonContent,"emp"=>$empTab);

            return new JsonResponse($content);
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
            print_r($empTab);
            print_r($empClockinRecordTab);
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
