<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use AppBundle\Entity\Employe;
use Swagger\Annotations as SWG;

class EmployeApiControllerController extends Controller
{
    /**
     * @Get(
     *     path = "/api/v1/employes",
     *     name = "api_employes",
     * )
     *
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns list of all employes",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=Employe::class, groups={"full"}))
     *     )
     *   )
     * )
     * @SWG\Tag(name="employes")
     * @Security(name="Bearer")
     *
     */
    public function employeesAction(){
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('AppBundle:Employe')->findAll();
        $response = new Response();

        if(count($employes)<=0){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>"Pas d'employé"]
            ], 'json');
            $response->setStatusCode(405);
        }else{
            $data = $this->get('jms_serializer')->serialize($employes, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * @Get(
     *     path = "/api/v1/employes/{departement}",
     *     name = "api_employes_departement",
     *     requirements = {"departement"="\d+"}
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return a department's employes",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(
     *          type="object",
     *          @SWG\Items(ref=@Model(type=Employe::class, groups={"full"}))
     *     )
     *   )
     * )
     * @SWG\Tag(name="department's employe")
     * @Security(name="Bearer")
     *
     */
    public function employeesByDepartementAction($departement){
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('AppBundle:Employe')
            ->findby(['departement'=>$departement]);

        $response = new Response();

        if(count($employes)<=0){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>['code'=>405, 'message'=>"Ce departement n'a pas d'employé"]
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return an employe",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items(ref=@Model(type=Employe::class, groups={"full"}))
     *   )
     * )
     * @SWG\Tag(name="employe")
     * @Security(name="Bearer")
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
