<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 23/05/2018
 * Time: 12:40
 */

namespace AppBundle\fpdf181;
use AppBundle\fpdf181\fpdf;


class todaytablepdf extends fpdf
{
    public function convertHour($value){

        $value = (int)$value;
        $day = floor ($value / 1440);
        $hour = floor (($value - $day * 1440) / 60);
        $min = $value - ($day * 1440) - ($hour * 60);

        if($value >= (60*24)){
            return $day."J ".$hour."H ".$min."min";
        }elseif ($value >= 60){
            return $hour."H ".$min."min";
        }else{
            return $min." min";
        }
    }

    function Footer()
    {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial','I',8);
        // Numéro de page
        $this->Cell(0,10,'Rapport genere par le '.date('d').'/'.date('m').'/'.date('Y'),0,0,'C');
    }

    public function convertInHour($value){

        $value = (int)$value;
        $hour = floor ($value  / 60);

        return $hour."H ";
    }

    public function formatInt($value){
        $value = (string)$value;
        $value_lenght = strlen($value);
        $str = "";
        if ($value_lenght >= 4) {
            $value_tab = str_split($value);
            $cpt=0;
            for ($i=$value_lenght-1;$i>=0;$i--){
                if($cpt==3){
                    $cpt=0;
                    $str = $value_tab[$i].".".$str;
                }else{
                    $str = $value_tab[$i]."".$str;
                }
                $cpt++;
            }
            return $str;
        }else{
            return $value;
        }
    }

    public function FancyTable($header, $data){
        // Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(100,100,100);
        $this->SetTextColor(255);
        $this->SetDrawColor(60,60,60);
        $this->SetLineWidth(.3);
        $this->SetFont('helvetica','','11');
        $fill = false;
        // En-tête
        $w = array(47.5);
        
        $this->Ln(5);
        
        for($i=0;$i<count($header);$i++){
            $this->Cell($w[0],7,$header[$i],1,0,'C',true);
        }
        $this->Ln();
        // Restauration des couleurs et de la police
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Données
        foreach($data as $row)
        {
            $this->Cell($w[0],6,date('H:i',$row[0]->getClockinTime()),'LRTB',0,'C',$fill);
            $this->Cell($w[0],6,$row[0]->getEmploye()->getSurname()." ".$row[0]->getEmploye()->getLastName(),'LRTB',0,'C',$fill);
            $this->Cell($w[0],6,$row[0]->getEmploye()->getFunction(),'LRTB',0,'C',$fill);
            $this->Cell($w[0],6,$row[1],'LRTB',0,'C',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }

}