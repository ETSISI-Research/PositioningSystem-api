<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Families
 *
 * @ORM\Table(name="families", indexes={@ORM\Index(name="fk_Family_Project1", columns={"Project_id"}), @ORM\Index(name="fk_families_partners1", columns={"Partner_id"})})
 * @ORM\Entity
 */
class Families
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
     * @var \Partners
     *
     * @ORM\ManyToOne(targetEntity="Partners")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Partner_id", referencedColumnName="id")
     * })
     */
    private $partner;

    /**
     * @var \Projects
     *
     * @ORM\ManyToOne(targetEntity="Projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Project_id", referencedColumnName="id")
     * })
     */
    private $project;

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

    public function getPartner()
    {
        return $this->partner;
    }

    public function getProject()
    {
        return $this->project;
    }


}
