<?php

namespace TmyeDeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestBlob
 *
 * @ORM\Table(name="request_blob")
 * @ORM\Entity(repositoryClass="Tmye\DeviceBundle\Repository\RequestBlobRepository")
 */
class RequestBlob
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
     * @ORM\Column(name="content", type="text")
     */
    private $content = "{}";

    /**
     * @var String
     *
     * @ORM\Column(name="creation_date", type="string")
     */
    private $creationDate;

    /**
     * @var String
     *
     * @ORM\Column(name="validation_date", type="string")
     */
    private $validationDate;


    /**
     * @var String
     *
     * @ORM\Column(name="device_id", type="string")
     */
    private $deviceId;


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
     * Set content
     *
     * @param string $content
     *
     * @return RequestBlob
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
     * Set creationDate
     *
     * @param \String $creationDate
     *
     * @return RequestBlob
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \String
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set validationDate
     *
     * @param \String $validationDate
     *
     * @return RequestBlob
     */
    public function setValidationDate($validationDate)
    {
        $this->validationDate = $validationDate;

        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \String
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Set deviceId
     *
     * @param string $deviceId
     *
     * @return RequestBlob
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
}
