<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Journal;
use AppBundle\Form\DepartementType;
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

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as xlswriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TmyeDeviceBundle\Controller\BaseController;

class EmployeController extends BaseController {

    /**
     * @Route("/importEmployees",name="importEmployees")
     */
    public function importEmployeesAction(Request $request)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $employe = new Employe();
            $employe->setLastUpdate(new \DateTime());
            $employe->setCreateDate(new \DateTime());
            $employe->setEmployeeCcid(10000);
            $employe->setPassword(md5($this->getParameter("default_password")));
            $employe->setPicture($this->getDefaultPicture());
            //$employe->setGodfatherCcid($this->getUser()->getId());
            $employe->setGodfatherCcid(0);

            // On crée le FormBuilder grâce au service form factory


            if ($request->isMethod('POST')) {

                $em = $this->getDoctrine()->getManager();
                $file_name_arr = explode(".",$_FILES["excelFile"]["name"]);
                    $extension = end($file_name_arr);
                    if(($extension == "xlsx") || ($extension == "xls")){
                        // We are in the case of a valid excel file
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $spreadsheet = $reader->load($_FILES['excelFile']['tmp_name']);
                        $sheetData = $spreadsheet->getActiveSheet()->toArray();
                        $i=0;
                        foreach ($sheetData as $empArr){
                            if($i>0){
                                $employe = new Employe();
                                $departement = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->find($empArr[0]);
                                $working_hour = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->find($empArr[1]);
                                $employe->setDepartement($departement);
                                $employe->setWorkingHour($working_hour);
                                $employe->setSurname($empArr[2]);
                                $employe->setLastName($empArr[3]);
                                $employe->setShortName($empArr[4]);
                                $employe->setAdress($empArr[5]);
                                $employe->setContact($empArr[6]);
                                $employe->setSalary($empArr[7]);
                                $employe->setFunction($empArr[8]);


                                if((trim($empArr[9]) != null) && (trim($empArr[9]) != null)){
                                    $hireTime = strtotime($empArr[9]." 00:00:00");
                                    $hireDate = new \DateTime();
                                    $hireDate->setTimestamp($hireTime);
                                }else{
                                    print_r("Erreur : la date n'est pas bien formatée");
                                    $hireDate = null;
                                }

                                $employe->setHireDate($hireDate);
                                $employe->setEmployeeCcid(10000);
                                $employe->setGodfatherCcid($this->getUser()->getId());
                                $employe->setPassword(md5(555));
                                $employe->setCreateDate(new \DateTime());

                                $em->persist($employe);
                                // First flush to get the last ID
                                $em->flush();
                                $last_id = $employe->getId();
                                $employe->setEmployeeCcid(10000 + $last_id);
                                $employe->setUsername($employe->getEmployeeCcid());

                                $journal = new Journal();
                                $journal->setCrudType('C');
                                $journal->setAuthor($this->getUser()->getName().' '.$this->getUser()->getSurname());
                                $journal->setDescription($journal->getAuthor()." a importé des employés depuis un fichier excel");
                                $em->persist($journal);

                                // Final flush
                                $em->flush();
                            }
                            $i++;
                        }
                    }else{
                        print_r("Erreur d'extension");
                    }

                    //$wh = $this->returnWorkingHoursAction();
                    $this->get('session')->getFlashBag()->set('notice', 'Ces employés ont été importés avec succès');
                    return $this->redirectToRoute("importEmployees");
            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

            $wh = $this->returnWorkingHoursAction();
            return $this->render('cas/importEmployees.html.twig');
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/exportEmployees",name="exportEmployees")
     */
    public function exportEmployeesAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');

            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            return $this->render('cas/exportEmployees.html.twig');
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/exportEmployeesExcel",name="exportEmployeesExcel")
     */
    public function exportEmployeesExcelAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');

            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', "departement_id");
            $sheet->setCellValue('B1', "working_hour_id");
            $sheet->setCellValue('C1', "surname");
            $sheet->setCellValue('D1', "last_name");
            $sheet->setCellValue('E1', "short_name");
            $sheet->setCellValue('F1', "adress");
            $sheet->setCellValue('G1', "contact");
            $sheet->setCellValue('H1', "salary");
            $sheet->setCellValue('I1', "function");
            $sheet->setCellValue('J1', "hire_date");
            $sheet->setCellValue('K1', "departement_name");

            $employees = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();

            $i = 1;
            foreach ($employees as $employe){
                $i++;
                $sheet->setCellValue('A'.$i, $employe->getDepartement()->getId());
                $sheet->setCellValue('B'.$i, $employe->getWorkingHour()->getId());
                $sheet->setCellValue('C'.$i, $employe->getSurname());
                $sheet->setCellValue('D'.$i, $employe->getLastName());
                $sheet->setCellValue('E'.$i, $employe->getShortName());
                $sheet->setCellValue('F'.$i, $employe->getAdress());
                $sheet->setCellValue('G'.$i, $employe->getContact());
                $sheet->setCellValue('H'.$i, $employe->getSalary());
                $sheet->setCellValue('I'.$i, $employe->getFunction());
                $sheet->setCellValue('J'.$i, $employe->getHireDate());
                $sheet->setCellValue('K'.$i, $employe->getDepartement()->getName());
            }

            $writer = new xlswriter($spreadsheet);
            $now_date = date('d')."-".date('m').'-'.date('Y').'_'.date('H').':'.date('i').':'.date('s');
            $writer->save('employes_'.$now_date.'.xlsx');
            $filePath = $this->getParameter("web_dir")."/employes_".$now_date.".xlsx";

            $response = new BinaryFileResponse($filePath);
            $response->trustXSendfileTypeHeader();
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                "employes_".$now_date.".xlsx",
                iconv('UTF-8', 'ASCII//TRANSLIT', "employes_".$now_date.".xlsx")
            );

            $journal = new Journal();
            $journal->setCrudType('R');
            $journal->setAuthor($this->getUser()->getName().' '.$this->getUser()->getSurname());
            $journal->setDescription($journal->getAuthor()." a exporté des employés vers un fichier excel");
            $em = $this->getDoctrine()->getManager();
            $em->persist($journal);
            $em->flush();

            return $response;

        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/addEmployee",name="addEmployee")
     */
    public function addEmployeeAction(Request $request)
    {

        //print_r($this->get('session')->getFlashBag()->get('notice'));

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $employe = new Employe();
            $employe->setLastUpdate(new \DateTime());
            $employe->setCreateDate(new \DateTime());
            $employe->setEmployeeCcid(10000);
            $employe->setPassword(md5($this->getParameter("default_password")));
            $employe->setPicture($this->getDefaultPicture());
            //$employe->setGodfatherCcid($this->getUser()->getId());
            $employe->setGodfatherCcid(0);

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $employe);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('surname', TextType::class,array('label'=>' '))
                ->add('last_name', TextType::class,array('label'=>' '))
                ->add('adress', TextType::class,array('label'=>' '))
                ->add('contact', TextType::class,array('label'=>' '))
                ->add('picture', FileType::class,array(
                    'required'=>false,
                    'label'=>' ',
                    'data_class' => null
                ))
                ->add('salary', IntegerType::class,array('label'=>' '))
                ->add('function', TextType::class,array('label'=>' '))
                ->add('hire_date', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
                ->add('departement',EntityType::class,array(
                    'label'=>' ',
                    'class' => 'AppBundle:Departement',
                    'choice_label' => 'name',
                    'multiple' => false,
                ))
                ->add('workingHour',EntityType::class,array(
                    'label'=>' ',
                    'class' => 'AppBundle:WorkingHours',
                    'choice_label' => 'code',
                    'multiple' => false,
                ))
                ->add("creer", SubmitType::class);
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */


                    $file = $employe->getPicture();
                    if ($file == null) {
                        $employe->setPicture($this->getDefaultPicture());
                    }

                    if(isset($file) && !empty($file)){
                        // Generate a unique name for the file before saving it
                        $file_extension = $file->guessExtension();
                        $fileName = $employe->getEmployeeCcid().'.'.$file->guessExtension();
                        $employe->setPicture($fileName);
                    }

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($employe);
                    $em->flush();

                    $last_id = $employe->getId();
                    $employe->setEmployeeCcid(10000 + $last_id);

                    $employe->setUsername($employe->getEmployeeCcid());
                    // Maintenant qu'un a un CCID on modifie le nom du fichier avant de l'uploader

                    if(isset($file) && !empty($file)) {
                        $timest = time();
                        $fileName = $employe->getEmployeeCcid().'_'.$timest.'.'.$file->guessExtension();
                        $user_profile_pictures = $this->getParameter("user_profile_pictures");
                        $file->move($user_profile_pictures, $fileName);

                        $employe->setPicture($employe->getEmployeeCcid().'_'.$timest.'.'.$file_extension);
                    }
                    $em->persist($employe);

                    $journal = new Journal();
                    $journal->setCrudType('C');
                    $journal->setAuthor($this->getUser()->getName().' '.$this->getUser()->getSurname());
                    $journal->setDescription($journal->getAuthor()." a ajouté un employé");
                    $journal->setElementConcerned($employe->getSurname()." ".$employe->getLastName());
                    $em->persist($journal);
                    $em->flush();

                    //$wh = $this->returnWorkingHoursAction();
                    $this->get('session')->getFlashBag()->set('notice', 'Cet employé a été ajouté avec succès');
                    return $this->redirectToRoute("addEmployee");
                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

            $wh = $this->returnWorkingHoursAction();
            return $this->render('cas/addEmployee.html.twig', array(
                'form' => $form->createView(),
                'whList' => $wh,
                'page' => "add"
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/editEmployee/{id}",name="editEmployee")
     */
    public function editEmployeeAction(Request $request, $id)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $employe = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($id);
            $last_picture = $employe->getPicture();

            if($employe != null){
                $employe->setLastUpdate(new \DateTime());
                $employe->setGodfatherCcid($this->getUser()->getId());
            }else{
                throw new NotFoundHttpException("L'employé d'id " . $id . " n'existe pas.");
            }

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $employe);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('surname', TextType::class,array('label'=>' '))
                ->add('last_name', TextType::class,array('label'=>' '))
                ->add('adress', TextType::class,array('label'=>' '))
                ->add('contact', TextType::class,array('label'=>' '))
                ->add('salary', IntegerType::class,array('label'=>' '))
                ->add('picture', FileType::class,array(
                    'required'=>false,
                    'label'=>' ',
                    'data_class' => null
                ))
                ->add('function', TextType::class,array('label'=>' '))
                ->add('hire_date', DateTimeType::class,array('widget'=>'single_text','label'=>' '))
                ->add('departement',EntityType::class,array(
                    'label'=>' ',
                    'class' => 'AppBundle:Departement',
                    'choice_label' => 'name',
                    'multiple' => false,
                ))
                ->add('workingHour',EntityType::class,array(
                    'label'=>' ',
                    'class' => 'AppBundle:WorkingHours',
                    'choice_label' => 'code',
                    'multiple' => false,
                ))
                ->add('creer', SubmitType::class);
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                    $file = $employe->getPicture();

                    $user_profile_pictures = $this->getParameter("user_profile_pictures");
                    if(isset($file) && !empty($file)){
                        $timest = time();
                        $fileName = $employe->getEmployeeCcid().'_'.$timest.'.'.$file->guessExtension();
                        // Move the file to the directory where images are stored
                        $file->move($user_profile_pictures, $fileName);
                        // Before setting the new file name to the employee,we must delete the older picture
                        if($last_picture != null && !empty($last_picture)){
                            //if($last_picture != "default-profile.png"){
                            if(!strpos($last_picture,"default")){
                                unlink($user_profile_pictures."/".$last_picture);
                            }
                        }
                        $employe->setPicture($fileName);
                    }else{
                        $employe->setPicture($last_picture);
                    }

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($employe);
                    $journal = new Journal();
                    $journal->setCrudType('U');
                    $journal->setAuthor($this->getUser()->getName().' '.$this->getUser()->getSurname());
                    $journal->setDescription($journal->getAuthor()." a modifié un employé");
                    $journal->setElementConcerned($employe->getSurname()." ".$employe->getLastName());
                    $em->persist($journal);
                    $em->flush();


                    //return $this->redirectToRoute('viewEmploye');

                    $wh = $this->returnWorkingHoursAction();
                    $this->get('session')->getFlashBag()->set('notice', 'Cet employé a été modifié avec succès');
                    return $this->redirectToRoute("editEmployee",array("id"=>$id));
                }

            }

            $wh = $this->returnWorkingHoursAction();

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('cas/addEmployee.html.twig', array(
                'form' => $form->createView(),
                'picture' => $employe->getPicture(),
                'whList'=>$wh,
                'employe'=>$employe
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/viewEmployee",name="viewEmployee")
     */
    public function viewEmployeeAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $depRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement");
            $listDep = $depRep->findAll();
            return $this->render('cas/viewEmployee.html.twig', array(
                'listDep' => $listDep,
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/returnOneEmployee/{id}",name="returnOneEmployee")
     */
    public function returnOneEmployeeAction(Request $request,$id,$fromDate=null,$toDate=null)
    {
        if($fromDate==null && $toDate==null){
            $dateFrom = $request->request->get("dateFrom");
            $dateTo = $request->request->get("dateTo");
        }else{
            $dateFrom = $fromDate;
            $dateTo = $toDate;
        }
        $timeFrom = strtotime($request->request->get("dateFrom")." 00:00:00");
        $timeTo = strtotime($request->request->get("dateTo")." 00:00:00");
        $timeDays = $timeTo-$timeFrom;
        $days = $timeDays/(60*60*24);
        $nowTime = $timeFrom;

        $em = $this->getDoctrine()->getManager();
        $emp = $em->getRepository("AppBundle:Employe")->find($id);
        $empWH = json_decode($emp->getWorkingHour()->getWorkingHour(),true);

        $toBeSubstracted = 0;
        for ($cpt=0;$cpt<=$days;$cpt++){
            $theDay = date('N',$nowTime);
            $theDay = $this->dateDayNameFrench($theDay);
            $type = $empWH[$theDay][0]["type"];
            if($theDay == "samedi" || $theDay == "dimanche"){
                if($type == null || $type == "null"){
                    $toBeSubstracted ++;
                }
            }
            $nowTime = $nowTime+86400;
        }
        $finallyDays = ($days - $toBeSubstracted)+1; // plus 1 because the first date(nowDate) was not considered
        // Now we know the final days
        $salaryPerDays = $emp->getSalary()/30;
        $salaryFinal = $salaryPerDays*$finallyDays;

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize(['emp' => $emp,"salaryFinal"=>$salaryFinal],'json');

        return new Response($jsonContent);
    }

    /**
     * @Route("/returnEmployees",name="returnEmployees")
     */
    public function returnEmployeesAction(Request $request)
    {
        $tab = array();
        $em = $this->getDoctrine()->getManager();
        $emp = $em->getRepository("AppBundle:Employe")->findAll();
        foreach ($emp as $e){
            $tempTab = [];
            $tempTab["id"] = $e->getId();
            $tempTab["surname"] = $e->getSurname();
            $tempTab["lastName"] = $e->getLastName();
            $tempTab["function"] = $e->getFunction();
            $tempTab["salary"] = $e->getSalary();
            $tempTab["contact"] = $e->getContact();
            $tempTab["adress"] = $e->getAdress();
            $tempTab["fingerprint"] = $e->getFingerprints();
            $tempTab["hireDate"] = $e->getHireDate()->format('d-m-Y');
            $tempTab["dep"] = $e->getDepartement()->getId();
            $tempTab["picPath"] = $e->getPicture();
            array_push($tab,$tempTab);

        }

        return new JsonResponse($tab);
    }

    /**
     * @Route("/deleteEmployee/{id}" ,name="deleteEmployee")
     */
    public function deleteEmployeeAction(Request $request, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $emp = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($id);
            if ($emp != null) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($emp);

                $journal = new Journal();
                $journal->setCrudType('D');
                $journal->setAuthor($this->getUser()->getName().' '.$this->getUser()->getSurname());
                $journal->setDescription($journal->getAuthor()." a supprimé un employé");
                $journal->setElementConcerned($emp->getSurname()." ".$emp->getLastName());
                $em->persist($journal);
                $em->flush();

                $this->get('session')->getFlashBag()->set('notice', 'Cet employé a été supprimé de la base de données');
                return $this->redirectToRoute("viewEmployee");
            } else{
                throw new NotFoundHttpException("L'employé d'id " . $id . " n'existe pas.");
            }
        }else{
            return $this->redirectToRoute("login");
        }

    }

    protected function returnWorkingHoursAction()
    {
        $whList = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->safeWorkingHour();
        $tab = array();

        foreach ($whList as $wh){
            $tab[] = ['id'=>$wh->getId(),'workingHour'=>(array)json_decode($wh->getWorkingHour())];
        }

        return $tab;
    }

    private function getDefaultPicture()
    {
        $employee_default_pic = /*$this->getParameter('user_profile_pictures').*/ DIRECTORY_SEPARATOR."default-profile.png";
        return $employee_default_pic;
    }

    private function conv_text($value) {
        $result = mb_detect_encoding($value." ","UTF-8,CP125") == "UTF-8" ? iconv("UTF-8", "CP1252", $value ) : $value;
        return $result;
    }

    private function dateDayNameFrench($day){
        switch ($day){
            case 1:
                return "lundi";
                break;
            case 2:
                return "mardi";
                break;
            case 3:
                return "mercredi";
                break;
            case 4:
                return "jeudi";
                break;
            case 5:
                return "vendredi";
                break;
            case 6:
                return "samedi";
                break;
            case 7:
                return "dimanche";
                break;
        }
    }

    /**
     * @Route("/repairEmployee",name="repairEmployee")
     */
    public function repairEmployee(Request $request) {

        $employees = $this->getDoctrine()->getRepository("AppBundle:Employe")->findAll();

        foreach ($employees as $employee) {

            $short_name = /*$employee->getSurname(). " ".*/ $employee->getLastname();

//            echo $short_name.'<br/>';
            $employee->setShortName($short_name);
            $this->getDoctrine()->getManager()->flush();
        }

        echo "finish";exit;

    }


}