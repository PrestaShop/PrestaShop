<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;

interface GetProductCustomizationFieldsHandlerInterface
{
    /**
     * @param GetProductCustomizationFields $query
     *
     * @return CustomizationField[]
     */
    public function handle(GetProductCustomizationFields $query): array;
}
