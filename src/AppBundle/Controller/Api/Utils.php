<?php

namespace AppBundle\Controller\Api;

class Utils{
    public function jourSemaine($date){
        $time = strtotime($date."00:00:00");
        $j = date('N',$time);
        $dayToSubstract = 0;
        if($j > 1){
            $dayToSubstract = $j-1;
        }
        $timeToSubstract = 60*60*24*$dayToSubstract;
        $timeAfterSubstract = $time - $timeToSubstract;
        $dateAfterSubstract = date('d-m-Y',$timeAfterSubstract);
        return array($j,$dayToSubstract,$dateAfterSubstract);
    }

    public function dateDayNameFrench($day){
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
}


?>


