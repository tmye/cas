<?php

namespace Tmye\DeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfigEntity
 *
 * @ORM\Table(name="config_entity")
 * @ORM\Entity(repositoryClass="Tmye\DeviceBundle\Repository\ConfigEntityRepository")
 */
class ConfigEntity
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
     * @ORM\Column(name="sysname", type="string", length=255)
     */
    private $sysname;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;

    /**
     * @var int
     *
     * @ORM\Column(name="max", type="string", length=255)
     */
    private $max;

    /**
     * @var string
     *
     * @ORM\Column(name="function", type="string", length=255)
     */
    private $function;

    /**
     * @var int
     *
     * @ORM\Column(name="delay", type="integer")
     */
    private $delay;

    /**
     * @var string
     *
     * @ORM\Column(name="errdelay", type="integer")
     */
    private $errdelay;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255)
     */
    private $timezone;


    function __construct()
    {
        $this->delay = 20;
        $this->errdelay = 30;
        $this->timezone = "+0";
        $this->max = '3000';
        $this->function = 65535;

        $this->company = "---";
        $this->sysname = "---";
    }

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
     * Set sysname
     *
     * @param string $sysname
     *
     * @return ConfigEntity
     */
    public function setSysname($sysname)
    {
        $this->sysname = $sysname;

        return $this;
    }

    /**
     * Get sysname
     *
     * @return string
     */
    public function getSysname()
    {
        return $this->sysname;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return ConfigEntity
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set max
     *
     * @param integer $max
     *
     * @return ConfigEntity
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return ConfigEntity
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set delay
     *
     * @param integer $delay
     *
     * @return ConfigEntity
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set errdelay
     *
     * @param string $errdelay
     *
     * @return ConfigEntity
     */
    public function setErrdelay($errdelay)
    {
        $this->errdelay = $errdelay;

        return $this;
    }

    /**
     * Get errdelay
     *
     * @return string
     */
    public function getErrdelay()
    {
        return $this->errdelay;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     *
     * @return ConfigEntity
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}

