<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyConfig
 *
 * @ORM\Table(name="company_config")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyConfigRepository")
 */
class CompanyConfig
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
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_logo", type="string", length=255, nullable=true)
     */
    private $companyLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="expiration_date", type="string", length=255, nullable=true)
     */
    private $expirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="late_hours_allowed", type="string", length=255, nullable=true)
     */
    private $late_hours_allowed;


    /**
     * @var string
     *
     * @ORM\Column(name="absent_hours_allowed", type="string", length=255, nullable=true)
     */
    private $absent_hours_allowed;

    /**
     * @var string
     *
     * @ORM\Column(name="open_door_at", type="string", length=255, nullable=true)
     */
    private $open_door_at;

    /**
     * @var string
     *
     * @ORM\Column(name="departure_hours_allowed", type="string", length=255, nullable=true)
     */
    private $departure_hours_allowed;

    /**
     * @var string
     *
     * @ORM\Column(name="close_door_at", type="string", length=255, nullable=true)
     */
    private $close_door_at;

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
     * Set companyName
     *
     * @param string $companyName
     *
     * @return CompanyConfig
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set companyLogo
     *
     * @param string $companyLogo
     *
     * @return CompanyConfig
     */
    public function setCompanyLogo($companyLogo)
    {
        $this->companyLogo = $companyLogo;

        return $this;
    }

    /**
     * Get companyLogo
     *
     * @return string
     */
    public function getCompanyLogo()
    {
        return $this->companyLogo;
    }

    /**
     * Set expirationDate
     *
     * @param string $expirationDate
     *
     * @return CompanyConfig
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set late_hours_allowed
     *
     * @param string $late_hours_allowed
     *
     * @return CompanyConfig
     */
    public function setLateHoursAllowed($late_hours_allowed)
    {
        $this->late_hours_allowed = $late_hours_allowed;

        return $this;
    }

    /**
     * Get late_hours_allowed
     *
     * @return string
     */
    public function getLateHoursAllowed()
    {
        return $this->late_hours_allowed;
    }

    /**
     * Set absent_hours_allowed
     *
     * @param string $absent_hours_allowed
     *
     * @return CompanyConfig
     */
    public function setAbsentHoursAllowed($absent_hours_allowed)
    {
        $this->absent_hours_allowed = $absent_hours_allowed;

        return $this;
    }

    /**
     * Get absent_hours_allowed
     *
     * @return string
     */
    public function getAbsentHoursAllowed()
    {
        return $this->absent_hours_allowed;
    }

    /**
     * Set departure_hours_allowed
     *
     * @param string $departure_hours_allowed
     *
     * @return CompanyConfig
     */
    public function setDepartureHoursAllowed($departure_hours_allowed)
    {
        $this->departure_hours_allowed = $departure_hours_allowed;

        return $this;
    }

    /**
     * Get departure_hours_allowed
     *
     * @return string
     */
    public function getDepartureHoursAllowed()
    {
        return $this->departure_hours_allowed;
    }
}
