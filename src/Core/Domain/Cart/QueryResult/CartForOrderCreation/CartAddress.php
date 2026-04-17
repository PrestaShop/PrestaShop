<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds address data for cart information
 */
class CartAddress
{
    /**
     * @var int
     */
    private $addressId;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $formattedAddress;

    /**
     * @var bool is it used as delivery address
     */
    private $delivery;

    /**
     * @var bool is it used as invoice address
     */
    private $invoice;

    /**
     * @param int $addressId
     * @param string $alias
     * @param string $formattedAddress
     * @param bool $delivery
     * @param bool $invoice
     */
    public function __construct(
        int $addressId,
        string $alias,
        string $formattedAddress,
        bool $delivery,
        bool $invoice
    ) {
        $this->addressId = $addressId;
        $this->alias = $alias;
        $this->formattedAddress = $formattedAddress;
        $this->delivery = $delivery;
        $this->invoice = $invoice;
    }

    /**
     * @return int
     */
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getFormattedAddress(): string
    {
        return $this->formattedAddress;
    }

    /**
     * @return bool
     */
    public function isDelivery(): bool
    {
        return $this->delivery;
    }

    /**
     * @return bool
     */
    public function isInvoice(): bool
    {
        return $this->invoice;
    }
}
