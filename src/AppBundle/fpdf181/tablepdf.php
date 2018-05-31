<?php
/**
 * Created by PhpStorm.
 * User: ebenezer
 * Date: 23/05/2018
 * Time: 12:40
 */

namespace AppBundle\fpdf181;
use AppBundle\fpdf181\fpdf;


class tablepdf extends fpdf
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

    public function FancyTable($header, $data, $data2,$data3,$data4){
        // Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(100,100,100);
        $this->SetTextColor(255);
        $this->SetDrawColor(60,60,60);
        $this->SetLineWidth(.3);
        $this->SetFont('helvetica','','11');
        // En-tête
        $w = array(30, 35, 25, 25,25,25,25);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Restauration des couleurs et de la police
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Données
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
            $this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
            $this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
            $this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[5],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[6],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        foreach($data2 as $row)
        {
            $this->Cell($w[0],6,$row[0],'LT',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'TR',0,'L',$fill);
            $this->Cell($w[2],6,$row[2].' H','LR',0,'R',$fill);
            $this->Cell($w[3],6,"-",'LR',0,'R',$fill);
            $this->Cell($w[4],6,$this->convertHour($row[4]),'LR',0,'L',$fill);
            $this->Cell($w[5],6,$this->convertHour($row[5]),'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[6],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        foreach($data3 as $row)
        {
            $this->Cell($w[0],6,$row[0],'LT',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'TR',0,'L',$fill);
            $this->Cell($w[2],6,$this->formatInt(ceil($row[2])),'LRB',0,'R',$fill);
            $this->Cell($w[3],6,'-','LRB',0,'R',$fill);
            $this->Cell($w[4],6,$this->formatInt(ceil($row[4])),'LRB',0,'L',$fill);
            $this->Cell($w[5],6,$this->formatInt(ceil($row[5])),'LRB',0,'L',$fill);
            $this->Cell($w[5],6,$this->formatInt(ceil($row[6])),'LRB',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        foreach($data4 as $row)
        {
            $this->Cell($w[0],6,$row[0],'LT',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'TR',0,'L',$fill);
            $this->Cell(125,6,$this->formatInt(ceil($row[2])),'LRT',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }

}