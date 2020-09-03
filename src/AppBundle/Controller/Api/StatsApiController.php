<?php


namespace AppBundle\Controller\Api;


use AppBundle\Controller\HomeStatsController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StatsApiController extends Controller
{

    public function persStatAction(Request $request)
    {


        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return 0;
            }
            $em = $this->getDoctrine()->getManager();
            $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/viewPersStat.html.twig',array(
                'listDep'=>$dep,
                'listEmployee'=>$listEmployee
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

}