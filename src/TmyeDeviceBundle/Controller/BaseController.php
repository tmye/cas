<?php

namespace TmyeDeviceBundle\Controller;

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

    protected function persist($obj)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($obj);
        $em->flush();
    }

    protected function deleteEntity($entity)
    {
        $this->getManager()->remove($entity);
        $this->getManager()->flush();
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


    protected function getUserNameFromToken($token)
    {
        // check if the token is still valid, if no redirect to login page.
        $token = $this->TokenRepo()->findOneByTokenkey($token);
        $user = $this->AdminRepo()->find($token->getUserId());
        return $user;
    }


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
    protected function WorkingHourRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:WorkingHours");
    }

    /* done */
    protected function DepartementRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:Departement");
    }

    protected function RequestBlobRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:RequestBlob");
    }

    protected function PubsRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:DevicePubPic");
    }

    /* protected function AdminRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Admin");
     }*/

    /*  protected function TokenRepo () {
          return $this->getDoctrine()->getRepository("AppBundle:Token");
      }*/

    protected function EmployeeRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:Employe");
    }

    protected function ClockinRecordRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:ClockinRecord");
    }

     protected function PermissionRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Permission");
     }

    protected function MachineRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:Machine");
    }

    protected function UpdateEntityRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:UpdateEntity");
    }

    protected function OkidRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:OkIdEntity");
    }

    protected function ConfigEntityRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:ConfigEntity");
    }

    protected function getManager () {
        return $this->getDoctrine()->getManager();
    }

    protected function flush (){
        $this->getDoctrine()->getManager()->flush();
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