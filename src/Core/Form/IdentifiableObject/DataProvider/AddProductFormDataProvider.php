<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class AddProductFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private CurrencyDataProvider $currencyDataProvider,
        private FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    public function getData($orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->queryBus->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));
        $currency = $this->currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());
        $multishipmentIsEnabled = $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT);

        return [
            'order_id' => $orderId,
            'currency' => $currency,
            'is_multishipment_is_enabled' => $multishipmentIsEnabled,
        ];
    }

    public function getDefaultData()
    {
        return [];
    }
}
