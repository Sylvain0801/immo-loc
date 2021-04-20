<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @Vich\Uploadable
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $documentName;

    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="documentName")
     * @var File
     */
    private $documentFile;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $category;

    /**
     * @var \DateTime $ created_at
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="documents")
     */
    private $doc_user_access;

    public function __construct()
    {
        $this->doc_user_access = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setDocumentFile(File $document = null)
    {
        $this->documentFile = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;
    }

    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * @return Collection|User[]
     */
    public function getDocUserAccess(): Collection
    {
        return $this->doc_user_access;
    }

    public function addDocUserAccess(User $docUserAccess): self
    {
        if (!$this->doc_user_access->contains($docUserAccess)) {
            $this->doc_user_access[] = $docUserAccess;
        }

        return $this;
    }

    public function removeDocUserAccess(User $docUserAccess): self
    {
        $this->doc_user_access->removeElement($docUserAccess);

        return $this;
    }
}
