<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRepository")
 */
class Permission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employe")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */

    public $employee;


    /**
     * @var int
     *
     * @ORM\Column(name="asker_id", type="integer")
     */
    public $askerId;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45)
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    public $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    public $createTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_time", type="datetime")
     */
    public $updateTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    public $dateFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="time_from", type="string", nullable=true)
     */
    public $timeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    public $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(name="time_to", type="string", nullable=true)
     */
    public $timeTo;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=0,max=2)
     */
    public $state;


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
     * Set askerId
     *
     * @param integer $askerId
     * @return Permission
     */
    public function setAskerId($askerId)
    {
        $this->askerId = $askerId;

        return $this;
    }

    /**
     * Get askerId
     *
     * @return integer 
     */
    public function getAskerId()
    {
        return $this->askerId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Permission
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Permission
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     * @return Permission
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime 
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return Permission
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     * @return Permission
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime 
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Permission
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Permission
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
     * Set employee
     *
     * @param \AppBundle\Entity\Employe $employee
     * @return Permission
     */
    public function setEmployee(\AppBundle\Entity\Employe $employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employe 
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set timeFrom
     *
     * @param string $timeFrom
     *
     * @return Permission
     */
    public function setTimeFrom($timeFrom)
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    /**
     * Get timeFrom
     *
     * @return string
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * Set timeTo
     *
     * @param string $timeTo
     *
     * @return Permission
     */
    public function setTimeTo($timeTo)
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    /**
     * Get timeTo
     *
     * @return string
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }
}
