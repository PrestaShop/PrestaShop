<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
