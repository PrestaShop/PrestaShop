<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShop\PrestaShop\Core\Domain\Zone\QueryResult\EditableZone;

/**
 * Provides data for zone add/edit form.
 */
final class ZoneFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $multistoreEnabled;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $multistoreEnabled
     * @param array $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $queryBus,
        bool $multistoreEnabled,
        array $defaultShopAssociation
    ) {
        $this->queryBus = $queryBus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /**
         * @var EditableZone
         */
        $result = $this->queryBus->handle(new GetZoneForEditing($id));

        $data = [
            'id' => $id,
            'name' => $result->getName(),
            'enabled' => $result->isEnabled(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $result->getAssociatedShops();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        $data = [
            'name' => '',
            'enabled' => true,
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
