<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyInfos
 *
 * @ORM\Table(name="company_infos")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyInfosRepository")
 */
class CompanyInfos
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
     * @ORM\Column(name="vision", type="string", length=255, nullable=true)
     */
    private $vision;

    /**
     * @var string
     *
     * @ORM\Column(name="mission", type="string", length=255, nullable=true)
     */
    private $mission;

    /**
     * @var string
     *
     * @ORM\Column(name="foundation", type="string", length=255, nullable=true)
     */
    private $foundation;

    /**
     * @var string
     *
     * @ORM\Column(name="headoffice", type="string", length=255, nullable=true)
     */
    private $headoffice;

    /**
     * @var int
     *
     * @ORM\Column(name="employees", type="integer", nullable=true)
     */
    private $employees;

    /**
     * @var string
     *
     * @ORM\Column(name="director", type="string", length=255, nullable=true)
     */
    private $director;


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
     * Set vision
     *
     * @param string $vision
     *
     * @return CompanyInfos
     */
    public function setVision($vision)
    {
        $this->vision = $vision;

        return $this;
    }

    /**
     * Get vision
     *
     * @return string
     */
    public function getVision()
    {
        return $this->vision;
    }

    /**
     * Set mission
     *
     * @param string $mission
     *
     * @return CompanyInfos
     */
    public function setMission($mission)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return string
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set foundation
     *
     * @param string $foundation
     *
     * @return CompanyInfos
     */
    public function setFoundation($foundation)
    {
        $this->foundation = $foundation;

        return $this;
    }

    /**
     * Get foundation
     *
     * @return string
     */
    public function getFoundation()
    {
        return $this->foundation;
    }

    /**
     * Set headoffice
     *
     * @param string $headoffice
     *
     * @return CompanyInfos
     */
    public function setHeadoffice($headoffice)
    {
        $this->headoffice = $headoffice;

        return $this;
    }

    /**
     * Get headoffice
     *
     * @return string
     */
    public function getHeadoffice()
    {
        return $this->headoffice;
    }

    /**
     * Set employees
     *
     * @param string $employees
     *
     * @return CompanyInfos
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;

        return $this;
    }

    /**
     * Get employees
     *
     * @return string
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * Set director
     *
     * @param string $director
     *
     * @return CompanyInfos
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }
}
