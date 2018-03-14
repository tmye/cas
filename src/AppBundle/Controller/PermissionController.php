<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;


class PermissionController extends Controller {

    /**
     * @Route("/addPermission",name="addPermission")
     */
    public function addPermissionAction(Request $request)
    {
        $permission = new Permission();
        $permission->setUpdateTime(new \DateTime());
        $permission->setState(0);
        $permission->setCreateTime(new \DateTime());
        $permission->setAskerId($this->getUser()->getId());

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $permission);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('title', TextType::class,array('label'=>' '))
            ->add('description', TextType::class,array('label'=>' '))
            ->add('dateFrom', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
            ->add('dateTo', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
            ->add('employee',EntityType::class,array(
                'label'=>' ',
                'class' => 'AppBundle:Employe',
                'choice_label' => function ($employee) {
                    return $employee->getEmployeeCcid().' '.$employee->getSurname() . ' ' . $employee->getLastName();
                },
                'multiple' => false,
            ))
            ->add('Créer', SubmitType::class);
        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($permission);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Employé bien enregistrée.');

                return new Response("Cette permission a été ajoutée");

            }

        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/addPermission.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/editPermission/{id}",name="editPermission")
     */
    public function editPermissionAction(Request $request,$id)
    {
        $permission = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->find($id);

        $permission->setUpdateTime(new \DateTime());
        $permission->setState(0);
        //$permission->setAskerId($this->getUser()->getId());

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $permission);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('dateFrom', DateTimeType::class)
            ->add('dateTo', DateTimeType::class)
            ->add('state', IntegerType::class)
            ->add('employee',EntityType::class,array(
                'class' => 'AppBundle:Employe',
                'choice_label' => 'employee_ccid',
                'multiple' => false,
            ))
            ->add('Modifier', SubmitType::class);
        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($permission);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Permission bien modifiée.');

                return new Response("Cette permission a été modifiée");
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/addPermission.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/viewPermission",name="viewPermission")
     */
    public function viewEmployeeAction(Request $request)
    {
        $permRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");
        $listPerm = $permRep->findByOrder();
        return $this->render('cas/viewPermission.html.twig', array(
            'listPerm' => $listPerm,
        ));
    }

    /**
     * @Route("/deletePermission/{id}" ,name="deletePermision")
     */
    public function deleteEmployeeAction(Request $request, $id)
    {
        $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
        if ($perm != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($perm);
            $em->flush();
            return new Response("Cette permission a été supprimée");
        } else{
            throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/grantPermission/{id}" ,name="grantPermision")
     */
    public function grantPermissionAction(Request $request, $id)
    {
        $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
        if ($perm != null) {
            $perm->setState(1);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return new Response("Permission granted");
        } else{
            throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/rejectPermission/{id}" ,name="rejectPermision")
     */
    public function rejectPermissionAction(Request $request, $id)
    {
        $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
        if ($perm != null) {
            $perm->setState(2);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return new Response("Permission not granted");
        } else{
            throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
        }
    }
}