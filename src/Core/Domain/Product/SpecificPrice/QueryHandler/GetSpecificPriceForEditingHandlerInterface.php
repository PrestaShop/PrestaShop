<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceForEditing;

interface GetSpecificPriceForEditingHandlerInterface
{
    /**
     * @param GetSpecificPriceForEditing $query
     *
     * @return SpecificPriceForEditing
     */
    public function handle(GetSpecificPriceForEditing $query): SpecificPriceForEditing;
}
