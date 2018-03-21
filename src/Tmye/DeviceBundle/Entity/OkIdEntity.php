<?php

namespace TmyeDeviceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OkIdEntity
 *
 * @ORM\Table(name="ok_id_entity")
 * @ORM\Entity(repositoryClass="Tmye\DeviceBundle\Repository\OkIdEntityRepository")
 */
class OkIdEntity
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
     * @var text
     *
     * @ORM\Column(name="okid", type="text")
     */
    private $okid;


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
     * Set okid
     *
     * @param integer $okid
     *
     * @return OkIdEntity
     */
    public function setOkid($okid)
    {
        $this->okid = $okid;

        return $this;
    }

    /**
     * Get okid
     *
     * @return int
     */
    public function getOkid()
    {
        return $this->okid;
    }
}

