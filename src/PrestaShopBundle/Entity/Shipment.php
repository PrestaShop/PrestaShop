<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ShipmentRepository"))
 *
 * @ORM\HasLifecycleCallbacks
 */
class Shipment
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_shipment", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="id_order", type="integer")
     */
    private int $orderId;

    /**
     * @ORM\Column(name="id_carrier", type="integer")
     */
    private int $carrierId;

    /**
     * @ORM\Column(name="id_delivery_address", type="integer")
     */
    private int $addressId;

    /**
     * @ORM\Column(name="shipping_cost_tax_excl", type="float")
     */
    private float $shippingCostTaxExcluded;

    /**
     * @ORM\Column(name="shipping_cost_tax_incl", type="float")
     */
    private float $shippingCostTaxIncluded;

    /**
     * @ORM\Column(name="packed_at", type="datetime", nullable=true)
     */
    private ?DateTime $packedAt;

    /**
     * @ORM\Column(name="shipped_at", type="datetime", nullable=true)
     */
    private ?DateTime $shippedAt;

    /**
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     */
    private ?DateTime $deliveredAt;

    /**
     * @ORM\Column(name="cancelled_at", type="datetime", nullable=true)
     */
    private ?DateTime $cancelledAt;

    /**
     * @ORM\Column(name="tracking_number", type="string", nullable=true)
     */
    private ?string $trackingNumber;

    /**
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="date_upd", type="datetime", nullable=false)
     */
    private DateTime $updatedAt;

    /**
     * @var Collection<ShipmentProduct>
     *
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\ShipmentProduct", mappedBy="shipment", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getAddressId(): int
    {
        return $this->addressId;
    }

    public function getShippingCostTaxExcluded(): float
    {
        return $this->shippingCostTaxExcluded;
    }

    public function getShippingCostTaxIncluded(): float
    {
        return $this->shippingCostTaxIncluded;
    }

    public function getPackedAt(): ?DateTime
    {
        return $this->packedAt;
    }

    public function getShippedAt(): ?DateTime
    {
        return $this->shippedAt;
    }

    public function getDeliveredAt(): ?DateTime
    {
        return $this->deliveredAt;
    }

    public function getCancelledAt(): ?DateTime
    {
        return $this->cancelledAt;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setCarrierId(int $carrierId): self
    {
        $this->carrierId = $carrierId;

        return $this;
    }

    public function setAddressId(int $addressId): self
    {
        $this->addressId = $addressId;

        return $this;
    }

    public function setShippingCostTaxExcluded(
        float $shippingCostTaxExcluded
    ): self {
        $this->shippingCostTaxExcluded = $shippingCostTaxExcluded;

        return $this;
    }

    public function setShippingCostTaxIncluded(
        float $shippingCostTaxIncluded
    ): self {
        $this->shippingCostTaxIncluded = $shippingCostTaxIncluded;

        return $this;
    }

    public function setPackedAt(?DateTime $packedAt): self
    {
        $this->packedAt = $packedAt;

        return $this;
    }

    public function setShippedAt(?DateTime $shippedAt): self
    {
        $this->shippedAt = $shippedAt;

        return $this;
    }

    public function setDeliveredAt(?DateTime $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    public function setTrackingNumber(?string $trackingNumber): self
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    public function setCancelledAt(?DateTime $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function addShipmentProduct(ShipmentProduct $shipmentProduct): self
    {
        $this->products[] = $shipmentProduct;
        $shipmentProduct->setShipment($this);

        return $this;
    }

    public function removeProduct(ShipmentProduct $product): self
    {
        if ($product->getShipment() !== $this) {
            throw new ShipmentException('Trying to remove a product that does not belong to the shipment');
        }

        if ($this->products->removeElement($product)) {
            $product->setShipment(null);
        }

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
