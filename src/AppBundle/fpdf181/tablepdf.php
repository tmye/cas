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
    public function FancyTable($header, $data, $data2,$data3){
        // Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(100,100,100);
        $this->SetTextColor(255);
        $this->SetDrawColor(60,60,60);
        $this->SetLineWidth(.3);
        $this->SetFont('helvetica','','11');
        // En-tête
        $w = array(40, 35, 30, 30,30,30);
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
            $this->Cell($w[2],6,number_format($row[2],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[5],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        foreach($data2 as $row)
        {
            $this->Cell($w[0],6,$row[0],'LT',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'TR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($row[2],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[5],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        foreach($data3 as $row)
        {
            $this->Cell($w[0],6,$row[0],'LT',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'TR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($row[2],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3],0,',',' '),'LR',0,'R',$fill);
            $this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
            $this->Cell($w[5],6,$row[5],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }

}