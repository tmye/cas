<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingRepository")
 */
class Setting
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
     * @var bool
     *
     * @ORM\Column(name="first_time", type="boolean")
     */
    private $firstTime;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstTime.
     *
     * @param bool $firstTime
     *
     * @return Setting
     */
    public function setFirstTime($firstTime)
    {
        $this->firstTime = $firstTime;

        return $this;
    }

    /**
     * Get firstTime.
     *
     * @return bool
     */
    public function getFirstTime()
    {
        return $this->firstTime;
    }
}
