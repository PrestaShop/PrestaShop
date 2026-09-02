<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\FeatureValue\Update\ProductFeatureValueUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\CommandHandler\SetProductFeatureValuesHandlerInterface;

/**
 * Handles @see SetProductFeatureValuesCommand using legacy object model
 */
#[AsCommandHandler]
class SetProductFeatureValuesHandler implements SetProductFeatureValuesHandlerInterface
{
    /**
     * @var ProductFeatureValueUpdater
     */
    private $productFeatureValueUpdater;

    public function __construct(ProductFeatureValueUpdater $productFeatureValueUpdater)
    {
        $this->productFeatureValueUpdater = $productFeatureValueUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetProductFeatureValuesCommand $command): array
    {
        return $this->productFeatureValueUpdater->setFeatureValues($command->getProductId(), $command->getFeatureValues());
    }
}
