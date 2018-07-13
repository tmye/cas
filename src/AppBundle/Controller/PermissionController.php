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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Session\Session;



class PermissionController extends Controller {

    /**
     * @Route("/addPermission",name="addPermission")
     */
    public function addPermissionAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
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
                ->add('timeFrom', TextType::class,array('label'=>' '))
                ->add('dateTo', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
                ->add('timeTo', TextType::class,array('label'=>' '))
                ->add('employee',EntityType::class,array(
                    'em'=>$session->get("connection"),
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

                    $em = $this->getDoctrine()->getManager($session->get("connection"));

                    $dateFrom = $request->request->get("form")["dateFrom"];
                    $dateTo = $request->request->get("form")["dateTo"];
                    $sentTimeFrom = $request->request->get("form")["timeFrom"];
                    $sentTimeTo = $request->request->get("form")["timeTo"];
                    $timeFrom = strtotime($dateFrom." 00:00:00");
                    $timeTo = strtotime($dateTo." 00:00:00");

                    $timeDays = $timeTo-$timeFrom;

                    $days = $timeDays/(60*60*24);

                    $nowTime = strtotime($dateFrom." 00:00:00");

                     /*
                      * If days > 0 it means that the permission is extended on many days
                      * */
                    if($days > 0){
                        for ($i=0;$i<=$days;$i++) {
                            //print_r("<br>$i - NowTime : " . $nowTime . "<br>");
                            $permission = new Permission();

                            $empId = (int)$request->request->get("form")["employee"];
                            $emp = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:employe")->find($empId);

                            $permission->setTitle($request->request->get("form")["title"]);
                            $permission->setDescription($request->request->get("form")["description"]);
                            $permission->setEmployee($emp);

                            $permission->setUpdateTime(new \DateTime());
                            $permission->setState(0);
                            $permission->setCreateTime(new \DateTime());
                            $permission->setAskerId($this->getUser()->getId());
                            $permission->setDateFrom(new \DateTime(date('Y-m-d',$nowTime)));
                            if($i==0){
                                $permission->setTimeFrom($sentTimeFrom);
                            }
                            if($i==($days)){
                                $permission->setTimeTo($sentTimeTo);
                            }else{
                                $permission->setTimeTo("23:59");
                            }

                            $em->persist($permission);
                            $em->flush();

                            $nowTime += 86400;
                         }
                    }else{
                        $permission->setUpdateTime(new \DateTime());
                        $permission->setState(0);
                        $permission->setCreateTime(new \DateTime());
                        $permission->setAskerId($this->getUser()->getId());


                        $em->persist($permission);
                        $em->flush();
                    }

                    $request->getSession()->getFlashBag()->add('notice', 'Employé bien enregistrée.');

                    return $this->render("cas/addPermission.html.twig",array(
                        'form' => $form->createView(),
                        'message'=>"Cette permission a été ajoutée avec succès"
                    ));

                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('cas/addPermission.html.twig', array(
                'form' => $form->createView(),
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }
    /**
     * @Route("/editPermission/{id}",name="editPermission")
     */
    public function editPermissionAction(Request $request,$id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $permission = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission")->find($id);

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
                ->add('timeFrom', TextType::class, array('label' => ' '))
                ->add('dateTo', DateTimeType::class)
                ->add('timeTo', TextType::class, array('label' => ' '))
                ->add('state', IntegerType::class)
                ->add('employee', EntityType::class, array(
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
                    $em = $this->getDoctrine()->getManager($session->get("connection"));

                    $em->persist($permission);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Permission bien modifiée.');

                    return $this->render("cas/addPermission.html.twig", array(
                        'form' => $form->createView(),
                        'message' => "Cette permission a été modifiée avec succès"
                    ));
                }
            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('cas/addPermission.html.twig', array(
                'form' => $form->createView(),
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/viewPermission",name="viewPermission")
     */
    public function viewPermissionAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $permRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission");

            $numberOfStack = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission")->countPermission(0);
            $numberOfGranted = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission")->countPermission(1);
            $numberOfRefused = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission")->countPermission(2);

            $listPerm = $permRep->findByOrder();
            return $this->render('cas/viewPermission.html.twig', array(
                'listPerm' => $listPerm,
                'stack'=>$numberOfStack,
                'granted'=>$numberOfGranted,
                'refused'=>$numberOfRefused
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/deletePermission/{id}" ,name="deletePermision")
     */
    public function deleteEmployeeAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $perm = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Permission')->find($id);
            if ($perm != null) {
                $em = $this->getDoctrine()->getManager($session->get("connection"));
                $em->remove($perm);
                $em->flush();
                return $this->render("cas/addPermission.html.twig",array(
                    'message'=>"Cette permission a été supprimée avec succès"
                ));
            } else{
                throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/grantPermission/{id}" ,name="grantPermision")
     */
    public function grantPermissionAction(Request $request, $id)
    {
        $session = new Session();

        $perm = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Permission')->find($id);
        if ($perm != null) {
            $perm->setState(1);
            $em = $this->getDoctrine()->getManager($session->get("connection"));
            $em->flush();
            $permRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission");
            $listPerm = $permRep->findByOrder();
            return $this->render("cas/viewPermission.html.twig",array(
                'message'=>"Cette permission a été accordée",
                'listPerm'=>$listPerm
            ));
        } else{
            throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/rejectPermission/{id}" ,name="rejectPermision")
     */
    public function rejectPermissionAction(Request $request, $id)
    {
        $session = new Session();

        $perm = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Permission')->find($id);
        if ($perm != null) {
            $perm->setState(2);
            $em = $this->getDoctrine()->getManager($session->get("connection"));
            $em->flush();
            $permRep = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Permission");
            $listPerm = $permRep->findByOrder();
            return $this->render("cas/viewPermission.html.twig",array(
                'message'=>"Cette permission a été rejetée",
                'listPerm'=>$listPerm
            ));
        } else{
            throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
        }
    }
}