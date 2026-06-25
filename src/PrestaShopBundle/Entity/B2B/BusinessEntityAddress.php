<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

/**
 * BusinessEntityAddress.
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="business_entity_address_be_idx", columns={"id_business_entity"}),
 *         @ORM\Index(name="business_entity_address_address_idx", columns={"id_address"})
 *     },
 *     uniqueConstraints={
 *
 *         @ORM\UniqueConstraint(name="uniq_be_address", columns={"id_business_entity", "id_address", "address_type"})
 *     }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Entity()
 */
class BusinessEntityAddress
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_business_entity_address", type="integer", options={"unsigned"=true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntity", inversedBy="businessEntityAddresses")
     *
     * @ORM\JoinColumn(name="id_business_entity", referencedColumnName="id_business_entity", nullable=false)
     */
    private BusinessEntity $businessEntity;

    /**
     * @ORM\Column(name="id_address", type="integer", options={"unsigned"=true})
     */
    private int $idAddress;

    /**
     * @ORM\Column(name="is_default", type="boolean", options={"default"=false})
     */
    private bool $isDefault = false;

    /**
     * @ORM\Column(name="address_type", enumType=AddressTypeEnum::class, length=50)
     */
    private AddressTypeEnum $addressType = AddressTypeEnum::BOTH;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private DateTime $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusinessEntity(): BusinessEntity
    {
        return $this->businessEntity;
    }

    public function setBusinessEntity(BusinessEntity $businessEntity): self
    {
        $this->businessEntity = $businessEntity;

        return $this;
    }

    public function getAddressId(): int
    {
        return $this->idAddress;
    }

    public function setAddressId(int $idAddress): self
    {
        $this->idAddress = $idAddress;

        return $this;
    }

    public function getAddressType(): AddressTypeEnum
    {
        return $this->addressType;
    }

    public function setAddressType(AddressTypeEnum $addressType): self
    {
        $this->addressType = $addressType;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setDefault(bool $default): self
    {
        $this->isDefault = $default;

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
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();

        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
    }
}
