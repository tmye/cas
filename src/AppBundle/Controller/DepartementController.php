<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DepartementController extends Controller
{

    /**
     * @Route("/departement",name="departement")
     */
    public function departementAction(Request $request)
    {
        // Recuperation des departements existants

        $depRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement");
        $listDep = $depRep->findAll();

        $departement = new Departement();
        $departement->setLastUpdate(new \DateTime());
        $departement->setCreateDate(new \DateTime());
        $departement->setAuthor($this->getUser()->getUsername());

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $departement);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', TextType::class,array('label'=>' '))
            ->add('maxCount', IntegerType::class,array('label'=>' '))
            ->add('Créer', SubmitType::class);

        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($departement);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Département bien enregistrée.');

                return $this->redirectToRoute('departement', array("listDep" => $listDep));

            }

        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/departement.html.twig', array(
            'form' => $form->createView(),
            'listDep' => $listDep
        ));
    }

    /**
     * @Route("/deleteDepartement/{id}",name="deleteDepartement")
     */
    public function deleteDepartementAction(Request $request, $id)
    {
        // On vérifie que le département est vide
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from('AppBundle:Employe','e')
            ->andWhere('e.departement='.$id);
        $listEmployee = $qb->getQuery()->getArrayResult();

        $dep = $this->getDoctrine()->getManager()->getRepository('AppBundle:Departement')->find($id);
        if ($dep != null) {
            if($listEmployee == null){
                $em = $this->getDoctrine()->getManager();
                $em->remove($dep);
                $em->flush();
                return new Response("Ce département a été supprimé");
            }else{
                return new Response("Ce département contient des employés et ne peut donc etre supprimé");
            }

        } else{
            throw new NotFoundHttpException("Le département d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/editDepartement/{id}",name="editDepartement")
     */
    public function editDepartementAction(Request $request, $id)
    {
        $departement = $this->getDoctrine()->getManager()->getRepository('AppBundle:Departement')->find($id);

        // Recuperation des departements existants

        $depRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement");
        $listDep = $depRep->findAll();

        if($departement != null){
            $departement->setLastUpdate(new \DateTime());
            $departement->setCreateDate(new \DateTime());
            $departement->setAuthor($this->getUser()->getUsername());
        }else{
            throw new NotFoundHttpException("Le département d'id " . $id . " n'existe pas.");
        }

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $departement);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', TextType::class)
            ->add('maxCount', IntegerType::class,array('label'=>' '))
            ->add('Modifier', SubmitType::class,array('label'=>' '));

        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($departement);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Département bien enregistrée.');

                return $this->redirectToRoute('departement', array("listDep" => $listDep));

            }

        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/departement.html.twig', array(
            'form' => $form->createView(),
            'listDep' => $listDep
        ));
    }
}