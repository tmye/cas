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
        $employe = new Employe();
        $employe->setLastUpdate(new \DateTime());
        $employe->setCreateDate(new \DateTime());
        $employe->setEmployeeCcid(10000);
        $employe->setPassword("5555");
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
            ->add('picture', FileType::class,array('label'=>' '))
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
            ->add('Créer', SubmitType::class);
        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                $file = $employe->getPicture();

                // Generate a unique name for the file before saving it
                $file_extension = $file->guessExtension();
                $fileName = $employe->getEmployeeCcid().'.'.$file->guessExtension();

                $employe->setPicture($fileName);

                $em = $this->getDoctrine()->getManager();

                $em->persist($employe);
                $em->flush();

                $last_id = $employe->getId();
                $employe->setEmployeeCcid(10000 + $last_id);

                $employe->setUsername($employe->getEmployeeCcid());
                // Maintenant qu'un a un CCID on modifie le nom du fichier avant de l'uploader

                $fileName = $employe->getEmployeeCcid().'.'.$file->guessExtension();
                $file->move('uploads/img', $fileName);

                $employe->setPicture($employe->getEmployeeCcid().'.'.$file_extension);
                $em->persist($employe);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Employé bien enregistrée.');

                //return $this->redirectToRoute('viewEmploye');

                return new Response("OK");

            }

        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

        $wh = $this->returnWorkingHoursAction();

        return $this->render('cas/addEmployee.html.twig', array(
            'form' => $form->createView(),
            'whList' => $wh
        ));
    }

    /**
     * @Route("/editEmployee/{id}",name="editEmployee")
     */
    public function editEmployeeAction(Request $request, $id)
    {
        $employe = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($id);

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
            ->add('surname', TextType::class)
            ->add('middle_name', TextType::class,array('required' => false))
            ->add('last_name', TextType::class)
            ->add('adress', TextType::class)
            ->add('contact', TextType::class)
            ->add('salary', IntegerType::class)
            ->add('picture', FileType::class,array(
                'data_class' => null
            ))
            ->add('function', TextType::class)
            ->add('hire_date', DateType::class)
            ->add('departement',EntityType::class,array(
                'class' => 'AppBundle:Departement',
                'choice_label' => 'name',
                'multiple' => false,
            ))
            ->add('Modifier', SubmitType::class);
        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                $file = $employe->getPicture();

                // Generate a unique name for the file before saving it
                $file_extension = $file->guessExtension();
                $fileName = $employe->getEmployeeCcid().'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                $file->move('uploads/img', $fileName);

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $employe->setPicture($fileName);


                $em = $this->getDoctrine()->getManager();

                $em->persist($employe);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Employé bien enregistrée.');

                //return $this->redirectToRoute('viewEmploye');

                return new Response("Employé modifié");

            }

        }

        $wh = $this->returnWorkingHoursAction();

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('cas/addEmployee.html.twig', array(
            'form' => $form->createView(),
            'picture' => $employe->getPicture(),
            'whList'=>$wh
        ));
    }

    /**
     * @Route("/viewEmployee",name="viewEmployee")
     */
    public function viewEmployeeAction(Request $request)
    {
        $depRep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement");
        $listDep = $depRep->findAll();
        return $this->render('cas/viewEmployee.html.twig', array(
            'listDep' => $listDep,
        ));
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
        $emp = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($id);
        if ($emp != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($emp);
            $em->flush();
            return new Response("Cet Employé a été supprimé");
        } else{
            throw new NotFoundHttpException("L'employé d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/randomEmployee" ,name="randomEmployee")
     */
    public function randomEmployeeAction()
    {
        $alpha = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $random_name = array();
        $limite = random_int(5,20);
        for($i=0;$i<$limite;$i++){
            $random_name[] = $alpha[random_int(0,25)];
        }
        foreach ($random_name as $letters){
            echo $letters;
        }

        return new Response("<br>OK");
    }

    protected function returnWorkingHoursAction()
    {
        $whList = $this->getDoctrine()->getManager()->getRepository("AppBundle:WorkingHours")->findAll();
        $tab = array();

        foreach ($whList as $wh){
            $tab[] = ['id'=>$wh->getId(),'workingHour'=>(array)json_decode($wh->getWorkingHour())];
        }

        return $tab;
    }
}