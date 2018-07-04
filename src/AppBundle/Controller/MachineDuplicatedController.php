<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\MachineDuplicated;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Session\Session;



class MachineDuplicatedController extends Controller {


    public function returnCompanyAction($context, $device_id)
    {

//        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
        $md = $context->getDoctrine()->getManager("cas")->getRepository("AppBundle:MachineDuplicated")->findOneBy(array(
            "machineId"=>$device_id
        ));
        if($md != null  && !empty($md)){
            return $md->getCompany();
        }else{
            return null;
        }
//        }

    }
}