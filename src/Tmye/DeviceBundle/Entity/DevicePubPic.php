<?php

namespace Tmye\DeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevicePubPic
 *
 * @ORM\Table(name="device_pub_pic")
 * @ORM\Entity(repositoryClass="Tmye\DeviceBundle\Repository\DevicePubPicRepository")
 */
class DevicePubPic
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
     * @ORM\Column(name="deviceid", type="string", length=255)
     */
    private $deviceid = "X_X";

    /**
     * @var string
     *
     * @ORM\Column(name="image1", type="string", length=255)
     */
    private $image1 = "img/pubdef.jpg";

    /**
     * @var string
     *
     * @ORM\Column(name="image2", type="string", length=255)
     */
    private $image2  = "img/pubdef.jpg";

    /**
     * @var string
     *
     * @ORM\Column(name="image3", type="string", length=255)
     */
    private $image3  = "img/pubdef.jpg";


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
     * Set deviceid
     *
     * @param string $deviceid
     *
     * @return DevicePubPic
     */
    public function setDeviceid($deviceid)
    {
        $this->deviceid = $deviceid;

        return $this;
    }

    /**
     * Get deviceid
     *
     * @return string
     */
    public function getDeviceid()
    {
        return $this->deviceid;
    }

    /**
     * Set image1
     *
     * @param string $image1
     *
     * @return DevicePubPic
     */
    public function setImage1($image1)
    {
        $this->image1 = $image1;

        return $this;
    }

    /**
     * Get image1
     *
     * @return string
     */
    public function getImage1()
    {
        return $this->image1;
    }

    /**
     * Set image2
     *
     * @param string $image2
     *
     * @return DevicePubPic
     */
    public function setImage2($image2)
    {
        $this->image2 = $image2;

        return $this;
    }

    /**
     * Get image2
     *
     * @return string
     */
    public function getImage2()
    {
        return $this->image2;
    }

    /**
     * Set image3
     *
     * @param string $image3
     *
     * @return DevicePubPic
     */
    public function setImage3($image3)
    {
        $this->image3 = $image3;

        return $this;
    }

    /**
     * Get image3
     *
     * @return string
     */
    public function getImage3()
    {
        return $this->image3;
    }
}

