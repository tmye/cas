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

class EmployeController extends Controller {

    /**
     * @Route("/addEmployee",name="addEmployee")
     */
    public function addEmployeeAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
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
                ->add('middle_name', TextType::class,array('required' => false,'label'=>' '))
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
                    $em->flush();

                    //$wh = $this->returnWorkingHoursAction();
                    $session->getFlashBag()->add('notice', 'Cet employé a été ajouté avec succès');
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
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
                ->add('middle_name', TextType::class,array('required' => false,'label'=>' '))
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
                            unlink($user_profile_pictures."/".$last_picture);
                        }
                        $employe->setPicture($fileName);
                    }else{
                        $employe->setPicture($last_picture);
                    }

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($employe);
                    $em->flush();


                    //return $this->redirectToRoute('viewEmploye');

                    $wh = $this->returnWorkingHoursAction();
                    return $this->render("editEmployee");
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
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
    public function returnOneEmployeeAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $emp = $em->getRepository("AppBundle:Employe")->find($id);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize(['emp' => $emp],'json');

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
            $tempTab["middleName"] = $e->getMiddleName();
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $emp = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($id);
            if ($emp != null) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($emp);
                $em->flush();

                $employe = new Employe();
                $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $employe);

                // On ajoute les champs de l'entité que l'on veut à notre formulaire
                $formBuilder
                    ->add('surname', TextType::class,array('label'=>' '))
                    ->add('middle_name', TextType::class,array('required' => false,'label'=>' '))
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

                $wh = $this->returnWorkingHoursAction();
                return $this->render("cas/addEmployee.html.twig",array(
                    'message'=>"Cet employé a été supprimé de la base de données",
                    'form' => $form->createView(),
                    'whList' => $wh
                ));
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
}