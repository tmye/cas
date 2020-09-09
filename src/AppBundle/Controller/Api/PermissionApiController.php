<?php


namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Permission;

class PermissionApiController extends Controller
{

    /**
     * @Rest\Get(
     *     path="/api/v1/current-permissions",
     *     name="api_current_permissions"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return all current permissions",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items(ref=@Model(type=Permission::class, groups={"full"}))
     *   )
     * )
     * @SWG\Tag(name="Current permissions")
     * @Security(name="Bearer")
     *
     */
    public function currentsPermissionsAction(){

        $em = $this->getDoctrine()->getManager();

        $date = date("Y-m-d");
        $today = (new \DateTime($date))->getTimestamp();

        $permissions = $em->getRepository('AppBundle:Permission')->currentPermissions($today);

        $response = new Response();

        if(count($permissions)<=0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Pas de permission en cours"
            ]], 'json');

            $response->setStatusCode(200);

        }else{
            $data = $this->get('jms_serializer')->serialize($permissions, 'json');
            $response->setStatusCode(200);
        }

        $response->setContent($data);
        $response->headers->set('Content-TYpe', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get(
     *     path="/api/v1/terminated-permissions/{month_ago}/{state}",
     *     name="api_terminated_permissions",
     *     requirements={"id":"\d", "state":"\d"}
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return terminated permissions depend on state and date",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items(ref=@Model(type=Permission::class, groups={"full"}))
     *   )
     * )
     * @SWG\Tag(name="Terminated permissions")
     * @Security(name="Bearer")
     *
     */
    public function terminatedPermissionsAction(Request $request, $month_ago=2, $state=1){

        $em = $this->getDoctrine()->getManager();

        $today_date = date("Y-m-d");

        $today = strtotime($today_date.'-'.$month_ago.' months');

        $permissions = $em->getRepository('AppBundle:Permission')->terminatedPermissions($today);

        $state_via_query = $request->query->get('state');

        if($state_via_query != null){
            $state = (int)$state_via_query;
        }


        if($state <=2 && $state >= 0){
            $permissions = array_filter($permissions, function($permission) use ($state) {
                return $permission->getState() == $state;
            });
        }

        $response = new Response();
        if(count($permissions)<=0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Aucune permission n'est a terme"
            ]], 'json');
            $response->setStatusCode(200);
        }else{
            $data = $this->get('jms_serializer')->serialize($permissions, 'json');
            $response->setStatusCode(200);
        }
        $response->setContent($data);
        $response->headers->set('Content-TYpe', 'application/json');
        return $response;
    }

    /**
     * @Rest\Get(
     *     path="/api/v1/incoming-permissions/{month}/{state}",
     *     name="api_incoming_permissions"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return Incomming permissions depend on state and date",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items(ref=@Model(type=Permission::class, groups={"full"}))
     *   )
     * )
     * @SWG\Tag(name="Incoming permissions")
     * @Security(name="Bearer")
     *
     */
    public function incomingPermissionsAction(Request $request, $month=0, $state=1){

        $em = $this->getDoctrine()->getManager();

        $today_date = date("Y-m-d");

        $today = strtotime($today_date.'+'.$month.' months');


        $permissions = $em->getRepository('AppBundle:Permission')->incomingPermissions($today);

        $state_via_query = $request->query->get('state');

        if($state_via_query != null){
            $state = (int)$state_via_query;
        }


        if($state <=2 && $state >= 0){
            $permissions = array_filter($permissions, function($permission) use ($state) {
                return $permission->getState() == $state;
            });
        }

        $response = new Response();

        if(count($permissions)<=0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Pas de permission a venir"
            ]], 'json');

            $response->setStatusCode(200);

        }else{
            $data = $this->get('jms_serializer')->serialize($permissions, 'json');
            $response->setStatusCode(200);
        }

        $response->setContent($data);
        $response->headers->set('Content-TYpe', 'application/json');

        return $response;
    }

    /**
     * @Rest\Get(
     *     path="/api/v1/permission/{id}",
     *     name="api_permission",
     *     requirements={"id":"\d"}
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Return a permission",
     *     @SWG\Schema(
     *         type="object",
     *          @SWG\Items(ref=@Model(type=Permission::class, groups={"full"}))
     *   )
     * )
     * @SWG\Tag(name="Permission")
     * @Security(name="Bearer")
     */
    public function permissionAction($id){

        $em = $this->getDoctrine()->getManager();

        $permission = $em->getRepository('AppBundle:Permission')->find($id);
        $response = new Response();

        if(!$permission){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Cette permission n'existe pas"
            ]], 'json');
        }else{
            $data = $this->get('jms_serializer')->serialize($permission, 'json');
        }
        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }

}