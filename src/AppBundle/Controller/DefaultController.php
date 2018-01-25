<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('cas/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/historique",name="historique")
     */
    public function historiqueAction(Request $request)
    {
        return $this->render('cas/historique.html.twig');
    }

    /**
     * @Route("/addEmployee",name="addEmployee")
     */
    public function addEmployeeAction(Request $request)
    {
        return $this->render('cas/addEmployee.html.twig');
    }

    /**
     * @Route("/viewEmployee",name="viewEmployee")
     */
    public function viewEmployeeAction(Request $request)
    {
        return $this->render('cas/viewEmployee.html.twig');
    }

    /**
     * @Route("/viewDepStat",name="viewDepStat")
     */
    public function viewDepStatAction(Request $request)
    {
        return $this->render('cas/viewDepStat.html.twig');
    }

    /**
     * @Route("/viewPersStat",name="viewPersStat")
     */
    public function viewPersStatAction(Request $request)
    {
        return $this->render('cas/viewPersStat.html.twig');
    }

    /**
     * @Route("/imageVeille",name="imageVeille")
     */
    public function imageVeilleAction(Request $request)
    {
        return $this->render('cas/imageVeille.html.twig');
    }
}
