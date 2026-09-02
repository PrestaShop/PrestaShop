<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderDocumentsForViewing
{
    /**
     * @var OrderDocumentForViewing[]
     */
    private $documents = [];

    /**
     * @var bool
     */
    private $canGenerateInvoice;

    /**
     * @var bool
     */
    private $canGenerateDeliverySlip;

    /**
     * @param bool $canGenerateInvoice
     * @param bool $canGenerateDeliverySlip
     * @param OrderDocumentForViewing[] $documents
     */
    public function __construct(bool $canGenerateInvoice, bool $canGenerateDeliverySlip, array $documents)
    {
        foreach ($documents as $document) {
            $this->add($document);
        }

        $this->canGenerateInvoice = $canGenerateInvoice;
        $this->canGenerateDeliverySlip = $canGenerateDeliverySlip;
    }

    /**
     * @return OrderDocumentForViewing[]
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @return bool
     */
    public function canGenerateInvoice(): bool
    {
        return $this->canGenerateInvoice;
    }

    /**
     * @return bool
     */
    public function canGenerateDeliverySlip(): bool
    {
        return $this->canGenerateDeliverySlip;
    }

    private function add(OrderDocumentForViewing $document): void
    {
        $this->documents[] = $document;
    }
}
