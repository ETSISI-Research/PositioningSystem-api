<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Privileges
 *
 * @ORM\Table(name="privileges", indexes={@ORM\Index(name="fk_projects_has_users_users1_idx", columns={"privileges_users_id"}), @ORM\Index(name="fk_projects_has_users_projects1_idx", columns={"projects_id"})})
 * @ORM\Entity
 */
class Privileges
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
     * @var \Projects
     *
     * @ORM\ManyToOne(targetEntity="Projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="projects_id", referencedColumnName="id")
     * })
     */
    private $projects;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="privileges_users_id", referencedColumnName="id")
     * })
     */
    private $privilegesUsers;


    public function getId()
    {
        return $this->id;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function setProjects($projects)
    {
        return $this->projects = $projects;
    }

    public function getPrivilegesUsers()
    {
        return $this->privilegesUsers;
    }

    public function setPrivilegesUsers($privilegesUsers)
    {
        return $this->privilegesUsers = $privilegesUsers;
    }

}
