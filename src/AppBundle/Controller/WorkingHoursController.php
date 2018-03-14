<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkingHours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class WorkingHoursController extends Controller
{

    /**
     * @Route("/addWorkingHour", name="addWorkingHour")
     */
    public function addWorkingHourAction(Request $request)
    {
        $whList = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->findAll();
        $tab = array();
        foreach ($whList as $wh){
            $tab[] = ['id'=>$wh->getId(),'workingHour'=>(array)json_decode($wh->getWorkingHour())];
        }

        return $this->render('cas/addWorkingHour.html.twig', array(
            'whList'=>$tab
        ));
    }

    /**
     * @Route("/persistWorkingHour", name="persistWorkingHour")
     */
    public function persistWorkingHourAction(Request $request)
    {
        $wh = new WorkingHours();
        $em = $this->getDoctrine()->getManager();

        $don = $request->request->get('json_s');
        $code = $request->request->get('code');

        $wh->setCode($code);
        $wh->setWorkingHour($don);
        $em->persist($wh);
        $em->flush();

        return new Response(1);
    }

    /**
     * @Route("/editWorkingHour/{id}", name="editWorkingHour")
     */
    public function editWorkingHourAction(Request $request, $id)
    {
        $wh = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->find($id);
        $whList = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->findAll();

        if($wh !=null){

        }else{
            throw new NotFoundHttpException("Le working hour ayant l'id ".$id."n'a pas été trouvé");
        }
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $wh);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder->add('beginHour',TimeType::class,array('widget'=>'single_text','label'=>' '))
            ->add('pauseBeginHour',TimeType::class,array('widget'=>'single_text','label'=>' ','required'=>false))
            ->add('pauseEndHour',TimeType::class,array('widget'=>'single_text','label'=>' ','required'=>false))
            ->add('endHour',TimeType::class,array('widget'=>'single_text','label'=>' '))
            ->add('isFor',TextType::class,array('label'=>' '))
            ->add('quota',IntegerType::class,array('label'=>' '))
            ->add('Ajouter', SubmitType::class);

        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($wh);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Enregistrement effectué.');

                return new Response("Enregistrement effectué");
            }

        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/addWorkingHour.html.twig', array(
            'form' => $form->createView(),
            'whList'=>$whList
        ));
    }

    /**
     * @Route("/deleteWorkingHour/{id}", name="deleteWorkingHour")
    */

    public function deleteWorkingHourAction(Request $request, $id){
        $wh = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->find($id);

        if($wh != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($wh);
            $em->flush();
            return new Response("Ce working hour a été supprimé");
        }else{
            throw new NotFoundHttpException("Le working hour d'id ".$id." n'existe pas");
        }

    }
}
