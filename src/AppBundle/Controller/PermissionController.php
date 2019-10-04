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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Session\Session;
use TmyeDeviceBundle\Controller\BaseController;


class PermissionController extends BaseController {

    /**
     * @Route("/addPermission",name="addPermission")
     */
    public function addPermissionAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
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
                    'label'=>' ',
                    'class' => 'AppBundle:Employe',
                    'choice_label' => function ($employee) {
                        return $employee->getEmployeeCcid().' '.$employee->getSurname() . ' ' . $employee->getLastName();
                    },
                    'multiple' => false,
                ))
                ->add('creer', SubmitType::class);
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $em = $this->getDoctrine()->getManager();

                    $dateFrom = $request->request->get("form")["dateFrom"];
                    $dateTo = $request->request->get("form")["dateTo"];
                    $sentTimeFrom = $request->request->get("form")["timeFrom"];
                    $sentTimeTo = $request->request->get("form")["timeTo"];
                    $timeFrom = strtotime($dateFrom." ".$sentTimeFrom);
                    $timeTo = strtotime($dateTo." ".$sentTimeTo);

//                    if($timeFrom >= time()) {
                        $nowTime = strtotime($dateFrom." 00:00:00");

                                //print_r("<br>$i - NowTime : " . $nowTime . "<br>");
                                $permission = new Permission();
    
                                $empId = (int)$request->request->get("form")["employee"];
                                $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:employe")->find($empId);
    
                                $permission->setTitle($request->request->get("form")["title"]);
                                $permission->setDescription($request->request->get("form")["description"]);
                                $permission->setEmployee($emp);
    
                                $permission->setUpdateTime(new \DateTime());
                                $permission->setState(0);
                                $permission->setCreateTime(new \DateTime());
                                $permission->setAskerId($this->getUser()->getId());
                                $permission->setDateFrom(new \DateTime(date('Y-m-d',$timeFrom)));
                                $permission->setDateTo(new \DateTime(date('Y-m-d',$timeTo)));

                                $permission->setTimeFrom($sentTimeFrom);

                                $permission->setTimeTo($sentTimeTo);

                                $em->persist($permission);
                                $em->flush();

                        $this->get('session')->getFlashBag()->set('notice', 'Cette permission a bien été enregistrée.');
                        //$request->getSession()->getFlashBag()->add('notice', 'Cette permission a bien été enregistrée.');
    
                        return $this->redirectToRoute("addPermission");
                    /* }else{
                         $this->get('session')->getFlashBag()->set('error_notice', 'Enregistrement non effectué : vous ne pouvez pas demander une permission pour une date déjà passée!');
                         return $this->redirectToRoute("addPermission");
                     }*/
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

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

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
                    $em = $this->getDoctrine()->getManager();

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
            $this->get('session')->getFlashBag()->set('error_notice', 'Vous n\'avez pas les droits nécessaires pour modifier une permission.');
            return $this->redirectToRoute("viewPermission");
        }
    }

    /**
     * @Route("/viewPermission",name="viewPermission")
     */
    public function viewPermissionAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }


            $resultArray = $this->findPermissionState($request);

            return $this->render("cas/viewPermission.html.twig" , array(
                'listPermEnd' =>  $resultArray["listPermEnd"],
                'listPermCrs' =>  $resultArray["listPermCrs"],
                'listPermCom' =>  $resultArray["listPermCom"],
                'nbPermEnd'=> $resultArray["nbPermEnd"],
                'nbPermCrs'=>$resultArray["nbPermCrs"],
                'nbPermCom'=>$resultArray["nbPermCom"]
            ));

        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/viewPermissionData",name="viewPermissionData")
     */
    public function viewPermissionData(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $empRep = $this-> EmployeeRepo();
            $deptRep = $this-> DepartementRepo();
            $empRep = $empRep->findAll();
            $deptRep = $deptRep -> findAll();
            $permRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");
//            $numberOfStack = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(0);
//            $numberOfGranted = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(1);
//            $numberOfRefused = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(2);


            $listPerm = $permRep->findAll();

            return new JsonResponse($listPerm);
        }else{
            return $this->redirectToRoute("login");
        }
    }


    /**
     * @Route("/deletePermission/{id}" ,name="deletePermision")
     */
    public function deletePermissionAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
            if ($perm != null) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($perm);
                $em->flush();
                return $this->render("cas/addPermission.html.twig",array(
                    'message'=>"Cette permission a été supprimée avec succès"
                ));
            } else{
                throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
            }
        }else{
            $this->get('session')->getFlashBag()->set('error_notice', 'Vous n\'avez pas les droits nécessaires pour supprimer une permission.');
            return $this->redirectToRoute("viewPermission");
        }
    }

    /**
     * @Route("/grantPermission/{id}" ,name="grantPermision")
     */
    public function grantPermissionAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
            if ($perm != null) {
                $perm->setState(1);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $permRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");

                $numberOfStack = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(0);
                $numberOfGranted = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(1);
                $numberOfRefused = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(2);

                $listPerm = $permRep->findByOrder();
                return $this->render("cas/viewPermission.html.twig",array(
                    'message'=>"Cette permission a été accordée",
                    'listPerm'=>$listPerm,
                    'stack'=>$numberOfStack,
                    'granted'=>$numberOfGranted,
                    'refused'=>$numberOfRefused
                ));
            } else{
                throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
            }
        }else{
            $this->get('session')->getFlashBag()->set('error_notice', 'Vous n\'avez pas les droits nécessaires pour traiter une permission.');
            return $this->redirectToRoute("viewPermission");
        }
    }

    /**
     * @Route("/rejectPermission/{id}" ,name="rejectPermision")
     */
    public function rejectPermissionAction(Request $request, $id)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $perm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Permission')->find($id);
            if ($perm != null) {
                $perm->setState(2);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $permRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");

                $numberOfStack = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(0);
                $numberOfGranted = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(1);
                $numberOfRefused = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(2);

                $listPerm = $permRep->findByOrder();
                return $this->render("cas/viewPermission.html.twig",array(
                    'message'=>"Cette permission a été rejetée",
                    'listPerm'=>$listPerm,
                    'stack'=>$numberOfStack,
                    'granted'=>$numberOfGranted,
                    'refused'=>$numberOfRefused
                ));
            } else{
                throw new NotFoundHttpException("La permission d'id " . $id . " n'existe pas.");
            }
        }else{
            $this->get('session')->getFlashBag()->set('error_notice', 'Vous n\'avez pas les droits nécessaires pour traiter une permission.');
            return $this->redirectToRoute("viewPermission");
        }
    }

    /**
     * @Route("/findPermissionState", name="findPermissionState")
     */

    public function findPermissionState(Request $request){
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            $empRep = $this-> EmployeeRepo();
            $deptRep = $this-> DepartementRepo();
            $empRep = $empRep->findAll();
            $deptRep = $deptRep -> findAll();
            $permEnd = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findEndPerms();
            $permEnCours = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findPermEnCours();
            $permComing = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findComingPerms();

            $nbEnd = 0;
            $nbCrs = 0;
            $nbCom = 0;

            //$listPerm = $permRep ->findAll();
            $listPermEnd=array();
            $listPermCrs = $permEnCours;
            $listPermCom = array();

            foreach (  $permEnd as $end){

                $diff =date_diff($end->dateTo, new \DateTime('now'));
                $diff = $diff->format("%a");

                if( $diff <=30  ){
                    array_push($listPermEnd, $end);
                    $nbEnd++;
                }

            }

            foreach (  $permComing as $com){

                $diff =date_diff( new \DateTime('now'),$end->dateTo);
                $diff = $diff->format("%a");

                if( $diff <=30  ){
                    array_push($listPermCom, $com);
                    $nbCom++;
                }

            }

            foreach (  $listPermCrs as $crs){
                $nbCrs++;
            }

            return array(
                'listPermEnd' => $listPermEnd,
                'listPermCrs'=>$listPermCrs,
                'listPermCom'=>$listPermCom,
                'nbPermEnd' => $nbEnd,
                'nbPermCrs' => $nbCrs,
                'nbPermCom' => $nbCom

            );
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/findPermissionStatee", name="findPermissionStatee")
     */

    public function findPermissionStatee(Request $request){
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            $empRep = $this-> EmployeeRepo();
            $deptRep = $this-> DepartementRepo();
            $empRep = $empRep->findAll();
            $deptRep = $deptRep -> findAll();
            $permEnd = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findEndPerms();
            $permEnCours = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findPermEnCours();
            $permComing = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->findComingPerms();

            $nbEnd = 0;
            $nbCrs = 0;
            $nbCom = 0;

            //$listPerm = $permRep ->findAll();$permEnd
            $listPermEnd=array();
            $listPermCrs = $permEnCours;
            $listPermCom = array();

            foreach (  $permEnd as $end){

                $diff =date_diff($end->dateTo, new \DateTime('now'));
                $diff = $diff->format("%a");

                if( $diff <=30  ){
                   array_push($listPermEnd, $end);
                    $nbEnd++;
                }

            }

            foreach (  $permComing as $com){

                $diff =date_diff( new \DateTime('now'),$end->dateTo);
                $diff = $diff->format("%a");

                if( $diff <=30  ){
                    array_push($listPermCom, $com);
                    $nbCom++;
                }

            }

            foreach (  $listPermCrs as $crs){
                $nbCrs++;
            }

            return new JsonResponse( array(
                'listPermEnd' => $listPermEnd,
                'listPermCrs'=>$listPermCrs,
                'listPermCom'=>$listPermCom,
                'nbPermEnd' => $nbEnd,
                'nbPermCom' => $nbCom,
                'nbPermCrs' => $nbCrs
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }



}