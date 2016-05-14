<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Subfamilies
 *
 * @ORM\Table(name="subfamilies", indexes={@ORM\Index(name="fk_Subfamily_Family_idx", columns={"Family_id"}), @ORM\Index(name="fk_subfamilies_partners1", columns={"Partner_id"})})
 * @ORM\Entity
 */
class Subfamilies implements \JsonSerializable 
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
     * @var \Families
     *
     * @ORM\ManyToOne(targetEntity="Families")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Family_id", referencedColumnName="id")
     * })
     */
    private $family;

    public function JsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFamily()
    {
        return $this->family;
    }

    public function getName()
    {
        return $this->name;
    }

}
