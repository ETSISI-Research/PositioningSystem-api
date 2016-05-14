<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Products
 *
 * @ORM\Table(name="products", indexes={@ORM\Index(name="fk_Product_Subfamily1_idx", columns={"Subfamily_id"}), @ORM\Index(name="fk_products_partners1_idx", columns={"Partner_id"})})
 * @ORM\Entity
 */
class Products implements \JsonSerializable 
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
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=false)
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=false)
     */
    private $enddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \Subfamilies
     *
     * @ORM\ManyToOne(targetEntity="Subfamilies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Subfamily_id", referencedColumnName="id")
     * })
     */
    private $subfamily;

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
     * Constructor
     */
    public function __construct()
    {
        $this->products1 = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setParnet($partner)
    {
        return $this->partner = $partner;
    }

    public function getStartDate()
    {
        return $this->startdate;
    }

    public function setStartDate($startDate)
    {
        return $this->startdate = $startDate;
    }

    public function getEndDate()
    {
        return $this->enddate;
    }

    public function setEndDate($endDate)
    {
        return $this->enddate = $endDate;
    }

    public function getSubfamily()
    {
        return $this->subfamily;
    }

}
