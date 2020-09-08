<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepartementRepository")
 */
class Departement
{
    /**
     * @var int
     *
     * @SWG\Property(description="The unique identifier of the user.")
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @SWG\Property(type="string", maxLength=255)
     *
     * @ORM\Column(name="name", type="string", length=45, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @SWG\Property(type="string")
     *
     * @ORM\Column(name="max_count", type="string", length=45)
     * @Assert\NotBlank()
     * @Assert\Range(min=1)
     */
    private $maxCount;

    /**
     * @var \DateTime
     *
     * @SWG\Property(type="date")
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string")
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $lastUpdate;


    /**
     * @var int
     */
    public $depEmployeeCount;

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
     * @return Departement
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }



    /**
     * Set depEmployeeCount
     *
     */
    public function setEmployeeCount($value)
    {
        $this->depEmployeeCount = $value;
    }

    /**
     * Get depEmployeeCount
     *
     * @return integer
     */
    public function getEmployeeCount()
    {
        return $this->depEmployeeCount;
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
     * Set maxCount
     *
     * @param string $maxCount
     * @return Departement
     */
    public function setMaxCount($maxCount)
    {
        $this->maxCount = $maxCount;

        return $this;
    }

    /**
     * Get maxCount
     *
     * @return string 
     */
    public function getMaxCount()
    {
        return $this->maxCount;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return Departement
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime 
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Departement
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Departement
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
