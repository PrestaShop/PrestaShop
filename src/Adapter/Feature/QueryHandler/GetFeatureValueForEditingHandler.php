<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureValueForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryHandler\GetFeatureValueForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

#[AsQueryHandler]
class GetFeatureValueForEditingHandler implements GetFeatureValueForEditingHandlerInterface
{
    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    /**
     * @param FeatureValueRepository $featureValueRepository
     */
    public function __construct(FeatureValueRepository $featureValueRepository)
    {
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetFeatureValueForEditing $query): EditableFeatureValue
    {
        $featureValue = $this->featureValueRepository->get($query->getFeatureValueId());

        return new EditableFeatureValue(
            $query->getFeatureValueId(),
            new FeatureId((int) $featureValue->id_feature),
            $featureValue->value
        );
    }
}
