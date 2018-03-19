<?php
/**
 * Created by PhpStorm.
 * User: abiguime
 * Date: 24/07/2017
 * Time: 12:26 PM
 */

namespace Tyme\DeviceBundle\Controller;

use FingerPrintBundle\Controller\BaseController;
use FingerPrintBundle\Entity\Admin;
use FingerPrintBundle\Entity\ClokinRecord;
use FingerPrintBundle\Entity\ConfigEntity;
use FingerPrintBundle\Entity\Employee;
use FingerPrintBundle\Entity\OkIdEntity;
use FingerPrintBundle\Entity\RequestBlob;
use FingerPrintBundle\Entity\UpdateEntity;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;



class MachineSysController extends \Tmye\DeviceBundle\Controller\BaseController
{

    /**
     * @Route("/api/data/get",name="machine_data_in_get")
     * @Method("GET")
     */
    public function index_getAction (Request $request) {


        $sn = trim($request->query->get("sn"));
        $all = $this->UpdateEntityRepo()->findBy(
            ['deviceId' => $sn ],
            ['id' => 'ASC']
        );

        $res['status'] = 1;
        $res['info'] = 'ok';
        $res['data'] = [];

        foreach ($all as &$item) {

            if ($item->getType() == "reboot") {
                if ($item != null) {
                    $tmp = json_decode("{\"id\":\"0\",\"do\":\"cmd\",\"cmd\":\"reboot\"}", true);
                    $tmp['id'] = $item->getId();
                    array_push($res['data'], $tmp);
                    break;
                }
            }
            if ($item->getType() == "emp") {
                if ($item != null) {
                    $tmp = $this->getAllUsers($item->getId());
                    $res['data'] = $tmp;
                }
            }
            if ($item->getType() == "fingerprints") {
                if ($item != null) {
                    $tmp = $this->getAllFingerprints($item->getId());
                    $res['data'] = $tmp;
                    break;
                }
            }
            if ($item->getType() == "pub") {
                $tmp = json_decode($item->getContent(), true);
                if ($tmp != []) {
                    $tmp = $this->getPubSetupContent(intval($tmp['index']));
                    $tmp['id'] = $item->getId();
                    array_push($res['data'], $tmp);
//                    break;
                }
            }
            if ($item->getType() == "dept") {
                if ($item != null) {

                    if ($item->getContent() == "clear") {

                        // clear the dept
                        $tmp = [
                            'id' => $item->getId(),
                            'do' => 'delete',
                            'data' => "dept",
                            'dept' =>  $this->getAllDepartementsIdzOnly()
                        ];
                        array_push($res['data'], $tmp);


                        // clear the employees
//                        {id:”1006”,do:”delete”,data:[”user”,”fingerprint”,”face”,”headpic”,”clockin”,”pic”],ccid:[13245,8784,54878]}
                        $tmp = [
                            'id' => $item->getId(),
                            'do' => 'delete',
                            'data' => ["user", "fingerprint", "face", "headpic", "clockin", "pic"],
                            'ccid' =>  $this->getAllEmployeesIdzOnly()
                        ];
                        array_push($res['data'], $tmp);


                    } else {
                        $tmp = [
                            'id' => $item->getId(),
                            'do' => 'update',
                            'data' => "dept",
                            'dept' =>  $this->getAllDepartements()
                        ];
                        array_push($res['data'], $tmp);
                    }
                }
            }
            if ($item->getType() == "pp") {
                if ($item != null) {
                    $tmp = $this->getProfilePictures($item->getId());
                    $res['data'] = $tmp;
                    break;
                }
            }
            if ($item->getType() == "doclean") {
                if ($item != null) {
                    $tmp = [
                        'id' => $item->getId(),
                        'do' => 'delete',
                        'data' => ["user","fingerprint", "face", "headpic", "clockin", "pic", "dept"]
                    ];
                    array_push($res['data'], $tmp);
                }
            }
        }

        /* this is the standard setup that doesn't move from here */
        array_push($res['data'], $this->standardSetup());

        // reset return values
        if (intval(date("i", time()))%10 == 0)
            $this->resetReturnValues();

        $this->info("GETFF   ".$this->serialize($res));
        return new Response($this->serialize($res));
    }


    /**
     * @Route("/api/data/unixtime",name="machine_data_unixtime")
     * @Method("GET")
     */
    public function unixTimeSetupAction (Request $request) {

        $sn = $request->query->get("sn");
        $all = $this->UpdateEntityRepo()->findBy([
            'deviceId' => $sn,
            'type' => 'time'
        ]);
        $res['status'] = 1;
        $res['info'] = 'ok';
        $res['data']["timezone"] = "UTC";
        $res['data']["unixtime"] = time()-3600;
        $res['data']["datetime"] = date("Y-m-d H:i:s",   $res['data']["unixtime"]);

        $this->info($this->serialize($res));

        return new Response($this->serialize($res));
    }


    /**
     * @Route("/api/data/post",name="machine_data_in_post")
     * @Method("POST")
     */
    public function index_postAction (Request $request) {

        $responsePack = json_decode($request->getContent(), true);

        $this->info("YYYYYYYYYYYY -- ".$request->getContent());
        $sn = $request->get("sn");

        // open the file here
        foreach ($responsePack as &$resp) {
            $resp['sn'] = $sn;
            $this->manage($resp);
        }

        $returnresp = [
            'status' => 1,
            'info' => "ok",
            'data' => $this->getOkStatusArray()
        ];

        $response = json_encode($returnresp);
        $this->info($response);
        return new Response($response);
    }


    /**
     * @Route("/sys/update/timezone",name="sys_update_timezone")
     * @Method("GET")
     */
    public function systemUpdateTimezoneAction (Request $request) {

        $gmt_time_zone = $request->query->get("zone");

        // add in the database update requests...
        $allMachines = $this->MachineRepo()->findAll();

        $content = ["timezone" => "GMT"];

        if ($gmt_time_zone >= 1) {
            $content = ["timezone" => "GMT+$gmt_time_zone"];
        }

        $content = $this->serialize($content);

        foreach ($allMachines as &$machine) {
            $updateEntity = new UpdateEntity();
            $updateEntity->setContent($content);
            $updateEntity->setCreationDate('' . time());
            $updateEntity->setIsactive(true);
            $updateEntity->setType("time");
            $updateEntity->setDeviceId($machine->getDeviceId());
            // persist
            $this->persist($updateEntity);
        }
        return new Response("ok");
    }


    /**
     * @Route("/sys/update/pubs",name="sys_update_pubs")
     * @Method("GET")
     */
    public function updatePubsAction (Request $request) {

        $machines = $this->MachineRepo()->findAll();

//        $pubimages = $this->getPubSetupContent(-1);
        foreach ($machines as &$machine) {
            for ($z = 1; $z <= 3; $z++) {
                $up = new UpdateEntity();
                $up->setType("pub");
                $up->setDeviceId($machine->getDeviceId());
                $up->setContent($this->serialize($this->emptyImageObj($z)));
                $this->persist($up);
            }
        }

        return new Response("ok");
    }

    /**
     * @Route("/sys/update/departements",name="sys_update_departements")
     * @Method("GET")
     */
    public function updateDepartements(Request $request) {


        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("dept");
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }

    /**
     * @Route("/sys/clear/departements",name="sys_clear_departements")
     * @Method("GET")
     */
    public function clearDepartements(Request $request) {

//        {id:”1006”,do:”delete”,data:[”user”,”fingerprint”,”face”,”headpic”,”clockin”,”pic”],ccid:[13245,8784,54878]}

//        {id:”1007”,do:”delete”,data:”dept”,deptid:[1,2,3]}


        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("dept");
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("clear");
            $this->persist($up);
        }
        return new Response("ok");
    }



    /**
     * @Route("/sys/update/employees",name="sys_update_employees")
     * @Method("GET")
     */
    public function updateEmployees (Request $request) {

        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("emp");
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }


    /**
     * @Route("/sys/update/fingerprints",name="sys_update_fingerprints")
     * @Method("GET")
     */
    public function updateFingerPrints (Request $request) {

        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("fingerprints");
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }

    /**
     * @Route("/sys/update/profilepictures",name="sys_update_profilepictures")
     * @Method("GET")
     */
    public function updateProfilePictures (Request $request) {

        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("pp"); // profile pictures
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }


    /**
     * @Route("/sys/reboot",name="sys_reboot")
     * @Method("GET")
     */
    public function sysReboot (Request $request) {

        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("reboot"); // profile pictures
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }


    /**
     * @Route("/sys/update/companyname",name="sys_update_companyname")
     * @Method("GET")
     */
    public function updateCompanyname (Request $request) {


        $companyName = $request->query->get("cn"); // company name
        if ($companyName != null)
        {
            $config = $this->ConfigEntityRepo()->findOneBy([]);
            if ($config == null) {
                $config = new ConfigEntity();
            }
            $config->setCompany($companyName);
            $this->persist($config);
        }
        return new Response("ok");
    }

    /**
     * @Route("/sys/doclean",name="sys_clean_all")
     * @Method("GET")
     */
    public function doCleanSys (Request $request) {
        $machines = $this->MachineRepo()->findAll();
        foreach ($machines as &$machine) {

            $up = new UpdateEntity();
            $up->setType("doclean"); // profile pictures
            $up->setDeviceId($machine->getDeviceId());
            $up->setContent("");
            $this->persist($up);
        }
        return new Response("ok");
    }


    private function manage($resp)
    {
        switch ($resp['data']) {
            case 'clockin':
                $this->Clockin($resp);
                $this->OkStatus($resp['sn'], $resp['id']);
                break;
            /*   case 'user';
                   $this->OkStatus($resp['id']);
                   break;*/
            case 'fingerprint';
                $this->info(json_encode($resp));
                $this->EmployeeFingerPrint($resp);
                $this->OkStatus($resp['sn'], $resp['id']);
                break;
            /*    case 'deleteface';
                break; */
            case 'headpic';
                $this->SetEmployeeHeadpic($resp);
                $this->OkStatus($resp['sn'], $resp['id']);
                break;
            /* case 'info';
                 break;*/
            case 'return';
                $this->OkStatus($resp['sn'], $resp['id'], $resp["return"]);
                break;
        }
    }

    private function OkStatus ($sn, $mainId, $returnObj = null) {


        if ($returnObj != null)
            foreach ($returnObj as &$ret) {

                $id = intval($ret['id']);

                if (intval($id) == 0)
                    continue;

                // delete the entry from the db
                $currentEntity = $this->UpdateEntityRepo()->findOneBy(
                    ['id' => $id,
                        'deviceId' => $sn]);

                if ($currentEntity != null)
                    $this->deleteEntity($currentEntity);
            }

        // save the ok id inside the array
        $obj = $this->OkidRepo()->findOneBy([]);
        if ($obj == null) {
            $obj = new OkIdEntity();
            $obj->setOkid(json_encode([]));
        }

        $idz = json_decode($obj->getOkid(), true);
        array_push($idz, $mainId);

        $this->info($this->serialize($idz));

        $obj->setOkid(json_encode($idz));
        $this->persist($obj);
    }


    private function getOkStatusArray()
    {
        $obj = $this->OkidRepo()->findOneBy([]);
        if ($obj == null) {
            $obj = new OkIdEntity();
            $obj->setOkid("[]");
        }
        return json_decode($obj->getOkid(), true);
    }

    private function Clockin($resp)
    {

        /*
        { id:5, data:"clockin", ccid:123456,time:"2015-09-05 18:05:21",verify:0,pic:"base64"}
        { id:5, data:"clockin", ccid:123456,time:"2015-09-05 18:05:21",verify:0}
        备注：verify指打卡验证方式，0-》密码，1-》指纹，2-》刷卡，15-》人脸
        */

        // save that a user has actually been recorded
        $clockin = new ClokinRecord();
        $clockin->setEmployeeId($resp['ccid']);
        $clockin->setTime((new \DateTime($resp['time']))->getTimestamp());
        $clockin->setVerify($resp['verify']);
        $clockin->setDeviceid($resp['sn']);


        $employee =  $this->EmployeeRepo()->findOneById(intval($resp['ccid']));

        if ($employee instanceof Employee && $employee != null) {

            // get user dep id
            $clockin->setDepartementId(
                $employee->getDepartementId()
            );
        } else
            $clockin->setDepartementId(6);

        // save the picture inside a file
        $filename = "employee_".$clockin->getEmployeeId()."_".time().'_'.$this->get("fingerprints.utils")->getToken(7).'.jpg';

        // create a new file
        $filename = $this->base64_to_jpeg($resp['pic'], $filename, $this->getParameter('onregisterpics').DIRECTORY_SEPARATOR);
        $clockin->setPic($filename);

        $this->persist($clockin);
    }

    function base64_to_jpeg($data, $output_file, $folder) {
        $this->info($folder.'/'.$output_file);
        $ifp = fopen($folder.'/'.$output_file, "wb");
        fwrite($ifp, base64_decode($data));
        fclose($ifp);
        return $folder.$output_file;
    }

    private function standardSetup () {

        $config = $this->ConfigEntityRepo()->findOneBy([]);
        if ($config == null) {
            $config = new ConfigEntity();
            $this->persist($config);
        }

        $tmp['id'] = 0;
        $tmp['do'] = 'update';
        $tmp['data'] = 'config';
        $tmp['name'] = $config->getSysname();
        $tmp['company'] = $config->getCompany(); // company name
        $tmp['max'] = $config->getMax();
        $tmp['function'] = $config->getFunction();
        $tmp['delay'] = $config->getDelay();
        $tmp['errdelay'] = $config->getErrdelay();
        $tmp['timezone'] = $config->getTimezone();
        return $tmp;
    }

    private function getPubSetupContent($id)
    {
//        {id:”1005”,do:”update”,data:”advert”,index:1,advert:”base64”}
        $pubsetup = $this->PubsRepo()->findOneBy(array("deviceid"=>"X_X"));

        $pic_slide_1 = [
            'id' => 0,
            'do' => 'update',
            'data' => 'advert',
            'index' => 1,
            'advert' => $this->base64__($pubsetup->getImage1())
        ];
        if ($id == 1)
            return $pic_slide_1;

        $pic_slide_2 = [
            'id' => 1,
            'do' => 'update',
            'data' => 'advert',
            'index' => 2,
            'advert' => $this->base64__($pubsetup->getImage2())
        ];
        if ($id == 2)
            return $pic_slide_2;

        $pic_slide_3 = [
            'id' => 2,
            'do' => 'update',
            'data' => 'advert',
            'index' => 3,
            'advert' => $this->base64__($pubsetup->getImage3())
        ];
        if ($id == 3)
            return $pic_slide_3;

//        $res = [];
//        array_push($res, $pic_slide_1);
//        array_push($res, $pic_slide_2);
//        array_push($res, $pic_slide_3);
//        return $res;
    }

    private function getCompanyName()
    {
        return "GIM-UEMOA - Lomé";
    }

    private function getSysName()
    {
        return "Sys Assidu";
    }

    private function emptyImageObj($z)
    {
        $tmp = [
            'id' => 0,
            'do' => 'update',
            'data' => 'advert',
            'index' => $z,
            'advert' => "#"
        ];
        return $tmp;
    }

    /**
     * @return array of all departements
     */
    private function getAllDepartements()
    {
        $res = [];
        $departements = $this->DepartementRepo()->findAll();
        foreach ($departements as &$departement) {
            $ttmp = ['id'=>$departement->getId(), 'pid'=>0, 'name' => $departement->getName()];
            array_push($res, $ttmp);
        }
        return $res;
    }


    /**
     * @return array of all departements idz only
     */
    private function getAllDepartementsIdzOnly()
    {
        $res = [];
        $departements = $this->DepartementRepo()->findAll();
        foreach ($departements as &$departement) {
            $ttmp = ['id'=>$departement->getId()];
            array_push($res, $ttmp);
        }
        return $res;
    }



    private function getAllUsers($id)
    {
        /*{
    "id": "1001",
    "do": "update",
    "data": "user",
    "ccid": "1236",
    "name": "张三 ",
    "passwd": "md5",
    "card": "65852",
    "deptid": 11,
    "auth": 0,
    "faceexist": 0
}*/

        $res = [];
        $empl = $this->EmployeeRepo()->findAll();
        foreach ($empl as &$e) {
            $tmp = [
                'id' => $id++,
                'do' => 'update',
                'data' => "user",
                'ccid' => $e->getId(),
                'name' => $e->getFirstname().' '.$e->getSurname(),
                'passwd' => md5("555"),
                'deptid' => $e->getDepartementId(),
                'auth' => $e->getAuth(),
                'faceexist' => 0
            ];
            array_push($res, $tmp);
        }
        return $res;
    }

    private function resetReturnValues()
    {
        $obj = $this->OkidRepo()->findOneBy([]);
        if ($obj != null)
            $this->deleteEntity($obj);
    }

    private function getAllFingerprints($id)
    {
        $employees = $this->EmployeeRepo()->findAll();

        $res = [];

        foreach ($employees as &$employee) {

            $fingerprints = ["",""];

            $fg = json_decode($employee->getFingerprints());

            if ($fg[0] != "")
                $fingerprints[0] = $this->base64__($fg[0], "f") ;
            else
            {}

            if ($fg[1] != "")
                $fingerprints[1] = $this->base64__($fg[1], "f") ;
            else
            {}

            $ttmp = [
                'id' => $id++,
                'do' => 'update',
                'data' => 'fingerprint',
                'ccid' => $employee->getId(),
                'fingerprint' => $fingerprints
            ];

            array_push($res, $ttmp);
        }
        return $res;
    }


    /* fonction de reception de donnees depuis la machine */



    private function EmployeeFingerPrint($resp)
    {
//        { id:2, data:"fingerprint",ccid:123456,fingerprint:[”base64”,”base64”]}

        // save the fingerprints inside files
        $fingerprints = $resp['fingerprint'];

        // get employee
        $employee = $this->EmployeeRepo()->find($resp['ccid']);

        if ($employee == null)
            return;

        $resultFingerprints = [];

        foreach ($fingerprints as &$fingerprint) {

            if ($fingerprint == "") {
                array_push($resultFingerprints, "");
                continue;
            }
            // save the picture inside a file
            $filename = "employee_fingerprint".$resp['ccid']."_".time().'_'.$this->get("fingerprints.utils")->getToken(7).'.jpg';
            $filename = $this->base64_to_jpeg($fingerprint, $filename, $this->getParameter('fingerprints').DIRECTORY_SEPARATOR);
            array_push($resultFingerprints, $filename);
        }

        $employee->setFingerprints(json_encode($resultFingerprints));
        $this->persist($employee);
    }

    private function getProfilePictures($id)
    {

        $res = [];
        $employees = $this->EmployeeRepo()->findAll();
        foreach ($employees as &$employee) {
//{"id":"1004","do":"update","data":"headpic","ccid":"123456","headpic":"base64"}
            $tmp = [
                'id' => $id++,
                'do' => 'update',
                'data' => "headpic",
                'ccid' => $employee->getId(),
                'headpic' => $this->base64__($this->getParameter('user_profile_pictures').DIRECTORY_SEPARATOR.$employee->getPicture())
            ];
            array_push($res, $tmp);
        }
        return $res;
    }

    private function SetEmployeeHeadpic ($resp) {

        $profilePicture = $resp["headpic"];
        // get employee
        $employee = $this->EmployeeRepo()->find($resp['ccid']);
        if ($employee == null)
            return;

        // create a name under which to save the current profile picture
        $filename = "employee_headpic".$resp['ccid']."_".time().'_'.$this->get("fingerprints.utils")->getToken(7).'.jpg';
        // save the profile picture under the directory
        /*$filename = */ $this->base64_to_jpeg($profilePicture, $filename, $this->getParameter('user_profile_pictures').DIRECTORY_SEPARATOR);

        $employee->setPicture($filename);
        $this->persist($employee);
    }

    private function getAllEmployeesIdzOnly()
    {

        $res = [];
        $empl = $this->EmployeeRepo()->findAll();
        foreach ($empl as &$e) {
            array_push($res, $e->getId());
        }
        return $res;
    }


}


?>