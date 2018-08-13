<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkingHours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AlertController extends ClockinReccordController
{

    /**
     * @Route("/today",name="today")
     */
    public function todayAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $day = $this->dateDayNameFrench(date('N'));
            $finalTab = array();
            $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            $listCR = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->todaysClockinTimes(date('Y').'-'.date('m').'-'.date('d'));
            foreach($listCR as $cr){
                if($this->arrive($cr,$day,$request)){
                    $finalTab[] = array($cr,"Arrivée");
                }elseif ($this->pause($cr,$day,$request)){
                    $finalTab[] = array($cr,"Pause");
                }elseif($this->finPause($cr,$day,$request)){
                    $finalTab[] = array($cr,"Fin pause");
                }elseif($this->depart($cr,$day,$request)){
                    $finalTab[] = array($cr,"Départ");
                }
            }
            return $this->render('cas/today.html.twig',array(
                'listDep'=>$listDep,
                'listCR'=>$finalTab
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }
}
