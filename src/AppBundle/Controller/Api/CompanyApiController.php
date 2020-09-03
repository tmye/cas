<?php


namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CompanyApiController extends Controller
{
    /**
     * @Rest\Get(
     *     path="/api/v1/company",
     *     name="api_company"
     *
     * )
     */
    public function companyInfosAction(){
        $em = $this->getDoctrine()->getManager();

        $companyInfo = $em->getRepository('AppBundle:CompanyInfos')->findAll();
        $response = new Response();


        if(count($companyInfo)<0){
            $data = $this->get('jms_serializer')->serialize([
                'error'=>405,
                'message'=>'Les informations ne sont pas disponbles. Réessayez ultérieurement.'
            ], 'json');
            $response->setStatusCode(405);
        }else{
            $data = $this->get('jms_serializer')->serialize($companyInfo, 'json');
            $response->setStatusCode(200);
        }
        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }

}