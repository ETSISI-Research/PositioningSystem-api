<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Snapshots
 *
 * @ORM\Table(name="snapshots", indexes={@ORM\Index(name="fk_snapshots_projects1_idx", columns={"projects_id"})})
 * @ORM\Entity
 */
class Snapshots implements \JsonSerializable 
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var \Projects
     *
     * @ORM\ManyToOne(targetEntity="Projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="projects_id", referencedColumnName="id")
     * })
     */
    private $projects;

    public function JsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function setDescription($description)
    {
        return $this->description = $description;
    }

    public function setCreationdate($creationdate)
    {
        return $this->creationdate = $creationdate;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        return $this->data = $data;
    }

    public function setProjects($projects)
    {
        return $this->projects = $projects;
    }

}
