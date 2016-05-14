<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="fk_users_has_users_users2_idx", columns={"users_receiverId"}), @ORM\Index(name="fk_users_has_users_users1_idx", columns={"users_senderId"})})
 * @ORM\Entity
 */
class Messages
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
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=false)
     */
    private $creationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=50, nullable=false)
     */
    private $subject;

    /**
     * @var boolean
     *
     * @ORM\Column(name="readed", type="boolean", nullable=false)
     */
    private $readed = '0';

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_senderId", referencedColumnName="id")
     * })
     */
    private $usersSenderid;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_receiverId", referencedColumnName="id")
     * })
     */
    private $usersReceiverid;

    public function getId()
    {
        return $this->id;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getSender()
    {
        return $this->usersSenderid;
    }

    public function setSender($sender)
    {
        $this->usersSenderid = $sender;
    }

    public function setReceiver($receiver)
    {
        $this->usersReceiverid = $receiver;
    }

    public function getCreationDate()
    {
        return $this->creationdate;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationdate = $creationDate;
    }

    public function setReaded($readed)
    {
        $this->readed = $readed;
    }

    public function getMessage()
    {
        return $this->message;
    }


}
