<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Permission;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
use TmyeDeviceBundle\Controller\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


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
                ->add('type', ChoiceType::class,array('multiple'=>false, 'label'=>false, 'choices' => [
                    1,2,3
                ], 'choice_label' => function ($choice) {
                    $typeChoices = [1=>'Repos maladie', 2=>'Congés', 3=>'Autre'];
                    return $typeChoices[$choice];
                } ))
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

//                    var_dump(intval($request->request->get("form")["type"]));

//                    if($timeFrom >= time()) {
                    $nowTime = strtotime($dateFrom." 00:00:00");

                    //print_r("<br>$i - NowTime : " . $nowTime . "<br>");
                    $permission = new Permission();

                    $empId = intval($request->request->get("form")["employee"]);
                    $emp = $this->getDoctrine()->getManager()->getRepository("AppBundle:employe")->find($empId);

                    $permission->setType(intval($request->request->get("form")["type"]));
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
                ->add('type', ChoiceType::class,array('multiple'=>false, 'label'=>false, 'choices' => [
                    0,1,2
                ], 'choice_label' => function ($choice) {
                    $typeChoices = [0=>'Congés', 1=>'Repos maladie', 2=>'Autre'];
                    return $typeChoices[$choice];
                } ))
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
            $permRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission");

            $numberOfStack = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(0);
            $numberOfGranted = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(1);
            $numberOfRefused = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->countPermission(2);

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
     * @Route("/statistiquesPermission" ,name="statistiquesPermissions")
     */
    public function statistiquesPermissionAction(Request $request)
    {

        // from a date to another.
        set_time_limit(0);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $em = $this->getDoctrine()->getManager();
            $listEmployee = $em->getRepository("AppBundle:Employe")->findAll();

            $dep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/rapports_permissions.html.twig',array(
                'listDep'=>$dep,
                'listEmployee'=>$listEmployee
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/generatePermissionExcel",name="generatePermissionExcel")
     */
    public function generatePermissionExcelAction(Request $request)
    {

        set_time_limit(0);

        // title
        $miniTitleStyleArray = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FFcccccc',
                ]
            ],
        ];

        // total resumes
        $totalResumeStyleArray = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FF50C547',
                ]
            ],
        ];




        $t = $request->request->get('type');
        $empId = $request->request->get('destination');
        $dateType = $request->request->get('dateType');

        if ($dateType != "0" && $dateType != null) {
            $fromDate = "01-" . $dateType . "-" . date('Y');
            $toDate = date('t-m-Y', strtotime($fromDate));
        } else {
            $fromDate = $request->request->get('fromDate');
            $toDate = $request->request->get('toDate');
        }

        $permissionInTimePeriodz = [];

        foreach ($empId as $emp) {
            set_time_limit(0);
//            $nowTime = $timeFrom;

            $employee = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
            $myPermissions = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")
                ->permissionFromToDate($employee, $fromDate, $toDate);

            $permissionInTimePeriodz[$emp]["employee"] = $employee;
            $permissionInTimePeriodz[$emp]["permissions"] = $myPermissions;
            // for each employee, check if they have a permission during this time period
            // if they have, then keep track of them and of those permissions,

            // compute the count of those permissions and also the
            $permissionInTimePeriodz[$emp]["count"] = count($myPermissions);

            if (count($myPermissions) == 0) {
                unset($permissionInTimePeriodz[$emp]);
                continue;
            }

            // compute the quantity of time that its make
            $permissionInTimePeriodz[$emp]["time"] = $this->permissionsTime($myPermissions);
            $permissionInTimePeriodz[$emp]["daysCount"] = $this->permissionsDays($myPermissions);

            $type1Permission = [];
            $type2Permission = [];
            $type3Permission = [];

            foreach ($myPermissions as $permission) {

//                $permissionInTimePeriodz[$emp]["permission_type"][$permission->getType()][] = $permission;
                if ($permission->getType() == 1) {
                    $type1Permission[] = $permission;
                }
                if ($permission->getType() == 2) {
                    $type2Permission[] = $permission;
                }
                if ($permission->getType() == 3) {
                    $type3Permission[] = $permission;
                }
            }

            // totalize the amount of time and the count people done of this different permissions
            $permissionInTimePeriodz[$emp]["type_1_count"] = count($type1Permission);
            $permissionInTimePeriodz[$emp]["type_2_count"] = count($type2Permission);
            $permissionInTimePeriodz[$emp]["type_3_count"] = count($type3Permission);

            $permissionInTimePeriodz[$emp]["type_1_time"] = $this->permissionsTime($type1Permission);
            $permissionInTimePeriodz[$emp]["type_2_time"] = $this->permissionsTime($type2Permission);
            $permissionInTimePeriodz[$emp]["type_3_time"] = $this->permissionsTime($type3Permission);

            $permissionInTimePeriodz[$emp]["type_1_days"] = $this->permissionsDays($type1Permission);
            $permissionInTimePeriodz[$emp]["type_2_days"] = $this->permissionsDays($type2Permission);
            $permissionInTimePeriodz[$emp]["type_3_days"] = $this->permissionsDays($type3Permission);
        }

        $tmp = [];
        $maxsize = count($permissionInTimePeriodz);

        for (; count($tmp) < $maxsize; ){

            $biggest_count = 0;
            $biggest = [];

            foreach ($permissionInTimePeriodz as $permissionInTimePeriod) {
//            for ($j = 0; $j < count($permissionInTimePeriodz); $j++) {

                if ($biggest_count == 0){
                    $biggest_count = $permissionInTimePeriod["time"];
                    $biggest = $permissionInTimePeriod;
                }

                // get the smaller and remove it and start again
                if ($permissionInTimePeriod["time"]> $biggest_count) {
                    $biggest_count = $permissionInTimePeriod["time"];
                    $biggest = $permissionInTimePeriod;
                }
            }

            // we have the current biggest count, and we remove it from the list.
            $tmp[] = $biggest;
//            echo "biggest $biggest_count"."<br/>";
//            var_dump(json_encode($bigest_count["employee"], true));exit;
            if (isset($biggest["employee"]) && $biggest["employee"]!=null)
                unset($permissionInTimePeriodz[$biggest["employee"]->getId()]);
        }

        $permissionInTimePeriodz  = $tmp;


        $beginNameCellNumber = 5;
        $nextNameCellNumber = 5;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $boldStyle = [
            'font' => [
                'name' => 'Arial',
                'bold' => true,
            ],
            'quotePrefix' => true
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($boldStyle);
        $sheet->setCellValue('A1', 'BILAN PERMISSIONS DU ' . $fromDate . ' AU ' . $toDate);

        $nextNameCellNumber = 5;

        // loop on the permission array
        foreach ($permissionInTimePeriodz as $permissionInTimePeriod) {

            if ($permissionInTimePeriod["count"] == 0)
                continue;

            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber - 1))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('B' . ($nextNameCellNumber - 1))->applyFromArray($boldStyle);
            $sheet->setCellValue('A' . ($nextNameCellNumber - 1), "NOM");
            $sheet->setCellValue('B' . ($nextNameCellNumber - 1), "PRENOMS");
            $sheet->setCellValue('A' . ($nextNameCellNumber), $permissionInTimePeriod["employee"]->getSurname());
            $sheet->setCellValue('B' . ($nextNameCellNumber), $permissionInTimePeriod["employee"]->getLastname());

            $nextNameCellNumber+=2;

            // colorer ces cells la comme des titres

            $sheet->setCellValue('A' . ($nextNameCellNumber - 1), "TYPE");
            $sheet->setCellValue('B' . ($nextNameCellNumber - 1), "DEBUT");
            $sheet->setCellValue('C' . ($nextNameCellNumber - 1), "FIN");
            $sheet->setCellValue('D' . ($nextNameCellNumber - 1), "DUREE (H)");
            $sheet->setCellValue('E' . ($nextNameCellNumber - 1), "NOMBRE DE JOURS");
            $sheet->setCellValue('F' . ($nextNameCellNumber - 1), "DESCRIPTION");
            $sheet->setCellValue('G' . ($nextNameCellNumber - 1), "DATE DE DEMANDE");

            $sheet->getStyle('A' . ($nextNameCellNumber - 1).':'.'G' . ($nextNameCellNumber -1))
                ->applyFromArray($miniTitleStyleArray);

            // push all the permission with all the details
            // type -> debut -> fin -> duree -> description -> demandee le...
            foreach ($permissionInTimePeriod["permissions"] as $permission) {

                $nextNameCellNumber++;
                $sheet->setCellValue('A' . ($nextNameCellNumber - 1), [1=>'Repos maladie', 2=>'Congés', 3=>'Autre'][$permission->getType()]);
                $sheet->setCellValue('B' . ($nextNameCellNumber - 1), date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom());
                $sheet->setCellValue('C' . ($nextNameCellNumber - 1), date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo());
                $sheet->setCellValue('D' . ($nextNameCellNumber - 1), $this->permissionsTime([$permission]));
                $sheet->setCellValue('E' . ($nextNameCellNumber - 1), $this->permissionsDays([$permission]));
                $sheet->setCellValue('F' . ($nextNameCellNumber - 1), $permission->getDescription());
                $sheet->setCellValue('G' . ($nextNameCellNumber - 1), date('Y-m-d H:i',$permission->getCreateTime()->getTimestamp()));
            }
            $nextNameCellNumber+=2;
            // nombre de permissions total
            $sheet->setCellValue('A' . ($nextNameCellNumber - 1), "NOM PERMISSIONS TOTAL");
            $sheet->setCellValue('B' . ($nextNameCellNumber - 1), $permissionInTimePeriod["count"]);
            $sheet->setCellValue('A' . ($nextNameCellNumber), "CUMULÉS TOTAL HEURES");
            $sheet->setCellValue('B' . ($nextNameCellNumber), $this->permissionsTime($permissionInTimePeriod["permissions"]));
            $sheet->setCellValue('A' . ($nextNameCellNumber+1 ), "NOMBRE TOTAL JOURS");
            $sheet->setCellValue('B' . ($nextNameCellNumber+1 ), $this->permissionsDays($permissionInTimePeriod["permissions"]));

            // these final cells must be in orange
            $sheet->getStyle('A' . ($nextNameCellNumber - 1).':'.'B' . ($nextNameCellNumber +1))
                ->applyFromArray($totalResumeStyleArray);

            // style A and B cells
            $sheet->getStyle('A'.($nextNameCellNumber - 1))->applyFromArray($boldStyle);
            $sheet->getStyle('A'.($nextNameCellNumber))->applyFromArray($boldStyle);
            $sheet->getStyle('A'.($nextNameCellNumber + 1))->applyFromArray($boldStyle);


            $nextNameCellNumber+=4;
        }

        // ranking of permission according to type

//        [1=>'Repos maladie', 2=>'Congés', 3=>'Autre'];
        $type1sheet = $spreadsheet->createSheet(1); // 1 -> repos maladie
        $type2sheet = $spreadsheet->createSheet(2); // 2 -> conges
        $type3sheet = $spreadsheet->createSheet(3); // 3 -> autre


        $sheet->setTitle("GENERAL");
        $type1sheet->setTitle("Maladie");
        $type2sheet->setTitle("Congés");
        $type3sheet->setTitle("Autre");

        $nextNameCellNumber_1 = 5;
        $nextNameCellNumber_2 = 5;
        $nextNameCellNumber_3 = 5;

        $sheet->getDefaultColumnDimension()->setWidth(16);
        $sheet->getColumnDimension("A")->setWidth(26);
        $sheet->getColumnDimension("D")->setWidth(10);
        $sheet->getColumnDimension("E")->setWidth(10);

        $type1sheet->getDefaultColumnDimension()->setWidth(16);
        $type2sheet->getDefaultColumnDimension()->setWidth(16);
        $type3sheet->getDefaultColumnDimension()->setWidth(16);

        $type1sheet->getStyle('A' . ($nextNameCellNumber_1 - 1).':'.'E' . ($nextNameCellNumber_1 -1))
            ->applyFromArray($miniTitleStyleArray);
        $type1sheet->getStyle('A' . ($nextNameCellNumber_1 - 1).':'.'E' . ($nextNameCellNumber_1 -1))
            ->applyFromArray($boldStyle);
        $type1sheet->setCellValue('A' . ($nextNameCellNumber_1 - 1), "NOM");
        $type1sheet->setCellValue('B' . ($nextNameCellNumber_1 - 1), "PRENOMS");
        $type1sheet->setCellValue('C' . ($nextNameCellNumber_1 - 1), "NOMBRE DE PERMISSIONS");
        $type1sheet->setCellValue('D' . ($nextNameCellNumber_1 - 1), "CUMULÉ EN TEMPS(H)");
        $type1sheet->setCellValue('E' . ($nextNameCellNumber_1 - 1), "NOMBRE DE JOURS");


        $type2sheet->getStyle('A' . ($nextNameCellNumber_2 - 1).':'.'E' . ($nextNameCellNumber_2 -1))
            ->applyFromArray($miniTitleStyleArray);
        $type2sheet->getStyle('A' . ($nextNameCellNumber_2 - 1).':'.'E' . ($nextNameCellNumber_2 -1))
            ->applyFromArray($boldStyle);
        $type2sheet->setCellValue('A' . ($nextNameCellNumber_2 - 1), "NOM");
        $type2sheet->setCellValue('B' . ($nextNameCellNumber_2 - 1), "PRENOMS");
        $type2sheet->setCellValue('C' . ($nextNameCellNumber_2 - 1), "NOMBRE DE PERMISSIONS");
        $type2sheet->setCellValue('D' . ($nextNameCellNumber_2 - 1), "CUMULÉ EN TEMPS(H)");
        $type2sheet->setCellValue('E' . ($nextNameCellNumber_2 - 1), "NOMBRE DE JOURS");

        $type3sheet->getStyle('A' . ($nextNameCellNumber_3 - 1).':'.'E' . ($nextNameCellNumber_3 -1))
            ->applyFromArray($miniTitleStyleArray);
        $type3sheet->getStyle('A' . ($nextNameCellNumber_3 - 1).':'.'E' . ($nextNameCellNumber_3 -1))
            ->applyFromArray($boldStyle);
        $type3sheet->getStyle('A' . ($nextNameCellNumber_3 - 1))->applyFromArray($boldStyle);
        $type3sheet->setCellValue('A' . ($nextNameCellNumber_3 - 1), "NOM");
        $type3sheet->setCellValue('B' . ($nextNameCellNumber_3 - 1), "PRENOMS");
        $type3sheet->setCellValue('C' . ($nextNameCellNumber_3 - 1), "NOMBRE DE PERMISSIONS");
        $type3sheet->setCellValue('D' . ($nextNameCellNumber_3 - 1), "CUMULÉ EN TEMPS(H)");
        $type3sheet->setCellValue('E' . ($nextNameCellNumber_3 - 1), "NOMBRE DE JOURS");

        $nextNameCellNumber_1++;
        $nextNameCellNumber_2++;
        $nextNameCellNumber_3++;

        foreach ($permissionInTimePeriodz as $permissionInTimePeriod) {

            if ($permissionInTimePeriod == null)
                continue;

            if ($permissionInTimePeriod["type_1_count"] != 0) {
                $type1sheet->setCellValue('A' . ($nextNameCellNumber_1), $permissionInTimePeriod["employee"]->getSurname());
                $type1sheet->setCellValue('B' . ($nextNameCellNumber_1), $permissionInTimePeriod["employee"]->getLastname());
                $type1sheet->setCellValue('C' . ($nextNameCellNumber_1), $permissionInTimePeriod["type_1_count"]);
                $type1sheet->setCellValue('D' . ($nextNameCellNumber_1), $permissionInTimePeriod["type_1_time"]);
                $type1sheet->setCellValue('E' . ($nextNameCellNumber_1), $permissionInTimePeriod["type_1_days"]);
                $nextNameCellNumber_1++;
            }

            if ($permissionInTimePeriod["type_2_count"] != 0) {
                $type2sheet->setCellValue('A' . ($nextNameCellNumber_2), $permissionInTimePeriod["employee"]->getSurname());
                $type2sheet->setCellValue('B' . ($nextNameCellNumber_2), $permissionInTimePeriod["employee"]->getLastname());
                $type2sheet->setCellValue('C' . ($nextNameCellNumber_2), $permissionInTimePeriod["type_2_count"]);
                $type2sheet->setCellValue('D' . ($nextNameCellNumber_2), $permissionInTimePeriod["type_2_time"]);
                $type2sheet->setCellValue('E' . ($nextNameCellNumber_2), $permissionInTimePeriod["type_2_days"]);
                $nextNameCellNumber_2++;
            }

            if ($permissionInTimePeriod["type_3_count"] != 0) {
                $type3sheet->setCellValue('A' . ($nextNameCellNumber_3), $permissionInTimePeriod["employee"]->getSurname());
                $type3sheet->setCellValue('B' . ($nextNameCellNumber_3), $permissionInTimePeriod["employee"]->getLastname());
                $type3sheet->setCellValue('C' . ($nextNameCellNumber_3), $permissionInTimePeriod["type_3_count"]);
                $type3sheet->setCellValue('D' . ($nextNameCellNumber_3), $permissionInTimePeriod["type_3_time"]);
                $type3sheet->setCellValue('E' . ($nextNameCellNumber_3), $permissionInTimePeriod["type_3_days"]);
                $nextNameCellNumber_3++;
            }


        }



        $writer = new Xlsx($spreadsheet);
        $now_date = date('d') . "-" . date('m') . '-' . date('Y') . '_' . date('H') . ':' . date('i') . ':' . date('s');
        $writer->save('cache/' . $this->getUser()->getUsername() . '_bilan_permissions_' . $now_date . '.xlsx');

        //sleep(10);

        $filePath = $this->getParameter("web_dir") . "/cache/" . $this->getUser()->getUsername() . "_bilan_permissions_" . $now_date . ".xlsx";

        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $this->getUser()->getUsername() . "_bilan_permissions_" . $now_date . ".xlsx",
            iconv('UTF-8', 'ASCII//TRANSLIT', $this->getUser()->getUsername() . "_bilan_permissions_" . $now_date . ".xlsx")
        );
        return $response;
    }


}