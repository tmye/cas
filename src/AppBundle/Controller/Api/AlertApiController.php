<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AlertApiController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @Route("/api/v1/top-retard",name="topretard")/
     */
    public function topDelayAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $clokinRecords = $em->getRepository('AppBundle:ClockinRecord')->findAll();


        $employees = $em->getRepository('AppBundle:Employe')->findAll();

        $data = ["Hello"=>"Late is better than never"];
        $data = $this->get('jms_serializer')->serialize($data, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


}
