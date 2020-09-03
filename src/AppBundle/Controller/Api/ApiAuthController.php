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
//        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
//        $this->get('security.token_storage')->setToken($token);
//        // If the firewall name is not main, then the set value would be instead:
//        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
//        $this->get('session')->set('_security_main', serialize($token));
//        // Fire the login event manually
//        $event = new InteractiveLoginEvent($request, $token);
//        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        return new JsonResponse(['token' => $jwtManager->create($user)]);    }
}