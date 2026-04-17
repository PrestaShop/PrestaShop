<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\B2B;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

/**
 * BusinessEntityAddress.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="business_entity_address_address_idx", columns={"id_address"})}
 * )
 *
 * @ORM\Entity()
 */
class BusinessEntityAddress
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\B2B\BusinessEntity", inversedBy="businessEntityAddresses")
     *
     * @ORM\JoinColumn(name="id_business_entity", referencedColumnName="id_business_entity", nullable=false)
     */
    private BusinessEntity $businessEntity;

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_address", type="integer", options={"unsigned"=true})
     */
    private int $idAddress;

    /**
     * @ORM\Column(name="`default`", type="boolean", options={"default"=false})
     */
    private bool $default = false;

    /**
     * @ORM\Column(name="address_type", enumType=AddressTypeEnum::class, length=50)
     */
    private AddressTypeEnum $addressType = AddressTypeEnum::BOTH;

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
        return $this->default;
    }

    public function setDefault(bool $default): self
    {
        $this->default = $default;

        return $this;
    }
}
