<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkingHours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use AppBundle\fpdf181\fpdf;
use AppBundle\fpdf181\todaytablepdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AlertController extends ClockinReccordController
{

    /**
     * @Route("/today",name="today")
     */
    public function todayAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $expiry_service = $this->container->get('app_bundle_expired');
            if($expiry_service->hasExpired()){
                return $this->redirectToRoute("expiryPage");
            }
            $day = $this->dateDayNameFrench(date('N'));
            $finalTab = array();
            $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
            $listCR = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->todaysClockinTimes(date('Y').'-'.date('m').'-'.date('d'));
            foreach($listCR as $cr){
                if($this->arrive($cr,$day,$request)){
                    $finalTab[] = array($cr,"Arrivée");
                }elseif ($this->pause($cr,$day,$request)){
                    $finalTab[] = array($cr,"Pause");
                }elseif($this->finPause($cr,$day,$request)){
                    $finalTab[] = array($cr,"Fin pause");
                }elseif($this->depart($cr,$day,$request)){
                    $finalTab[] = array($cr,"Départ");
                }
            }
            return $this->render('cas/today.html.twig',array(
                'listDep'=>$listDep,
                'listCR'=>$finalTab
            ));
        }else{
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/generateTodayPDF",name="generateTodayPDF")
     */
    public function generateTodayPDFAction(Request $request)
    {
        $header = array('Heure','Employe', 'Fonction', 'Type');

        $day = $this->dateDayNameFrench(date('N'));
        $finalTab = array();
        $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
        $listCR = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->todaysClockinTimes(date('Y').'-'.date('m').'-'.date('d'));
        foreach($listCR as $cr){
            if($this->arrive($cr,$day,$request)){
                $finalTab[] = array($cr,"Arrivee");
            }elseif ($this->pause($cr,$day,$request)){
                $finalTab[] = array($cr,"Pause");
            }elseif($this->finPause($cr,$day,$request)){
                $finalTab[] = array($cr,"Fin pause");
            }elseif($this->depart($cr,$day,$request)){
                $finalTab[] = array($cr,"Depart");
            }
        }
        
        $pdf = new todaytablepdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->FancyTable($header,$finalTab);
        $pdf->Output();
    }

    /**
     * @Route("/generateTodayExcel",name="generateTodayExcel")
     */
    public function generateTodayExcelAction(Request $request)
    {    
        $day = $this->dateDayNameFrench(date('N'));
        $finalTab = array();
        $listDep = $this->getDoctrine()->getManager()->getRepository("AppBundle:Departement")->findAllSafe();
        $listCR = $this->getDoctrine()->getManager()->getRepository("AppBundle:ClockinRecord")->todaysClockinTimes(date('Y').'-'.date('m').'-'.date('d'));
        foreach($listCR as $cr){
            if($this->arrive($cr,$day,$request)){
                $finalTab[] = array($cr,"Arrivée");
            }elseif ($this->pause($cr,$day,$request)){
                $finalTab[] = array($cr,"Pause");
            }elseif($this->finPause($cr,$day,$request)){
                $finalTab[] = array($cr,"Fin pause");
            }elseif($this->depart($cr,$day,$request)){
                $finalTab[] = array($cr,"Départ");
            }
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A3', 'Heure');
        $sheet->setCellValue('B3', 'Employé');
        $sheet->setCellValue('C3', 'Fonction');
        $sheet->setCellValue('D3', 'Type');

        $i = 3;
        foreach($finalTab as $el){
            $i++;
            $sheet->setCellValue('A'.$i, date('H:i',$el[0]->getClockinTime()));
            $sheet->setCellValue('B'.$i, $el[0]->getEmploye()->getSurname().' '.$el[0]->getEmploye()->getLastName());
            $sheet->setCellValue('C'.$i, $el[0]->getEmploye()->getFunction());
            $sheet->setCellValue('D'.$i, $el[1]);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('presences.xlsx');

        //sleep(10);

        $filePath = $this->getParameter("web_dir")."/presences.xlsx";

        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            "presences.xlsx",
            iconv('UTF-8', 'ASCII//TRANSLIT', "presences.xlsx")
        );
        return $response;
    }
    
}
