<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 20/04/2018
 * Time: 10:22
 */

namespace AppBundle\MyServices;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Expiry extends Controller
{
    public function hasExpired(){
        $session = new Session();
        $expiryDate = $session->get('expiryDate');
        if(!$expiryDate){
            return false;
        }
        $timeExpiry = strtotime($expiryDate);
        $nowTime = strtotime(date("Y").'-'.date('m').'-'.date("d"));
        if ($nowTime > $timeExpiry){
            return true;
        }else{
            return False;
        }
    }

}