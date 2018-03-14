<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Machine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MachinesController extends Controller
{

    /**
     * @Route("/addMachine",name="addMachine")
     */
    public function addMachineAction(Request $request)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $machine = new Machine();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', TextType::class, array('label' =>' '))
            ->add('machineId', TextType::class, array('label' =>' '))
            ->add('description', TextareaType::class, array('label' =>' '))
            ->add('departements', EntityType::class,array(
                'class'=>'AppBundle:Departement',
                'label'=>' ',
                'choice_label' => 'name',
                'multiple' => true,
            ))
            ->add('Ajouter', SubmitType::class);

        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($machine);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Machine bien enregistrée.');

                return new Response("OK machine enregistrée");
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('api/addMachine.html.twig', array(
            'form' => $form->createView(),
            'machines' => $machines
        ));
    }

    /**
     * @Route("/editMachine/{id}",name="editMachine")
     */
    public function editMachineAction(Request $request, $id)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $machine = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->find($id);

        if($machine != null){
            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('name', TextType::class, array('label' =>' '))
                ->add('machineId', TextType::class, array('label' =>' '))
                ->add('description', TextareaType::class, array('label' =>' '))
                ->add('departements', EntityType::class,array(
                    'class'=>'AppBundle:Departement',
                    'label'=>' ',
                    'choice_label' => 'name',
                    'multiple' => true,
                ))
                ->add('Modifier', SubmitType::class);

            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($machine);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Machine bien enregistrée.');

                    return new Response("OK machine modifiée");
                }
            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('api/addMachine.html.twig', array(
                'form' => $form->createView(),
                'machines' => $machines
            ));
        }else{
            throw new NotFoundHttpException("Le département d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/deleteMachine/{id}",name="deleteMachine")
     */
    public function deleteMachineAction(Request $request, $id)
    {
        // On vérifie que le département n'est pas vide

        $machine = $this->getDoctrine()->getManager()->getRepository('AppBundle:Machine')->find($id);
        if ($machine != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($machine);
            $em->flush();
            return new Response("Cette machine a été supprimée de la base de données");
        } else{
            throw new NotFoundHttpException("La machine d'id " . $id . " n'existe pas.");
        }
    }
}