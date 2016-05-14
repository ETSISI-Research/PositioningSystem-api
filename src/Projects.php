<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Projects
 *
 * @ORM\Table(name="projects", indexes={@ORM\Index(name="fk_projects_partners1", columns={"Partner_id"})})
 * @ORM\Entity
 */
class Projects implements \JsonSerializable 
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
     * @var \Partners
     *
     * @ORM\ManyToOne(targetEntity="Partners")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Partner_id", referencedColumnName="id")
     * })
     */
    private $partner;

    /**
     * @var \Partners
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $user;

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

    public function getCreationDate()
    {
        return $this->creationdate;
    }

    public function setCreationDate($creationdate)
    {
        return $this->creationdate = $creationdate;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        return $this->image = $image;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setPartner($partner)
    {
        return $this->partner = $partner;
    }


    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        return $this->user = $user;
    }
}
