<?php

namespace Tmye\DeviceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TmyeDeviceBundle:Default:index.html.twig');
    }
}
