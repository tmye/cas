<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkingHours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class WorkingHoursController extends Controller
{

    /**
     * @Route("/addWorkingHour", name="addWorkingHour")
     */
    public function addWorkingHourAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $whList = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->safeWorkingHour();
            $tab = array();
            foreach ($whList as $wh){
                $tab[] = ['id'=>$wh->getId(),'workingHour'=>(array)json_decode($wh->getWorkingHour())];
            }

            return $this->render('cas/addWorkingHour.html.twig', array(
                'whList'=>$tab
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/persistWorkingHour", name="persistWorkingHour")
     */
    public function persistWorkingHourAction(Request $request)
    {
        $wh = new WorkingHours();
        $em = $this->getDoctrine()->getManager();

        $don = $request->request->get('json_s');
        $code = $request->request->get('code');

        $wh->setCode($code);
        $wh->setWorkingHour($don);
        $em->persist($wh);
        $em->flush();

        return new Response(1);
    }

    /**
     * @Route("/updateWorkingHour", name="updateWorkingHour")
     */
    public function updateWorkingHourAction(Request $request)
    {
        $don = $request->request->get('json_s');
        $code = $request->request->get('code');
        $id = $request->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $wh = $em->getRepository("AppBundle:WorkingHours")->find($id);

        $wh->setCode($code);
        $wh->setWorkingHour($don);
        $em->flush();

        return new Response(1);
    }

    /**
     * @Route("/editWorkingHour/{id}", name="editWorkingHour")
     */
    public function editWorkingHourAction(Request $request, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $wh = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->find($id);
                if($wh == null){
                    throw new NotFoundHttpException("Ce workingHour n'a pas été trouvé");
                }
                $tab = array();

                $json_wh = json_decode($wh->getWorkingHour(),true);
                $tab[] = ['id'=>$wh->getId(),'workingHour'=>(array)json_decode($wh->getWorkingHour())];

                return $this->render('cas/editWorkingHour.html.twig', array(
                    'id'=>$id,
                    'wh'=>$wh,
                    'whJson'=>$json_wh
                ));
            }else{
                return $this->redirectToRoute("login");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/deleteWorkingHour/{id}", name="deleteWorkingHour")
    */
    public function deleteWorkingHourAction(Request $request, $id){
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $wh = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->find($id);

            if($wh != null){
                $em = $this->getDoctrine()->getManager();
                $em->remove($wh);
                $em->flush();
                return new Response("Ce working hour a été supprimé");
            }else{
                throw new NotFoundHttpException("Le working hour d'id ".$id." n'existe pas");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }
}
