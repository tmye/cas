<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/uploadCoverAll", name="uploadCoverAll")
     */
    public function uploadCoverAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findAll();
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

            $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
            $found = 0;
            $i = 0;

            foreach ($devices as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="emp" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    // On met a jour le UpdateEntity
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("pub");
                    $updateE->setContent("");
                    $em->persist($updateE);
                }
            }



            $em->flush();
        }
        //return new Response("Ok");
        return new Response("Image(s) uploadée(s)");
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

        foreach ($devices as $dev){
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
            $em->flush();
        }

        /*if (isset($devices) && !empty($devices)){
            $b = "true";
        }else{
            $b = "false";
        }*/

        // Juste pour le débogage
        $t = array();
        foreach ($devices as $de){
            $t[] = $de->getId();
        }

        //return new Response(json_encode($_POST["depId"]));
        return new Response("Image(s) uploadée(s)");
    }

    /**
     * @Route("/uploadCoverMac", name="uploadCoverMac")
     */
    public function uploadCoverMacAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findBy(array("deviceid"=>$_POST["macId"]));

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

        // Juste pour le débogage
        $t = array();
        foreach ($devices as $de){
            $t[] = $de->getId();
        }

        //return new Response(json_encode($t));
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
     * @Route("/manageDepartement",name="manageDepartement")
     */
    public function manageDepartementAction(Request $request)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        return $this->render('cas/manageDepartement.html.twig',array(
            "machines"=>$machines,
            "departements"=>json_encode($departements)
        ));
    }
}
