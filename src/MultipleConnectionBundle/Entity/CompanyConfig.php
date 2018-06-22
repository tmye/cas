<?php

namespace MultipleConnectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyConfig
 *
 * @ORM\Table(name="company_config")
 * @ORM\Entity(repositoryClass="MultipleConnectionBundle\Repository\CompanyConfigRepository")
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
     * @ORM\Column(name="companyName", type="string", length=255, unique=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="companyLogo", type="string", length=255)
     */
    private $companyLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="expirationDate", type="string", length=255, nullable=true)
     */
    private $expirationDate;


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
}
