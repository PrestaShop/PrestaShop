<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Tax\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\QueryHandler\GetTaxForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult\EditableTax;

/**
 * Handles query which gets tax for editing
 */
#[AsQueryHandler]
final class GetTaxForEditingHandler extends AbstractTaxHandler implements GetTaxForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetTaxForEditing $query)
    {
        $tax = $this->getTax($query->getTaxId());

        return new EditableTax(
            $query->getTaxId(),
            $tax->name,
            (float) $tax->rate,
            (bool) $tax->active
        );
    }
}
