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

        // Reading the first time parameter directly from the database
        $ft = $this->getDoctrine()->getManager()->getRepository("AppBundle:Setting")->findAll();
        if(sizeof($ft) > 0){
            $ft = $ft[0]->getFirstTime();
        }else{
            $ft = true;
        }

        if(!$ft){
            // Is not the first time
            $session = new Session();
            $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
            if(($cc != null) && (!empty($cc))){
                $cc = $cc[0];
                $compName = $cc->getCompanyName();
                $compLogo = $cc->getCompanyLogo();
                $compExpiration = $cc->getExpirationDate();
                $session->set("companyName",$compName);
                $session->set("companyLogo",$compLogo);
                $session->set("expiryDate",$compExpiration);
            }else{
                $cc = null;
                $session->set("companyName",null);
                $session->set("companyLogo",null);
                return $this->redirectToRoute("changeSocietyName");
            }

            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
                return $this->redirectToRoute("homepage");
            }
        }else{
            // Is the first time application is launched
            // Execute some instructions before updating the file
            return $this->redirectToRoute("firstTimeInitialization");
        }

        // Les éventuelles erreurs de soumission
        $authenticationUtils = $this->get('security.authentication_utils');
        return $this->render('UserBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }
}