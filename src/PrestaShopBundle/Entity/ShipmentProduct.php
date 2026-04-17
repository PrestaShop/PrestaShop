<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 *
 * @ORM\Entity()
 */
class ShipmentProduct
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_shipment_product", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Shipment", inversedBy="products", cascade={"all"})
     *
     * @ORM\JoinColumn(name="id_shipment", referencedColumnName="id_shipment", nullable=false, onDelete="CASCADE")
     */
    private ?Shipment $shipment = null;

    /**
     * @ORM\Column(name="id_order_detail", type="integer")
     */
    private int $orderDetailId;

    /**
     * @ORM\Column(name="quantity", type="integer")
     */
    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function setOrderDetailId(int $orderDetailId): self
    {
        $this->orderDetailId = $orderDetailId;

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
