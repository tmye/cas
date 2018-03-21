<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Employe
 *
 * @ORM\Table(name="employe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmployeRepository")
 */
class Employe implements UserInterface
{

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Departement")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */

    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WorkingHours")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */

    private $workingHour;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="employee_ccid", type="integer", unique=true)
     */
    private $employeeCcid;

    /*
     * Les propriétés d'authentification
     * */

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=4)
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**

     * @ORM\Column(name="roles", type="array")

     */

    private $roles = array();

    /* Les autres propriétés */

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=45, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="adress", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $adress;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="auth", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $auth;

    /**
     * @var array
     *
     * @ORM\Column(name="fingerprints", type="array", length=255)
     * @Assert\NotBlank()
     */
    private $fingerprints = array();

    /**
     * @var int
     *
     * @ORM\Column(name="salary", type="integer")
     * @Assert\Range(min=1)
     */
    private $salary;

    /**
     * @var int
     *
     * @ORM\Column(name="function", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $function;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hire_date", type="datetime")
     */
    private $hire_date;

    /**
     * @var int
     *
     * @ORM\Column(name="godfather_ccid", type="integer")
     */
    private $godfatherCcid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;



    public function __construct()
    {
        $this->setUsername("user");
        $this->setRoles(array("ROLE_USER"));
        $this->setFingerprints(array("ROLE_USER"));
        $this->setAuth(0);
    }
    // Getters and Setters

    // Methode obligatoire de part l'interface qu'elle implémente
    public function eraseCredentials()
    {
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
     * Set employeeCcid
     *
     * @param integer $employeeCcid
     * @return Employe
     */
    public function setEmployeeCcid($employeeCcid)
    {
        $this->employeeCcid = $employeeCcid;

        return $this;
    }

    /**
     * Get employeeCcid
     *
     * @return integer 
     */
    public function getEmployeeCcid()
    {
        return $this->employeeCcid;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Employe
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return Employe
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string 
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Employe
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Employe
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set adress
     *
     * @param string $adress
     * @return Employe
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string 
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Employe
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set salary
     *
     * @param integer $salary
     * @return Employe
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * Get salary
     *
     * @return integer 
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * Set godfatherCcid
     *
     * @param integer $godfatherCcid
     * @return Employe
     */
    public function setGodfatherCcid($godfatherCcid)
    {
        $this->godfatherCcid = $godfatherCcid;

        return $this;
    }

    /**
     * Get godfatherCcid
     *
     * @return integer 
     */
    public function getGodfatherCcid()
    {
        return $this->godfatherCcid;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return Employe
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
     * @return Employe
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
     * Set departement
     *
     * @param \AppBundle\Entity\Departement $departement
     * @return Employe
     */
    public function setDepartement(\AppBundle\Entity\Departement $departement)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \AppBundle\Entity\Departement 
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set function
     *
     * @param string $function
     * @return Employe
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
     * Set hire_date
     *
     * @param \DateTime $hireDate
     * @return Employe
     */
    public function setHireDate($hireDate)
    {
        $this->hire_date = $hireDate;

        return $this;
    }

    /**
     * Get hire_date
     *
     * @return \DateTime 
     */
    public function getHireDate()
    {
        return $this->hire_date;
    }

    /**
     * Set workingHour
     *
     * @param \AppBundle\Entity\WorkingHours $workingHour
     * @return Employe
     */
    public function setWorkingHour(\AppBundle\Entity\WorkingHours $workingHour)
    {
        $this->workingHour = $workingHour;

        return $this;
    }

    /**
     * Get workingHour
     *
     * @return \AppBundle\Entity\WorkingHours 
     */
    public function getWorkingHour()
    {
        return $this->workingHour;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return Employe
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return Employe
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return Employe
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Employe
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set auth
     *
     * @param string $auth
     *
     * @return Employe
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get auth
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set fingerprints
     *
     * @param array $fingerprints
     *
     * @return Employe
     */
    public function setFingerprints($fingerprints)
    {
        $this->fingerprints = $fingerprints;

        return $this;
    }

    /**
     * Get fingerprints
     *
     * @return array
     */
    public function getFingerprints()
    {
        return $this->fingerprints;
    }
}
