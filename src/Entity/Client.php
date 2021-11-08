<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Facture;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ApiResource(
 *  attributes={
 * "pagination_enabled"=true,
 * "pagination_items_per_page"=1
 * },
 * normalizationContext={"groups"={"client-read"}},
 * collectionOperations={"GET"},
 * itemOperations={"GET"={"path"="/Clients/{id}"},"PUT"}
 * )
 * 
 * @ApiFilter(SearchFilter::class , properties={"nom":"partial","preNom":"start","mail":"exact"})
 * 
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"client-read","facture-read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"client-read","facture-read"})
     */
    private $preNom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"client-read","facture-read"})
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"client-read","facture-read"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Facture::class, mappedBy="customer", orphanRemoval=true)
     * 
     */
    private $factures;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="client")
     * @Groups({"client-read"})
     */
    private $user;
 

    public function __construct()
    {
        $this->factures = new ArrayCollection();
    }
    /**
     * Undocumented function
     * @Groups({"client-read"})
     * @return float
     */ 
    public function getTotalFac(): float{
        return array_reduce($this->factures->toArray(),function($total,$facture){
            return $total + $facture->getMontant();
        },0);
    }
    /**
     * Undocumented function
     * @Groups({"client-read"})
     * @return float
     */
    public function getTotalDu(): float{
        return array_reduce($this->factures->toArray(),function($total,$facture){
            return $total + ($facture->getSatut() === "PAID" || $facture->getSatut() === "CANCELED" ? 0 :$facture->getMontant());
        },0);
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPreNom(): ?string
    {
        return $this->preNom;
    }

    public function setPreNom(string $preNom): self
    {
        $this->preNom = $preNom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Facture[]
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setCustomer($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getCustomer() === $this) {
                $facture->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *@Groups({"facture-read"})
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
