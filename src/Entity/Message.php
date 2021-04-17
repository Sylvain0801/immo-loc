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
     * @ORM\Column(type="string", length=255)
     */
    private $sender;

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
     * @ORM\Column(type="string", length=128)
     */
    private $firstname_sender;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $lastname_sender;

    /**
     * @ORM\OneToMany(targetEntity=MessageRead::class, mappedBy="message")
     */
    private $messageReads;


    public function __construct()
    {
        $this->recipient = new ArrayCollection();
        $this->messageReads = new ArrayCollection();
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

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

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

    public function getFirstnameSender(): ?string
    {
        return $this->firstname_sender;
    }

    public function setFirstnameSender(string $firstname_sender): self
    {
        $this->firstname_sender = $firstname_sender;

        return $this;
    }

    public function getLastnameSender(): ?string
    {
        return $this->lastname_sender;
    }

    public function setLastnameSender(string $lastname_sender): self
    {
        $this->lastname_sender = $lastname_sender;

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

}