<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Departement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class DepartementApiControlerController extends Controller
{
    /**
     * @Get(
     *     path = "/api/v1/departements",
     *     name = "api_departements",
     * )
     */
    public function departementsAction(){

        $em = $this->getDoctrine()->getManager();

        $departements = $em->getRepository('AppBundle:Departement')->findAll();
        $response = new Response();

        if(count($departements)<0){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>'Pas de departement']
            ], 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
        }else{
            $data = $this->get('jms_serializer')->serialize($departements, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }

    /**
     * @Get(
     *     path = "/api/v1/departement/{id}",
     *     name = "api_departement",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function departementsByIDAction($id){
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $departement = $em->getRepository('AppBundle:Departement')
            ->find($id);

        if($departement){
            $data = $this->get('jms_serializer')->serialize($departement, 'json');
            $response->setStatusCode(200);

        }else{
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>'Departement inéxistant']
            ], 'json');
            $response->setStatusCode(405);
        }
        $response->setContent($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
