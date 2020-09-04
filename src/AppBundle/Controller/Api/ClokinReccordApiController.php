<?php


namespace AppBundle\Controller\Api;


use AppBundle\Controller\HomeStatsController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClokinReccordApiController extends Controller
{
    /**
     * @Rest\Get(
     *     path="/api/v1/clockings",
     *     name="api_clokings"
     * )
     */
    public function ClokinRecordAction(Request $request){


    }

    /**
     * @Rest\Get(
     *     path="/api/v1/today-clocking",
     *     name="api_today_clocking"
     * )
     */
    public function todayClockinRecordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $today = date("Y-m-d");
        $date_start = new \DateTime($today." 00:00:00");
        $date_end = new \DateTime($today." 23:59:59");

        $qb = $em->createQueryBuilder()
            ->select('clockin_record')
            ->from('AppBundle:ClockinRecord', 'clockin_record')
            ->where('clockin_record.createTime BETWEEN :firstDate AND :lastDate')
            ->setParameter('firstDate', $date_start)
            ->setParameter('lastDate', $date_end);

        $today_clockin = $qb->getQuery()->getResult();
        $response = new Response();

        if(count($today_clockin)<0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Pas de valeur"
]], 'json');

$response->setStatusCode(200);

        }else{
            $data = $this->get('jms_serializer')->serialize($today_clockin, 'json');
            $response->setStatusCode(200);
        }

        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }


    /**
     * @Rest\Get(
     *     path="/api/v1/clocking/{date}",
     *     name="api_clocking_date"
     * )
     */
    public function ClockinRecordByDateAction($date){
        $em = $this->getDoctrine()->getManager();
        $today = $date;
        $date_start = new \DateTime($today." 00:00:00");
        $date_end = new \DateTime($today." 23:59:59");

        $qb = $em->createQueryBuilder()
            ->select('clockin_record')
            ->from('AppBundle:ClockinRecord', 'clockin_record')
            ->where('clockin_record.createTime BETWEEN :firstDate AND :lastDate')
            ->setParameter('firstDate', $date_start)
            ->setParameter('lastDate', $date_end);

        $today_clockin = $qb->getQuery()->getResult();
        $response = new Response();

        if(count($today_clockin)<0){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"Pas de valeur"
            ]], 'json');

            $response->setStatusCode(200);

        }else{
            $data = $this->get('jms_serializer')->serialize($today_clockin, 'json');
            $response->setStatusCode(200);
        }

        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }


    /**
     * @Rest\Get(
     *     path="/api/v1/clocking/{begin_at}/{end_at}",
     *     name="api_clocking_date"
     * )
     */
    public function ClockinRecordByDateIntervalAction($begin_at, $end_at){

        $em = $this->getDoctrine()->getManager();
        $date_start = new \DateTime($begin_at." 00:00:00");
        $date_end = new \DateTime($end_at." 23:59:59");

        $response = new Response();
        if($date_start > $date_end){
            $data = $this->get('jms_serializer')->serialize(['error'=>[
                'code'=>405,
                'message'=>"L'intervalle de date est incorrect"
            ]], 'json');
            $response->setStatusCode(200);
        }else{
            $qb = $em->createQueryBuilder()
                ->select('clockin_record')
                ->from('AppBundle:ClockinRecord', 'clockin_record')
                ->where('clockin_record.createTime BETWEEN :firstDate AND :lastDate')
                ->setParameter('firstDate', $date_start)
                ->setParameter('lastDate', $date_end);

            $clockings = $qb->getQuery()->getResult();

            if(count($clockings)<0){
                $data = $this->get('jms_serializer')->serialize(['error'=>[
                    'code'=>405,
                    'message'=>"Pas de valeur"
                ]], 'json');

                $response->setStatusCode(200);

            }else{
                $data = $this->get('jms_serializer')->serialize($clockings, 'json');
                $response->setStatusCode(200);
            }

        }


        $response->setContent($data);
        $response->headers->set('content-type', 'application/json');

        return $response;
    }

}