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
use Symfony\Component\HttpFoundation\Session\Session;


class DepartementController extends Controller
{

    /**
     * @Route("/departement",name="departement")
     */
    public function departementAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            // Recuperation des departements existants
            $depRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement");
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
                ->add('creer', SubmitType::class);

            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager($session->get("connection"));

                    $em->persist($departement);
                    $em->flush();

                    $this->get('session')->getFlashBag()->set('notice', 'Ce département a été ajouté avec succès');
                    return $this->redirectToRoute("departement");
                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('cas/departement.html.twig', array(
                'form' => $form->createView(),
                'listDep' => $listDep
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/deleteDepartement/{id}",name="deleteDepartement")
     */
    public function deleteDepartementAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            // On vérifie que le département est vide
            $em = $this->getDoctrine()->getManager($session->get("connection"));
            $qb = $em->createQueryBuilder();
            $qb->select('e')
                ->from('AppBundle:Employe','e')
                ->andWhere('e.departement='.$id);
            $listEmployee = $qb->getQuery()->getArrayResult();

            $dep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Departement')->find($id);
            if ($dep != null) {
                if($listEmployee == null){
                    $em = $this->getDoctrine()->getManager($session->get("connection"));
                    $em->remove($dep);
                    $em->flush();

                    $depRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement");
                    $listDep = $depRep->findAll();

                    $departement = new Departement();
                    $departement->setLastUpdate(new \DateTime());
                    $departement->setCreateDate(new \DateTime());
                    $departement->setAuthor($this->getUser()->getUsername());

                    // On crée le FormBuilder grâce au service form factory
                    // On recréé le formulaire pour ne pas passer un formulaire vide au render()
                    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $departement);

                    // On ajoute les champs de l'entité que l'on veut à notre formulaire
                    $formBuilder
                        ->add('name', TextType::class,array('label'=>' '))
                        ->add('maxCount', IntegerType::class,array('label'=>' '))
                        ->add('creer', SubmitType::class);

                    // À partir du formBuilder, on génère le formulaire

                    $form = $formBuilder->getForm();

                    return $this->render("cas/departement.html.twig",array(
                        'message'=>"Ce département a été supprimé avec succès",
                        'form' => $form->createView(),
                        'listDep' => $listDep
                    ));
                }else{
                    $depRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement");
                    $listDep = $depRep->findAll();

                    $departement = new Departement();
                    $departement->setLastUpdate(new \DateTime());
                    $departement->setCreateDate(new \DateTime());
                    $departement->setAuthor($this->getUser()->getUsername());

                    // On crée le FormBuilder grâce au service form factory
                    // On recréé le formulaire pour ne pas passer un formulaire vide au render()
                    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $departement);

                    // On ajoute les champs de l'entité que l'on veut à notre formulaire
                    $formBuilder
                        ->add('name', TextType::class,array('label'=>' '))
                        ->add('maxCount', IntegerType::class,array('label'=>' '))
                        ->add('creer', SubmitType::class);

                    // À partir du formBuilder, on génère le formulaire

                    $form = $formBuilder->getForm();

                    return $this->render("cas/departement.html.twig",array(
                        'message'=>"Ce département contient des employés et ne peut donc etre supprimé",
                        'form' => $form->createView(),
                        'listDep' => $listDep
                    ));
                }

            } else{
                throw new NotFoundHttpException("Le département d'id " . $id . " n'existe pas.");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/editDepartement/{id}",name="editDepartement")
     */
    public function editDepartementAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $departement = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Departement')->find($id);

            // Recuperation des departements existants

            $depRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement");
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
                ->add('creer', SubmitType::class);

            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager($session->get("connection"));
                    $em->persist($departement);
                    $em->flush();

                    return $this->render("cas/departement.html.twig",array(
                        'message'=>"Ce département a été modifié",
                        'form' => $form->createView(),
                        'listDep' => $listDep
                    ));

                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('cas/departement.html.twig', array(
                'form' => $form->createView(),
                'listDep' => $listDep
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }
}