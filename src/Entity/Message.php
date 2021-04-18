<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

   /**
     * @var \DateTime $ created_at
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="messages")
     */
    private $recipient;

    /**
     * @ORM\OneToMany(targetEntity=MessageRead::class, mappedBy="message")
     */
    private $messageReads;

    /**
     * @ORM\ManyToMany(targetEntity=Admin::class, inversedBy="messages")
     */
    private $admin_recipient;

    /**
     * @ORM\OneToMany(targetEntity=AdminMessageRead::class, mappedBy="message")
     */
    private $adminMessageReads;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $firstname_sender;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $lastname_sender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email_sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages_sent")
     */
    private $sender_user;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="messages_sent")
     */
    private $sender_admin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender;


    public function __construct()
    {
        $this->recipient = new ArrayCollection();
        $this->messageReads = new ArrayCollection();
        $this->admin_recipient = new ArrayCollection();
        $this->adminMessageReads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @return Collection|User[]
     */
    public function getRecipient(): Collection
    {
        return $this->recipient;
    }

    public function addRecipient(User $recipient): self
    {
        if (!$this->recipient->contains($recipient)) {
            $this->recipient[] = $recipient;
        }

        return $this;
    }

    public function removeRecipient(User $recipient): self
    {
        $this->recipient->removeElement($recipient);

        return $this;
    }

    /**
     * @return Collection|MessageRead[]
     */
    public function getMessageReads(): Collection
    {
        return $this->messageReads;
    }

    public function addMessageRead(MessageRead $messageRead): self
    {
        if (!$this->messageReads->contains($messageRead)) {
            $this->messageReads[] = $messageRead;
            $messageRead->setMessage($this);
        }

        return $this;
    }

    public function removeMessageRead(MessageRead $messageRead): self
    {
        if ($this->messageReads->removeElement($messageRead)) {
            // set the owning side to null (unless already changed)
            if ($messageRead->getMessage() === $this) {
                $messageRead->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Admin[]
     */
    public function getAdminRecipient(): Collection
    {
        return $this->admin_recipient;
    }

    public function addAdminRecipient(Admin $adminRecipient): self
    {
        if (!$this->admin_recipient->contains($adminRecipient)) {
            $this->admin_recipient[] = $adminRecipient;
        }

        return $this;
    }

    public function removeAdminRecipient(Admin $adminRecipient): self
    {
        $this->admin_recipient->removeElement($adminRecipient);

        return $this;
    }

    /**
     * @return Collection|AdminMessageRead[]
     */
    public function getAdminMessageReads(): Collection
    {
        return $this->adminMessageReads;
    }

    public function addAdminMessageRead(AdminMessageRead $adminMessageRead): self
    {
        if (!$this->adminMessageReads->contains($adminMessageRead)) {
            $this->adminMessageReads[] = $adminMessageRead;
            $adminMessageRead->setMessage($this);
        }

        return $this;
    }

    public function removeAdminMessageRead(AdminMessageRead $adminMessageRead): self
    {
        if ($this->adminMessageReads->removeElement($adminMessageRead)) {
            // set the owning side to null (unless already changed)
            if ($adminMessageRead->getMessage() === $this) {
                $adminMessageRead->setMessage(null);
            }
        }

        return $this;
    }

    public function getFirstnameSender(): ?string
    {
        return $this->firstname_sender;
    }

    public function setFirstnameSender(?string $firstname_sender): self
    {
        $this->firstname_sender = $firstname_sender;

        return $this;
    }

    public function getLastnameSender(): ?string
    {
        return $this->lastname_sender;
    }

    public function setLastnameSender(?string $lastname_sender): self
    {
        $this->lastname_sender = $lastname_sender;

        return $this;
    }

    public function getEmailSender(): ?string
    {
        return $this->email_sender;
    }

    public function setEmailSender(?string $email_sender): self
    {
        $this->email_sender = $email_sender;

        return $this;
    }

    public function getSenderUser(): ?User
    {
        return $this->sender_user;
    }

    public function setSenderUser(?User $sender_user): self
    {
        $this->sender_user = $sender_user;

        return $this;
    }

    public function getSenderAdmin(): ?Admin
    {
        return $this->sender_admin;
    }

    public function setSenderAdmin(?Admin $sender_admin): self
    {
        $this->sender_admin = $sender_admin;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

}