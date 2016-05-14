<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * Partners
 *
 * @ORM\Table(name="partners", indexes={@ORM\Index(name="fk_partners_countries1_idx", columns={"Country_id"}), @ORM\Index(name="fk_partners_users1_idx", columns={"users_id"})})
 * @ORM\Entity
 */
class Partners implements \JsonSerializable 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    private $creationdate;


    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=36, nullable=true)
     */
    private $image;

    /**
     * @var \Countries
     *
     * @ORM\ManyToOne(targetEntity="Countries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Country_id", referencedColumnName="id")
     * })
     */
    private $country;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $users;

    public function JsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        return $this->description = $description;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setCountry($country)
    {
        return $this->country = $country;
    }

    public function getCreationDate()
    {
        return $this->creationdate;
    }

    public function setCreationDate($creationdate)
    {
        return $this->creationdate = $creationdate;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        return $this->users = $users;
    }

}
