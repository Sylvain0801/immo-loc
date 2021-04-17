<?php

namespace App\Entity;

use App\Repository\MessageReadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageReadRepository::class)
 */
class MessageRead
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messageReads")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="messageReads")
     * @ORM\JoinColumn(nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $not_read;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getNotRead(): ?bool
    {
        return $this->not_read;
    }

    public function setNotRead(bool $not_read): self
    {
        $this->not_read = $not_read;

        return $this;
    }
}
