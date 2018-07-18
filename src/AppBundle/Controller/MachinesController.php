<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 29/01/2018
 * Time: 09:15
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Machine;
use AppBundle\Entity\MachineDuplicated;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;
use TmyeDeviceBundle\Entity\UpdateEntity;

class MachinesController extends Controller
{

    /**
     * @Route("/addMachine",name="addMachine")
     */
    public function addMachineAction(Request $request)
    {
        $session = new Session();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine")->findAll();
            $machine = new Machine();
            //$machine->setCompany($session->get("connection"));

            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('name', TextType::class, array('label' =>' '))
                ->add('machineId', TextType::class, array('label' =>' '))
                ->add('description', TextareaType::class, array('label' =>' '))
                ->add('departements', EntityType::class,array(
                    'em'=>$session->get("connection"),
                    'class'=>'AppBundle:Departement',
                    'label'=>' ',
                    'choice_label' => 'name',
                    'multiple' => true,
                ))
                ->add('Ajouter', SubmitType::class);

            // À partir du formBuilder, on génère le formulaire

            $form = $formBuilder->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager($session->get("connection"));
                    $em->persist($machine);
                    $em->flush();

                    $cas_machine = new MachineDuplicated();
                    $cas_machine->setName($machine->getName());
                    $cas_machine->setMachineId($machine->getMachineId());
                    $cas_machine->setDescription($machine->getDescription());
                    $cas_machine->setCompany($session->get("connection"));

                    $cas_em = $this->getDoctrine()->getManager("cas");
                    $cas_em->persist($cas_machine);
                    $cas_em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Machine bien enregistrée.');

                    return $this->redirectToRoute("addMachine");
                }
            }

            // À ce stade, le formulaire n'est pas valide car :
            // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
            // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
            return $this->render('api/addMachine.html.twig', array(
                'form' => $form->createView(),
                'machines' => $machines
            ));
        }else{
            return $this->redirectToRoute("login");
        }

    }

    /**
     * @Route("/editMachine/{id}",name="editMachine")
     */
    public function editMachineAction(Request $request, $id)
    {
        $session = new Session();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $expiry_service = $this->container->get('app_bundle_expired');
                if ($expiry_service->hasExpired()) {
                    return $this->redirectToRoute("expiryPage");
                }
                $machines = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine")->findAll();
                $machine = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine")->find($id);

                if($machine != null){
                    // On crée le FormBuilder grâce au service form factory
                    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

                    // On ajoute les champs de l'entité que l'on veut à notre formulaire
                    $formBuilder
                        ->add('name', TextType::class, array('label' =>' '))
                        ->add('machineId', TextType::class, array('label' =>' '))
                        ->add('description', TextareaType::class, array('label' =>' '))
                        ->add('departements', EntityType::class,array(
                            'em'=>$session->get("connection"),
                            'class'=>'AppBundle:Departement',
                            'label'=>' ',
                            'choice_label' => 'name',
                            'multiple' => true,
                        ))
                        ->add('Modifier', SubmitType::class);

                    // À partir du formBuilder, on génère le formulaire

                    $form = $formBuilder->getForm();

                    if ($request->isMethod('POST')) {
                        $form->handleRequest($request);
                        if ($form->isValid()) {
                            $em = $this->getDoctrine()->getManager($session->get("connection"));
                            $em->persist($machine);
                            $em->flush();

                            $request->getSession()->getFlashBag()->add('notice', 'Cette machine a bien été modifiée.');

                            //return $this->redirectToRoute("addMachine");
                        }
                    }

                    // À ce stade, le formulaire n'est pas valide car :
                    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
                    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
                    return $this->render('api/addMachine.html.twig', array(
                        'form' => $form->createView(),
                        'machines' => $machines
                    ));
                }else{
                    throw new NotFoundHttpException("La machine d'id " . $id . " n'existe pas.");
                }
            }else{
                throw new AccessDeniedException("Accès réservé aux administrateurs");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/deleteMachine/{id}",name="deleteMachine")
     */
    public function deleteMachineAction(Request $request, $id)
    {
        $session = new Session();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            // On vérifie que le département n'est pas vide
            $machine = $this->getDoctrine()->getManager($session->get("connection"))->getRepository('AppBundle:Machine')->find($id);
            if ($machine != null) {
                $em = $this->getDoctrine()->getManager($session->get("connection"));
                $em->remove($machine);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Cette machine bien été supprimée.');
                return $this->redirectToRoute("addMachine");
            } else{
                throw new NotFoundHttpException("La machine d'id " . $id . " n'existe pas.");
            }
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/pubCovers",name="pubCovers")
     */
    public function pubCoversAction(Request $request)
    {
        $session = new Session();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if ($expiry_service->hasExpired()) {
                return $this->redirectToRoute("expiryPage");
            }
            $machines = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine")->findAll();
            $departements = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            $machines = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine")->findAll();
            return $this->render('cas/pubCovers.html.twig',array(
                'departements'=>$departements,
                'machines'=>$machines
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    // Les fonctions relatives à la gestion globale du système

    private function returnMachinesForSelectedDeps($tab){
        $session = new Session();

        $emMac = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Machine");
        $tabMac = array();
        foreach ($tab as $depId){
            $data = $emMac->machineByDep($depId);
            array_push($tabMac,$data);
        }
        return $tabMac;
    }

    /**
     * @Route("/syncEmp",name="syncEmp")
     */
    public function syncEmpAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
            // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;

        if($len >= 2){
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */
            foreach ($finalTab as $mac){
                // On boucle sur tous les departements
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="emp" && $donnees[$i]->getIsactive()==1 && $donnees[$i]->getContent()==$ee->getId()){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("emp");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="emp" && $donnees[$i]->getIsactive()==1 && $donnees[$i]->getContent()==$ee->getId()){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("emp");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncEmpPP",name="syncEmpPP")
     */
    public function syncEmpPPAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $i = 0;
        $found = 0;

        if($len >= 2){
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
                // On boucle sur tous les departements
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pp" && $donnees[$i]->getIsactive()==1){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("pp");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }
            return new Response("OK");
        }elseif($len == 1){
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                // On boucle sur tous les departements
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pp" && $donnees[$i]->getIsactive()==1){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("pp");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }

            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncEmpF",name="syncEmpF")
     */
    public function syncEmpFAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $i = 0;
        $found = 0;

        if($len >= 2){
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }
            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */
            foreach ($finalTab as $mac){
                // On boucle sur tous les departements
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="fingerprints" && $donnees[$i]->getIsactive()==1){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("fingerprints");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                // On boucle sur tous les departements
                foreach ($tabDeps as $d){
                    // On boucle sur les employes
                    $empl = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Employe")->employeeByDep($d);
                    foreach ($empl as $ee){
                        $found = 0;
                        while($found == 0 && $i < sizeof($donnees)){
                            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="fingerprints" && $donnees[$i]->getIsactive()==1){
                                $found = 1;
                            }
                            $i++;
                        }
                        if ($found == 0){
                            $updateE = new UpdateEntity();
                            $updateE->setDeviceId($mac);
                            $updateE->setContent($ee->getId());
                            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                            $updateE->setIsactive(true);
                            $updateE->setType("fingerprints");

                            $em->persist($updateE);
                            $em->flush();
                        }
                    }
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncReboot",name="syncReboot")
     */
    public function syncRebootAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;
        echo "\nlength : ".$len;

        if($len >= 2){
            echo "\nJe suis dans le premier cas";
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
                $found = 0;
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="reboot" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("reboot");

                    $em->persist($updateE);
                    $em->flush();
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="reboot" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "Found2 = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("reboot");

                    $em->persist($updateE);
                    $em->flush();
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncRebootByMac",name="syncRebootByMac")
     */
    public function syncRebootByMacAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        $mac = $request->request->get("mac");
        echo "Mac : ".$mac;

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;


        /*
         * On persiste les éléments en fonction du cas
         * Mais bien en avant ça, on vérifie s'il n'ya pas
         * déjà ces memes données dans la table.
         */

        while($found == 0 && $i < sizeof($donnees)){
            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="reboot" && $donnees[$i]->getIsactive()==1){
                $found = 1;
            }
            //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
            $i++;
        }
        echo "\n Found = :".$found;
        if ($found == 0){
            $updateE = new UpdateEntity();
            $updateE->setDeviceId($mac);
            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
            $updateE->setIsactive(true);
            $updateE->setType("reboot");

            $em->persist($updateE);
            $em->flush();
        }
        //return new Response(json_encode($finalTab));
        return new Response("OK");
    }

    /**
     * @Route("/syncDeleteForAll",name="syncDeleteForAll")
     */
    public function syncDeleteForAllAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;
        echo "\nlength : ".$len;

        if($len >= 2){
            echo "\nJe suis dans le premier cas";
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
            $found = 0;
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="1doclean" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("1doclean");

                    $em->persist($updateE);
                    $em->flush();
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="1doclean" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "Found2 = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("1doclean");

                    $em->persist($updateE);
                    $em->flush();
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }

        //return new Response(json_encode($finalTab));
        return new Response("Données mises à jour");
    }

    /**
     * @Route("/syncDeleteByMac",name="syncDeleteByMac")
     */
    public function syncDeleteByMacAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        $mac = $request->request->get("mac");
        echo "Mac : ".$mac;

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;


        /*
         * On persiste les éléments en fonction du cas
         * Mais bien en avant ça, on vérifie s'il n'ya pas
         * déjà ces memes données dans la table.
         */

        while($found == 0 && $i < sizeof($donnees)){
            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="1doclean" && $donnees[$i]->getIsactive()==1){
                $found = 1;
            }
            //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
            $i++;
        }
        echo "\n Found = :".$found;
        if ($found == 0){
            $updateE = new UpdateEntity();
            $updateE->setDeviceId($mac);
            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
            $updateE->setIsactive(true);
            $updateE->setType("1doclean");

            $em->persist($updateE);
            $em->flush();
        }
        //return new Response(json_encode($finalTab));
        return new Response("OK");
    }


    /**
     * @Route("/syncDepartement",name="syncDepartement")
     */
    public function syncDepartementAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;
        echo "\nlength : ".$len;

        if($len >= 2){
            echo "\nJe suis dans le premier cas";
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
                $found = 0;
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId()." vs ".$mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="dept" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("dept");

                    $em->persist($updateE);
                    $em->flush();
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="dept" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "Found2 = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("dept");

                    $em->persist($updateE);
                    $em->flush();
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncPubCoverAll",name="syncPubCoverAll")
     */
    public function syncPubCoverAllAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        /*
         * Must do a test
         * In case that we call this function in syncAll()
        */
        if($request->request->get("deps") != null && !empty($request->request->get("deps"))){
            $tabDeps = $request->request->get("deps");
        }else{
            $result = $this->getDoctrine()->getManager($session->get("connection"))->getRepository("AppBundle:Departement")->findAll();
            foreach ($result as $dep){
                $tabDeps[]=$dep->getId();
            }
        }
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;
        echo "\nlength : ".$len;

        if($len >= 2){
            echo "\nJe suis dans le premier cas";
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
                $found = 0;
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId()." vs ".$mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pub" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("pub");

                    $em->persist($updateE);
                    $em->flush();
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pub" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "Found2 = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("pub");

                    $em->persist($updateE);
                    $em->flush();
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncPubCoverByDep",name="syncPubCoverByDep")
     */
    public function syncPubCoverByDepAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        $tabDeps = $request->request->get("deps");
        $tab = $this->returnMachinesForSelectedDeps($tabDeps);
        // Pour éviter la duplication des données
        $len = sizeof($tab);

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;
        echo "\nlength : ".$len;

        if($len >= 2){
            echo "\nJe suis dans le premier cas";
            $finalTab = $tab[0];
            for($cpt=0;$cpt<$len;$cpt++){
                foreach ($tab[$cpt] as $t){
                    if (!in_array($t,$tab[0],true)){
                        array_push($finalTab,$t);
                    }
                }
            }

            print_r($finalTab);

            /*
             * On persiste les éléments en fonction du cas
             * Mais bien en avant ça, on vérifie s'il n'ya pas
             * déjà ces memes données dans la table.
            */


            foreach ($finalTab as $mac){
                $found = 0;
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId()." vs ".$mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pub" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }else{
                        echo "\n not found";
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "\n Found = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("pub");

                    $em->persist($updateE);
                    $em->flush();
                }
            }
            //return new Response(json_encode($finalTab));
            return new Response("OK");
        }elseif($len == 1){
            echo "Je suis dans le dernier cas";
            // On persiste les éléments en fonction du cas
            foreach ($tab[0] as $mac){
                while($found == 0 && $i < sizeof($donnees)){
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pub" && $donnees[$i]->getIsactive()==1){
                        $found = 1;
                    }
                    //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
                    $i++;
                }
                echo "Found2 = :".$found;
                if ($found == 0){
                    $updateE = new UpdateEntity();
                    $updateE->setDeviceId($mac);
                    $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
                    $updateE->setIsactive(true);
                    $updateE->setType("pub");

                    $em->persist($updateE);
                    $em->flush();
                }
            }

            //return new Response(json_encode($tab));
            return new Response("OK pour le second cas");
        }else{
            echo "Je ne rentre jamais dedans";
        }
    }

    /**
     * @Route("/syncPubCoverByMac",name="syncPubCoverByMac")
     */
    public function syncPubCoverByMacAction(Request $request)
    {
        $session = new Session();

        $em = $this->getDoctrine()->getManager($session->get("connection"));

        $mac = $request->request->get("mac");
        echo "Mac : ".$mac;

        // Variables d'élimination de doublons
        // Anciennes données
        $donnees = $em->getRepository("TmyeDeviceBundle:UpdateEntity")->findAll();
        //print_r($donnees);
        $found = 0;
        $i = 0;


        /*
         * On persiste les éléments en fonction du cas
         * Mais bien en avant ça, on vérifie s'il n'ya pas
         * déjà ces memes données dans la table.
         */

        while($found == 0 && $i < sizeof($donnees)){
            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pub" && $donnees[$i]->getIsactive()==1){
                $found = 1;
            }
            //$session->getFlashBag()->add('passage : ',$donnees[$i]->getDeviceId());
            $i++;
        }
        echo "\n Found = :".$found;
        if ($found == 0){
            $updateE = new UpdateEntity();
            $updateE->setDeviceId($mac);
            $updateE->setCreationDate(date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'));
            $updateE->setIsactive(true);
            $updateE->setType("pub");

            $em->persist($updateE);
            $em->flush();
        }
        //return new Response(json_encode($finalTab));
        return new Response("OK");
    }

    /**
     * @Route("/syncAll", name="syncAll")
     */
    public function syncAllAction(Request $request)
    {
        $a = $this->syncDeleteForAllAction($request);
        $b = $this->syncDepartementAction($request);
        $c = $this->syncEmpAction($request);
        $d = $this->syncEmpFAction($request);
        $e = $this->syncEmpPPAction($request);
        $f = $this->syncPubCoverAllAction($request);
        //$g = $this->syncRebootAction($request);

        return new Response("OK");

    }
}