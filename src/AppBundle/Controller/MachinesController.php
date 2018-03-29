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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $machine = new Machine();

        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', TextType::class, array('label' =>' '))
            ->add('machineId', TextType::class, array('label' =>' '))
            ->add('description', TextareaType::class, array('label' =>' '))
            ->add('departements', EntityType::class,array(
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
                $em = $this->getDoctrine()->getManager();
                $em->persist($machine);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Machine bien enregistrée.');

                return new Response("OK machine enregistrée");
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('api/addMachine.html.twig', array(
            'form' => $form->createView(),
            'machines' => $machines
        ));
    }

    /**
     * @Route("/editMachine/{id}",name="editMachine")
     */
    public function editMachineAction(Request $request, $id)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $machine = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->find($id);

        if($machine != null){
            // On crée le FormBuilder grâce au service form factory
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $machine);

            // On ajoute les champs de l'entité que l'on veut à notre formulaire
            $formBuilder
                ->add('name', TextType::class, array('label' =>' '))
                ->add('machineId', TextType::class, array('label' =>' '))
                ->add('description', TextareaType::class, array('label' =>' '))
                ->add('departements', EntityType::class,array(
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
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($machine);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Machine bien enregistrée.');

                    return new Response("OK machine modifiée");
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
            throw new NotFoundHttpException("Le département d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/deleteMachine/{id}",name="deleteMachine")
     */
    public function deleteMachineAction(Request $request, $id)
    {
        // On vérifie que le département n'est pas vide

        $machine = $this->getDoctrine()->getManager()->getRepository('AppBundle:Machine')->find($id);
        if ($machine != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($machine);
            $em->flush();
            return new Response("Cette machine a été supprimée de la base de données");
        } else{
            throw new NotFoundHttpException("La machine d'id " . $id . " n'existe pas.");
        }
    }

    /**
     * @Route("/pubCovers",name="pubCovers")
     */
    public function pubCoversAction(Request $request)
    {
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        $departements = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAll();
        $machines = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine")->findAll();
        return $this->render('cas/pubCovers.html.twig',array(
            'departements'=>$departements,
            'machines'=>$machines
        ));
    }

    // Les fonctions relatives à la gestion globale du système

    private function returnMachinesForSelectedDeps($tab){
        $emMac = $this->getDoctrine()->getManager()->getRepository("AppBundle:Machine");
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

        $em = $this->getDoctrine()->getManager();

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
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="emp" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("emp");
                    $updateE->setContent("");

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
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="emp" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("emp");
                    $updateE->setContent("");

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
     * @Route("/syncEmpPP",name="syncEmpPP")
     */
    public function syncEmpPPAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

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
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pp" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("pp");
                    $updateE->setContent("");

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
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="pp" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("pp");
                    $updateE->setContent("");

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
     * @Route("/syncEmpF",name="syncEmpF")
     */
    public function syncEmpFAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

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
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="fingerprints" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("fingerprints");
                    $updateE->setContent("");

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
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="fingerprints" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("fingerprints");
                    $updateE->setContent("");

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
     * @Route("/syncReboot",name="syncReboot")
     */
    public function syncRebootAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

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
                    $updateE->setContent("");

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
                    $updateE->setContent("");

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

        $em = $this->getDoctrine()->getManager();

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
            $updateE->setContent("");

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

        $em = $this->getDoctrine()->getManager();

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
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="doclean" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("doclean");
                    $updateE->setContent("");

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
                    if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="doclean" && $donnees[$i]->getIsactive()==1){
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
                    $updateE->setType("doclean");
                    $updateE->setContent("");

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

        $em = $this->getDoctrine()->getManager();

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
            if($donnees[$i]->getDeviceId() == $mac && $donnees[$i]->getType()=="doclean" && $donnees[$i]->getIsactive()==1){
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
            $updateE->setType("doclean");
            $updateE->setContent("");

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

        $em = $this->getDoctrine()->getManager();

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
                echo "\n J'arrive meme ici et le found est : ".$found;
                while($found == 0 && $i < sizeof($donnees)){
                    echo "\n size of donnees =: ".sizeof($donnees);
                    echo "\n isActive =: ".$donnees[$i]->getIsactive();
                    echo ("Comparaison : ".$donnees[$i]->getDeviceId() == $mac);
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
                    $updateE->setContent("");

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
                    $updateE->setContent("");

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
}