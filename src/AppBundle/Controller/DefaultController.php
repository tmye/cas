<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\CompanyInfos;
use AppBundle\fpdf181\fpdf;
use AppBundle\Entity\CompanyConfig;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Expiration;
use AppBundle\Entity\WorkingHours;
use AppBundle\fpdf181\tablepdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TmyeDeviceBundle\Entity\DevicePubPic;
use TmyeDeviceBundle\Entity\UpdateEntity;

class DefaultController extends StatsController
{
    public function formatInt($value){
        $value = (string)$value;
        $value_lenght = strlen($value);
        if ($value_lenght >= 4) {
            $number_of_points = $value_lenght % 3;
            $value_tab = str_split($value);
            $str = "";
            $cpt=0;
            for ($i=$value_lenght-1;$i>=0;$i--){
                if($cpt==3){
                    $cpt=0;
                    $str = $value_tab[$i].".".$str;
                }else{
                    $str = $value_tab[$i]."".$str;
                }
                $cpt++;
            }
        }
        return $str;
    }

    /**
     * @Route("/functionTest", name="functionTest")
     */
    public function functionTestAction(Request $request)
    {
        /*$date = "2018-06-04";
        $result = $this->getDoctrine()->getManager()->getRepository("AppBundle:NullDate")->dayIsNull($date);
        print_r($result);
        return $result;
        /*$heureNormaleArrivee = "08:00";
        $heureEnregistre = "23:59";
        $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission(36,$date,$heureEnregistre,$heureNormaleArrivee);
        print_r($p);
        foreach ($p as $perm){
            echo "<br>".$perm->getDescription()."<br>";
        }*/

        //require('fpdf181/fpdf.php');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Hello World !');

        $pdf2 = new FPDF();
        $pdf2->AddPage();
        $pdf2->SetFont('Arial','B',16);
        $pdf2->Cell(40,10,'Bonjour le monde !');

        $pdf->Output();

        //return new Response(date("Y-m-d H:i:s",1537163970));
        //return new Response(date("Y-m-d H:i:s",(new \DateTime())->getTimestamp()));
        //return new Response(strtotime("2018-07-05 08:30"));
        //return new Response($this->formatInt(12253008000000));
        //return $this->render("cas/errorPage.html.twig");
    }

    /**
     * @Route("/firstTimeInitialization", name="firstTimeInitialization")
     */
    public function firstTimeInitializationAction(Request $request)
    {
        return $this->render("cas/firstTime.html.twig");
    }

    /**
     * @Route("/initializeApplication", name="initializeApplication")
     */
    public function initializeApplicationAction(Request $request)
    {
        $admins = $this->getDoctrine()->getManager()->getRepository("AppBundle:Admin");

        // Searching for already used username
        $i = 0;
        $found = false;
        if(sizeof($admins) > 1){
            while($i < sizeof($admins) && $found == false){
                if($admins[$i]->getUsername() == $request->request->get("adminUsername")){
                    $found = true;
                }
                $i++;
            }
        }

        if($found == false){
            $cc = new CompanyConfig();
            $em = $this->getDoctrine()->getManager();
            if(isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])){
                $resultat = move_uploaded_file($_FILES['image']['tmp_name'],"company_images/".basename($_FILES["image"]["name"]));
                $cc->setCompanyName($request->request->get("compName"));
                $cc->setCompanyLogo($_FILES["image"]["name"]);
                $em->persist($cc);
                $em->flush();

                $session = new Session();
                $session->set("companyLogo",$_FILES['image']['name']);
                $session->set("companyName",$request->request->get("name"));
                // We are done with the companyConfiguration.Now we must persist an Admin
                $e = new Admin();
                $e->setName($request->request->get("adminName"));
                $e->setSurname($request->request->get("adminSurname"));
                $e->setUsername($request->request->get("adminUsername"));
                $e->setPassword(md5($request->request->get("adminPassword")));
                $e->setRoles(array("ROLE_SUPER_ADMIN"));
                $e->setAddress($request->request->get("adminAdress"));
                $e->setPhonenumber($request->request->get("adminPhoneNumber"));

                // We continue with the rest of the admin (Employee) properties

                $em->persist($e);
                $em->flush();

                // After persistance operation, we must edit initialization file

                $file = fopen($this->getParameter("web_dir")."/first_time",'r+');
                fseek($file,0);
                fputs($file,sha1("initialized"));
                fclose($file);

                // Now that all operations are achieved, we can return a response

                return new Response(1);
            }else{
                return new Response("Erreur avec la soumission du logo");
            }
        }else{
            return $this->redirectToRoute("firstTimeInitialization");
        }
    }

    /**
     * @Route("/changeSocietyName", name="changeSocietyName")
     */
    public function changeSocietyNameAction(Request $request,$name = null)
    {
        $name = $request->request->get("name");
        $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
        $em = $this->getDoctrine()->getManager();
        if($cc != null){
            $cc = $cc[0];
            $cc->setCompanyName($name);
            $em->flush();

            $session = new Session();
            $session->set("companyName",$name);
        }else{
            $newCC = new CompanyConfig();
            $newCC->setCompanyName($name);
            $em->persist($newCC);
            $em->flush();

            $session = new Session();
            $session->set("companyName",$name);
        }
        return new Response("OK");
    }

    /**
     * @Route("/expiryPage", name="expiryPage")
     */
    public function expiryPageAction(Request $request)
    {
        return $this->render("cas/expiryPage.html.twig");
    }

    /**
     * @Route("/expiryDate", name="expiryDate")
     */
    public function expiryDateAction(Request $request)
    {
        $date = $request->request->get("date");
        $ex = $this->getDoctrine()->getManager()->getRepository("AppBundle:Expiration")->findAll();
        $em = $this->getDoctrine()->getManager();
        if($ex != null){
            $ex = $ex[0];
            $ex->setExpiryDate($date);
            $em->flush();

            $session = new Session();
            $session->set("expiryDate",$date);
        }else{
            $newEX = new Expiration;
            $newEX->setExpiryDate($date);
            $em->persist($newEX);
            $em->flush();

            $session = new Session();
            $session->set("expiryDate",$date);
        }
        return new Response("OK");
    }

    /**
     * @Route("/changeSocietyLogo", name="changeSocietyLogo")
     */
    public function changeSocietyLogoAction(Request $request,$name = null)
    {
        $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
        $em = $this->getDoctrine()->getManager();
        if($cc != null){
            if(isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])){
                $cc = $cc[0];
                $lastCompanyLogo = $cc->getCompanyLogo();
                if($lastCompanyLogo != null && !empty($lastCompanyLogo) && file_exists("company_images/".$lastCompanyLogo)){
                    unlink("company_images/".$lastCompanyLogo);
                }
                $cc->setCompanyLogo($_FILES["image"]["name"]);
                $em->flush();

                $resultat = move_uploaded_file($_FILES['image']['tmp_name'],"company_images/".basename($_FILES["image"]["name"]));
                $em->flush();
                $session = new Session();
                $session->set("companyLogo",$_FILES['image']['name']);
            }else{
                return new Response("Erreur");
            }
        }else{
            return new Response("Spécifiez d'abord un nom de société");
        }
        return new Response("OK");
    }

    /**
     * @Route("/uploadCoverAll", name="uploadCoverAll")
     */
    public function uploadCoverAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findAll();

        if ($devices != null){
            foreach ($devices as $dev){
                if(isset($_FILES["first_image_input_1"]["name"]) && !empty($_FILES["first_image_input_1"]["name"])){
                    $lastImage = $dev->getImage1();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage1($_FILES["first_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])){
                    $lastImage = $dev->getImage2();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage2($_FILES["second_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_1"]["name"]));
                }
                if(isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])){
                    $lastImage = $dev->getImage3();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage3($_FILES["third_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_1"]["name"]));
                }
                $em->flush();
            }
        }else{
            foreach ($machines as $mac){
                $dev = new DevicePubPic();
                $dev->setDeviceid($mac->getMachineId());
                if(isset($_FILES["first_image_input_1"]["name"]) && !empty($_FILES["first_image_input_1"]["name"])){
                    $dev->setImage1($_FILES["first_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])){
                    $dev->setImage2($_FILES["second_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_1"]["name"]));
                }
                if(isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])){
                    $dev->setImage3($_FILES["third_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_1"]["name"]));
                }
                $em->persist($dev);
                $em->flush();
            }
        }

        return new Response("Image(s) uploadée(s) xxxxxx");
    }

    /**
     * @Route("/uploadCoverDep", name="uploadCoverDep")
     */
    public function uploadCoverDepAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $tab = array();
        foreach ($machines as $mac){
            foreach ($mac->getDepartements() as $dep){
                if(in_array($dep->getId(),array($_POST["depId"]))){
                    $tab[] = $mac->getMachineId();
                }
            }
        }

        // Maintenant que j'ai la liste des machines de ce département je peux faire les traitements

        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->deviceByArray($tab);
        if ($devices != null) {
            foreach ($devices as $dev){
                if(isset($_FILES["first_image_input_2"]["name"]) && !empty($_FILES["first_image_input_2"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage1();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage1($_FILES["first_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_2"]["name"]));
                }
                if(isset($_FILES["second_image_input_2"]["name"]) && !empty($_FILES["second_image_input_2"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage2();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage2($_FILES["second_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_2"]["name"]));
                }
                if(isset($_FILES["third_image_input_2"]["name"]) && !empty($_FILES["third_image_input_2"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage3();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage3($_FILES["third_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_2"]["name"]));
                }
                $em->flush();
            }
        }else{
            foreach ($machines as $mac){
                $dev = new DevicePubPic();
                $dev->setDeviceid($mac->getMachineId());
                if(isset($_FILES["first_image_input_2"]["name"]) && !empty($_FILES["first_image_input_2"]["name"])){
                    $dev->setImage1($_FILES["first_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_2"]["name"]));
                }
                if(isset($_FILES["second_image_input_2"]["name"]) && !empty($_FILES["second_image_input_2"]["name"])){
                    $dev->setImage2($_FILES["second_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_2"]["name"]));
                }
                if(isset($_FILES["third_image_input_2"]["name"]) && !empty($_FILES["third_image_input_2"]["name"])){
                    $dev->setImage3($_FILES["third_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_2"]["name"]));
                }
                $em->persist($dev);
                $em->flush();
            }
        }

        return new Response("Image(s) uploadée(s)");
    }

    /**
     * @Route("/uploadCoverMac", name="uploadCoverMac")
     */
    public function uploadCoverMacAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mac = $request->request->get("macId");
        echo "\n Mac Id : ".$mac."\n";
        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findBy(array("deviceid"=>$_POST["macId"]));

        if ($devices != null) {
            foreach ($devices as $dev){
                if(isset($_FILES["first_image_input_3"]["name"]) && !empty($_FILES["first_image_input_3"]["name"])){
                    $lastImage = $dev->getImage1();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage1($_FILES["first_image_input_3"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_3"]["name"]));
                }
                if(isset($_FILES["second_image_input_3"]["name"]) && !empty($_FILES["second_image_input_3"]["name"])){
                    $lastImage = $dev->getImage2();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage2($_FILES["second_image_input_3"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_3"]["name"]));
                }
                if(isset($_FILES["third_image_input_3"]["name"]) && !empty($_FILES["third_image_input_3"]["name"])){
                    $lastImage = $dev->getImage3();
                    if($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg"){
                        // Verify if the file exists because we are in a loop
                        if(file_exists("pub_covers/".$lastImage)){
                            unlink("pub_covers/".$lastImage);
                        }
                    }
                    $dev->setImage3($_FILES["third_image_input_3"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_3"]["name"]));
                }
                $em->flush();
            }
        }else{
            $dev = new DevicePubPic();
            $dev->setDeviceid($mac);
            if(isset($_FILES["first_image_input_3"]["name"]) && !empty($_FILES["first_image_input_3"]["name"])){
                $dev->setImage1($_FILES["first_image_input_3"]["name"]);
                $resultat = move_uploaded_file($_FILES['first_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_3"]["name"]));
            }
            if(isset($_FILES["second_image_input_3"]["name"]) && !empty($_FILES["second_image_input_3"]["name"])){
                $dev->setImage2($_FILES["second_image_input_3"]["name"]);
                $resultat = move_uploaded_file($_FILES['second_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_3"]["name"]));
            }
            if(isset($_FILES["third_image_input_3"]["name"]) && !empty($_FILES["third_image_input_3"]["name"])){
                $dev->setImage3($_FILES["third_image_input_3"]["name"]);
                $resultat = move_uploaded_file($_FILES['third_image_input_3']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_3"]["name"]));
            }
            $em->persist($dev);
            $em->flush();
        }

        return new Response("Image(s) uploadée(s)");
    }

    /**
     * @Route("/returnCoverMac", name="returnCoverMac")
     */
    public function returnCoverMacAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->request->get("code");
        $device = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findOneBy(array("deviceid"=>$code));

        $t = array();
        if(!empty($device)){
            $t[] = $device->getImage1();
            $t[] = $device->getImage2();
            $t[] = $device->getImage3();
            return new Response(json_encode($t));
        }else{
            return new Response("0");
        }
        //return new Response(json_encode($t));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            $expiry_service->hasExpired();
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $session = new Session();
            if($session->get('companyName') == null || $session->get('companyLogo') == null){
                return $this->redirectToRoute("manageSocietyName",array('badConfig'=>1));
            }
            $ci = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyInfos")->findAll();
            if(($ci != null) && (!empty($ci))){
                $ci = $ci[0];
            }else{
                $ci = null;
            }
            return $this->render('cas/index.html.twig', array(
                'ci'=>$ci,
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/historique",name="historique")
     */
    public function historiqueAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/historique.html.twig',array('listDep'=>$listDep));
        }else{
            return $this->redirectToRoute("login");
        }
    }


    /*
     * Routes concernant les admins et le super admin
     */

    /**
     * @Route("/admin/page",name="admin")
     */
    public function adminAction(Request $request)
    {
        return new Response("Page des admins");
    }

    /**
     * @Route("/SupAdmin/page",name="SupAdmin")
     */
    public function SupAdminAction(Request $request)
    {
        return new Response("Page du super admin");
    }

    /**
     * @Route("/synchroniser",name="synchroniser")
     */
    public function synchroniserAction(Request $request)
    {
        return $this->render('cas/synchroniser.html.twig');
    }

    /**
     * @Route("/switch",name="switch")
     */
    public function switchAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            return $this->render('cas/switch.html.twig',array(
                'departements'=>$departements,
                'machines'=>$machines
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manage",name="manage")
     */
    public function manageAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            return $this->render('cas/manage.html.twig');
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageEmpProfilePicture",name="manageEmpProfilePicture")
     */
    public function manageEmpProfilePictureAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmpProfilePicture.html.twig',array(
                "departements"=>$departements
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageEmpFingerprint",name="manageEmpFingerprint")
     */
    public function manageEmpFingerprintAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmpFingerprint.html.twig',array(
                "departements"=>$departements
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageEmployee",name="manageEmployee")
     */
    public function manageEmployeeAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmployee.html.twig',array(
                "departements"=>$departements
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageDeleteData",name="manageDeleteData")
     */
    public function manageDeleteDataAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/delete.html.twig',array(
                "departements"=>$departements,
                "machines"=>$machines
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageDepartement",name="manageDepartement")
     */
    public function manageDepartementAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageDepartement.html.twig',array(
                "departements"=>$departements,
                "machines"=>$machines
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageSocietyName",name="manageSocietyName")
     */
    public function manageSocietyNameAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            // Token Sent
            $token = $request->query->get("badConfig");
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            return $this->render('cas/manageSocietyName.html.twig',array("token"=>$token));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/generatePDF",name="generatePDF")
     */
    public function generatePDFAction(Request $request)
    {
        $session = new Session();

        $empId = $request->request->get('destination');
        $fromDate = $request->request->get('fromDate');
        $toDate = $request->request->get('toDate');
        $t = $request->request->get('type');
        $pdf = new tablepdf();
        if($t != "1"){
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Image($this->getParameter("web_dir")."/company_images/".$session->get("companyLogo"),10,10,20,20);
            $pdf->Image($this->getParameter("web_dir")."/img/logo.png",180,10,12.62,19.4);
            $pdf->Ln('25');
            $pdf->Cell(500,10,$session->get("companyName"));
            $pdf->Cell(500,10,$session->get("companyName"));
            $pdf->Ln('17');
            $pdf->Cell(25,10,"");
            $pdf->SetFont('Arial','BU',16);
            $pdf->Cell(40,10,'Rapport des employes du '.$fromDate.' au '.$toDate);
            $pdf->Ln('15');
        }
        $i=0;
        foreach ($empId as $emp){
            $i++;
            if($i>=5){ // set up how many table on a page
                $pdf->AddPage();
                $i=0;
            }
            $employe = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->find($emp);
            $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(),true);
            $type = $empWH["lundi"][0]["type"];

            $empData = $this->returnOneEmployeeAction($request,$emp,$fromDate,$toDate);
            $empDataFormated = json_decode($empData->getContent(),true);

            $donnees = $this->userStatsAction($request,$emp,$fromDate,$toDate);
            $donnees = json_decode($donnees->getContent(),true);
            $finalSalary = ((int)$employe->getSalary())/30;
            $finalSalaryPerHour = $finalSalary/24;
            $finalSalaryPerMin = $finalSalaryPerHour/60;
            $name = $employe->getSurname();
            $lastName = $employe->getLastName();
            $permissions = sizeof($donnees["permissionData"]["retardStats"])+sizeof($donnees["permissionData"]["retardPauseStats"])+sizeof($donnees["permissionData"]["pauseStats"])+sizeof($donnees["permissionData"]["finStats"])+sizeof($donnees["permissionData"]["absenceStats"]);

            if($type == "2" or $type == 2){
                $quota_restant = $donnees["quota_total"]-$donnees["quota_fait"];
                if ($quota_restant > 0){
                    $qr = $quota_restant;
                }else{
                    $qr = 0;
                }
                $header = array('Nom', 'Prenom(s)', 'Absences', 'Quota Fait','Quota normal', 'Quota restant','Auth incomp');
                $data = array(
                    array($name,$lastName,$donnees["absences"],$donnees["quota_fait"],$donnees["quota_total"],$qr,$donnees["inc_auth"]),
                );
                $data2 = array(
                    array("Pertes en temps","",$donnees["quota_total"]/60,$donnees["quota_fait"],"-",$qr,$donnees["lost_time"]),
                );
                $data3 = array(
                    array("Pertes en argent (FCFA)","",($donnees["quota_total"]/60)*$finalSalaryPerHour,$donnees["quota_fait"]*$finalSalaryPerMin,"-",$qr*$finalSalaryPerMin,$donnees["lost_time"]*$finalSalaryPerMin),
                );
                $data4 = array(
                    //array("Total","",($donnees["absences"]*$finalSalary)+($qr*$finalSalaryPerMin)),
                    array("Total des pertes","",($donnees["absences"]*$finalSalary)),
                );
                $data5 = array(
                    array("Salaire","",$employe->getSalary()),
                );
                $data6 = array(
                    array("Net a payer","",$employe->getSalary()-(($donnees["absences"]*$finalSalary)+($donnees["tpr"]*$finalSalaryPerMin)+($donnees["tpd"]*$finalSalaryPerMin)+($donnees["lost_time"]*$finalSalaryPerMin))),
                );
            }else{
                $header = array('Nom', 'Prenom(s)', 'Absences', 'Permissions','Retards','Departs','Auth incomp');
                $data = array(
                    array($name,$lastName,$donnees["absences"],$permissions,$donnees["retards"],$donnees["departs"],$donnees["inc_auth"]),
                );
                $data2 = array(
                    array("Pertes en temps","",$donnees["absences"]*24,0,$donnees["tpr"]/*temp perdu retard*/,$donnees["tpd"],$donnees["lost_time"]),
                );
                $data3 = array(
                    array("Pertes en argent (FCFA)","",$donnees["absences"]*$finalSalary,0,$donnees["tpr"]*$finalSalaryPerMin,$donnees["tpd"]*$finalSalaryPerMin,$donnees["lost_time"]*$finalSalaryPerMin),
                );
                $data4 = array(
                    array("Total des pertes","",($donnees["absences"]*$finalSalary)+($donnees["tpr"]*$finalSalaryPerMin)+($donnees["tpd"]*$finalSalaryPerMin)+($donnees["lost_time"]*$finalSalaryPerMin)),
                );
                $data5 = array(
                    array("Salaire","",$employe->getSalary()),
                );
                $data6 = array(
                    array("Net a payer","",$employe->getSalary()-(($donnees["absences"]*$finalSalary)+($donnees["tpr"]*$finalSalaryPerMin)+($donnees["tpd"]*$finalSalaryPerMin)+($donnees["lost_time"]*$finalSalaryPerMin))),
                );
            }
            switch ($t){
                case "1":
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',16);
                    $pdf->Image($this->getParameter("web_dir")."/company_images/".$session->get("companyLogo"),10,10,20,20);
                    $pdf->Image($this->getParameter("web_dir")."/img/logo.png",180,10,12,12);
                    $pdf->Ln('25');
                    $pdf->Cell(500,10,$session->get("companyName"));
                    $pdf->Ln('17');
                    $pdf->Cell(25,10,"");
                    $pdf->SetFont('Arial','BU',16);
                    $pdf->Cell(40,10,'Rapport des employes du '.$fromDate.' au '.$toDate);
                    $pdf->Ln('15');
                    break;
            }
            $pdf->FancyTable($header,$data,$data2,$data3,$data4,$data5,$data6);
            $pdf->Ln('5');
        }
        $pdf->Output();

        //return new Response("OK");
    }

    /**
     * @Route("/customizeCompanyInfos",name="customizeCompanyInfos")
     */
    public function customizeCompanyInfosAction(Request $request){
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            $ci = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyInfos")->findAll();
            if(($ci != null) && (!empty($ci))){
                $ci = $ci[0];
            }else{
                $ci = new CompanyInfos();
            }

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $ci);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('vision', TextType::class,array('label'=>' '))
                ->add('mission', TextType::class,array('label'=>' '))
                ->add('foundation', TextType::class,array('label'=>' '))
                ->add('headoffice', TextType::class,array('label'=>' '))
                ->add('employees', IntegerType::class,array('required'=>false, 'label'=>' ',))
                ->add('director', TextType::class,array('label'=>' '))
                ->add("creer", SubmitType::class);
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($ci);
                    $em->flush();

                    $this->get('session')->getFlashBag()->set('notice', 'Cet employé a été ajouté avec succès');
                    return $this->redirectToRoute("addEmployee");
                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

            return $this->render('cas/companyInfos.html.twig',array(
                'form' => $form->createView()
            ));
        }else{
            return $this->redirectToRoute("login");
        }

        return $this->render("cas/companyInfos.html.twig",array(
            "ci"=>$ci
        ));
    }
}
