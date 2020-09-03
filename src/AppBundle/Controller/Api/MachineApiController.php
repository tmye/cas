<?php


namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MachineApiController extends Controller
{

    /**
     * @Rest\Get(
     *     path="/api/v1/machines",
     *     name="api_machines"
     * )
     */
    public function machines(){

        $em = $this->getDoctrine()->getManager();

        $machines = $em->getRepository('AppBundle:Machine')->findAll();
        $response = new Response();

        if(count($machines)<0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>'Pas de machines'
            ]], 'json');

            $response->setStatusCode(200);
        }else{
            $data = $this->get('jms_serializer')->serialize($machines, 'json');
            $response->setStatusCode(200);
        }
        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }


    /**
     * @Rest\Get(
     *     path="/api/v1/machine/{id}",
     *     name="api_machine_id",
     *     requirements={"id"="\d+"}
     * )
     */
    public function machineByIdAction($id){

        $em = $this->getDoctrine()->getManager();

        $machine = $em->getRepository('AppBundle:Machine')->find($id);

        $response = new Response();

        if(!$machine){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>'Cette machine est inexistante'
            ]], 'json');

            $response->setStatusCode(200);
        }else{
            $data = $this->get('jms_serializer')->serialize($machine, 'json');
            $response->setStatusCode(200);
        }

        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get(
     *     path="/api/v1/machine",
     *     name="api_machine",
     * )
     */
    public function machineAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $params = $request->query->all();

        $machine_columns = $em->getClassMetadata('AppBundle:Machine')
            ->getColumnNames();

        $response = new Response();

        if(count($params)>0 && count($params)==1){
            foreach ($params as $parameter => $value){
                if(in_array($parameter, $machine_columns)){
                    $machine = $em->getRepository('AppBundle:Machine')->findBy([
                       $parameter=>$value
                    ]);

                    if(!$machine){
                        $data = $this->get('jms_serializer')->serialize(['error'=>[
                            'code'=>405,
                            'message'=>'Cette machine est inexistante'
                        ]], 'json');

                    }else{
                        $data = $this->get('jms_serializer')->serialize($machine, 'json');
                        $response->setStatusCode(200);
                    }
                }else{
                    $data = $this->get('jms_serializer')->serialize(['error'=>[
                        'code'=>405,
                        'message'=>"Le parametre fournit n'est pas correct"
                    ]], 'json');
                }
                $response->setContent($data);
                $response->headers->set('content-type', 'application/json');
            }
        }else{
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>'Vous devez envoyer un et un seul parametre'
            ]], 'json');

            $response->setContent($data);
            $response->headers->set('content-type', 'application/json');
        }

        return $response;

    }

}