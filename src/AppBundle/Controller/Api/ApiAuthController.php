<?php


namespace AppBundle\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiAuthController extends Controller
{
    /**
     * @POST(
     *     path = "/api/v1/login", name="api_login",
     *     name = "api_login",
     *     requirements = {"username"="\w+"}
     * )
     */
    public function loginApiAction(){
        return 1;
    }

    /**
     * @POST(
     *     path = "/api/login_check",
     *     name = "api_login",
     *     requirements = {"username"="\w+", "password"="\w+"}
     * )
     */
    public function loginCheckAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $user = $this->getDoctrine()->getManager()->getRepository("AppBundle:Admin")
            ->findOneBy(array('username' => $username));
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        if($user){
            return new JsonResponse(['token' => $jwtManager->create($user)]);
        }else{
            return new JsonResponse(['error'=>['code'=>404, 'message'=>'Bad credential']]);
        }
    }


    /**
     * @POST(
     *     path = "/api/logout",
     *     name = "api_logout",
     *     requirements = {"token"="\w+"}
     * )
     */
    public function logout(Request $request)
    {
        return $this->redirectToRoute('logout');
    }

}