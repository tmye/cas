<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Journal
 *
 * @ORM\Table(name="journal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JournalRepository")
 */
class Journal
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
     * @ORM\Column(name="crud_type", type="string", length=255)
     */
    private $crudType;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="elementConcerned", type="string", length=255,nullable=true)
     */
    private $elementConcerned;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="doneDate", type="datetime")
     */
    private $doneDate;

    public function __construct(){
        $this->setDoneDate(new \DateTime());
    }


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
     * Set crudType.
     *
     * @param string $crudType
     *
     * @return Journal
     */
    public function setCrudType($crudType)
    {
        $this->crudType = $crudType;

        return $this;
    }

    /**
     * Get crudType.
     *
     * @return string
     */
    public function getCrudType()
    {
        return $this->crudType;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return Journal
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Journal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set when.
     *
     * @param \DateTime $when
     *
     * @return Journal
     */
    public function setWhen($when)
    {
        $this->when = $when;

        return $this;
    }

    /**
     * Get when.
     *
     * @return \DateTime
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * Set doneDate.
     *
     * @param \DateTime $doneDate
     *
     * @return Journal
     */
    public function setDoneDate($doneDate)
    {
        $this->doneDate = $doneDate;

        return $this;
    }

    /**
     * Get doneDate.
     *
     * @return \DateTime
     */
    public function getDoneDate()
    {
        return $this->doneDate;
    }

    /**
     * Set elementConcerned.
     *
     * @param string $elementConcerned
     *
     * @return Journal
     */
    public function setElementConcerned($elementConcerned)
    {
        $this->elementConcerned = $elementConcerned;

        return $this;
    }

    /**
     * Get elementConcerned.
     *
     * @return string
     */
    public function getElementConcerned()
    {
        return $this->elementConcerned;
    }
}
