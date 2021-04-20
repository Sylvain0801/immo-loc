<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $lastname;

    /**
     * @var \DateTime $ created_at
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

      /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Announce::class, mappedBy="created_by")
     */
    private $announces;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="owner")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=MessageRead::class, mappedBy="user")
     */
    private $messageReads;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender_user")
     */
    private $messages_sent;

    public function __construct()
    {
        $this->announces = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->messageReads = new ArrayCollection();
        $this->messages_sent = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @return Collection|Announce[]
     */
    public function getAnnounces(): Collection
    {
        return $this->announces;
    }

    public function addAnnounce(Announce $announce): self
    {
        if (!$this->announces->contains($announce)) {
            $this->announces[] = $announce;
            $announce->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnnounce(Announce $announce): self
    {
        if ($this->announces->removeElement($announce)) {
            // set the owning side to null (unless already changed)
            if ($announce->getCreatedBy() === $this) {
                $announce->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getUserMailRole()
    {
        $roles = $this->getRoles();
        
        if(in_array('ROLE_ADMIN', $roles)){
            return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' '.'Admin';
        }
        if(!in_array('ROLE_ADMIN', $roles) && in_array('ROLE_AGENT', $roles)){
            return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' '.'Agent';
        }
        if(!in_array('ROLE_ADMIN', $roles) && in_array('ROLE_OWNER', $roles)){
            return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' '.'Owner';
        }
        if(!in_array('ROLE_ADMIN', $roles) && in_array('ROLE_LEASEOWNER', $roles)){
            return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' '.'Lease owner';
        }
        if(!in_array('ROLE_ADMIN', $roles) && in_array('ROLE_TENANT', $roles)){
            return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' '.'Tenant';
        }
    }

    public function __toString()
    {
        return $this->getUserMailRole();
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setOwner($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getOwner() === $this) {
                $document->setOwner(null);
            }
        }

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
            $messageRead->setUser($this);
        }

        return $this;
    }

    public function removeMessageRead(MessageRead $messageRead): self
    {
        if ($this->messageReads->removeElement($messageRead)) {
            // set the owning side to null (unless already changed)
            if ($messageRead->getUser() === $this) {
                $messageRead->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesSent(): Collection
    {
        return $this->messages_sent;
    }

    public function addMessagesSent(Message $messagesSent): self
    {
        if (!$this->messages_sent->contains($messagesSent)) {
            $this->messages_sent[] = $messagesSent;
            $messagesSent->setSenderUser($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): self
    {
        if ($this->messages_sent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSenderUser() === $this) {
                $messagesSent->setSenderUser(null);
            }
        }

        return $this;
    }


}
