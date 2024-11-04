<?php

namespace App\Entity;

use App\Enum\SaleStatus;
use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $saleDate = null;

    #[ORM\Column]
    private ?int $totalAmount = null;

    #[ORM\Column(enumType: SaleStatus::class)]
    private ?SaleStatus $status = null;

    #[ORM\OneToOne(mappedBy: 'sale', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    /**
     * @var Collection<int, SaleDetails>
     */
    #[ORM\OneToMany(targetEntity: SaleDetails::class, mappedBy: 'sale')]
    private Collection $saleDetails;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->saleDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleDate(): ?\DateTimeInterface
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTimeInterface $saleDate): static
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getStatus(): ?SaleStatus
    {
        return $this->status;
    }

    public function setStatus(SaleStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): static
    {
        // set the owning side of the relation if necessary
        if ($invoice->getSale() !== $this) {
            $invoice->setSale($this);
        }

        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collection<int, SaleDetails>
     */
    public function getSaleDetails(): Collection
    {
        return $this->saleDetails;
    }

    public function addSaleDetail(SaleDetails $saleDetail): static
    {
        if (!$this->saleDetails->contains($saleDetail)) {
            $this->saleDetails->add($saleDetail);
            $saleDetail->setSale($this);
        }

        return $this;
    }

    public function removeSaleDetail(SaleDetails $saleDetail): static
    {
        if ($this->saleDetails->removeElement($saleDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleDetail->getSale() === $this) {
                $saleDetail->setSale(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
