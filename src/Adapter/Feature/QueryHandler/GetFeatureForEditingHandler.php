<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Feature\QueryHandler;

use Feature;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryHandler\GetFeatureForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;

/**
 * Handles get feature for editing query.
 */
#[AsQueryHandler]
final class GetFeatureForEditingHandler implements GetFeatureForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetFeatureForEditing $query)
    {
        $feature = new Feature($query->getFeatureId()->getValue());

        if (empty($feature->id)) {
            throw new FeatureNotFoundException('Feature could not be found.');
        }

        return new EditableFeature(
            $query->getFeatureId(),
            $feature->name,
            $feature->getAssociatedShops()
        );
    }
}
