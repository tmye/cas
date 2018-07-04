<?php

namespace TmyeDeviceBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Created by PhpStorm.
 * User: abiguime
 * Date: 16/02/2017
 * Time: 1:12 AM
 */
class BaseController extends Controller
{

    private $logger = 0;

    protected function processForm(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Form\Form $form)
    {

        if (!($form->isSubmitted() && $form->isValid())) {
            $this->requestInvalid();
        } else {
            echo "form is valid";
        }
    }

    protected function persist(EntityManager $manager, $obj) {

        $em = $this->getDoctrine()->getManager($manager);
        $em->persist($obj);
        $em->flush();
    }

    protected function deleteEntity(EntityManager $manager, $entity)
    {
        $this->getDoctrine()->getManager($manager)->remove($entity);
        $this->getDoctrine()->getManager($manager)->flush();
    }

    protected function serialize($data)
    {
        /* return $this->container->get('jms_serializer')
             ->serialize($data, 'json');*/
        return json_encode($data);
    }

    protected function deserialize($data)
    {
//        return $this->container->get('jms_serializer')
//            ->deserialize($data, \ArrayObject::class, 'json');
        return json_decode(data, true);
    }

    protected function requestInvalid()
    {
        $data = [
            'error' => -1,
            'message' => "parameters error",
            'data' => []
        ];
        echo json_encode($data);
        exit();
    }


   /* protected function getUserNameFromToken(EntityManager $manager, $token)
    {
        // check if the token is still valid, if no redirect to login page.
        $token = $this->TokenRepo($manager)->findOneByTokenkey($token);
        $user = $this->AdminRepo()->find($token->getUserId());
        return $user;
    }*/


    protected function base64__($pathtopic, $type = "")
    {

        $rootWebDir = $this->getParameter('web_dir');
        $path = $rootWebDir . DIRECTORY_SEPARATOR . $pathtopic;
        if (file_exists($path) && !is_dir($path)) {
            $data = file_get_contents($path);
        } else {
            $data = file_get_contents("img/default-profile.png");
            if ($type == "f") {
                $base64 = "";
                return $base64;
            }
        }

        $base64 = /*'data:image/' . $type . ';base64,' . */
            base64_encode($data);

        return $base64;
    }


    protected function systimeToFrench ($time) {

        return date('d-m-Y', $time);
    }

    /* done */
    protected function WorkingHourRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("AppBundle:WorkingHours");
    }

    /* done */
    protected function DepartementRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("AppBundle:Departement");
    }

    protected function RequestBlobRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:RequestBlob");
    }

    protected function PubsRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:DevicePubPic");
    }

    /* protected function AdminRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Admin");
     }*/

    /*  protected function TokenRepo () {
          return $this->getDoctrine()->getRepository("AppBundle:Token");
      }*/

    protected function EmployeeRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("AppBundle:Employe");
    }

    protected function ClockinRecordRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("AppBundle:ClockinRecord");
    }

    /* protected function PermissionRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Permission");
     }*/

    protected function MachineRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:Machine");
    }

    protected function UpdateEntityRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:UpdateEntity");
    }

    protected function OkidRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:OkIdEntity");
    }

    protected function ConfigEntityRepo (EntityManager $manager) {
        return $this->getDoctrine()->getManager($manager)->getRepository("TmyeDeviceBundle:ConfigEntity");
    }

  /*  protected function getManager () {
        return $this->getDoctrine()->getManager();
    }*/

    protected function flush (EntityManager $manager) {
        $this->getDoctrine()->getManager($manager)->getManager()->flush();
    }

    protected function info($message) {
        $this->logger = $this->get('logger');
        $this->logger->info("XXXXXXXXXXXXXXX    ".$message);
    }


    // utils classes
    protected function dateformTimeStamp($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

}