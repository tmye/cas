<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Journal;
use AppBundle\Entity\CompanyInfos;
use AppBundle\Entity\Setting;
use AppBundle\fpdf181\fpdf;
use AppBundle\Entity\CompanyConfig;
use AppBundle\Entity\Expiration;
use AppBundle\Entity\WorkingHours;
use AppBundle\fpdf181\tablepdf;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TmyeDeviceBundle\Entity\DevicePubPic;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends StatsController
{


    public function formatInt($value)
    {
        $value = (string)$value;
        $value_lenght = strlen($value);
        if ($value_lenght >= 4) {
            $number_of_points = $value_lenght % 3;
            $value_tab = str_split($value);
            $str = "";
            $cpt = 0;
            for ($i = $value_lenght - 1; $i >= 0; $i--) {
                if ($cpt == 3) {
                    $cpt = 0;
                    $str = $value_tab[$i] . "." . $str;
                } else {
                    $str = $value_tab[$i] . "" . $str;
                }
                $cpt++;
            }
        }
        return $str;
    }

    /*public function precisionRound($number, $precision) {
        $factor = pow(10, $precision);
        return round(number * factor) / factor;
    }*/

    /**
     * @Route("/functionTest", name="functionTest")
     */
    public function functionTestAction(Request $request)
    {
        /*$date = "2018-06-04";
        $result = $this->getDoctrine()->getManager()->getRepository("AppBundle:NullDate")->dayIsNull($date);
        print_r($result);
        return $result;
        /*$heureNormaleArrivee = "08:00";
        $heureEnregistre = "23:59";
        $p = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->enPermission(36,$date,$heureEnregistre,$heureNormaleArrivee);
        print_r($p);
        foreach ($p as $perm){
            echo "<br>".$perm->getDescription()."<br>";
        }*/

        //require('fpdf181/fpdf.php');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Hello World !');

        $pdf2 = new FPDF();
        $pdf2->AddPage();
        $pdf2->SetFont('Arial', 'B', 16);
        $pdf2->Cell(40, 10, 'Bonjour le monde !');

        $pdf->Output();

        //return new Response(date("Y-m-d H:i:s",1537163970));
        //return new Response(date("Y-m-d H:i:s",(new \DateTime())->getTimestamp()));
        //return new Response(strtotime("2018-07-05 08:30"));
        //return new Response($this->formatInt(12253008000000));
        //return $this->render("cas/errorPage.html.twig");
    }

    /**
     * @Route("/firstTimeInitialization", name="firstTimeInitialization")
     */
    public function firstTimeInitializationAction(Request $request)
    {
        return $this->render("cas/firstTime.html.twig");
    }

    /**
     * @Route("/initializeApplication", name="initializeApplication")
     */
    public function initializeApplicationAction(Request $request)
    {
        $admins = $this->getDoctrine()->getManager()->getRepository("AppBundle:Admin")->findAll();

        // Searching for already used username
        $i = 0;
        $found = false;
        if ($admins != null) {
            if (sizeof($admins) > 1) {
                while ($i < sizeof($admins) && $found == false) {
                    if ($admins[$i]->getUsername() == $request->request->get("adminUsername")) {
                        $found = true;
                    }
                    $i++;
                }
            }
        }

        if ($found == false) {
            $cc = new CompanyConfig();
            $em = $this->getDoctrine()->getManager();
            if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
                $resultat = move_uploaded_file($_FILES['image']['tmp_name'], "company_images/" . basename($_FILES["image"]["name"]));
                $cc->setCompanyName($request->request->get("compName"));
                $cc->setCompanyLogo($_FILES["image"]["name"]);
                $em->persist($cc);
                $em->flush();

                session_cache_limiter();
                $session = new Session();
                $session->set("companyLogo", $_FILES['image']['name']);
                $session->set("companyName", $request->request->get("name"));
                // We are done with the companyConfiguration.Now we must persist an Admin
                $e = new Admin();
                $e->setName($request->request->get("adminName"));
                $e->setSurname($request->request->get("adminSurname"));
                $e->setUsername($request->request->get("adminUsername"));
                $e->setPassword(md5($request->request->get("adminPassword")));
                $e->setRoles(array("ROLE_ADMIN"));
                $e->setAddress($request->request->get("adminAdress"));
                $e->setPhonenumber($request->request->get("adminPhoneNumber"));

                // We continue with the rest of the admin (Employee) properties

                $em->persist($e);
                $em->flush();

                // After persistance operation, we must edit initialization file

                $em = $this->getDoctrine()->getManager();
                $ft = $this->getDoctrine()->getManager()->getRepository("AppBundle:Setting")->findAll();

                if (sizeof($ft) > 0) {
                    $ft = $ft[0]->getFirstTime();
                } else {
                    $ft = true;
                }

                // No data is inserted yet in the database
                if ($ft) {
                    $new_ft = new Setting();
                    $new_ft->setFirstTime(false);
                    $em->persist($new_ft);
                    $em->flush();
                } else {
                    $ft->setFirstTime(false);
                    $em->flush();
                }


                // Now that all operations are achieved, we can return a response

                return new Response(1);
            } else {
                return new Response("Erreur avec la soumission du logo");
            }
        } else {
            return $this->redirectToRoute("firstTimeInitialization");
        }
    }

    /**
     * @Route("/persistNewAdmin", name="persistNewAdmin")
     */
    public function persistNewAdminAction(Request $request)
    {
        $admins = $this->getDoctrine()->getManager()->getRepository("AppBundle:Admin");

        // Searching for already used username
        $i = 0;
        $found = false;
        if (sizeof($admins) > 1) {
            while ($i < sizeof($admins) && $found == false) {
                if ($admins[$i]->getUsername() == $request->request->get("adminUsername")) {
                    $found = true;
                }
                $i++;
            }
        }

        if ($found == false) {
            $em = $this->getDoctrine()->getManager();

            $e = new Admin();
            $e->setName($request->request->get("adminName"));
            $e->setSurname($request->request->get("adminSurname"));
            $e->setUsername($request->request->get("adminUsername"));
            $e->setPassword(md5($request->request->get("adminPassword")));
            $e->setRoles(array($request->request->get("role")));
            $e->setAddress($request->request->get("adminAdress"));
            $e->setPhonenumber($request->request->get("adminPhoneNumber"));

            // We continue with the rest of the admin (Employee) properties

            $em->persist($e);

            $journal = new Journal();
            $journal->setCrudType('C');
            $journal->setAuthor($this->getUser()->getName() . ' ' . $this->getUser()->getSurname());
            $journal->setDescription($journal->getAuthor() . " a ajouté un nouvel administrateur au système");
            $journal->setElementConcerned($e->getName() . " " . $e->getSurname());
            $em->persist($journal);

            $em->flush();
            $this->get('session')->getFlashBag()->set('notice', 'Cet administrateur a été défini avec succès');
            return new Response(1);
        } else {
            return $this->redirectToRoute("firstTimeInitialization");
        }
    }

    /**
     * @Route("/changeSocietyName", name="changeSocietyName")
     */
    public function changeSocietyNameAction(Request $request, $name = null)
    {
        $name = $request->request->get("name");
        $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
        $em = $this->getDoctrine()->getManager();
        if ($cc != null) {
            $cc = $cc[0];
            $cc->setCompanyName($name);
            $em->flush();

            $session = new Session();
            $session->set("companyName", $name);
        } else {
            $newCC = new CompanyConfig();
            $newCC->setCompanyName($name);
            $em->persist($newCC);
            $em->flush();

            $session = new Session();
            $session->set("companyName", $name);
        }
        $journal = new Journal();

        $journal->setCrudType('U');
        $journal->setAuthor($this->getUser()->getName() . ' ' . $this->getUser()->getSurname());
        $journal->setDescription($journal->getAuthor() . " a changé le nom de la société");
        $em->persist($journal);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/expiryPage", name="expiryPage")
     */
    public function expiryPageAction(Request $request)
    {
        return $this->render("cas/expiryPage.html.twig");
    }

    /**
     * @Route("/expiryDate", name="expiryDate")
     */
    public function expiryDateAction(Request $request)
    {
        $date = $request->request->get("date");
        $ex = $this->getDoctrine()->getManager()->getRepository("AppBundle:Expiration")->findAll();
        $em = $this->getDoctrine()->getManager();
        if ($ex != null) {
            $ex = $ex[0];
            $ex->setExpiryDate($date);
            $em->flush();

            $session = new Session();
            $session->set("expiryDate", $date);
        } else {
            $newEX = new Expiration;
            $newEX->setExpiryDate($date);
            $em->persist($newEX);
            $em->flush();

            $session = new Session();
            $session->set("expiryDate", $date);
        }
        return new Response("OK");
    }

    /**
     * @Route("/changeSocietyLogo", name="changeSocietyLogo")
     */
    public function changeSocietyLogoAction(Request $request, $name = null)
    {
        $cc = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyConfig")->findAll();
        $em = $this->getDoctrine()->getManager();
        if ($cc != null) {
            if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
                $cc = $cc[0];
                $lastCompanyLogo = $cc->getCompanyLogo();
                if ($lastCompanyLogo != null && !empty($lastCompanyLogo) && file_exists("company_images/" . $lastCompanyLogo)) {
                    unlink("company_images/" . $lastCompanyLogo);
                }
                $cc->setCompanyLogo($_FILES["image"]["name"]);
                $em->flush();

                $resultat = move_uploaded_file($_FILES['image']['tmp_name'], "company_images/" . basename($_FILES["image"]["name"]));

                $journal = new Journal();
                $journal->setCrudType('U');
                $journal->setAuthor($this->getUser()->getName() . ' ' . $this->getUser()->getSurname());
                $journal->setDescription($journal->getAuthor() . " a changé le logo de la société");
                $em->persist($journal);

                $em->flush();
                $session = new Session();
                $session->set("companyLogo", $_FILES['image']['name']);
            } else {
                return new Response("Erreur");
            }
        } else {
            return new Response("Spécifiez d'abord un nom de société");
        }
        return new Response("OK");
    }

    /**
     * @Route("/uploadCoverAll", name="uploadCoverAll")
     */
    public function uploadCoverAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findAll();

        if ($devices != null) {
            foreach ($devices as $dev) {
                if (isset($_FILES["first_image_input_1"]["name"]) && !empty($_FILES["first_image_input_1"]["name"])) {
                    $lastImage = $dev->getImage1();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage1(md5($_FILES["first_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_1"]["name"])));
                }
                if (isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])) {
                    $lastImage = $dev->getImage2();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage2(md5($_FILES["second_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_1"]["name"])));
                }
                if (isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])) {
                    $lastImage = $dev->getImage3();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage3(md5($_FILES["third_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_1"]["name"])));
                }
                $em->flush();
            }
        } else {
            foreach ($machines as $mac) {
                $dev = new DevicePubPic();
                $dev->setDeviceid($mac->getMachineId());
                if (isset($_FILES["first_image_input_1"]["name"]) && !empty($_FILES["first_image_input_1"]["name"])) {
                    $dev->setImage1(md5($_FILES["first_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['first_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_1"]["name"])));
                }
                if (isset($_FILES["second_image_input_1"]["name"]) && !empty($_FILES["second_image_input_1"]["name"])) {
                    $dev->setImage2(md5($_FILES["second_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['second_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_1"]["name"])));
                }
                if (isset($_FILES["third_image_input_1"]["name"]) && !empty($_FILES["third_image_input_1"]["name"])) {
                    $dev->setImage3(md5($_FILES["third_image_input_1"]["name"]));
                    $resultat = move_uploaded_file($_FILES['third_image_input_1']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_1"]["name"])));
                }
                $em->persist($dev);
                $em->flush();
            }
        }

        return new Response("Image(s) uploadée(s) xxxxxx");
    }

    /**
     * @Route("/uploadCoverDep", name="uploadCoverDep")
     */
    public function uploadCoverDepAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $tab = array();
        foreach ($machines as $mac) {
            foreach ($mac->getDepartements() as $dep) {
                if (in_array($dep->getId(), array($_POST["depId"]))) {
                    $tab[] = $mac->getMachineId();
                }
            }
        }

        // Maintenant que j'ai la liste des machines de ce département je peux faire les traitements

        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->deviceByArray($tab);
        if ($devices != null) {
            foreach ($devices as $dev) {
                if (isset($_FILES["first_image_input_2"]["name"]) && !empty($_FILES["first_image_input_2"]["name"])) {
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage1();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage1(md5($_FILES["first_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['first_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_2"]["name"])));
                }
                if (isset($_FILES["second_image_input_2"]["name"]) && !empty($_FILES["second_image_input_2"]["name"])) {
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage2();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage2(md5($_FILES["second_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['second_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_2"]["name"])));
                }
                if (isset($_FILES["third_image_input_2"]["name"]) && !empty($_FILES["third_image_input_2"]["name"])) {
                    $dev->setDeviceid($mac->getMachineId());
                    $lastImage = $dev->getImage3();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage3(md5($_FILES["third_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['third_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_2"]["name"])));
                }
                $em->flush();
            }
        } else {
            foreach ($machines as $mac) {
                $dev = new DevicePubPic();
                $dev->setDeviceid($mac->getMachineId());
                if (isset($_FILES["first_image_input_2"]["name"]) && !empty($_FILES["first_image_input_2"]["name"])) {
                    $dev->setImage1(md5($_FILES["first_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['first_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_2"]["name"])));
                }
                if (isset($_FILES["second_image_input_2"]["name"]) && !empty($_FILES["second_image_input_2"]["name"])) {
                    $dev->setImage2(md5($_FILES["second_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['second_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_2"]["name"])));
                }
                if (isset($_FILES["third_image_input_2"]["name"]) && !empty($_FILES["third_image_input_2"]["name"])) {
                    $dev->setImage3(md5($_FILES["third_image_input_2"]["name"]));
                    $resultat = move_uploaded_file($_FILES['third_image_input_2']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_2"]["name"])));
                }
                $em->persist($dev);
                $em->flush();
            }
        }

        return new Response("Image(s) uploadée(s)");
    }

    /**
     * @Route("/uploadCoverMac", name="uploadCoverMac")
     */
    public function uploadCoverMacAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mac = $request->request->get("macId");
        echo "\n Mac Id : " . $mac . "\n";
        $devices = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findBy(array("deviceid" => $_POST["macId"]));

        if ($devices != null) {
            foreach ($devices as $dev) {
                if (isset($_FILES["first_image_input_3"]["name"]) && !empty($_FILES["first_image_input_3"]["name"])) {
                    $lastImage = $dev->getImage1();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage1(md5($_FILES["first_image_input_3"]["name"]));
                    $resultat = move_uploaded_file($_FILES['first_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_3"]["name"])));
                }
                if (isset($_FILES["second_image_input_3"]["name"]) && !empty($_FILES["second_image_input_3"]["name"])) {
                    $lastImage = $dev->getImage2();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage2(md5($_FILES["second_image_input_3"]["name"]));
                    $resultat = move_uploaded_file($_FILES['second_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_3"]["name"])));
                }
                if (isset($_FILES["third_image_input_3"]["name"]) && !empty($_FILES["third_image_input_3"]["name"])) {
                    $lastImage = $dev->getImage3();
                    if ($lastImage != null && !empty($lastImage) && $lastImage != "img/pubdef.jpg") {
                        // Verify if the file exists because we are in a loop
                        if (file_exists("pub_covers/" . $lastImage)) {
                            unlink("pub_covers/" . $lastImage);
                        }
                    }
                    $dev->setImage3(md5($_FILES["third_image_input_3"]["name"]));
                    $resultat = move_uploaded_file($_FILES['third_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_3"]["name"])));
                }
                $em->flush();
            }
        } else {
            $dev = new DevicePubPic();
            $dev->setDeviceid($mac);
            if (isset($_FILES["first_image_input_3"]["name"]) && !empty($_FILES["first_image_input_3"]["name"])) {
                $dev->setImage1(md5($_FILES["first_image_input_3"]["name"]));
                $resultat = move_uploaded_file($_FILES['first_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["first_image_input_3"]["name"])));
            }
            if (isset($_FILES["second_image_input_3"]["name"]) && !empty($_FILES["second_image_input_3"]["name"])) {
                $dev->setImage2(md5($_FILES["second_image_input_3"]["name"]));
                $resultat = move_uploaded_file($_FILES['second_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["second_image_input_3"]["name"])));
            }
            if (isset($_FILES["third_image_input_3"]["name"]) && !empty($_FILES["third_image_input_3"]["name"])) {
                $dev->setImage3(md5($_FILES["third_image_input_3"]["name"]));
                $resultat = move_uploaded_file($_FILES['third_image_input_3']['tmp_name'], "pub_covers/" . md5(basename($_FILES["third_image_input_3"]["name"])));
            }
            $em->persist($dev);
            $em->flush();
        }

        return new Response("Image(s) uploadée(s)");
    }

    /**
     * @Route("/returnCoverMac", name="returnCoverMac")
     */
    public function returnCoverMacAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $code = $request->request->get("code");
        $device = $this->getDoctrine()->getManager()->getRepository("TmyeDeviceBundle:DevicePubPic")->findOneBy(array("deviceid" => $code));

        $t = array();
        if (!empty($device)) {
            $t[] = $device->getImage1();
            $t[] = $device->getImage2();
            $t[] = $device->getImage3();
            return new Response(json_encode($t));
        } else {
            return new Response("0");
        }
        //return new Response(json_encode($t));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            $expiry_service->hasExpired();
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $session = new Session();
            if ($session->get('companyName') == null || $session->get('companyLogo') == null) {
                return $this->redirectToRoute("manageSocietyName", array('badConfig' => 1));
            }
            $ci = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyInfos")->findAll();
            if (($ci != null) && (!empty($ci))) {
                $ci = $ci[0];
            } else {
                $ci = null;
            }
            $employes = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->findAll();
            return $this->render('cas/index.html.twig', array(
                'ci' => $ci,
                'employes' => $employes,
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/historique",name="historique")
     */
    public function historiqueAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN_CONTROL')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/historique.html.twig', array('listDep' => $listDep));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/historiqueBrute",name="historique_brute")
     */
    public function historiqueBruteAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN_CONTROL')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            return $this->render('cas/historiqueBrute.html.twig', array('listDep' => $listDep));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /*
     * Routes concernant les admins et le super admin
     */

    /**
     * @Route("/admin/page",name="admin")
     */
    public function adminAction(Request $request)
    {
        return new Response("Page des admins");
    }

    /**
     * @Route("/SupAdmin/page",name="SupAdmin")
     */
    public function SupAdminAction(Request $request)
    {
        return new Response("Page du super admin");
    }

    /**
     * @Route("/synchroniser",name="synchroniser")
     */
    public function synchroniserAction(Request $request)
    {
        return $this->render('cas/synchroniser.html.twig');
    }

    /**
     * @Route("/switch",name="switch")
     */
    public function switchAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            return $this->render('cas/switch.html.twig', array(
                'departements' => $departements,
                'machines' => $machines
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manage",name="manage")
     */
    public function manageAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN_CONTROL')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            return $this->render('cas/manage.html.twig');
        } else {
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageEmpProfilePicture",name="manageEmpProfilePicture")
     */
    public function manageEmpProfilePictureAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmpProfilePicture.html.twig', array(
                "departements" => $departements
            ));
        } else {
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageEmpFingerprint",name="manageEmpFingerprint")
     */
    public function manageEmpFingerprintAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmpFingerprint.html.twig', array(
                "departements" => $departements
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageEmployee",name="manageEmployee")
     */
    public function manageEmployeeAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageEmployee.html.twig', array(
                "departements" => $departements
            ));
        } else {
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/manageDeleteData",name="manageDeleteData")
     */
    public function manageDeleteDataAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/delete.html.twig', array(
                "departements" => $departements,
                "machines" => $machines
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageDepartement",name="manageDepartement")
     */
    public function manageDepartementAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
            $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
            return $this->render('cas/manageDepartement.html.twig', array(
                "departements" => $departements,
                "machines" => $machines
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/manageSocietyName",name="manageSocietyName")
     */
    public function manageSocietyNameAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            // Token Sent
            $token = $request->query->get("badConfig");
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            return $this->render('cas/manageSocietyName.html.twig', array("token" => $token));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/customizeCompanyInfos",name="customizeCompanyInfos")
     */
    public function customizeCompanyInfosAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SECRET')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }

            $ci = $this->getDoctrine()->getManager()->getRepository("AppBundle:CompanyInfos")->findAll();
            if (($ci != null) && (!empty($ci))) {
                $ci = $ci[0];
            } else {
                $ci = new CompanyInfos();
            }

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $ci);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('vision', TextType::class, array('label' => ' '))
                ->add('mission', TextType::class, array('label' => ' '))
                ->add('foundation', TextType::class, array('label' => ' '))
                ->add('headoffice', TextType::class, array('label' => ' '))
                ->add('employees', IntegerType::class, array('required' => false, 'label' => ' ',))
                ->add('director', TextType::class, array('label' => ' '))
<<<<<<< HEAD
<<<<<<< HEAD
                ->add(utf8_decode("creer"), SubmitType::class);
=======
                ->add("creer", SubmitType::class);
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
=======
                ->add("creer", SubmitType::class);
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($ci);
                    $em->flush();

                    $this->get('session')->getFlashBag()->set('notice', 'Les informations de la société ont été mises à jour');
                    return $this->redirectToRoute("customizeCompanyInfos");
                }

            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau

            return $this->render('cas/companyInfos.html.twig', array(
                'form' => $form->createView()
            ));
        }
        else {
            return $this->redirectToRoute("login");
        }

        return $this->render("cas/companyInfos.html.twig", array(
            "ci" => $ci
        ));
    }

    /**
     * @Route("/generatePDF",name="generatePDF")
     */
    public function generatePDFAction(Request $request )
    {
        set_time_limit(0);

        $SURNAME_MAX = 10;
        $LASTNAME_MAX = 10;

        $session = new Session();



        $empId = $request->request->get('destination');
        $dateType = $request->request->get('dateType');
        $sel = $request->request->get("statType");
        if ($dateType != "0" && $dateType != null ) {
            $fromDate = "01-" . $dateType . "-" . date('Y');
            $toDate = date('t-m-Y', strtotime($fromDate));
            $mes = "datetype non null";
        } else {
            $fromDate = $request->request->get('fromDate');
            $toDate = $request->request->get('toDate');
            $sel = $request->request->get("statType");
            $mes = "datetype null";
        }
        $t = $request->request->get('type');
        $pdf = new tablepdf();
        if ($t != "1" && $t != "2") {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $imageData = getimagesize($this->getParameter("web_dir") . "/company_images/" . $session->get("companyLogo"));
            $theWidth = $imageData[0];
            $theHeight = $imageData[1];
            $ratio = $theWidth / $theHeight;

            // if the width is greater than 100px we must fix it at 100
            if ($theWidth > 30) {
                $theWidth = 30;
            }
            $theHeight = $theWidth / $ratio;

            //print_r($theWidth."<br/>");
            //print_r($theHeight);

            $pdf->Image($this->getParameter("web_dir") . "/company_images/" . $session->get("companyLogo"), 10, 10, $theWidth, $theHeight);
            $pdf->Image($this->getParameter("web_dir") . "/img/logo.png", 180, 10, 12.62, 19.4);
            $pdf->Ln('25');
            $pdf->Cell(500, 10, $session->get("companyName"));
            $pdf->Cell(500, 10, $session->get("companyName"));
            $pdf->Ln('17');
            $pdf->Cell(25, 10, "");
            $pdf->SetFont('Arial', 'BU', 16);
            $pdf->Cell(40, 10, utf8_decode('Rapport des employés du '). $fromDate . ' au ' . $toDate);
            $pdf->Ln('15');
        }
        $i = 0;

        foreach ($empId as $emp) {
            set_time_limit(0);
            $i++;
            if ($i >= 4) { // set up how many table on a page
                $pdf->AddPage();
                $i = 0;
            }


            $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
            $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(), true);
            $type = $empWH["lundi"][0]["type"];

            $empData = $this->returnOneEmployeeAction($request, $emp, $fromDate, $toDate);
            $empDataFormated = json_decode($empData->getContent(), true);


            $donnees = $this->userStatsActionPDF($request, $emp, $fromDate, $toDate, $sel);
            $donnees = json_decode($donnees->getContent(), true);
            $permission_lost_time = 0;

            $nbreJourTravail = $donnees["nbreJourTravail"];

            /* recuperation du taux horaire */
            if (($employe->getWorkingHour()->getTaux()) == 0)
                $taux = 0;
            else
                $taux = ($employe->getWorkingHour()->getTaux());

            if ($type == "3" || $type == 3) {
//                $ss = (($employe->getSalary * 12) / 52) / ($employe->getWorkingHour()->getJourTravail()) * ($donnees["nbreJourTravail"]);
                $ss = (($employe->getSalary * 12) / 52) / ($employe->getWorkingHour()->getJourTravail()) * ($donnees["nbreJourTravail"]);
            } else if ($type == "2" || $type == 2) {
                if ($taux == 0)
                    $ss = 0;
                else
                    $ss = ((($employe->getSalary() * 12) / 52) / $taux) * $donnees["quota_total"] / 60;
            } else if ($type == "1" || $type == 1 || $type == "4" || $type == 4) {
                if ($taux == 0) {
                    $ss = 0;
                } else {
                    $ss = $donnees["salTotal"] ;

//                    $ss = ((($employe->getSalary() * 12) / 52) / $taux) * $donnees["quota_1_4"];
                }
            } else {
                $ss = 0;
            }

            // Permission datas
            $nbAbsence = 0;
            foreach ($donnees["permissionData"]["absenceStats"] as $row) {
                if($sel == 1){
                    if(strcasecmp($row["type"],"Absence")==0  ){
                        $nbAbsence++;
                        //$permission_lost_time += $row["tempsPerdu"];
                    }else{
                        $permission_lost_time += $row["tempsPerdu"];
                    }
                } else {
                    if(strcasecmp($row["type"],"Absence")==0 || strcasecmp($row["type"],"Permission")==0 ){
                        $nbAbsence++;
                    }
                }



            }
            foreach ($donnees["permissionData"]["finStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["pauseStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["retardPauseStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["retardStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            //print_r($donnees);
            $finalSalary = ((int)$employe->getSalary()) / 30;
            $finalSalaryPerHour = $finalSalary / 24;
            $finalSalaryPerMin = $finalSalaryPerHour / 60;
            $name = $employe->getSurname();
            $lastName = $employe->getLastName();
            $permissions = sizeof($donnees["permissionData"]["retardStats"]) + sizeof($donnees["permissionData"]["retardPauseStats"]) + sizeof($donnees["permissionData"]["pauseStats"]) + sizeof($donnees["permissionData"]["finStats"]) + sizeof($donnees["permissionData"]["absenceStats"]);

            $functionAppend = strlen($employe->getFunction()) > 5 ? "..." : "";
            $depAppend = strlen($employe->getDepartement()->getName()) > 5 ? "..." : "";

            $user_info_header = array('Nom', 'Prenom(s)', 'Fonction', 'Departement', 'Salaire', 'Revenu*', 'Duree hebdo');
            $user_info_data = array(
                array(substr($employe->getSurname(), 0,$SURNAME_MAX), substr($employe->getLastName(), 0,$LASTNAME_MAX), substr($employe->getFunction(), 0, 5) . "" . $functionAppend, substr($employe->getDepartement()->getName(), 0, 5) . "" . $depAppend, $employe->getSalary(), round($ss, 2), $taux)
            );

            if ($type == "2" or $type == 2) {
                $quota_restant = $donnees["quota_total"] - $donnees["quota_fait"];
                if ($quota_restant > 0) {
                    $qr = $quota_restant;
                } else {
                    $qr = 0;
                }
                if ($t == "2") {
                    $header = array('', 'Absences', 'Retards', 'Departs', 'Auth', 'Total', 'Permissions');
                    $data = array(
                        array("Nombre", $donnees["absences"], $donnees["retards"], $donnees["departs"], $donnees["inc_auth"], $donnees["absences"] + $donnees["retards"] + $donnees["departs"] + $donnees["inc_auth"], sizeof($donnees["permissionData"]["absenceStats"]) + sizeof($donnees["permissionData"]["finStats"]) + sizeof($donnees["permissionData"]["pauseStats"]) + sizeof($donnees["permissionData"]["retardPauseStats"]) + sizeof($donnees["permissionData"]["retardStats"])),
                    );
                    $data2 = array(
                        array("Temps", $donnees["tpa"], round($donnees["tpr"], 2), round($donnees["tpd"], 2), $donnees["lost_time"], round($donnees["tpa"] + $donnees["tpr"] + $donnees["tpd"] + $donnees["lost_time"], 2), $permission_lost_time),
                    );
                    $data3 = array(
                        array("Somme", round($donnees["spa"], 2), round($donnees["spr"], 2), round($donnees["spd"], 2), round($donnees["spAuth"], 2), round($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"], 2), $taux == 0 ? 0 : round((($employe->getSalary() * 12) / 52) / $taux * $permission_lost_time, 2)),
                    );
                    $data4 = array(
                        array(utf8_decode("Net à payer sans bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]), 2)),
                    );
                } else {
                    $header = array('', 'Absences', 'Retards', 'Departs', 'Auth', 'Total', 'Permissions', 'Bonus');
                    $data = array(
                        array("Nombre", $donnees["absences"], $donnees["retards"], $donnees["departs"], $donnees["inc_auth"], $donnees["absences"] + $donnees["retards"] + $donnees["departs"] + $donnees["inc_auth"], sizeof($donnees["permissionData"]["absenceStats"]) + sizeof($donnees["permissionData"]["finStats"]) + sizeof($donnees["permissionData"]["pauseStats"]) + sizeof($donnees["permissionData"]["retardPauseStats"]) + sizeof($donnees["permissionData"]["retardStats"]), $donnees["nbreBonus"]),
                    );
                    $data2 = array(
                        array("Temps", $donnees["tpa"], round($donnees["tpr"], 2), round($donnees["tpd"], 2), $donnees["lost_time"], round($donnees["tpa"] + $donnees["tpr"] + $donnees["tpd"] + $donnees["lost_time"], 2), $permission_lost_time, round($donnees["tempsBonus"] * (-1), 2)),
                    );
                    $data3 = array(
                        array("Somme", round($donnees["spa"], 2), round($donnees["spr"], 2), round($donnees["spd"], 2), round($donnees["spAuth"], 2), round($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"], 2), $taux == 0 ? 0 : round((($employe->getSalary() * 12) / 52) / $taux * $permission_lost_time, 2), round($donnees["sommeArgentBonus"] * (-1), 0)),
                    );
                    $data4 = array(
                        array(utf8_decode("Net à payer sans bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]), 2)),
                    );
                    $data5 = array(
                        array(utf8_decode("Net à payer avec bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]) + round($donnees["sommeArgentBonus"] * (-1),0), 2)),
                    );
                }
            } else {
                if ($t == "2") {
                    $header = array('', 'Absences', 'Retards', 'Departs', 'Auth', 'Total', 'Permissions');
                    $data = array(
                        array("Nombre", $donnees["absences"], $donnees["retards"], $donnees["departs"], $donnees["inc_auth"], $donnees["absences"] + $donnees["retards"] + $donnees["departs"] + $donnees["inc_auth"], sizeof($donnees["permissionData"]["absenceStats"]) + sizeof($donnees["permissionData"]["finStats"]) + sizeof($donnees["permissionData"]["pauseStats"]) + sizeof($donnees["permissionData"]["retardPauseStats"]) + sizeof($donnees["permissionData"]["retardStats"])),
                    );
                    $data2 = array(
                        array("Temps", $donnees["tpa"], round($donnees["tpr"], 2), round($donnees["tpd"], 2), $donnees["lost_time"], round($donnees["tpa"] + $donnees["tpr"] + $donnees["tpd"] + $donnees["lost_time"], 2), round($permission_lost_time,2)),
                    );
                    $data3 = array(
                        array("Somme", round($donnees["spa"], 2), round($donnees["spr"], 2), round($donnees["spd"], 2), round($donnees["spAuth"], 2), round($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"], 2), $taux == 0 ? 0 : round((($employe->getSalary() * 12) / 52) / $taux * $permission_lost_time, 2)),
                    );
                    $data4 = array(
                        array(utf8_decode("Net à payer sans bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]), 2)),
                    );
                } else {

                    $header = array('', 'Absences', 'Retards', 'Departs', 'Auth', 'Total', 'Permissions', 'Bonus');
                    $data = array(
                        array("Nombre", $donnees["absences"], $donnees["retards"], $donnees["departs"], $donnees["inc_auth"], $donnees["absences"] + $donnees["retards"] + $donnees["departs"] + $donnees["inc_auth"], sizeof($donnees["permissionData"]["absenceStats"]) + sizeof($donnees["permissionData"]["finStats"]) + sizeof($donnees["permissionData"]["pauseStats"]) + sizeof($donnees["permissionData"]["retardPauseStats"]) + sizeof($donnees["permissionData"]["retardStats"]) - $nbAbsence, $donnees["nbreBonus"]),
                    );
                    $data2 = array(
                        array("Temps", $donnees["tpa"], round($donnees["tpr"], 2), round($donnees["tpd"], 2), $donnees["lost_time"], round($donnees["tpa"] + $donnees["tpr"] + $donnees["tpd"] + $donnees["lost_time"], 2), round($permission_lost_time,2), round($donnees["tempsBonus"] * (-1), 2)),
                    );
                    $data3 = array(
                        array("Somme", round($donnees["spa"], 2), round($donnees["spr"], 2), round($donnees["spd"], 2), round($donnees["spAuth"], 2), round($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"], 2),  round(($donnees["salPerHour"]  * $permission_lost_time), 2), round($donnees["sommeArgentBonus"] * (-1), 0)),
                    );
                    $data4 = array(
                        array(utf8_decode("Net à payer sans bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]), 2)),
                    );
                    $data5 = array(
                        array(utf8_decode("Net à payer avec bonus"), round($ss - ($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"]) + round($donnees["sommeArgentBonus"] * (-1),0), 2)),
                    );
                }
            }
            if ($t == "1" || $t == "2") {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Image($this->getParameter("web_dir") . "/company_images/" . $session->get("companyLogo"), 10, 10, 20, 20);
                $pdf->Image($this->getParameter("web_dir") . "/img/logo.png", 180, 10, 12, 12);
                $pdf->Ln('25');
                $pdf->Cell(500, 10, $session->get("companyName"));
                $pdf->Ln('17');
                $pdf->Cell(25, 10, "");
                $pdf->SetFont('Arial', 'BU', 16);
                $pdf->Cell(40, 10, utf8_decode('Rapport des employés du ') . $fromDate . ' au ' . $toDate);
                $pdf->Ln('15');
            }
            if (isset($data5) && ($data5 != null)) {
                $pdf->FancyTable($user_info_header, $user_info_data, $header, $data, $data2, $data3, $data4, $data5);
            } else {
                $pdf->FancyTable($user_info_header, $user_info_data, $header, $data, $data2, $data3, $data4);
            }
            $pdf->Ln('5');
        }
        $pdf->Output();
        //}
       // return new JsonResponse(array("donnee"=>$donnees));
    }

    /**
     * @Route("/generatePDFPermission/{id}",name="generatePDFPermission")
     */
    public function generatePDFPermissionAction(Request $request, $id)
    {
        set_time_limit(0);

        $session = new Session();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 13);

        $imageData = getimagesize($this->getParameter("web_dir") . "/company_images/" . $session->get("companyLogo"));
        $permission = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->find($id);
        $theWidth = $imageData[0];
        $theHeight = $imageData[1];
        $ratio = $theWidth / $theHeight;

        // if the width is greater than 100px we must fix it at 100
        if ($theWidth > 30) {
            $theWidth = 30;
        }
        $theHeight = $theWidth / $ratio;

//        print_r($theWidth."<br/>");
//        print_r($theHeight);


        $pdf->Cell(25, 10, $session->get("companyName"));
        $pdf->Image($this->getParameter("web_dir") . "/company_images/" . $session->get("companyLogo"), 170, 10, $theWidth, $theHeight);
        $pdf->Ln('35');
        $pdf->Cell(0, 0, "Le " . date('d') . "/" . date('m') . "/" . date('Y'));
        $pdf->Ln('15');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 0, $permission->getEmployee()->getSurname() . " " . $permission->getEmployee()->getLastName());
        $pdf->Ln('10');
        $pdf->SetFont('Arial', '', 13);
        $pdf->Cell(0, 0, $permission->getEmployee()->getFunction());
        $pdf->Ln('10');
        $pdf->Cell(0, 0, $permission->getEmployee()->getContact());
        $pdf->Ln('10');
        $pdf->Cell(0, 0, "Au responsable du personnel", 0, 0, "R");
        $pdf->Ln('10');
        $pdf->SetFont('Arial','U');
        $pdf->Cell(0, 0, "Objet :");
        $pdf->Ln('10');
        $pdf->SetFont('Arial', '', 13);
        $pdf->Cell(0, 0, "Demande de permission");
        $pdf->Ln('15');
        $pdf->Cell(0, 0, "Monsieur, Madame");
        $pdf->Ln('10');
        $pdf->Write(7, "Je vous prie de m'accorder une permission pour m'absenter du ".$permission->getDateFrom()->format("Y-m-d")." ".$permission->getTimeFrom()." au ".$permission->getDateTo()->format("Y-m-d")." ".$permission->getTimeTo()." pour me permettre de (". utf8_decode($permission->getTitle()).utf8_decode(").
Je m'engage à fournir tous les documents pouvant justifier mon absence, à signaler tout éventuel changement ou annulation de ladite permission et à prendre mes dispositions avec la direction des ressources humaines pour que mon absence impacte dans une moindre mesure le fonctionnement habituel de la société.
Dans l'attente d'une réponse favorable, Veuillez recevoir mes salutations les plus respectueuses."));

        $pdf->Ln('25');

        $pdf->Cell(60, 0, "Le Demandeur",0,0,"C");
        $pdf->Cell(60, 0, utf8_decode("La Sécrétaire"),0,0,"C");
        $pdf->Cell(60, 0, "Le Responsable",0,0,"C");

        $pdf->Ln('45');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(158,158,158);
        $pdf->Write(7, utf8_decode("N.B Cette fiche est à faire signer au responsable du personnel ou à qui de droit. La signature de la sécrétaire ou de toute autre personne chez qui la demande est formulée ne peut en aucun cas remplaçer celle du responsable.  La fiche doit être conservée dans les archives pour constituer une preuve en cas de besoin."));


        /*$user_info_header = array('Nom', 'Prenom(s)', 'Departement', 'Début P', 'Fin P', 'Motif');
        $user_info_data = array(
            array($permission->getEmployee()->getSurname(), $permission->getEmployee()->getLastName(), $permission->getEmployee()->getDepartement()->getName(), $permission->getDateFrom()->format("Y-m-d"), $permission->getDateTo()->format("Y-m-d"), $permission->getTitle())
        );

        $pdf->PermissionFancyTable($user_info_header, $user_info_data);
        $pdf->Ln('5');*/
        $pdf->Output();
        //}
        //return new JsonResponse(array("donn "=>$donnees,"mes"=>$mes));
    }

    public function returnVerticalCells($numberOfDays)
    {
        $result_array = array();
        $arrayOfAlphabet = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'A', 'B');
        $normalArrayOfAlphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        //$restOfDivision = $numberOfDays%26;
        $numberOfPossibleDivision = (int)($numberOfDays / 26);
        $tour = $numberOfPossibleDivision + 1;
        $currentTour = 1;
        $j = 0;
        $i = 0;
        for ($cpt = 0; $cpt < $tour; $cpt++) {
            while ($i <= $numberOfDays) {
                if ($currentTour == 1) {
                    if ($i < 23) {
                        $result_array [] = $arrayOfAlphabet[$i];
                    } else if ($i == 23) {
                        $result_array [] = $arrayOfAlphabet[$i];
                        $numberOfDays -= 24;
                        $currentTour++;
                        $i = 0;
                    }
                } else {
                    if ($i < 26) {
                        $result_array [] = $normalArrayOfAlphabet[$j] . "" . $normalArrayOfAlphabet[$i - 1];
                    } else if ($i == 26) {
                        $result_array [] = $normalArrayOfAlphabet[$j] . "" . $normalArrayOfAlphabet[$i - 1];
                        $numberOfDays -= 26;
                        $currentTour++;
                        $i = 0;
                        $j++;
                    }
                }
                $i++;
            }
            //$j++;
        }
        return $result_array;
    }

    /**
     * @Route("/generateExcel",name="generateExcel")
     */
    public function generateExcelAction(Request $request)
    {

        set_time_limit(0);

<<<<<<< HEAD
<<<<<<< HEAD
        /* not obliged to come - cell fill*/
        $not_supposed_to_come_styleArray =  [
=======
        /* not obliged to come - cell fill */
        $styleArray =  [
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
=======
        /* not obliged to come - cell fill */
        $styleArray =  [
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => '03a9f4',
<<<<<<< HEAD
                ]
            ],
        ];

<<<<<<< HEAD
        $perm_date =  [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => '81C784',
=======
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
                ]
            ],
        ];

<<<<<<< HEAD
        $nullDayStyleArray =  $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => 'FFffeb3b'],
                ],
            ],
        ];

=======
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
=======
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019

//

        $this->returnVerticalCells(80);
        $t = $request->request->get('type');
        $empId = $request->request->get('destination');

        $dateType = $request->request->get('dateType');

        $sel = $request->request->get("statType");
        if ($dateType != "0" && $dateType != null ) {
            $fromDate = "01-" . $dateType . "-" . date('Y');
            $toDate = date('t-m-Y', strtotime($fromDate));
            $mes = "datetype non null";
        } else {
            $fromDate = $request->request->get('fromDate');
            $toDate = $request->request->get('toDate');
            $sel = $request->request->get("statType");
            $mes = "datetype null";
        }

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
        $sheet->setCellValue('A1', 'HISTORIQUE DU ' . $fromDate . ' AU ' . $toDate);

        $explodedFromDate = explode("-", $fromDate);
        $explodedToDate = explode("-", $toDate);
        $reformatedFromDate = $explodedFromDate[2] . "-" . $explodedFromDate[1] . "-" . $explodedFromDate[0];
        $reformatedToDate = $explodedToDate[2] . "-" . $explodedToDate[1] . "-" . $explodedToDate[0];
        $timeFrom = strtotime($reformatedFromDate . " 00:00:00");
        $timeTo = strtotime($reformatedToDate . " 00:00:00");
        $timeDays = $timeTo - $timeFrom;
        $days = $timeDays / (60 * 60 * 24);
        $theDay = $theDayNumber = null;

        $verticalCellsTab = $this->returnVerticalCells($days + 1);
        $newTab = $verticalCellsTab;
        array_push($newTab, "A");
        array_push($newTab, "B");

        foreach ($empId as $emp) {
            set_time_limit(0);
            $nowTime = $timeFrom;
            $employe = $this->getDoctrine()->getManager()->getRepository("AppBundle:Employe")->find($emp);
            $empWH = json_decode($employe->getWorkingHour()->getWorkingHour(), true);
            $type = $empWH["lundi"][0]["type"];

            $empData = $this->returnOneEmployeeAction($request, $emp, $fromDate, $toDate);
            $empDataFormated = json_decode($empData->getContent(), true);

            $donnees = $this->userStatsActionPDF($request, $emp, $fromDate, $toDate,$sel);
            $donnees = json_decode($donnees->getContent(), true);
            $permission_lost_time = 0;
            // Permission datas
            foreach ($donnees["permissionData"]["absenceStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["finStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["pauseStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["retardPauseStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }
            foreach ($donnees["permissionData"]["retardStats"] as $row) {
                $permission_lost_time += $row["tempsPerdu"];
            }

            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber - 1))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 2))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 3))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 4))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 5))->applyFromArray($boldStyle);

            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 7))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 8))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 9))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 10))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 11))->applyFromArray($boldStyle);

            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 13))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('A' . ($nextNameCellNumber + 14))->applyFromArray($boldStyle);
            $spreadsheet->getActiveSheet()->getStyle('B' . ($nextNameCellNumber - 1))->applyFromArray($boldStyle);

            $sheet->setCellValue('A' . ($nextNameCellNumber - 1), "NOM");
            $sheet->setCellValue('A' . ($nextNameCellNumber + 2), "Arrivée");
            $sheet->setCellValue('A' . ($nextNameCellNumber + 3), "Pause");
            $sheet->setCellValue('A' . ($nextNameCellNumber + 4), "Reprise");
            $sheet->setCellValue('A' . ($nextNameCellNumber + 5), "Départ");

            $sheet->setCellValue('A' . ($nextNameCellNumber + 7), "NOMBRE D'ABSENCES : " . $donnees["absences"]);
            $sheet->setCellValue('A' . ($nextNameCellNumber + 8), "NOMBRE DE RETARDS : " . $donnees["retards"]);
            $sheet->setCellValue('A' . ($nextNameCellNumber + 9), "NOMBRE DE DEPARTS : " . $donnees["departs"]);
            $sheet->setCellValue('A' . ($nextNameCellNumber + 10), "NOMBRE D'AUTH INC : " . $donnees["inc_auth"]);
            $sheet->setCellValue('A' . ($nextNameCellNumber + 11), "NOMBRE DE PERM: " . $donnees["nbrePermission"]);

            $sheet->setCellValue('A' . ($nextNameCellNumber + 13), "TOTAL DES PERTES EN TEMPS : " . round($donnees["tpa"] + $donnees["tpr"] + $donnees["tpd"] + $donnees["lost_time"], 4) . " H");
            $sheet->setCellValue('A' . ($nextNameCellNumber + 14), "TOTAL DES PERTES EN ARGENT : " . round($donnees["spa"] + $donnees["spr"] + $donnees["spd"] + $donnees["spAuth"], 4) . " FCFA");

            $sheet->setCellValue('B' . ($nextNameCellNumber - 1), "PRENOMS");
            $sheet->setCellValue('A' . $nextNameCellNumber, $employe->getSurname());
            $sheet->setCellValue('B' . $nextNameCellNumber, $employe->getLastName());

            for ($cpt = 0; $cpt <= $days; $cpt++) {
                set_time_limit(0);
                $his = $this->findHistoriqueAction($employe->getDepartement()->getId(), date('d-m-Y', $nowTime), $employe->getId(), $request);
                $his = json_decode($his->getContent(), true);

                $theDayNumber = date('N', $nowTime);
                $theDay = $this->dateDayNameFrench($theDayNumber);

                $spreadsheet->getActiveSheet()->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber - 1))->applyFromArray($boldStyle);
                $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber - 1), date("d", $nowTime) . '/' . date("m", $nowTime));


                foreach ($newTab as $el) {
                    $spreadsheet->getActiveSheet()->getStyle($el . "" . ($nextNameCellNumber + 16))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $spreadsheet->getActiveSheet()->getStyle($el . "" . ($nextNameCellNumber + 16))->getFill()->getStartColor()->setARGB('bdbdbd');
                }

<<<<<<< HEAD
<<<<<<< HEAD
                /* if you are not supposed to come on this day*/
                if ($this->checkIfEmployeePresenceIsNecessaryToday($emp, $nowTime)) {
                    $sheet->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber -1).':'.$verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5))
                        ->applyFromArray($not_supposed_to_come_styleArray);
                }

                /* if the day is a null day*/
                if ($this->checkIfDayIsNull($nowTime)) {
                    $sheet->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber -1).':'.$verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5))
                        ->applyFromArray($nullDayStyleArray);
                }

                if ($this->checkIfDayHasPermission ($emp,$nowTime)) {
                    $sheet->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber -1).':'.$verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5))
                        ->applyFromArray($perm_date);
                }




=======
                /* get a function that check if this day is a normal coming day */
                if ($this->checkIfEmployeePresenceIsNecessaryToday($emp, $nowTime)) {
                    $sheet->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber -1).':'.$verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5))
                        ->applyFromArray($styleArray);
                }

>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
=======
                /* get a function that check if this day is a normal coming day */
                if ($this->checkIfEmployeePresenceIsNecessaryToday($emp, $nowTime)) {
                    $sheet->getStyle($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber -1).':'.$verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5))
                        ->applyFromArray($styleArray);
                }

>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
                if ($his["arrive"] != null && $his["arrive"] != "") {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 2), $his["arrive"]);
                } else {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 2), "--");
                }

                if ($his["pause"] != null && $his["pause"] != "") {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 3), $his["pause"]);
                } else {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 3), "--");
                }

                if ($his["finPause"] != null && $his["finPause"] != "") {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 4), $his["finPause"]);
                } else {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 4), "--");
                }

                if ($his["depart"] != null && $his["depart"] != "") {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5), $his["depart"]);
                } else {
                    $sheet->setCellValue($verticalCellsTab[$cpt] . '' . ($nextNameCellNumber + 5), "--");
                }

                $nowTime = $nowTime + 86400;
            }

            $nextNameCellNumber += 20;
        }

        $writer = new Xlsx($spreadsheet);
        $now_date = date('d') . "-" . date('m') . '-' . date('Y') . '_' . date('H') . ':' . date('i') . ':' . date('s');
        $file_date = $now_date;
        $rd = rand(0,100);
        $filePath = $this->getParameter("web_dir") . "/output_files/" . $this->getUser()->getUsername() . "_rapport_" ."_".$rd.  ".xlsx";

        $writer->save($filePath."");

        //sleep(10);


        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $this->getUser()->getUsername() . "_rapport_" . "_".$rd. ".xlsx",
            iconv('UTF-8', 'ASCII//TRANSLIT', $this->getUser()->getUsername() . "_rapport_" ."_".$rd. ".xlsx")
        );
        return $response;
//        return new Response("Excel generated succesfully ".$file_date."  *** ".$now_date." **** ".$filePath);
        //return $response;

       /* $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->setTitle("My First Worksheet");

        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);

        // In this case, we want to write the file in the public directory
       // $publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
        // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
        $excelFilepath = 'my_first_excel_symfony4.xlsx';

        // Create the file
        $writer->save($excelFilepath);

        // Return a text response to the browser saying that the excel was succesfully created
        return new Response("Excel generated succesfully");*/
    }

    /**
     * @Route("/editProfile",name="editProfile")
     */
    public function editProfileAction(Request $request)
    {
        dump($this->getUser());

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $admin = $this->getUser();

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $admin);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('surname', TextType::class, array('label' => ' '))
                ->add('name', TextType::class, array('label' => ' '))
                ->add('address', TextType::class, array('label' => ' '))
                ->add('phonenumber', TextType::class, array('label' => ' '))
                ->add('password', RepeatedType::class, array(
                    'label' => ' ',
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'required' => true,
                    'first_options' => array('label' => ' '),
                    'second_options' => array('label' => ' ')
                ))
                ->add('picture', FileType::class, array(
                    'required' => false,
                    'label' => ' ',
                    'data_class' => null
                ))
                ->add('Modifier', SubmitType::class);
            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {

                $old_password = $admin->getPassword();

                $last_picture = $admin->getPicture();
                $form->handleRequest($request);
                if ($form->isValid()) {

                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                    $file = $admin->getPicture();

                    $user_profile_pictures = $this->getParameter("user_profile_pictures");
                    if (isset($file) && !empty($file)) {
                        $fileName = $admin->getUsername() . '.' . $file->guessExtension();
                        // Move the file to the directory where images are stored
                        $file->move($user_profile_pictures, $fileName);
                        // Before setting the new file name to the employee,we must delete the older picture
                        if ($last_picture != null && !empty($last_picture)) {
                            unlink($user_profile_pictures . "/" . $last_picture);
                        }
                        $admin->setPicture($fileName);
                    } else {
                        $admin->setPicture($last_picture);
                    }

                    $em = $this->getDoctrine()->getManager();

                    $admin->setPassword(md5($admin->getPassword()));

                    $em->persist($admin);
                    $journal = new Journal();
                    $journal->setCrudType('U');
                    $journal->setAuthor($this->getUser()->getName() . ' ' . $this->getUser()->getSurname());
                    $journal->setDescription($journal->getAuthor() . " a modifié ses propres informations");
                    $journal->setElementConcerned($admin->getSurname() . " " . $admin->getName());
                    $em->persist($journal);
                    $em->flush();

                    $this->get('session')->getFlashBag()->set('notice', 'Vos informations ont été modifié avec succès');
                    return $this->redirectToRoute("editProfile");
                }

            }

            return $this->render("cas/editProfile.html.twig", array(
                "form" => $form->createView()
            ));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    private function checkIfEmployeePresenceIsNecessaryToday($emp, $nowTime)
    {

        $employee = $this->EmployeeRepo()->find($emp);
        $res = 0;
        if ($employee != null) {
            $wh_id = $employee->getWorkingHour();
            $wh_obj = $this->WorkingHourRepo()->find($wh_id);
            $code = $wh_obj->getWorkingHour();
            $wh_array = json_decode($code, true);

            $dayOfTheWeek = date('w', $nowTime); // 0 sunday, 6 saturday

            if (($dayOfTheWeek == 0 && $wh_array["dimanche"][0]["type"] ==  "null" ) ||
                $dayOfTheWeek == 1 && $wh_array["lundi"][0]["type"] ==  "null" ||
                $dayOfTheWeek == 2 && $wh_array["mardi"][0]["type"] ==  "null" ||
                $dayOfTheWeek == 3 && $wh_array["mercredi"][0]["type"] == "null" ||
                $dayOfTheWeek == 4 && $wh_array["jeudi"][0]["type"] ==  "null" ||
                $dayOfTheWeek == 5 && $wh_array["vendredi"][0]["type"] == "null" ||
                $dayOfTheWeek == 6 && $wh_array["samedi"][0]["type"] ==  "null"
            ) {
                $res = 1;
            }
        }
        return $res;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    private function checkIfDayIsNull($nowTime)
    {

        $day =  date('d-m-Y', $nowTime);

        $res = $this->JourNullRepo()->findBy(['jour'=> $day]);

        if (sizeof($res) == 0) {
            return false;
        }

        return true;
    }

    private function checkIfDayHasPermission($emp,$nowTime)
    {
//        $day =  date('d-m-Y', $nowTime);
        return  $isPermDate = $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")->checkInPerm($emp,date('Y-m-d',$nowTime));

//        return false;
    }

=======
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
=======
>>>>>>> parent of 0490b61... ÂMon Aug 26 17:07:49 GMT 2019
}
