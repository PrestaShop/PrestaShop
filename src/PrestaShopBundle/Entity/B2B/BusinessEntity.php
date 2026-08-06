<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * BusinessEntity.
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="business_entity_shop_idx", columns={"id_shop"}),
 *         @ORM\Index(name="business_entity_customer_group_idx", columns={"id_customer_group"}),
 *         @ORM\Index(name="business_entity_external_ref_idx", columns={"external_ref"}),
 *         @ORM\Index(name="business_entity_deleted_idx", columns={"deleted"})
 *     }
 *  )
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity()
 */
class BusinessEntity
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_business_entity", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_shop", type="integer", options={"unsigned"=true})
     */
    private int $idShop;

    /**
     * @ORM\Column(name="id_customer_group", type="integer", options={"unsigned"=true})
     */
    private int $idCustomerGroup;

    /**
     * @ORM\Column(name="external_ref", type="string", length=255, nullable=true)
     */
    private ?string $externalRef;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(name="legal_name", type="string", length=255, nullable=true)
     */
    private ?string $legalName;

    /**
     * @ORM\Column(name="delivery_authorized", type="boolean", options={"default"=false})
     */
    private bool $deliveryAuthorized = false;

    /**
     * @ORM\Column(name="status", enumType=BusinessEntityStatus::class, options={"default"="pending"})
     */
    private BusinessEntityStatus $status;

    /**
     * @ORM\Column(name="deleted", type="boolean", options={"default"=false})
     */
    private bool $deleted = false;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntityAddress", mappedBy="businessEntity")
     */
    private Collection $businessEntityAddresses;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntityIdentifier", mappedBy="businessEntity")
     */
    private Collection $businessEntityIdentifiers;

    /**
     * @ORM\OneToMany(
     *     targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b",
     *     mappedBy="businessEntity",
     *     orphanRemoval=true,
     *     cascade={"persist","remove"}
     * )
     */
    private Collection $businessEntityCustomerB2bs;

    public function __construct()
    {
        $this->businessEntityAddresses = new ArrayCollection();
        $this->businessEntityIdentifiers = new ArrayCollection();
        $this->businessEntityCustomerB2bs = new ArrayCollection();
        $this->status = BusinessEntityStatus::PENDING;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdShop(): int
    {
        return $this->idShop;
    }

    public function setIdShop(int $idShop): self
    {
        $this->idShop = $idShop;

        return $this;
    }

    public function getIdCustomerGroup(): int
    {
        return $this->idCustomerGroup;
    }

    public function setIdCustomerGroup(int $idCustomerGroup): self
    {
        $this->idCustomerGroup = $idCustomerGroup;

        return $this;
    }

    public function getExternalRef(): ?string
    {
        return $this->externalRef;
    }

    public function setExternalRef(?string $externalRef): self
    {
        $this->externalRef = $externalRef;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(?string $legalName): self
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function isDeliveryAuthorized(): bool
    {
        return $this->deliveryAuthorized;
    }

    public function setDeliveryAuthorized(bool $deliveryAuthorized): self
    {
        $this->deliveryAuthorized = $deliveryAuthorized;

        return $this;
    }

    public function getStatus(): BusinessEntityStatus
    {
        return $this->status;
    }

    public function setStatus(BusinessEntityStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, BusinessEntityAddress>
     */
    public function getBusinessEntityAddresses(): Collection
    {
        return $this->businessEntityAddresses;
    }

    public function addBusinessEntityAddress(BusinessEntityAddress $businessEntityAddress): self
    {
        if (!$this->businessEntityAddresses->contains($businessEntityAddress)) {
            $this->businessEntityAddresses[] = $businessEntityAddress;
            $businessEntityAddress->setBusinessEntity($this);
        }

        return $this;
    }

    public function removeBusinessEntityAddress(BusinessEntityAddress $businessEntityAddress): self
    {
        $this->businessEntityAddresses->removeElement($businessEntityAddress);

        return $this;
    }

    /**
     * @return Collection<int, BusinessEntityIdentifier>
     */
    public function getBusinessEntityIdentifiers(): Collection
    {
        return $this->businessEntityIdentifiers;
    }

    public function addBusinessEntityIdentifier(BusinessEntityIdentifier $businessEntityIdentifier): self
    {
        if (!$this->businessEntityIdentifiers->contains($businessEntityIdentifier)) {
            $this->businessEntityIdentifiers[] = $businessEntityIdentifier;
            $businessEntityIdentifier->setBusinessEntity($this);
        }

        return $this;
    }

    public function removeBusinessEntityIdentifier(BusinessEntityIdentifier $businessEntityIdentifier): self
    {
        $this->businessEntityIdentifiers->removeElement($businessEntityIdentifier);

        return $this;
    }

    /**
     * @return Collection<int, BusinessEntityCustomerB2b>
     */
    public function getBusinessEntityCustomerB2bs(): Collection
    {
        return $this->businessEntityCustomerB2bs;
    }

    public function addBusinessEntityCustomerB2b(BusinessEntityCustomerB2b $businessEntityCustomerB2b): self
    {
        if (!$this->businessEntityCustomerB2bs->contains($businessEntityCustomerB2b)) {
            $this->businessEntityCustomerB2bs[] = $businessEntityCustomerB2b;
            $businessEntityCustomerB2b->setBusinessEntity($this);
        }

        return $this;
    }

    public function removeBusinessEntityCustomerB2b(BusinessEntityCustomerB2b $businessEntityCustomerB2b): self
    {
        $this->businessEntityCustomerB2bs->removeElement($businessEntityCustomerB2b);

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->updatedAt = new DateTime();
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
    }
}
