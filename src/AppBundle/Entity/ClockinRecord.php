<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClockinRecord
 *
 * @ORM\Table(name="clockin_record")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClockinRecordRepository")
 */
class ClockinRecord
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employe;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Departement")
     * @ORM\JoinColumn(nullable=false)
     */

    private $departement;

    /**
     * @var int
     *
     * @ORM\Column(name="deviceId", type="integer")
     */
    private $deviceId;

    /**
     * @var int
     *
     * @ORM\Column(name="clockinTime", type="integer")
     */
    private $clockinTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createTime", type="datetime")
     */
    private $createTime;


    public function __construct()
    {
        $this->setCreateTime(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deviceId
     *
     * @param integer $deviceId
     * @return ClockinRecord
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * Get deviceId
     *
     * @return integer 
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * Set clockinTime
     *
     * @param \DateTime $clockinTime
     * @return ClockinRecord
     */
    public function setClockinTime($clockinTime)
    {
        $this->clockinTime = $clockinTime;

        return $this;
    }

    /**
     * Get clockinTime
     *
     * @return \DateTime 
     */
    public function getClockinTime()
    {
        return $this->clockinTime;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return ClockinRecord
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set employe
     *
     * @param \AppBundle\Entity\Employe $employe
     * @return ClockinRecord
     */
    public function setEmploye(\AppBundle\Entity\Employe $employe)
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * Get employe
     *
     * @return \AppBundle\Entity\Employe 
     */
    public function getEmploye()
    {
        return $this->employe;
    }

    /**
     * Set departement
     *
     * @param \AppBundle\Entity\Departement $departement
     * @return ClockinRecord
     */
    public function setDepartement(\AppBundle\Entity\Departement $departement)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \AppBundle\Entity\Departement 
     */
    public function getDepartement()
    {
        return $this->departement;
    }
}
