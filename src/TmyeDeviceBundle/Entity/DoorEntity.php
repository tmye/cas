<?php

namespace TmyeDeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoorEntity
 *
 * @ORM\Table(name="door_entity")
 * @ORM\Entity(repositoryClass="TmyeDeviceBundle\Repository\DoorEntityRepository")
 */
class DoorEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="device_id", type="string", length=255)
     */
    private $device_id = "X_X";

    /**
     * @var string
     *
     * @ORM\Column(name="time_frame", type="string", length=255)
     */
    private $time_frame;


    /**
     * @var int
     *
     * @ORM\Column(name="time_frame_value", type="integer", nullable=true)
     */
    private $time_frame_value;

    /**
     * @return string
     */
    public function getTimeFrame()
    {
        return $this->time_frame;
    }

    /**
     * Set time_frame
     *
     * @param string $time_frame
     */
    public function setTimeFrame($time_frame)
    {
        $this->time_frame = $time_frame;
    }

    /**
     * @return int
     */
    public function getTimeFrameValue()
    {
        return $this->time_frame_value;
    }

    /**
     * @param int $time_frame_value
     */
    public function setTimeFrameValue($time_frame_value)
    {
        $this->time_frame_value = $time_frame_value;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="opened_at", type="string", length=255)
     */
    private $opened_at;

    /**
     * @var string
     *
     * @ORM\Column(name="closed_at", type="string", length=255)
     */
    private $closed_at;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set device_id
     *
     * @param string $device_id
     *
     * @return DevicePubPic
     */
    public function setDeviceId($device_id)
    {
        $this->device_id = $device_id;

        return $this;
    }

    /**
     * Get device_id
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->device_id;
    }


    /**
     * Get opened_at
     *
     * @return string
     */
    public function getOpenedAt(){
        return $this->opened_at;
    }


    /**
     * Set open_at
     *
     * @param $open_at
     * @return $this
     */
    public function setOpenedAt($open_at){
        $this->opened_at = $open_at;
        return $this;
    }


    /**
     * Get closed_at
     *
     * @return string
     */
    public function getClosedAt(){
        return $this->closed_at;
    }

    /**
     * set closed_at
     *
     * @param $closed_at
     * @return $this
     */
    public function setclosedAt($closed_at){
        $this->closed_at = $closed_at;
        return $this;
    }

}

