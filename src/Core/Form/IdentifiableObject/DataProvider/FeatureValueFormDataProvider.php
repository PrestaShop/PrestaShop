<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureValueForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeatureValue;

class FeatureValueFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var EditableFeatureValue $featureValueForEditing */
        $featureValueForEditing = $this->queryBus->handle(new GetFeatureValueForEditing($id));

        return [
            'feature_id' => $featureValueForEditing->getFeatureId()->getValue(),
            'feature_value_id' => $featureValueForEditing->getFeatureValueId()->getValue(),
            'value' => $featureValueForEditing->getLocalizedValues(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
