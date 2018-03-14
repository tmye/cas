<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkingHours
 *
 * @ORM\Table(name="working_hours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkingHoursRepository")
 */
class WorkingHours
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
     * @ORM\Column(name="code", type="text")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="workingHour", type="text")
     */
    private $workingHour;

    /**
     * @var string
     *
     * @ORM\Column(name="isFor", type="string", length=255, nullable=true)
     */
    private $isFor;


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
     * Set workingHour
     *
     * @param string $workingHour
     * @return WorkingHours
     */
    public function setWorkingHour($workingHour)
    {
        $this->workingHour = $workingHour;

        return $this;
    }

    /**
     * Get workingHour
     *
     * @return string 
     */
    public function getWorkingHour()
    {
        return $this->workingHour;
    }

    /**
     * Set isFor
     *
     * @param string $isFor
     * @return WorkingHours
     */
    public function setIsFor($isFor)
    {
        $this->isFor = $isFor;

        return $this;
    }

    /**
     * Get isFor
     *
     * @return string 
     */
    public function getIsFor()
    {
        return $this->isFor;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return WorkingHours
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
