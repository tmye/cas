<?php

namespace TmyeDeviceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{

    /**
     * @Route("/server/time",name="serverTime")
     */
    public function indexAction()
    {
        $dateTime = new \DateTime();
        print_r($dateTime);
        exit;
    }

}
