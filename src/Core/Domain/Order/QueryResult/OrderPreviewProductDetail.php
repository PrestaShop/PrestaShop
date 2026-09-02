<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

/**
 * DTO for order product details
 */
class OrderPreviewProductDetail
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $unitPrice;

    /**
     * @var string
     */
    private $totalPrice;

    /**
     * @var string
     */
    private $totalTax;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $location;

    /**
     * @var int
     */
    private $id;

    /**
     * @param string $name
     * @param string $reference
     * @param string $location
     * @param int $quantity
     * @param string $unitPrice
     * @param string $totalPrice
     * @param string $totalTax
     * @param int $id
     */
    public function __construct(
        string $name,
        string $reference,
        string $location,
        int $quantity,
        string $unitPrice,
        string $totalPrice,
        string $totalTax,
        int $id
    ) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->totalPrice = $totalPrice;
        $this->totalTax = $totalTax;
        $this->reference = $reference;
        $this->location = $location;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    /**
     * @return string
     */
    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getTotalTax(): string
    {
        return $this->totalTax;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
