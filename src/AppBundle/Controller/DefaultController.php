<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\CompanyConfig;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Expiration;
use AppBundle\Entity\WorkingHours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TmyeDeviceBundle\Entity\DevicePubPic;
use TmyeDeviceBundle\Entity\UpdateEntity;

class DefaultController extends Controller
{

    /**
     * @Route("/functionTest", name="functionTest")
     */
    public function functionTestAction(Request $request)
    {
        /*$date = "2018-04-19";
        $heureNormaleArrivee = "14:00";
        $heureEnregistre = "14:10";
        $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission(26,$date,$heureEnregistre,$heureNormaleArrivee);
        foreach ($p as $perm){
            echo "<br>".$perm->getDescription()."<br>";
        }*/


        return new Response(date("Y-m-d H:i:s",1526335200));
        //return new Response(date("Y-m-d H:i:s",(new \DateTime())->getTimestamp()));
        //return new Response(strtotime("2018-05-13 08:35"));
        //return new Response(md5("ebenezer10"));
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
            return $this->render('cas/index.html.twig', array(
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
}
