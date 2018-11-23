<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{

    /**
     * @Route("/admin/testSerialize",name="testSerialize")
     */
    public function testSerialize(Request $request)
    {
        return new Response(serialize(array("ROLE_SUPER_ADMIN")));
    }

    /**
     * @Route("/superAdmin/add",name="addAdmin")
     */
    public function addAdminAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            if ($request->isMethod('POST')) {
                $empId = $request->request->get("employe");
                $em = $this->getDoctrine()->getManager();
                $e = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($empId);
                $e->setRoles(array("ROLE_ADMIN"));
                $e->setAuth(14);
                $em->flush();
                $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
                return $this->render("cas/superAdmin.html.twig",array(
                    "status"=>200,
                    "employe"=>$emp
                ));
            }

            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
            return $this->render("cas/superAdmin.html.twig",array(
                "employe" => $emp
            ));
        }else{
            throw new AccessDeniedException("Accès limité aux super-administrateurs");
        }
    }

    /**
     * @Route("/superAdmin/addNewAdmin",name="addNewAdmin")
     */
    public function addNewAdminAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            if ($request->isMethod('POST')) {
                $empId = $request->request->get("employe");
                $em = $this->getDoctrine()->getManager();
                $e = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($empId);
                $e->setRoles(array("ROLE_ADMIN"));
                $e->setAuth(14);
                $em->flush();
                $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
                return $this->render("cas/superAdmin.html.twig",array(
                    "status"=>200,
                    "employe"=>$emp
                ));
            }

            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
            return $this->render("cas/addNewAdmin.html.twig",array(
                "employe" => $emp
            ));
        }else{
            throw new AccessDeniedException("Accès limité aux super-administrateurs");
        }
    }

    /**
     * @Route("/superAdmin/expiry",name="expiry")
     */
    public function expiryAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->render("cas/expiration.html.twig");
        }else{
            throw new AccessDeniedException("Accès limité aux super-administrateurs");
        }
    }

    /**
     * @Route("/admin/roleChange",name="roleChange")
     */
    public function roleChangeAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            if ($request->isMethod('POST')) {
                $empId = $request->request->get("employe");
                $role = $request->request->get("role");
                $em = $this->getDoctrine()->getManager();
                $e = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($empId);
                $e->setRoles(array($role));
                $em->flush();
                $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeSafe();
                return $this->render("cas/admin.html.twig",array(
                    "status"=>200,
                    "employe"=>$emp
                ));
            }

            $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->employeeSafe();
            return $this->render("cas/admin.html.twig",array(
                "employe" => $emp
            ));
        }else{
            throw new AccessDeniedException("Accès limité aux administrateurs");
        }
    }
}