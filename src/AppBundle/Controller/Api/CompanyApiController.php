<?php


namespace AppBundle\Controller\Api;

use AppBundle\Entity\CompanyInfos;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the all company infos",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CompanyInfos::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="company infos")
     * @Security(name="Bearer")
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