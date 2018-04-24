<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 08/03/2018
 * Time: 15:55
 */

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    public function loginAction(){

        $session = new Session();
        $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
        $exp = $this->getDoctrine()->getManager()->getRepository("AppBundle:Expiration")->findAll();
        $cc = $cc[0];
        $compName = $cc->getCompanyName();
        $compLogo = $cc->getCompanyLogo();
        $session->set("companyName",$compName);
        $session->set("companyLogo",$compLogo);
        $session->set("expiryDate",$exp[0]->getExpiryDate());

        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            return $this->redirectToRoute("homepage");
        }

        // Les éventuelles erreurs de soumission
        $authenticationUtils = $this->get('security.authentication_utils');
        return $this->render('UserBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }
}