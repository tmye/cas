<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\NullDate;
use AppBundle\Entity\Permission;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;


class NullDateController extends Controller {

    /**
     * @Route("/nullDate",name="nullDate")
     */
    public function nullDateAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $nullDate = new NullDate();
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $nullDate);
            $formBuilder
                ->add('jour', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
                ->add('motif', TextType::class,array('label'=>' '))
                ->add('Ajouter', SubmitType::class);
            $form = $formBuilder->getForm();
            return $this->render("cas/addNullDate.html.twig",array(
                'form'=>$form->createView()
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/addNullDate",name="addNullDate")
     */
    public function addNullDateAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("addNullDate");
            }
            $nullDate = new NullDate();
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $nullDate);
            $formBuilder
                ->add('jour', TextType::class,array('label'=>' '))
                ->add('motif', TextType::class,array('label'=>' '))
                ->add('Ajouter', SubmitType::class);
            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $em = $this->getDoctrine()->getManager($session->get("connection"));
                    $em->persist($nullDate);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Cette date a bien été ajouté aux exceptions.');

                    $list = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->findAll();
                    return $this->redirectToRoute("addNullDate",array(
                        'listJourNull'=>$list
                    ));

                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            $list = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->findAll();
            return $this->render('cas/addNullDate.html.twig',array(
                'listJourNull'=>$list,
                'form' => $form->createView()
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/editNullDate/{id}",name="editNullDate")
     */
    public function editNullDateAction(Request $request,$id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("addNullDate");
            }
            $nullDate = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->find($id);
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $nullDate);
            $formBuilder
                ->add('jour', TextType::class,array('label'=>' '))
                ->add('motif', TextType::class,array('label'=>' '))
                ->add('Ajouter', SubmitType::class);
            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $em = $this->getDoctrine()->getManager($session->get("connection"));
                    $em->persist($nullDate);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Cette date a bien été modifée.');

                    $list = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->findAll();
                    return $this->redirectToRoute("addNullDate",array(
                        'listJourNull'=>$list
                    ));

                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            $list = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->findAll();
            return $this->render('cas/addNullDate.html.twig',array(
                'listJourNull'=>$list,
                'form' => $form->createView()
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/deleteNullDate/{id}",name="deleteNullDate")
     */
    public function deleteNullDateAction(Request $request,$id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("addNullDate");
            }
            $nullDate = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->find($id);

            $em = $this->getDoctrine()->getManager($session->get("connection"));
            $em->remove($nullDate);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Cette date a bien été supprimée.');

            $list = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:NullDate")->findAll();
            return $this->redirectToRoute("addNullDate",array(
                'listJourNull'=>$list
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }
}