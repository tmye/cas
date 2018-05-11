<?php

namespace TmyeDeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UpdateEntity
 *
 * @ORM\Table(name="update_entity")
 * @ORM\Entity(repositoryClass="TmyeDeviceBundle\Repository\UpdateEntityRepository")
 */
class UpdateEntity
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
     * @ORM\Column(name="device_id", type="string", length=255)
     */
    private $deviceId;

    /**
     * @var string
     *
     * @ORM\Column(name="creation_date", type="string", length=255)
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $function;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;


    /**
     * @var bool
     *
     * @ORM\Column(name="isactive", type="boolean")
     */
    private $isactive;


    function __construct()
    {
        $this->content = "";
        $this->creationDate = time();
        $this->isactive = 1;
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
     * Set creationDate
     *
     * @param string $creationDate
     *
     * @return UpdateEntity
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return UpdateEntity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return UpdateEntity
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
     * Set isactive
     *
     * @param boolean $isactive
     *
     * @return UpdateEntity
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;

        return $this;
    }

    /**
     * Get isactive
     *
     * @return bool
     */
    public function getIsactive()
    {
        return $this->isactive;
    }


    /**
     * Set deviceId
     *
     * @param string $deviceId
     *
     * @return UpdateEntity
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * Get deviceId
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return UpdateEntity
     */
    public function setType($type)
    {
        $this->type = $type;

        if ($type == "1doclean") {
            $this->priority = 6;
        }
        if ($type == "dept") {
            $this->priority = 5;
        }
        if ($type == "emp") {
            $this->priority = 4;
        }
        if ($type == "fingerprints") {
            $this->priority = 3;
        }
        if ($type == "pp") {
            $this->priority = 2;
        }
        if ($type == "pub") {
            $this->priority = 1;
        }
        if ($type == "reboot") {
            $this->priority = 0;
        }

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return UpdateEntity
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
