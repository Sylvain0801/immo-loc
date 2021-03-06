<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Admin implements UserInterface
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
     * @ORM\OneToMany(targetEntity=AdminMessageRead::class, mappedBy="admin")
     */
    private $adminMessageReads;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender_admin")
     */
    private $messages_sent;

    public function __construct()
    {
        $this->adminMessageReads = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
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
            $adminMessageRead->setAdmin($this);
        }

        return $this;
    }

    public function removeAdminMessageRead(AdminMessageRead $adminMessageRead): self
    {
        if ($this->adminMessageReads->removeElement($adminMessageRead)) {
            // set the owning side to null (unless already changed)
            if ($adminMessageRead->getAdmin() === $this) {
                $adminMessageRead->setAdmin(null);
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
            $messagesSent->setSenderAdmin($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): self
    {
        if ($this->messages_sent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSenderAdmin() === $this) {
                $messagesSent->setSenderAdmin(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname().' '.$this->getUsername().' ADMIN';
    }

}
