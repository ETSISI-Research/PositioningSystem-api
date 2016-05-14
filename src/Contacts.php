<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Contacts
 *
 * @ORM\Table(name="contacts", indexes={@ORM\Index(name="fk_users_has_users_users4_idx", columns={"users_id1"}), @ORM\Index(name="fk_users_has_users_users3_idx", columns={"users_id"})})
 * @ORM\Entity
 */
class Contacts implements \JsonSerializable 
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=45, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=true)
     */
    private $creationdate;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $users;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id1", referencedColumnName="id")
     * })
     */
    private $users1;

    public function JsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        return $this->users = $users;
    }

    public function getUsers1()
    {
        return $this->users1;
    }

    public function setUsers1($users1)
    {
        return $this->users1 = $users1;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        return $this->status = $status;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationdate = $creationDate;
    }

}


   