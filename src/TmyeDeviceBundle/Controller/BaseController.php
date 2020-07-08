<?php

namespace TmyeDeviceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Created by PhpStorm.
 * User: abiguime
 * Date: 16/02/2017
 * Time: 1:12 AM
 */
class BaseController extends Controller
{

    private $logger = 0;

    protected function processForm(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Form\Form $form)
    {

        if (!($form->isSubmitted() && $form->isValid())) {
            $this->requestInvalid();
        } else {
            echo "form is valid";
        }
    }

    protected function persist($obj)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($obj);
        $em->flush();
    }

    protected function deleteEntity($entity)
    {
        $this->getManager()->remove($entity);
        $this->getManager()->flush();
    }

    protected function serialize($data)
    {
        /* return $this->container->get('jms_serializer')
             ->serialize($data, 'json');*/
        return json_encode($data);
    }

    protected function deserialize($data)
    {
//        return $this->container->get('jms_serializer')
//            ->deserialize($data, \ArrayObject::class, 'json');
        return json_decode(data, true);
    }

    protected function requestInvalid()
    {
        $data = [
            'error' => -1,
            'message' => "parameters error",
            'data' => []
        ];
        echo json_encode($data);
        exit();
    }


    protected function getUserNameFromToken($token)
    {
        // check if the token is still valid, if no redirect to login page.
        $token = $this->TokenRepo()->findOneByTokenkey($token);
        $user = $this->AdminRepo()->find($token->getUserId());
        return $user;
    }

    public function permissionsDays ($permissions){

        $totalPermissionsDays = 0;
        foreach ($permissions as $permission) {

            // day that starts the time stamp
            $permissionDayStart =  strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()));
            $permissionDayEnd =  strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp())) + 86400-1;

            $wh_array = json_decode($permission->getEmployee()->getWorkingHour()->getWorkingHour(), true);

            // we can move one day at the time until the permission is ended
            for ($dayTimeStamp = $permissionDayStart; $dayTimeStamp < $permissionDayEnd; $dayTimeStamp+=86400) {


                $dayOfTheWeek = date('w', $dayTimeStamp); // 0 sunday, 6 saturday

                $dayLibelle = "";
                // we have something
                if ($dayOfTheWeek == 0) {
                    $dayLibelle = "dimanche";
                }
                if ($dayOfTheWeek == 1) {
                    $dayLibelle = "lundi";
                }
                if ($dayOfTheWeek == 2) {
                    $dayLibelle = "mardi";
                }
                if ($dayOfTheWeek == 3) {
                    $dayLibelle = "mercredi";
                }
                if ($dayOfTheWeek == 4) {
                    $dayLibelle = "jeudi";
                }
                if ($dayOfTheWeek == 5) {
                    $dayLibelle = "vendredi";
                }
                if ($dayOfTheWeek == 6) {
                    $dayLibelle = "samedi";
                }

                // check if the employee is supposed to work on this day
                if (($wh_array[$dayLibelle][0]["type"] != "null"))  {
                    $totalPermissionsDays++;
                }
            }
        }
        return $totalPermissionsDays;

    }

    public function permissionsDaysWithoutCheckWorkingTime ($permissions){

        $totalPermissionsDays = 0;
        foreach ($permissions as $permission) {

            // day that starts the time stamp
            $permissionDayStart =  strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()));
            $permissionDayEnd =  strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp())) + 86400-1;

            // we can move one day at the time until the permission is ended
            for ($dayTimeStamp = $permissionDayStart; $dayTimeStamp < $permissionDayEnd; $dayTimeStamp+=86400) {

                $totalPermissionsDays++;
            }
        }
        return $totalPermissionsDays;

    }

    public function permissionsTime ($permissions) {


        $totalPermissionsTime = 0;
        foreach ($permissions as $permission) {

            // single permission, compute : 01 Janvier 2020 , 14:00 -> 05 Janvier 2020 12:00

//            $totalPermissionsTime+=(strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo()) - strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom()));

            // get the start and then of the permission on each days.

            $permissionStart = strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom());
            $permissionEnd = strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo());

            $this->info("");
            $this->info("permissionStart ".date("Y-m-d H:i:s", $permissionStart)." permissionEnd ".date("Y-m-d H:i:s", $permissionEnd));

            // day that starts the time stamp
            $permissionDayStart =  strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()));
            $permissionDayEnd =  strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp())) + 86400-1;
            $this->info("permissionDayStart ".date("Y-m-d H:i:s", $permissionDayStart)." permissionDayEnd ".date("Y-m-d H:i:s", $permissionDayEnd));

            // i have the permission, but not the employee
//            echo ($permission->getEmployee()->getWorkingHour()->getWorkingHour()); exit;
            $wh_array = json_decode($permission->getEmployee()->getWorkingHour()->getWorkingHour(), true);

            // we can move one day at the time until the permission is ended
            for ($dayTimeStamp = $permissionDayStart; $dayTimeStamp < $permissionDayEnd; $dayTimeStamp+=86400) {

                $this->info("dayTimeStamp ".date("Y-m-d H:i:s", $dayTimeStamp));

                $dayOfTheWeek = date('w', $dayTimeStamp); // 0 sunday, 6 saturday

                $dayLibelle = "";
                // we have something
                if ($dayOfTheWeek == 0) {
                    $dayLibelle = "dimanche";
                }
                if ($dayOfTheWeek == 1) {
                    $dayLibelle = "lundi";
                }
                if ($dayOfTheWeek == 2) {
                    $dayLibelle = "mardi";
                }
                if ($dayOfTheWeek == 3) {
                    $dayLibelle = "mercredi";
                }
                if ($dayOfTheWeek == 4) {
                    $dayLibelle = "jeudi";
                }
                if ($dayOfTheWeek == 5) {
                    $dayLibelle = "vendredi";
                }
                if ($dayOfTheWeek == 6) {
                    $dayLibelle = "samedi";
                }


                if (($wh_array[$dayLibelle][0]["type"] ==  "null" )) {
                    continue;
                } else {

                    // start border
                    // same day as the permission day
                    if ($dayTimeStamp <= strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom())
                        && strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom()) < $dayTimeStamp+86400
                        && strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom()) > strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$wh_array[$dayLibelle][0]["beginHour"])) {
                        $startBorder = strtotime(date('Y-m-d',$permission->getDateFrom()->getTimestamp()).' '.$permission->getTimeFrom()); // equal to start of the permission if it's greater that the start of the day
                    } else {
                        $startBorder = strtotime(date('Y-m-d',$dayTimeStamp).' '.$wh_array[$dayLibelle][0]["beginHour"]); // equal to start of the permission if it's greater that the start of the day
                    }

                    // end border
                    // same day as the permission day
                    if ($dayTimeStamp <= strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo())
                        && strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo()) <= $dayTimeStamp+86400
                        && strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo()) <= strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$wh_array[$dayLibelle][0]["endHour"])) {

                        $endBorder = strtotime(date('Y-m-d',$permission->getDateTo()->getTimestamp()).' '.$permission->getTimeTo()); // equal to start of the permission if it's greater that the start of the day
                    } else {
                        $endBorder = strtotime(date('Y-m-d',$dayTimeStamp).' '.$wh_array[$dayLibelle][0]["endHour"]); // equal to start of the permission if it's greater that the start of the day
                    }

                    $totalPermissionsTime+=($endBorder - $startBorder <= 0 ? 0 : ($endBorder - $startBorder));
                    $this->info("startborder ".date("Y-m-d H:i:s", $startBorder)." endBorder ".date("Y-m-d H:i:s", $endBorder));
                }
            }
        }

//        exit;
        return $totalPermissionsTime/3600;
//        return ($totalPermissionsTime < 0 ? 0 : $totalPermissionsTime)/3600; // heures
    }


    protected function base64__($pathtopic, $type = "")
    {

        $rootWebDir = $this->getParameter('web_dir');
        $path = $rootWebDir . DIRECTORY_SEPARATOR . $pathtopic;
        if (file_exists($path) && !is_dir($path)) {
            $data = file_get_contents($path);
        } else {
            $data = file_get_contents("img/default-profile.png");
            if ($type == "f") {
                $base64 = "";
                return $base64;
            }
        }

        $base64 = /*'data:image/' . $type . ';base64,' . */
            base64_encode($data);

        return $base64;
    }


    protected function systimeToFrench ($time) {

        return date('d-m-Y', $time);
    }

    /* done */
    protected function WorkingHourRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:WorkingHours");
    }

    /* done */
    protected function DepartementRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:Departement");
    }

    protected function RequestBlobRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:RequestBlob");
    }

    protected function PubsRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:DevicePubPic");
    }

    /* protected function AdminRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Admin");
     }*/

    /*  protected function TokenRepo () {
          return $this->getDoctrine()->getRepository("AppBundle:Token");
      }*/

    protected function EmployeeRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:Employe");
    }

    protected function ClockinRecordRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:ClockinRecord");
    }

    /* protected function PermissionRepo () {
         return $this->getDoctrine()->getRepository("AppBundle:Permission");
     }*/

    protected function MachineRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:Machine");
    }

    protected function UpdateEntityRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:UpdateEntity");
    }

    protected function OkidRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:OkIdEntity");
    }

    protected function JourNullRepo () {
        return $this->getDoctrine()->getRepository("AppBundle:NullDate");
    }

    protected function ConfigEntityRepo () {
        return $this->getDoctrine()->getRepository("TmyeDeviceBundle:ConfigEntity");
    }

    protected function getManager () {
        return $this->getDoctrine()->getManager();
    }

    protected function flush (){
        $this->getDoctrine()->getManager()->flush();
    }

    protected function info($message) {
        $this->logger = $this->get('logger');
        $this->logger->info("XXXXXXXXXXXXXXX    ".$message);
    }


    // utils classes
    protected function dateformTimeStamp($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    public function dateIsUnderPermission($employee, $nowTime)
    {

        return $this->getDoctrine()->getManager()->getRepository("AppBundle:Permission")
            ->employeeDateUnderPermission($employee, $nowTime);
    }

}