<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompanyConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TmyeDeviceBundle\Entity\DevicePubPic;
use TmyeDeviceBundle\Entity\UpdateEntity;

class DefaultController extends Controller
{

    /**
     * @Route("/functionTest", name="functionTest")
     */
    public function functionTestAction(Request $request)
    {
        $res = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->machinesByDep(1);

        print_r($res);
        return new Response("OK");
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
        }else{
            $newCC = new CompanyConfig();
            $newCC->setCompanyName($name);
            $em->persist($newCC);
            $em->flush();
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
                    $dev->setImage1($_FILES["first_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])){
                    $dev->setImage2($_FILES["second_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])){
                    $dev->setImage3($_FILES["third_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }

                $em->flush();
            }
        }else{
            foreach ($machines as $mac){
                $dev = new DevicePubPic();
                if(isset($_FILES["first_image_input_1"]["name"]) && !empty($_FILES["first_image_input_1"]["name"])){
                    echo "\n I'm also here for 1 \n";
                    $dev->setDeviceid($mac->getMachineId());
                    $dev->setImage1($_FILES["first_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $dev->setImage2($_FILES["second_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
                }
                if(isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $dev->setImage3($_FILES["third_image_input_1"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_1"]["name"]));
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
                    $dev->setImage1($_FILES["first_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['first_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["first_image_input_2"]["name"]));
                }
                if(isset($_FILES["second_image_input_2"]["name"]) && !empty($_FILES["second_image_input_2"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $dev->setImage2($_FILES["second_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['second_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["second_image_input_2"]["name"]));
                }
                if(isset($_FILES["third_image_input_2"]["name"]) && !empty($_FILES["third_image_input_2"]["name"])){
                    $dev->setDeviceid($mac->getMachineId());
                    $dev->setImage3($_FILES["third_image_input_2"]["name"]);
                    $resultat = move_uploaded_file($_FILES['third_image_input_2']['tmp_name'],"pub_covers/".basename($_FILES["third_image_input_2"]["name"]));
                }
                $em->flush();
            }
        }else{
            foreach ($machines as $mac){
                $dev = new DevicePubPic();
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
        $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/historique.html.twig',array('listDep'=>$listDep));
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
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        return $this->render('cas/switch.html.twig',array(
            'departements'=>$departements,
            'machines'=>$machines
        ));
    }

    /**
     * @Route("/manage",name="manage")
     */
    public function manageAction(Request $request)
    {
        return $this->render('cas/manage.html.twig');
    }

    /**
     * @Route("/manageEmpProfilePicture",name="manageEmpProfilePicture")
     */
    public function manageEmpProfilePictureAction(Request $request)
    {
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/manageEmpProfilePicture.html.twig',array(
            "departements"=>$departements
        ));
    }

    /**
     * @Route("/manageEmpFingerprint",name="manageEmpFingerprint")
     */
    public function manageEmpFingerprintAction(Request $request)
    {
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/manageEmpFingerprint.html.twig',array(
            "departements"=>$departements
        ));
    }

    /**
     * @Route("/manageEmployee",name="manageEmployee")
     */
    public function manageEmployeeAction(Request $request)
    {
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/manageEmployee.html.twig',array(
            "departements"=>$departements
        ));
    }

    /**
     * @Route("/manageDeleteData",name="manageDeleteData")
     */
    public function manageDeleteDataAction(Request $request)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/delete.html.twig',array(
            "departements"=>$departements,
            "machines"=>$machines
        ));
    }

    /**
     * @Route("/manageDepartement",name="manageDepartement")
     */
    public function manageDepartementAction(Request $request)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/manageDepartement.html.twig',array(
            "departements"=>$departements,
            "machines"=>$machines
        ));
    }

    /**
     * @Route("/manageSocietyName",name="manageSocietyName")
     */
    public function manageSocietyNameAction(Request $request)
    {
        return $this->render('cas/manageSocietyName.html.twig');
    }
}
