<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class EmployeApiControllerController extends Controller
{
    /**
     * @Get(
     *     path = "/api/v1/employes",
     *     name = "api_employes",
     * )
     */
    public function employeesAction(){
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('AppBundle:Employe')->findAll();

        $data = $this->get('jms_serializer')->serialize($employes, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Get(
     *     path = "/api/v1/employes/{departement}",
     *     name = "api_employes_departement",
     *     requirements = {"departement"="\d+"}
     * )
     */
    public function employeesByDepartementAction($departement){
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('AppBundle:Employe')
            ->findby(['departement'=>$departement]);

        $response = new Response();

        if(count($employes)<=0){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>"Ce Departement n'a pas d'employé"]
            ], 'json');
            $response->setStatusCode(405);
        }else{
            $data = $this->get('jms_serializer')->serialize($employes, 'json');
            $response->setStatusCode(200);
        }
        $response = new response($data);

        $response->headers->set('content-type', 'application/json');

        return $response;
    }

    /**
     * @Get(
     *     path = "/api/v1/employe/{id}",
     *     name = "api_employe",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function employeesByIdAction($id){
        $em = $this->getdoctrine()->getmanager();

        $employe = $em->getRepository('AppBundle:Employe')
            ->find($id);

        $response = new Response();

        if(!$employe){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>"L'employé n'existe pas"]
            ], 'json');
            $response->setStatusCode(405);
        }else{
            $data = $this->get('jms_serializer')->serialize($employe, 'json');
            $response->setStatusCode(200);
        }
        $response = new response($data);

        $response->headers->set('content-type', 'application/json');

        return $response;
    }
}
