<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Machine
 *
 * @ORM\Table(name="machine")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MachineRepository")
 */
class Machine
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Departement", cascade={"persist"})
     */
    private $departements;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="machineId", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $machineId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;




    public function __construct()
    {
        $this->departements = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Machine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set machineId
     *
     * @param integer $machineId
     * @return Machine
     */
    public function setMachineId($machineId)
    {
        $this->machineId = $machineId;

        return $this;
    }

    /**
     * Get machineId
     *
     * @return integer 
     */
    public function getMachineId()
    {
        return $this->machineId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Machine
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
     * Add departements
     *
     * @param \AppBundle\Entity\Departement $departements
     * @return Machine
     */
    public function addDepartement(\AppBundle\Entity\Departement $departements)
    {
        $this->departements[] = $departements;

        return $this;
    }

    /**
     * Remove departements
     *
     * @param \AppBundle\Entity\Departement $departements
     */
    public function removeDepartement(\AppBundle\Entity\Departement $departements)
    {
        $this->departements->removeElement($departements);
    }

    /**
     * Get departements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepartements()
    {
        return $this->departements;
    }

}
