<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tax;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShopException;
use Tax;

/**
 * Provides reusable methods for tax command/query handlers.
 */
abstract class AbstractTaxHandler
{
    /**
     * Gets legacy Tax
     *
     * @param TaxId $taxId
     *
     * @return Tax
     */
    protected function getTax(TaxId $taxId)
    {
        try {
            $tax = new Tax($taxId->getValue());
        } catch (PrestaShopException $e) {
            throw new TaxException('Failed to create new tax', 0, $e);
        }

        if ($tax->id !== $taxId->getValue()) {
            throw new TaxNotFoundException(sprintf('Tax with id "%s" was not found.', $taxId->getValue()));
        }

        return $tax;
    }
}
