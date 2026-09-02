<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

/**
 * DTO for order preview data
 */
class OrderPreview
{
    /**
     * @var OrderPreviewInvoiceDetails
     */
    private $invoiceDetails;

    /**
     * @var OrderPreviewShippingDetails
     */
    private $shippingDetails;

    /**
     * @var OrderPreviewProductDetail[]
     */
    private $productDetails;

    /**
     * @var bool
     */
    private $taxIncluded;

    /**
     * @var bool
     */
    private $isVirtual;

    /**
     * @var string
     */
    private $invoiceAddressFormatted;

    /**
     * @var string
     */
    private $shippingAddressFormatted;

    /**
     * @param OrderPreviewInvoiceDetails $invoiceDetails
     * @param OrderPreviewShippingDetails $shippingDetails
     * @param array $productDetails
     * @param bool $isVirtual
     * @param bool $taxIncluded
     * @param string $invoiceAddressFormatted
     * @param string $shippingAddressFormatted
     */
    public function __construct(
        OrderPreviewInvoiceDetails $invoiceDetails,
        OrderPreviewShippingDetails $shippingDetails,
        array $productDetails,
        bool $isVirtual,
        bool $taxIncluded,
        string $invoiceAddressFormatted = '',
        string $shippingAddressFormatted = ''
    ) {
        $this->invoiceDetails = $invoiceDetails;
        $this->shippingDetails = $shippingDetails;
        $this->productDetails = $productDetails;
        $this->taxIncluded = $taxIncluded;
        $this->isVirtual = $isVirtual;
        $this->invoiceAddressFormatted = $invoiceAddressFormatted;
        $this->shippingAddressFormatted = $shippingAddressFormatted;
    }

    /**
     * @return OrderPreviewInvoiceDetails
     */
    public function getInvoiceDetails(): OrderPreviewInvoiceDetails
    {
        return $this->invoiceDetails;
    }

    /**
     * @return OrderPreviewShippingDetails
     */
    public function getShippingDetails(): OrderPreviewShippingDetails
    {
        return $this->shippingDetails;
    }

    /**
     * @return OrderPreviewProductDetail[]
     */
    public function getProductDetails(): array
    {
        return $this->productDetails;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->taxIncluded;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    /**
     * @return string
     */
    public function getInvoiceAddressFormatted(): string
    {
        return $this->invoiceAddressFormatted;
    }

    /**
     * @return string
     */
    public function getShippingAddressFormatted(): string
    {
        return $this->shippingAddressFormatted;
    }
}
