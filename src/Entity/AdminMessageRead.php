<?php

namespace App\Entity;

use App\Repository\AdminMessageReadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminMessageReadRepository::class)
 */
class AdminMessageRead
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="adminMessageReads")
     */
    private $admin;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="adminMessageReads")
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

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): self
    {
        $this->admin = $admin;

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
